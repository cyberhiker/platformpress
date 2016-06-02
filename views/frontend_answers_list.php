 <style>
.not-active {
 
  cursor:not-allowed;
}
.not-active  > * {
    pointer-events:none;
}
 


</style> <!--HTML-->

  
    <div class="qbot-answers-item" id="qbot-answers-item-<?php echo get_the_ID(); ?>">
	<a name="qbotanswer-<?php echo get_the_ID(); ?>"></a>
	  <?php //echo get_the_ID(); ?>
      <!--qbot-answers-item-->
      <div class="qbot-left vote_section">
		<!--qbot-left vote_section-->
		<?php
			if(is_user_logged_in()){
				$user_id = get_current_user_id();
				$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_qbot_votes WHERE wp_users_id='.$user_id.' AND qbot_answer_id='.get_the_ID().'', 'OBJECT');
				if(!empty($res)){
					if($res->is_up_vote==1){
						$upVoteClass 	= "qbot-voted-up";
						$downVoteClass 	= "";
					} else{
						$downVoteClass 	= "qbot-voted-down";
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
            <a title="Thumb up" class="vote-up" onClick="qbotVoteUp(<?php echo get_the_ID(); ?>)" href="javascript:void(0)">
              <i class="fa fa-thumbs-up"></i>
            </a>
          </li>
          <li>
		  <?php $avg = get_post_meta(get_the_ID(), 'qbot_answer_vote_count', true); ?>
		  <span class="votes"><?php if(($avg != '') && ($avg>0) ) { echo $avg; } else{ echo "0" ;} ?></span>
            <?php if(!$this->settings['stored']['disble_negative_rating']): //if not disabled negative rating show ?>
          <li class="vote-down <?php echo $downVoteClass; ?>">
            <a title="Thumb down" class="vote-down" onClick="qbotVoteDown(<?php echo get_the_ID(); ?>)" href="javascript:void(0)">
              <i class="fa fa-thumbs-down"></i>
            </a>
          </li>
          <?php endif; ?>
		  
        </ul>
      </div>
        <!--qbot-left vote_section End-->
      
      <div class="qbot-block vertop" id="social_sec">
        <!--social_sec-->
        <div class="bck-sect colm-2">
          <div class="user-img">
            <?php echo qbot_avatar($post->post_author,32); ?>
          </div>

		  <!-- Mark as resolved feature -->
		  <?php 
		  $answerId = get_post_meta($questionId, 'qbot_question_resolved', true);
		  ?>
		  
		  <ul class="qbot-resolove">
		  <?php if($answerId==get_the_ID()): ?>
			<li><a title="Marked as resolved" class="question-resolved" href="javascript:void(0)"><i class="fa fa-check question-resolved"></i></a></li>
		  <?php else: ?>
			  <?php if(($this->settings['general']['is_user_logged_in']) && ($this->settings['general']['user_id']==$questionAuthoId) && ($resolvedAnswerId=='')){ ?>
			  <li>
				<div class="mark-resolved">
				  <a title="Mark this as best answer" onClick="qbotMarkQuestionResolved(<?php echo $questionId; ?>,<?php echo get_the_ID(); ?>)" href="javascript:void(0)"><i class="fa fa-check"></i></a>
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
    <!--/qbot-answers-item-->
   <!--/HTML-->