<?php

class HootsuiteUtil {

    /**
     * Return an https source for an http image
     * @param $url
     * @return string
     */
    public static function getImageProxy($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $proxyUrl = URL::to('imgproxy') . '?url=' . $url;
            $hash = md5($proxyUrl . Config::get('auth.hash_secret'));
            return $proxyUrl . '&hash=' . $hash;
        } else {
            return URL::asset('img/reddit_thumb.png');
        }
    }

    /**
     * Returns the time elapsed
     * @param $time
     * @return string
     */
    public static function timeElapsed($time)
    {
        $time = time() - $time;

        $tokens = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
        }
    }
}