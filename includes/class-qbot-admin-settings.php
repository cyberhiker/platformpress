<?php 
class qbotAdminSettings extends qbotSettings{

	function run(){
		$this->loadScriptAndStyle();
		
		$action = isset($_GET['action']) ? $_GET['action'] : "";
		
		if(isset($_POST['qbot-submitted'])){
		
			switch($action)
			{
				case 'permalink-settings':
					//default settongs
					$settings = array(
						'permalink_question' =>'',
					);
					
					foreach($_POST as $key=>$val){
						if(isset($settings[$key])){
							switch($key){
								case 'permalink_question':
									$queried_post = get_page_by_path($val,OBJECT);
									if($queried_post){
										qbot_flash_set('error','This permalink already in use, please choose other.');
									} else{
										$settings[$key] = $val;
										$var1 = '^'.sanitize_text_field($_POST['permalink_question']).'/([^/]+)?';
										$var2 = 'index.php?page_id='.$this->settings['stored']['plugin_page_id'].'&qid=$matches[1]';
										add_rewrite_rule($var1,$var2,'top');
										global $wp_rewrite;
										$wp_rewrite->flush_rules(false);
										qbot_flash_set('success','Permalinks updated successfully.');				
									}
								break;
								case 'permalink_category':
								break;
							}
						}
					}
					
					$this->handelFormData($settings);
				break;
				case 'notification-settings':
				
					$settings = array(
						'notify_user'					=>'',//checkbox
						'notify_new_question'			=>'',//checkbox
						'notification_new_question'		=>'',//mail content
						'notification_new_answer'		=>'',//mail content
					);
					
					foreach($_POST as $key=>$val){
						if(isset($settings[$key])){
							$settings[$key] = sanitize_text_field($val);
						}
					}
					
					$this->handelFormData($settings);
					qbot_flash_set('success','Settings updated successfully.');				
				break;	
				default:
					//default settongs
					$settings = array(
					
						'plugin_page_id'				=>'',
						'login_and_registeration'		=>'',
						
						'disble_negative_rating'		=>'',
						'auto_approve_new_questions'	=>'',
						'auto_approve_new_answers'		=>'',
						
						'facebook_app_id'				=>'',
						'facebook_app_secret'			=>'',
						
						'google_app_id'					=>'', 
						'google_app_secret'				=>'',
						
						'social_locker'					=>'',
					);
					
					foreach($_POST as $key=>$val){
						if(isset($settings[$key])){
							$settings[$key] = $val;
						}
					}
					
					$this->handelFormData($settings);
					qbot_flash_set('success','Settings updated successfully.');
				break;
			}	
		}
		
		$_POST = $this->openRecord();
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_settings.php';
	}
	
	protected function openRecord(){
		$settings = qbot_setting_get_all($default='no');
		return $settings;
	}
	
	protected function handelFormData($settings){
		foreach($settings as $key=>$val){
			$option_name = $key;
			
			//If anyone user registeration enabled update wordpress functionality
			if(($option_name=='login_and_registeration') && ($val=='1')){
				update_option( 'users_can_register', 1 );
			} elseif(($option_name=='login_and_registeration') && ($val=='0')){
				update_option( 'users_can_register', 0 );
			}
			
			qbot_setting_save($option_name,$val);
		}
		if(isset($settings['plugin_page_id']) && ($settings['plugin_page_id']!="") && is_numeric($settings['plugin_page_id'])){
			$this->attachShortcodeToPage($settings['plugin_page_id']);
		}
		return true;
	}

	protected function loadScriptAndStyle(){
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
		wp_enqueue_style(
			'jquery-validate',
			plugin_dir_url( __FILE__ ) . '../css/style.css',
			array(),
			'1.0'
		);
		add_action('admin_footer', array($this,'runjs'));
	}

	/**
	 * Initiate the script.
	 * Calls the validation options on the comment form.
	 */
	 
	protected function attachShortcodeToPage($pageId){
		$pageId = (int)($pageId);
		$page_content = get_post_field( 'post_content', $pageId );
		if ( strpos( $page_content, '[qbot-frontend]' ) === false ){
			$res = wp_update_post( array(
				'ID'			=> $pageId,
				'post_content'	=> $page_content.'[qbot-frontend]',
			) );
		}
	}	
	
	function runjs(){ ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

				jQuery.validator.addMethod("checkslug", function(value, element){
					return /^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/.test(value);
				}, "Please choose correct slug for this question");		
			
				$('#qbotform').validate({	
				rules: {
					question_title: {
						required: true,
						minlength: 3
					},
					question_slug: {
						checkslug: true,
					},
					email: {
						required: true,
						email: true
					},
				 
					url: {
						url: true
					},
				 
					comment: {
						required: true,
						minlength: 20
					}
				},
				highlight: function(element) {
					$(element).parent().parent().addClass('form-invalid');
				},		
				unhighlight: function(element) {
					$(element).parent().parent().removeClass('form-invalid');
				},		
			});
			jQuery('.slug').slugify('#question_title');
		});
	</script>			
	<?php	
	}

}
?>