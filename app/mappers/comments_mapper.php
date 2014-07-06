<?php

class Comments_mapper{
    /**
     * Maps the data returned from Reddit comments into a Reddit_Response
     * @static
     * @param $comments
     * @return Reddit_Response
     */
    public static function map($comments)
    {
        $comments_arr = array();
        $comments = array_slice($comments, 1);
        foreach($comments[0]->data->children as $comment)
        {
            //if($comment->kind != 'more'){
                $comments_arr[] = get_comments($comment);
            //}
        }
        return $comments_arr;
        //return new Reddit_Response($comments_arr, $comments->data->before, $comments->data->after);
    }
}

/**
 * Recursive method to gather all comments and child comments available
 * @param $comment
 * @return array
 */
function get_comments($comment)
 {
     $comments_arr = array();

     if($comment->kind != 'more'){
         $tmp_comment = array(
             'body'  => $comment->data->body,
             'created'   => $comment->data->created,
             'author'    => $comment->data->author,
             'downs'     => $comment->data->downs,
             'ups'       => $comment->data->ups,
             'id'        => $comment->data->name,
             'parent'    => $comment->data->parent_id
         );

         $comments_arr[] = $tmp_comment;

        if( isset($comment->data->replies) &&  is_object($comment->data->replies)){
            foreach($comment->data->replies->data->children as $child){
                 $comments_arr[] = get_comments($child);
            }
        }

        return $comments_arr;
     }else{
         print_r($comment);
     }
 }