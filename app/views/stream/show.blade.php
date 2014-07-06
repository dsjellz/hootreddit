<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reddit HSP</title>
    <script type="text/javascript" src="https://hootsuite.s3.amazonaws.com/jsapi/0-5/hsp.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>

    <style type="text/css">
        a#inifiniteLoader{
            z-index: 2;
            display:none;
        }
    </style>

    <script type="text/javascript">
        $(document).ready(function() {
            var before = '{{ $before }}';
            var after = '{{ $after }}';
            var subreddit_name = "{{ $subreddit_name }}";

            // hootsuite plugin init
            hsp.init({
                apiKey: '{{ $api_key }}',
                receiverPath: "{{ URL::to('my_receiver.html') }}",
                useTheme: true
            });

            hsp.bind('refresh', function() {
                var url = '<?php echo URL::to('ajax/listings');?>' + '/' + subreddit_name;

                // Update the contents of the feed
                $.get(url, function(data){
                    var listings = $.parseJSON(data);
                    $("#stream_content").html(listings.html);
                    before = listings.before;
                    after = listings.after;
                });
            });

            /**
             * CUSTOM CLICK HANDLERS
             */

            // Set the current subreddit being displayed
            function setSubreddit(new_subreddit_name){
                var url = '<?php echo URL::to('ajax/listings');?>' + '/' + new_subreddit_name;

                // Update the contents of the feed
                $.get(url, function(data){
                    var listings = $.parseJSON(data);
                    before = listings.before;
                    after = listings.after;
                    $("#stream_content").html(listings.html);
                });

                // Let the application know the subreddit has been changed
                $.post("<?php echo URL::to('ajax/update-subreddit'); ?>",
                    {
                        subreddit: new_subreddit_name,
                        pid: '<?php echo $pid; ?>',
                        uid: '<?php echo $uid; ?>'
                    }
                );

                subreddit_name = new_subreddit_name;
                $("#subreddit_name").html("/r/" + new_subreddit_name);
            }

            // Infinite Scrolling
            $(window).scroll(function(){
                if($(window).scrollTop() + $(window).height() == $(document).height())
                {
                    $('.hs_messageMore').click();
                }
            });

            // 'More Messages' click handler
            $('.hs_messageMore').click(function(){
                $('a#inifiniteLoader').show('fast');
                var url = '<?php echo URL::to('ajax/listings');?>' + '/' + subreddit_name + '/' + after;

                // Update the contents of the feed
                $.get(url, function(data){
                    var listings = $.parseJSON(data);
                    $("#stream_content").append(listings.html);
                    before = listings.before;
                    after = listings.after;
                    $('a#inifiniteLoader').hide('1000');
                });

            });

            //  logout button click handler
            $('#logout_btn').click(function(){
                $.ajax({
                    url: "{{ action('SessionController@destroy') }}",
                    type: 'DELETE',
                    success: function(redirect_uri) {
                        window.location.href = redirect_uri;
                    }
                });
            });

            // Settings save button click handler
            $('#save_btn').click(function(){

                $(".hs_messageMore").html("");
                $("#stream_content").html("");

                setSubreddit( $('input[name=subreddit]:checked').val() );

                $dropdown = $('.hs_topBar .hs_dropdown');
                $dropdown.hide();

                $(".hs_messageMore").html("Show More");
            });

            /**
             * END CUSTOM CLICK HANDLERS
             */

                // Top bar controls and drop downs
            $('.hs_topBar .hs_controls a').click(function(e) {

                var $control = $(this),
                    $dropdown = $('.hs_topBar .hs_dropdown');

                $dropdown.children().hide();

                if ($control.attr('dropdown').length) {
                    $dropdown.children('.' + $control.attr('dropdown')).show();
                }

                if($dropdown.is(':visible') && $control.hasClass('active')) {
                    $dropdown.hide();
                } else {
                    $dropdown.show();
                    if($control.attr('dropdown') == '_search') {
                        $dropdown.find('.' + $control.attr('dropdown') + ' input[type="text"]').first().focus();
                    }
                    if($control.attr('dropdown') == '_writeMessage') {
                        $dropdown.find('.' + $control.attr('dropdown') + ' textarea').first().focus();
                    }
                }

                $control.siblings('.active').removeClass('active');
                $control.toggleClass('active');

                e.preventDefault();
            });

            // "More"
            $('.hs_topBar ._menuList a').click(function(e) {

                // show heading
                $('#app-stream-heading').html( $(this).text() + ' clicked (<a href="#" class="refresh_stream">Clear</a>)');
                $('#app-stream-heading').show();

                // clear messages
                $('#app-stream').html('');

                // close dropdown
                $('.hs_topBar .hs_dropdown').hide();
                $('.hs_topBar .hs_controls a.active').removeClass('active');
                window.open(this.getAttribute('href'));
                e.preventDefault();
            });

            // Message controls dropdown
            $('.hs_stream').delegate('.hs_message .hs_controls a.hs_expand', 'click', function(e) {
                $(this).parent().find('.hs_moreOptionsMenu').toggle();
                e.preventDefault();
            });
            $('.hs_stream').delegate('.hs_message .hs_controls .hs_moreOptionsMenu', 'mouseleave', function(e) {
                $(this).hide();
            });

            $('.hs_message .hs_controls a.hs_reply').live('click', function(e) {
                var post = JSON.parse( this.getAttribute('meta') );
                hsp.composeMessage(post.title + " " + post.url, { shortenLinks: true } );
                e.preventDefault();
            });

        });

    </script>

</head>

<body>

<div class="hs_stream">
    <div class="hs_topBar">

        <div class="hs_content">
            <div id="subreddit_name">/r/<?php echo $subreddit_name; ?></div>
            <!-- ICONS -->
            <div class="hs_controls">
                <a href="#" dropdown="_settings" title="Settings"><span class="icon-19 settings"></span></a>
                <a href="#" dropdown="_menuList" title="More"><span class="icon-19 dropdown"></span></a>
            </div>

        </div>

        <!-- DROPDOWNS -->
        <div class="hs_dropdown">

            <!-- MENU LIST -->
            <div class="_menuList hs_btns-right">
                <a href="http://help.hootsuite.com/forums/21416737-reddit-app" target="_blank">Help</a>
                <hr />
                <a href="http://feedback.hootsuite.com/forums/180560-hootsuite-app-reddit" target="_blank">Feedback</a>
                <hr />
                <a href="https://github.com/dsjellz" target="_blank">Developer</a>
            </div>
            {{-- Contents of the 'settings' dropdown button --}}
            @include('stream.partial.settings')
        </div>
    </div>
    <div class="hs_topBarSpace"></div><!-- Spacer underneath "hs_topBar" to prevent clipping of content -->
    <div id="stream_content">
        @foreach ($messages as $message)
            @include('stream.partial.message', array('message' => $message))
        @endforeach
    </div>

</div><!-- .hs_stream -->


{{-- LOAD MORE LINK --}}
<div id="showMore" style="text-align: center;">
    <a id="infiniteLoader"><img src="https://s7.static.hootsuite.com/3-0-115/images/themes/magnum/loader.gif"></a>
    <a href="#" class="hs_messageMore">Show More</a>
</div>


</body>
</html>