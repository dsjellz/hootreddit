<?php
/**
 * This class is meant to be used as a shell for a response from reddit. Mainly used to provide standardized format
 * to hold data, before, and after links
 *
 * @author djellesma
 */
class RedditResponse{
    public $data;
    public $before;
    public $after;

    public function __construct($data = null, $before = null, $after = null)
    {
        $this->data = $data;
        $this->before = $before;
        $this->after = $after;
    }
}