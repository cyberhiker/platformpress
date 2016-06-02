 <style>
.not-active {
 
  cursor:not-allowed;
}
.not-active  > * {
    pointer-events:none;
}
 


</style> <!--HTML-->

  
    <div class="platformpress-remarks-item" id="platformpress-remarks-item-<?php echo get_the_ID(); ?>">
	<a name="platformpressremark-<?php echo get_the_ID(); ?>"></a>
	  <?php //echo get_the_ID(); ?>
      <!--platformpress-remarks-item-->
      <div class="platformpress-left vote_section">
		<!--platformpress-left vote_section-->
		<?php
			if(is_user_logged_in()){
				$user_id = get_current_user_id();
				$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_platformpress_votes WHERE wp_users_id='.$user_id.' AND platformpress_remark_id='.get_the_ID().'', 'OBJECT');
				if(!empty($res)){
					if($res->is_up_vote==1){
						$upVoteClass 	= "platformpress-voted-up";
						$downVoteClass 	= "";
					} else{
						$downVoteClass 	= "platformpress-voted-down";
						$upVoteClass 	= "";
					}
				} else{
					$upVoteClass		= "";
					$downVoteClass		= "";
				}
			} else{
				$upVoteClass		= "";
				$downVoteClass		= "";
			}
		?>
		<ul>
          <li class="vote-up <?php echo $upVoteClass; ?>">
            <a title="Thumb up" class="vote-up" onClick="platformpressVoteUp(<?php echo get_the_ID(); ?>)" href="javascript:void(0)">
              <i class="fa fa-thumbs-up"></i>
            </a>
          </li>
          <li>
		  <?php $avg = get_post_meta(get_the_ID(), 'platformpress_remark_vote_count', true); ?>
		  <span class="votes"><?php if(($avg != '') && ($avg>0) ) { echo $avg; } else{ echo "0" ;} ?></span>
            <?php if(!$this->settings['stored']['disble_negative_rating']): //if not disabled negative rating show ?>
          <li class="vote-down <?php echo $downVoteClass; ?>">
            <a title="Thumb down" class="vote-down" onClick="platformpressVoteDown(<?php echo get_the_ID(); ?>)" href="javascript:void(0)">
              <i class="fa fa-thumbs-down"></i>
            </a>
          </li>
          <?php endif; ?>
		  
        </ul>
      </div>
        <!--platformpress-left vote_section End-->
      
      <div class="platformpress-block vertop" id="social_sec">
        <!--social_sec-->
        <div class="bck-sect colm-2">
          <div class="user-img">
            <?php echo platformpress_avatar($post->post_author,32); ?>
          </div>

		  <!-- Mark as resolved feature -->
		  <?php 
		  $remarkId = get_post_meta($plankId, 'platformpress_plank_resolved', true);
		  ?>
		  
		  <ul class="platformpress-resolove">
		  <?php if($remarkId==get_the_ID()): ?>
			<li><a title="Marked as resolved" class="plank-resolved" href="javascript:void(0)"><i class="fa fa-check plank-resolved"></i></a></li>
		  <?php else: ?>
			  <?php if(($this->settings['general']['is_user_logged_in']) && ($this->settings['general']['user_id']==$plankAuthoId) && ($resolvedRemarkId=='')){ ?>
			  <li>
				<div class="mark-resolved">
				  <a title="Mark this as best remark" onClick="platformpressMarkPlankResolved(<?php echo $plankId; ?>,<?php echo get_the_ID(); ?>)" href="javascript:void(0)"><i class="fa fa-check"></i></a>
				</div>
			  </li>
			  <?php } ?>
		  <?php endif; ?>
		  <!-- END Mark as resolved feature -->		  
		  </ul>
		  
        </div>
        <div class="bck-sect bck-sect colm-10">
            <div class="user-info">
			
			  <strong>
				<?php echo ucfirst(esc_html($userData->data->display_name)); ?>
			  </strong>
			
			  on <?php echo date_i18n('jS F Y',strtotime($post->post_date)); ?>
            </div>
			
			<div class="ques">
				<p><?php echo get_the_content(); ?></p>
			</div>
			
        </div>
		
		
      </div>
      <!--/social_sec-->
      
	  
    </div>
    <!--/platformpress-remarks-item-->
   <!--/HTML-->