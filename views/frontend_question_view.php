<div id="frontend_wrap" class="qbot-frontend-wrap que-view">


  <div class="back-btn">
  <!--back-btn-->
  <a class="btn-quaseo" href="<?php echo $this->getBaseUrl(); ?>">
   <i class="fa fa-arrow-left"></i>&nbsp;&nbsp; BACK TO QUESTIONS</a>
  </div>
  <!--/back-btn-->
  
  
  <div class="qbot-question">
  <!--qbot-question-->
 
    <h1><?php echo get_the_title(); ?></h1>
	
	<?php
	// Question variables
	$questionAuthoId = $post->post_author;
	$questionId 		= get_the_ID();
	$userData 			= get_userdata($questionAuthoId);				
	$questionUrl 		= get_permalink();
	$resolvedAnswerId	= get_post_meta($questionId, 'qbot_question_resolved', true);
	?>
	
	<?php if(($this->settings['stored']['social_locker']!=1)): ?>
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=1642509509312261";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<script type="text/javascript">
	  (function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/platform.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	<div class="qbot-social-share-plugins">
	<div class="fb-share-button" data-href="<?php echo $questionUrl; ?>" data-layout="button_count"></div>
	<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $questionUrl; ?>">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
<div class="g-plusone" data-size="medium" data-annotation="inline" data-width="300" data-href="<?php echo $questionUrl; ?>"></div>
	</div>
	<?php endif; ?>
	
	
	
	<?php
	$questionStyle = array();
	$styleSettings = array();
	$styleSettings[] = ((isset($questionStyle['font'])) && ($questionStyle['font']!="")) ? "font-family:".str_replace('+',' ',$questionStyle['font']).' !important' : "";
	$styleSettings[] = ((isset($questionStyle['font_bold'])) && ($questionStyle['font_bold']=="1")) ? "font-weight:bold".' !important' : "";
	$styleSettings[] = ((isset($questionStyle['font_italic'])) && ($questionStyle['font_italic']=="1")) ? "font-style:italic".' !important' : "";
	$styleSettings[] = ((isset($questionStyle['font_size'])) && ($questionStyle['font_size']!="")) ? "font-size:".$questionStyle['font_size']."px".' !important' : "";
	$styleSettings[] = ((isset($questionStyle['line_height_size'])) && ($questionStyle['line_height_size']!="")) ? "line-height:".$questionStyle['line_height_size']."px".' !important' : "";
	$styleSettings[] = ((isset($questionStyle['font_color'])) && ($questionStyle['font_color']!="")) ? "color:#".$questionStyle['font_color'].' !important' : "";
	$styleSettings = array_filter($styleSettings);
	$styleSettings = implode(';',$styleSettings);
	$question_style = (isset($this->settings['stored']['question_style'])) ? $this->settings['stored']['question_style'] : "";
	?>
	
	<?php
		$attachmentId = get_post_meta($questionId, 'qbot_question_attachment', true);
		if(($attachmentId!=='') && is_numeric($attachmentId) && ($attachmentId>0)){
		$attachment_url = get_permalink().'?action=download&attachmentId='.$attachmentId;
		$attachmentHtml = '<br /><a class="qbot-attachment" title="Download attachment" href="'.$attachment_url.'"> <i class="fa fa-paperclip"></i> &nbsp; Download attachment</a>';
		} else{
		$attachmentHtml = '';
		}
	
	$question_content = get_the_content();
	$question_content = strip_tags($question_content,'<strong><b><i><a><em><span><font><ul><ol><li>');
	$question_content = $question_content.$attachmentHtml;
	switch($question_style){
		case 1:
			echo "<div class=\"description1\" style=\"".$styleSettings."\">".$question_content."</div>";
		break;
		case 2:
			echo "<div class=\"description2 bg_wrap\" style=\"".$styleSettings."\"><div class=\"arrow_down\"></div>".$question_content."</div>";
		break;
		case 3:
			echo "<div class=\"description3\" style=\"".$styleSettings."\">".$question_content."</div>";
		break;
		case 4:
			echo "<div class=\"description4\" style=\"".$styleSettings."\">".$question_content."</div>";
		break;
		case 5:
			echo "<div class=\"description5\" style=\"".$styleSettings."\">".$question_content."</div>";
		break;
		default:
			echo "<div class=\"description0\" style=\"".$styleSettings."\">".$question_content."</div>";
		break;
	}
	?>
    <div class="qbot-block" id="social_sec">
      <div class="bck-sect">
        <div class="user-img">
		  <?php echo qbot_avatar($post->post_author,32); ?>
        </div>
      </div>
	  
	  <div class="bck-sect ">
	  
		<div class="user-info">
		  <strong>
			<?php echo ucfirst(esc_html($userData->data->display_name)); ?>
		  </strong>
		  on <?php echo date_i18n('jS F Y',strtotime($post->post_date)); ?>
		  
			<?php if($this->settings['general']['user_id']==$questionAuthoId): ?>
				<?php $editurl = add_query_arg(array('action'=>'update-question','post_id'=>$questionId),$this->getBaseUrl()); ?>
				<?php $deleteurl = add_query_arg(array('action'=>'delete-question','post_id'=>$questionId),$this->getBaseUrl()); ?>
				&nbsp;&nbsp;
				<a href="<?php echo $editurl; ?>">Edit</a> | 
				<a href="<?php echo $deleteurl; ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
			<?php endif; ?>			  
		  
		</div>
	   
		<div class="analysis">
		  <ul>
		  <li><i class="fa fa-eye"></i>
			  <?php $avg = get_post_meta(get_the_ID(), 'qbot_views_count', true); ?>
			  <span title="Views"><?php if($avg != '' ) { echo $avg; } else{ echo "0" ;} ?></span>
			</li>
			<li>
			  <i class="fa fa-thumbs-up"></i>
			  <?php $avg = get_post_meta(get_the_ID(), 'qbot_question_vote_count', true); ?>
			  <span title="Votes"><?php if($avg != '' ) { echo $avg; } else{ echo "0" ;} ?></span>
			</li>
			<li><i class="fa fa-heart "></i>
				<?php $count = get_post_meta(get_the_ID(), 'qbot_question_favorite', true); ?>
			 <span title="Favourites" id="qasefavcount"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>
            </li>
			<li>
			  <i class="fa fa-comment"></i>
				  <?php $count = get_post_meta(get_the_ID(), 'qbot_answers_count', true); ?>
				  <span title="Answers" title="Answers"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>			  
			</li>
			
			<li>
			<?php if($this->settings['general']['is_user_logged_in']): ?>
				  <a title="My favorite" onClick="qbotMarkQuestionFavorite(<?php echo $questionId; ?>)" href="javascript:void(0)" class="hear">
				  <i id="qbot-fav-ques" class="fa fa-heart <?php echo ($this->isQuestionMarkedAsFavorite($questionId)) ? "marked-fav-ques" : "" ?>"></i>
				  </a>
			<?php endif; ?>
			</li>
			
			<?php if($category = $this->post_categories($questionId)): ?>
				<li>
				<i class="fa fa-tag rotate"></i>
				<?php
				$cat_url = add_query_arg(array('cat'=>$category->name),$question_listing_url);
				?>					
				<a href="<?php echo $cat_url; ?>" class="cat_list"><?php echo $category->name; ?></a>
				</li>
			<?php endif; ?>			
			   
		  </ul>
		</div>
		
	  </div>
    </div>

  </div>
  <!--/qbot-question-->
  
  
  <?php require_once(QBOT_PLUGIN_INCLUDE_PATH.'qbot-flash-messages.php'); ?>	

	<?php $count = get_post_meta(get_the_ID(), 'qbot_answers_count', true); ?>
	
	<?php if($count<1): ?>
	<div class="qbot-noanswer">No answer found, be first to answer this question</div>
	<?php endif; ?>
  
	<?php if($count >= 1 ): ?>
	<div class="qbot-block ansblock">
	<div class="bck-sect"><h3><?php echo ($count>1) ? $count." Answers" : $count." Answer"; ?></h3></div>
	<?php if(($count>1)){ ?>
    <div class="bck-sect">
	
		<?php
		if(isset($_GET['sort']) && ($_GET['sort']!="")){
		$sort = $_GET['sort'];
		} else{
		$sort = 'newest';
		}
		?>
		<ul class="qbot-sort">
		<?php
		$params = array('sort'=>'newest');
		$url = esc_url(add_query_arg($params));
		?>
        <li <?php echo ($sort=='newest') ? "class=\"active\"" : "" ?>>
          <a href="<?php echo $url; ?>">Newest first</a>
        </li>
		<?php
		$params = array('sort'=>'vote');
		$url = esc_url(add_query_arg($params));
		?>
        <li <?php echo ($sort=='vote') ? "class=\"active\"" : "" ?>>
          <a href="<?php echo $url; ?>" >Most voted</a>
        </li>
		</ul>
    </div><!--/bck-sect-->
	<?php } ?>
	
  </div><!--/qbot-block-->
  <?php endif; ?>
  
  
  <script type="text/javascript">
	function qbotVoteUp(answerId){
	
		jQuery(document).ready(function(){
		
			if(!jQuery("#qbot-answers-item-"+answerId+" .vote_section li").hasClass('qbot-voted-up')){
				vote_count = jQuery("#qbot-answers-item-"+answerId+" li span.votes").text();
				jQuery("#qbot-answers-item-"+answerId+" li span.votes").html("<i class='fa fa-refresh fa-spin text-mutted'></i>");
			
				jQuery.ajax({
					method: 'POST',
					data : {answerId:answerId,action:'vote-up'},
					dataType : 'JSON',
					success:function(res) {
						if(res.type=='success'){
							jQuery("#qbot-answers-item-"+answerId+" li span.votes").html(res.vote_count);
							jQuery("#qbot-answers-item-"+answerId+" li.vote-up").removeClass("qbot-voted-up");
							jQuery("#qbot-answers-item-"+answerId+" li.vote-down").removeClass("qbot-voted-down");
							jQuery("#qbot-answers-item-"+answerId+" li.vote-up").addClass("qbot-voted-up");
						} else{
							jQuery("#qbot-answers-item-"+answerId+" li span.votes").html(vote_count);
							alert(res.message);
						}
					}
				});
			}	
		});
		
	}
	
	function qbotVoteDown(answerId){
		jQuery(document).ready(function(){
			if(!jQuery("#qbot-answers-item-"+answerId+" .vote_section li").hasClass('qbot-voted-down')){
				vote_count = jQuery("#qbot-answers-item-"+answerId+" li span.votes").text();
				jQuery("#qbot-answers-item-"+answerId+" li span.votes").html("<i class='fa fa-refresh fa-spin text-mutted'></i>");
				jQuery.ajax({
					method: 'POST',
					data : {answerId:answerId,action:'vote-down'},
					dataType : 'JSON',
					success:function(res) {
						if(res.type=='success'){
							jQuery("#qbot-answers-item-"+answerId+" li span.votes").html(res.vote_count);
							jQuery("#qbot-answers-item-"+answerId+" li.vote-up").removeClass("qbot-voted-up");
							jQuery("#qbot-answers-item-"+answerId+" li.vote-down").removeClass("qbot-voted-down");
							jQuery("#qbot-answers-item-"+answerId+" li.vote-down").addClass("qbot-voted-down");
						} else{
							jQuery("#qbot-answers-item-"+answerId+" li span.votes").html(vote_count);
							alert(res.message);
						}
					}
				});
			}
		});
	}

	function qbotMarkQuestionFavorite(questionId){
		jQuery(document).ready(function(){
			//if already fav question then remove
			if(jQuery("#qbot-fav-ques").hasClass('marked-fav-ques')){
			 var actionPerform = "remove";
			} else{
			 var actionPerform = "add";
			}
			jQuery.ajax({
				method: 'POST',
				data : {questionId:questionId,action:'mark-question-favorite',actionPerform:actionPerform},
				dataType : 'JSON',
				success:function(res) {
					
					if(res.type=='success'){
						if(res.actionPerformed=='added'){
							jQuery("#qbot-fav-ques").addClass('marked-fav-ques');
						} else{
							jQuery("#qbot-fav-ques").removeClass('marked-fav-ques');
						}
						jQuery("#qasefavcount").text(res.fav_count);
					} else{
						alert(res.message);
					}
					
				}
			});
		});
	}
	
	function qbotMarkQuestionResolved(questionId,answerId){
		jQuery(document).ready(function(){
			jQuery.ajax({
				method: 'POST',
				data : {questionId:questionId,answerId:answerId,action:'mark-question-resolved'},
				dataType : 'JSON',
				success:function(res) {
					
					if(res.type=='success'){
						jQuery(".qbot-answers-item .mark-resolved").html('');
						jQuery("#qbot-answers-item-"+answerId+" .mark-resolved").html('<a title="Marked as resolved" class="question-resolved" href="javascript:void(0)"><i class="fa fa-check question-resolved"></i></a>');
					} else{
						alert(res.message);
					}
					
				}
			});
		});
	}
	</script>
	
	<div class="qbot-answers-list">
	
		<!--qbot-answers-list-->
		<?php
		if(isset($_GET['sort']) && ($_GET['sort']=='vote')){
			$query = array(
				'post_type' 	=> 'qbot-answer',
				'post_parent' 	=> get_the_ID(),
				'meta_key' 		=> 'qbot_answer_vote_count',
				'orderby' 		=> 'meta_value_num',
				'order' 		=> 'DESC'
			);
		} else{
			$query = array(
				'post_type' 	=> 'qbot-answer',
				'post_parent' 	=> get_the_ID(),
				'order_by' 		=> array('ID'),
				'order' 		=> 'DESC'
			);
		}
		query_posts($query); 
		?>
		<?php while (have_posts()) : the_post(); ?>
			<?php $userData =  get_userdata($post->post_author); ?>
			<?php require('frontend_answers_list.php'); ?>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	</div>
	<!-- End qbot-answers-list-->
	
   
  
	<?php if($this->settings['general']['is_user_logged_in']): ?>
		<?php if(!$this->isUserAllowed(get_current_user_id(),'can_answer_questions')){ ?>
		<div class="no-access">
			<h4>Access not allowed</h4>
			Sorry, you are not allowed to answer questions
		</div>
		<?php }else{ ?>
		<div class="answer-heading"><h4>Answer this question</h4></div>
		<div class="answer_wrapper"><form id="qbotform" method="post" action="<?php echo $questionUrl;?>">
		<div class="input-row">
		<?php 
		//Answer textarea
		qbot_editor( array( 
		'content' => ( isset( $_POST['qbotanswercontent'] ) ? wp_kses_data( $_POST['qbotanswercontent'] ) : '' ),
		'id' => 'qbotanswercontent', 
		'textarea_name' => 'qbotanswercontent',
		'media_buttons' => true,
		) ); 
		?>
		</div>
    
		<div class="input-row"><input id="ans" type="submit" value="Submit" /></div>
		</form></div>
		<?php } ?>
  <?php else: //NOT is_user_logged_in ?>
  <?php require_once QBOT_PLUGIN_VIEW_PATH.'/_frontend_login_form.php'; ?>
  <?php endif; ?>
  
</div>

<?php
wp_enqueue_script(
	'jquery-validate',
	plugin_dir_url( __FILE__ ) . '../js/jquery.validate.min.js',
	array('jquery'),
	'1.10.0',
	true
);
if(($this->settings['stored']['social_locker']==1)){
	wp_enqueue_script(
		'jquery-social-locker',
		plugin_dir_url( __FILE__ ) . '../js/social-locker.js',
		array('jquery'),
		'1.10.0',
		true
	);
}
wp_enqueue_script(
	'qbot-viewques',
	plugin_dir_url( __FILE__ ) . '../js/qbot.viewques.js',
	array('jquery'),
	'1.10.0',
	true
);
?>