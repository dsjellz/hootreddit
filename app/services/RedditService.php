<?php
class RedditService
{

    /**
     * Get listings from cache if possible, otherwise refresh and cache data from Reddit
     * @param string $subreddit - name of the subreddit to get listings for
     * @param null $after - subreddit id to query for only posts before this one
     * @return array
     */
    public function getPosts($subreddit = 'home', $after = null)
    {
        $user = Auth::user();
        $cache_name = "{$user->reddit_username}_{$subreddit}_after_{$after}";

        if (Cache::has($cache_name)) {
            $response = Cache::get($cache_name);
        } else {
            $response = $this->getListing($subreddit, 25, $user->reddit_cookie, $after);
            Cache::put($cache_name, $response, Config::get('reddit.subreddit_cache'));
        }
        $postCollection = [];
        foreach ($response->data->children as $post) {
            $postCollection[] = RedditPost::fromReddit($post);
        }
        return new RedditResponse($postCollection, $response->data->before, $response->data->after);
    }

    /**
     * Get subscriptions from cache if possible, otherwise refresh and cache data from Reddit
     * @return array
     */
    public function get_subscriptions($refresh = false)
    {
        $user = Auth::user();
        $cache_name = "{$user->reddit_username}_subscriptions";

        if(Cache::has($cache_name)) {
            Log::info("Fetching subscriptions for " . $cache_name . " from cache.");
            return Cache::get($cache_name);
        } else {
            Log::info("Fetching subscriptions for " . $cache_name . " from reddit.");
            $reddit_subscriptions = $this->oauthRequest('https://oauth.reddit.com/reddits/mine/subscriber.json', $user->access_token);

            if( !$reddit_subscriptions && $refresh == true){
                Log::info("Request for subscriptions results in 401, trying one more time after token refresh");
                $this->refresh_token();
                return $this->get_subscriptions(false);
            }
            Log::info("Reddit subscriptions " . json_encode($reddit_subscriptions));
            $subscriptions = Subscriptions_mapper::map($reddit_subscriptions);
            Cache::put($cache_name, $subscriptions, Config::get('reddit.subscription_cache'));
            return $subscriptions;
        }
    }
    //return $this->oauth2_request('https://oauth.reddit.com/reddits/mine/subscriber.json', $access_token);

    public function oauthRequest($url, $accessToken, $postVals = null)
    {
        $ch = curl_init($url);
        $options = array(
            CURLOPT_USERAGENT => 'Hootsuite Plugin for Reddit user-agent string',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        if ($postVals){
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }
        // set access token
        $options[CURLOPT_HTTPHEADER] = array('Authorization: Bearer ' . $accessToken);

        curl_setopt_array($ch, $options);

        $resp = curl_exec($ch);
        $info = curl_getinfo($ch);

        $response = json_decode($resp);
        curl_close($ch);

        // Log all info about the request if the response code was not 200
        if($info['http_code'] != 200) {
            Log::info('OAuth2 Request URL: ' . $info['url'] . '. Response Code: ' . $info['http_code']);
            ob_start();
            print_r( $info );
            $msg = ob_get_contents();
            ob_end_clean();
            Log::info('Curl Info: ' . $msg);
        }

        if($info['http_code'] == 401) {
            return false;
        }

        return $response;
    }

    /**
     * Attempt to refresh an access token
     */
    public function refresh_token()
    {
        $user = Auth::user();
        $refresh_token =  Auth::user()->refresh_token;

        $params = array(
            'refresh_token' => $refresh_token,
            "redirect_uri" => Config::get('reddit.redirect_uri')
        );

        $client = new OAuth2\Client(Config::get('reddit.client_id'), Config::get('reddit.client_secret'), OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $refresh_response = $client->getAccessToken(Config::get('reddit.token_endpoint'), "refresh_token", $params);

        $user->access_token = $refresh_response['result']['access_token'];
//        $user->refresh_token = $refresh_response['result']['refresh_token'];

        $user->save();
        Log::info('Access token refreshed for ' . $user->reddit_username);
    }

    public function getListing($sr, $limit = 25, $cookie = NULL, $after = NULL)
    {
        $query_params = (isset($limit)) ? "?limit=".$limit : "";

        if($after){
            $query_params .= '&after=' . $after;
        }

        if($sr == 'home' || $sr == 'reddit' || !isset($sr))
        {
            $urlListing = "http://www.reddit.com/.json{$query_params}";
        }else{
            $urlListing = "http://www.reddit.com/r/{$sr}/.json{$query_params}";
        }
        $response = $this->runCurl($urlListing, NULL, $cookie);

        if($response == null)
        {
            throw new Exception('Response from Reddit.getListings() should not be null');
        }

        return $response;
    }

    private function runCurl($url, $postVals = null, $cookie = NULL){

        $ch = curl_init($url);
        $options = array(
            CURLOPT_USERAGENT => 'Hootsuite Plugin for Reddit user-agent string',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        if ($postVals){
            $options[CURLOPT_POSTFIELDS] = $postVals;
            $options[CURLOPT_CUSTOMREQUEST] = "POST";
        }

        if($cookie)
        {
            $options[CURLOPT_COOKIE] = "reddit_session={$cookie}";
        }

        curl_setopt_array($ch, $options);
        $resp = curl_exec($ch);
        $info = curl_getinfo($ch);

        $response = json_decode($resp);
        curl_close($ch);

        // Log all info about the request if the response code was not 200
        if($info['http_code'] != 200)
        {
            Log::info('OAuth2 Request URL: ' . $info['url'] . '. Response Code: ' . $info['http_code']);
            ob_start();
            print_r( $info );
            $msg = ob_get_contents();
            ob_end_clean();
            Log::info('Curl Info: ' . $msg);
        }

        return $response;
    }

}