<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Reddit HSP</title>
    <script type="text/javascript" src="https://static.hootsuite.com/jsapi/0-5/hsp.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>

    <script type="text/javascript">
        function loginWindow(){

            var win = window.open('<?php echo $auth_url; ?>',
                'authWindow', 'height=550, width=700');
            var timer = setInterval(function(){
                if(win.closed){
                    clearInterval(timer);
                    window.location.href='{{ URL::action("StreamController@show") }}' ;
                }
            });
        }

        $(document).ready(function() {
            // hootsuite plugin init
            hsp.init({
                apiKey: '<?php echo $api_key; ?>',
                receiverPath: '<?php echo URL::to('my_receiver.html'); ?>',
                useTheme: true
            });

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

        });

    </script>

</head>

<body>


<div id="stream" class="hs_stream">
    <div class="hs_topBar">

        <div class="hs_content">
            <!-- ICONS -->
            <div class="hs_controls">
                <a href="#" dropdown="_menuList" title="More"><span class="icon-19 dropdown"></span></a>
            </div>

        </div>

        <!-- DROPDOWNS -->
        <div class="hs_dropdown">

            <!-- MENU LIST -->
            <!-- MENU LIST -->
            <div class="_menuList hs_btns-right">
                <a href="http://help.hootsuite.com/forums/21416737-reddit-app" target="_blank">Help</a>
                <hr />
                <a href="http://feedback.hootsuite.com/forums/180560-hootsuite-app-reddit" target="_blank">Feedback</a>
                <hr />
                <a href="https://github.com/dsjellz" target="_blank">Developer</a>
            </div>

        </div>
    </div>
    <div class="hs_topBarSpace"></div><!-- Spacer underneath "hs_topBar" to prevent clipping of content -->
    <div id="hs_noMessage" style="padding-top: 25px; text-align: center;">
        <img src="<?php echo URL::to('img/reddit_alien.png');?>" width="275" alt="Reddit"/><br /><br />
        <a class="hs_btn-cmt" href="#" onclick="loginWindow();">Connect with Reddit</a>
    </div>

</div><!-- .hs_stream -->

</body>
</html>