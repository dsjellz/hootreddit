<?php

/**
 * Represents a single reddit post object
 * Class RedditPost
 */
class RedditPost {

    protected $properties = ['subreddit', 'permalink', 'domain', 'id', 'scope', 'title', 'url', 'num_comments',
        'created', 'author', 'nsfw', 'thumb'];

    public static function fromReddit($post)
    {
        $redditPost = new RedditPost;
        $redditPost->subreddit     = $post->data->subreddit;
        $redditPost->permalink     = $post->data->permalink;
        $redditPost->domain        = $post->data->domain;
        $redditPost->id            = $post->data->id;
        $redditPost->score         = $post->data->score;
        $redditPost->title         = $post->data->title;
        $redditPost->url           = $post->data->url;
        $redditPost->num_comments  = $post->data->num_comments;
        $redditPost->created       = HootsuiteUtil::timeElapsed($post->data->created_utc);
        $redditPost->author        = $post->data->author;
        $redditPost->nsfw          = $post->data->over_18;
        $redditPost->thumb         = HootsuiteUtil::getImageProxy($post->data->thumbnail);
        return $redditPost;
    }



}