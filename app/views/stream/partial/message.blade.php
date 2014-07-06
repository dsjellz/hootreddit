<div class="hs_message">
    <div class="hs_controls">
        <a href="#" class="hs_icon hs_reply" title="Share" meta='{"title":"{{$message->title}}","url":"{{$message->url}}"}'>Reply</a>
    </div>
    <img class="hs_networkAvatar" src="{{ stripslashes($message->thumb) }}" alt="thumbnail">

    <a href="#" class="hs_networkName">{{$message->author}}</a>
    <a target="_blank" href="{{$message->url}}" class="hs_postTime">
        {{$message->created}} ago in /r/{{$message->subreddit}}
        ({{$message->domain}})
    </a>
	<div class="hs_messageContent">
        <a target="_blank" href="{{$message->url}}">{{$message->title}}</a><br />
            <!-- Comment -->
            <div class="hs_messageComments">
                <span class="hs_arrow">☗</span>

                <!-- Likes, # of comments, external links to discussion etc. -->
                <div class="hs_comment hs_details hs_inlineDetails">

                    <a target="_blank" href="http://www.reddit.com/r/{{$message->subreddit}}/comments/{{$message->id}}"
                       id="{{$message->permalink}}">
                        @if ($message->nsfw == true)
                            <span style="color: #EE0000;"> NSFW</span> &nbsp; | &nbsp;
                        @endif
                        {{$message->num_comments}} comments</a> &nbsp; | &nbsp;
                        Rating: {{$message->score}}
                    <div class="comments" id="cmt_e81Uh-qvEhw" style="display: block; width: 98%; "></div>

                </div>
            </div>
            <!-- End Comment -->
	</div>
</div>