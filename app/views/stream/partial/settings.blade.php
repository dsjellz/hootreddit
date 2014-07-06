<div class="_settings hs_btns-right">
    <div style="align: right;">
        Currently logged in as: <?php echo $username; ?><br />
        <?php foreach($subscriptions as $subscription){ ?>
           <div><?php echo $subscription['display_name']; ?>
               <input type="radio" name="subreddit" id="<?php echo $subscription['url']; ?>"
                      value="<?php echo $subscription['display_name'];?>"
                      <?php
                          if($subscription['display_name'] == $subreddit_name)
                          {
                                echo 'checked="checked"';
                          }
                      ?>
                      >
            </div>
        <?php } ?>
        <a class="hs_btn-cmt" id="save_btn" href="#">Save</a>
        <a class="hs_btn-cmt" id="logout_btn" href="#">Logout</a>
        <br/>
    </div>
</div>