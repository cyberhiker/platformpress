  <!--/back-btn--><div class="qbot-frontend-wrap">
  <div class="back-btn" style="padding-top:8%">
  <!--back-btn-->
  <a class="btn-quaseo" href="<?php echo $this->getBaseUrl(); ?>">
   <i class="fa fa-arrow-left"></i>&nbsp;&nbsp; BACK TO QUESTIONS</a>
  </div>
	<?php if(isset($_GET['action']) && ($_GET['action']=='update-question')): ?>
	<h2>Update question</h2>
	<?php else: ?>
	<h2>Add new question</h2>
	<?php endif; ?>
	 <?php require_once(QBOT_PLUGIN_INCLUDE_PATH.'qbot-flash-messages.php'); ?>
	
	<?php if(!$this->isUserAllowed(get_current_user_id(),'can_ask_questions')) : ?>
	
		<div class="no-access">
		<h4>Access not allowed</h4>
		Sorry, you are not allowed to ask question
		</div>
	
	<?php else: ?>
	<?php
	# relative current URI:
	$url = add_query_arg( NULL, NULL );	
	?>
	<div class="form_question_wrapper"><form id="qbotform" name="qbotform" method="post" action="<?php echo $url; ?>">

		<div class="input-row qbot_wrap_wrapper">
		<label>Question title</label>
		<input id="question_title" type="text"  aria-required="true" value="<?php echo (isset($_POST['question_title'])) ? $_POST['question_title'] : ""; ?>" name="question_title">
		</div>
		
		<br />
		<div class="input-row">
			<label>Question description</label>		
			<?php 
			//Answer textarea
			qbot_editor( array( 
				'content' => ( isset( $_POST['question_description'] ) ? $_POST['question_description'] : '' ),
				'id' 			=> 'question_description', 
				'textarea_name' => 'question_description',
			));
			?>
		</div>
		
		<?php if((!current_user_can( 'manage_options' ))): ?>
		<?php if((qbot_setting_get("auto_approve_new_questions")=="0")): ?>
		<p>Note: Your question will reviewed before publishing.</p>
		<?php endif; ?>
		<?php endif; ?>
		
		<div class="input-row qbot_submit_button">
			<input type="submit" value="Submit" id="submit" />
		</div>
	</form></div>
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
	wp_enqueue_script(
		'jquery-slugify',
		plugin_dir_url( __FILE__ ) . '../js/jquery.slugify.js',
		array('jquery'),
		'1.10.0',
		true
	);
	wp_enqueue_script(
		'qbot-addques',
		plugin_dir_url( __FILE__ ) . '../js/qbot.addques.js',
		array('jquery'),
		'1.10.0',
		true
	);	
?>