<?php

class StreamController extends BaseController {

    protected $redditService;

    public function __construct()
    {
        $this->redditService = new RedditService;
    }

    public function show()
    {
        $user = Auth::user();
        $reddit_data =  $this->redditService->getPosts($user->current_subreddit);

        Log::info("messages being set to " . json_encode($reddit_data->data));

        return View::make('stream/show')->with('pid', $user->pid)
            ->with('uid', $user->uid)
            ->with('username', $user->reddit_username)
            ->with('subscriptions',  $this->redditService->get_subscriptions(true))
            ->with('messages', $reddit_data->data)
            ->with('api_key', Config::get('hootsuite.apikey'))
            ->with('before', $reddit_data->before)
            ->with('after', $reddit_data->after)
            ->with('subreddit_name', $user->current_subreddit)
            ->with('css_locations', Config::get('hootsuite.css_locations'));
    }
}
