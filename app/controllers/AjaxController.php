<?php

class AjaxController extends BaseController {

    protected $redditService;

    public function __construct()
    {
        $this->redditService = new RedditService;
    }

    public function getListings($subreddit = 'home', $after = null)
    {
        $response = $this->redditService->getPosts($subreddit, $after);
        $html = '';
        foreach ($response->data as $message) {
            $html .= View::make('stream.partial.message', ['message' => $message]);
        }

        $data = array(
            'html'  => $html,
            'before' => $response->before,
            'after' => $response->after
        );
        return json_encode($data);
    }

    public function postUpdateSubreddit()
    {
        if ($subreddit = Input::get('subreddit')) {
            $user = Auth::user();
//            $user = User::where('pid', Input::get('pid') . '-' . Input::get('uid'))->first();
            Log::info('the user found is ' . $user->reddit_username);
            $user->current_subreddit = $subreddit;
            $user->save();
        }
    }

} 