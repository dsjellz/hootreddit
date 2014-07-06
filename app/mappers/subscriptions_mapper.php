<?php
class Subscriptions_mapper{

    public static function map($subscriptions)
    {
        $subscriptions_arr[] = array('display_name' => 'home', 'url' => '');
         if(!empty($subscriptions->data->children))
         {
             foreach($subscriptions as $listing){
                 if(is_object($listing)){
                     foreach($listing->children as $subscription){
                         $subscriptions_arr[] = array(
                             'display_name' => $subscription->data->display_name,
                             'url' => $subscription->data->url);
                     }
                 }
             }
         }
        return $subscriptions_arr;
    }
}