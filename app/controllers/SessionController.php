<?php

class SessionController extends BaseController
{

    public function create()
    {
        $client = new OAuth2\Client(Config::get('reddit.client_id'), Config::get('reddit.client_secret'), OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        $auth_url = $client->getAuthenticationUrl(
            Config::get('reddit.authorization_endpoint'),
            Config::get('reddit.redirect_uri'),
            [
                "scope" => "identity,mysubreddits",
                "state" => "HootSuiteRedditHappyFuntimeHour",
                "duration" => "permanent"
            ]
        );

        $data = [
            'auth_url' => $auth_url,
            'api_key' => Config::get('hootsuite.apikey'),
            'css_locations' => Config::get('hootsuite.csslocations'),
        ];

        return View::make('session/create', $data);
    }

    public function store()
    {
        if(Input::get('error'))
        {
            Log::error("Authorization error occurred: ". Input::get('error'));
            return View::make('home.close');
        }

        $client = new OAuth2\Client(Config::get('reddit.client_id'), Config::get('reddit.client_secret'), OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
        if (!isset($_GET["code"])) {
            $query = http_build_query(array(
                    'theme'    => Input::get('theme'),
                    'pid'      => Input::get('pid'),
                    'uid'      => Input::get('uid')
                ));
            return Redirect::to('system/login/?' . $query);
        } else {
            $params = array("code" => Input::get('code'), "redirect_uri" => Config::get('reddit.redirect_uri'));
            $response = $client->getAccessToken(Config::get('reddit.token_endpoint'), "authorization_code", $params);

            $reddit = new Reddit();
            $me_response = $reddit->oauth2_request("https://oauth.reddit.com/api/v1/me.json", $response['result']['access_token']);

            Auth::user()->fill([
               'reddit_username' => $me_response->name,
                'access_token' => $response['result']['access_token'],
                'refresh_token' => $response['result']['refresh_token'],
                'active' => true
            ]);
            Auth::user()->save();
            return View::make('session/store');
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        Auth::logout();
        Log::info("params ?" . http_build_query($user->getHootsuiteParams()));
        return action('SessionController@create') . '?' . http_build_query($user->getHootsuiteParams());
    }
}
