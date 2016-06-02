<?php 
class qbotAdminQuestions extends qbotSettings{

	function run(){
		$this->loadScriptAndStyle();
		
		$action = isset($_GET['action']) ? $_GET['action'] : "";
		$action = (($action=="") && isset($_GET['edit'])) ? 'edit' : $action;
		
		/* Process actions */
		switch($action){
			case 'add':
				if(isset($_POST['qbot-submitted'])) {
					if($this->handelFormData()){
						$this->updateMyLocation();
						$params = array('page'=>'qbot-plugin-questions');
						$url = esc_url(add_query_arg($params,'admin.php'));
						add_action( 'admin_notices',  printf( '<div class="updated">Question created successfully.</div>'));
						unset($_POST);
						//wp_redirect($url);
					}
				}
			break;
			case 'edit':
				if(isset($_POST['qbot-submitted'])) {
					if($this->handelFormData()){
						add_action( 'admin_notices',  printf( '<div class="updated">Question updated successfully.</div>'));
						$params = array('page'=>'qbot-plugin-questions');
						$url = esc_url(add_query_arg($params,'admin.php'));
						//wp_redirect($url);
					}
				} else{
					$_POST = $this->openRecord($_GET['id']);
				}
			break;
			case 'delete':
				if(isset($_GET['id'])) {
					$question_id = (int)($_GET['id']);
					if($this->delete($question_id)){
						qbot_flash_set('success','Question deleted successfully.');
						$params = array('page'=>'qbot-plugin-questions');
						$url = esc_url(add_query_arg($params,'admin.php'));
						//wp_redirect($url);
					}
				}
			break;		
		}
		
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_questions.php';
	}
	
	protected function openRecord($id){
		global $wpdb;
		$id = (int)($id);
		$res = $wpdb->get_row( 'SELECT * FROM mcl_qbot_questions WHERE id = '.$id, 'OBJECT' );
		$data = array(
		    'id' 	=> $res->id,
			'question_title' 	=> $res->question_title,
			'question_slug' 	=> $res->question_slug,
			'question_description' 	=> $res->question_description,
			'wp_users_id' 	=> $res->wp_users_id,
			'is_active'			=> $res->is_active,
			
		);
		return $data;
	}
	
	public function getQuestion($questionId){
		global $wpdb;
		$questionId = (int)($questionId);
		$res = $wpdb->get_row( 'SELECT * FROM mcl_qbot_questions WHERE id = '.$questionId, 'OBJECT' );
		return $res;		
	}
	
	protected function delete($question_id){
		global $wpdb;
		
		$question_id = (int)($question_id);
		
		//Delete if favorite
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_favorite_questions WHERE qbot_questions_id = %d",$question_id));
		//Delete if spamed
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_spam WHERE obj_id = %d AND group_name='question'",$question_id));
		//Delete if comment
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_comments WHERE question_id = %d",$question_id));
		//Delete all answers
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_answers WHERE question_id = %d",$question_id));
		//Delete questions
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_questions WHERE id = %d",$question_id));
		return true;
	}
	
	
	protected function handelFormData(){
		global $wpdb;
		$table_name = 'mcl_qbot_questions';
		if(isset($_GET['id'])){
			$question_title = sanitize_text_field($_POST['question_title']);
			$question_slug 	= sanitize_text_field($_POST['question_slug']);
			//$question_description = sanitize_text_field($_POST['question_description']);
			$question_description = wp_kses_post($_POST['question_description']);
			$is_active 		= isset($_POST['is_active']) ? 1 : 0;
			
			$user_id 		= (int)($_POST['wp_users_id']);
			
			$data = array(
				'question_title' 	=> $question_title,
				'question_slug' 	=> $question_slug,
				'question_description' => $question_description,
				'is_active'			=> $is_active,
				
				'wp_users_id'		=> $user_id,	
				'modified_at'		=> current_time('mysql')
			);
			$where = array('id' => $_GET['id']);
			$format = array('%s','%s','%s','%d','%d','%s');
			
			$where_format = array('%d');
			$wpdb->update($table_name, $data, $where, $format, $where_format);
			
			if(isset($_POST['cat'])){
				$cat = sanitize_text_field($_POST['cat']);
				$table_name = 'mcl_qbot_term_relationships';
				$where = array('mcl_qbot_questions_id' => $_GET['id']);
				$data = array(
				'term_taxonomy_id' 		 => $cat);
				$format = array('%s','%s','%s','%d','%d','%s');
				$wpdb->update($table_name, $data, $where, $format);
			}
			
			return true;
		} else{
			$user_id = (isset($_POST['wp_users_id'])) ? $_POST['wp_users_id'] : get_current_user_id();
			$question_title 		= sanitize_text_field($_POST['question_title']);
			$question_slug 			= sanitize_text_field($_POST['question_slug']);
			$question_description   = wp_kses_post($_POST['question_description']);
			$is_active 		= isset($_POST['is_active']) ? 1 : 0;
			
			$table_name = 'mcl_qbot_questions';
			$wpdb->insert($table_name, array(
				'question_title' 		=> $question_title,
				'question_slug' 		=> $question_slug,
				'question_description'	=> $question_description,
				'wp_users_id'			=> $user_id,
				'is_active' 			=> $is_active,
				'created_at'				=> current_time('mysql')
			), array('%s','%s','%s','%d','%d','%s'));
			
			if(isset($_POST['cat'])){
				$cat = sanitize_text_field($_POST['cat']);
				$table_name = 'mcl_qbot_term_relationships';
				$wpdb->insert($table_name, array(
					'term_taxonomy_id' 		 => $cat,
					'mcl_qbot_questions_id' => $wpdb->insert_id,
				), array('%d','%d'));
			}
			unset($_POST);
			return true;
			
		}	
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
			'jquery-validate-css',
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
	
	
	function runjs(){ ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
				jQuery.validator.addMethod("checkslug", function(value, element){
					return /^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/.test(value);
				}, "Please choose correct slug for this question");		
			
				$('#qbotform').validate({
				ignore: "",
				rules: {
					question_title: {
						required: true,
						minlength: 3
					},
					question_slug: {
						checkslug: true,
					},
					question_description: {
						required: true,
						minlength: 3,
						highlight: function(element) {
							$(element).parent().parent().parent().parent().addClass('form-invalid');
						},							
					}	
				},
				highlight: function(element) {
					$(element).parent().parent().addClass('form-invalid');
				},		
				unhighlight: function(element) {
					$(element).parent().parent().removeClass('form-invalid');
					$(element).parent().parent().parent().parent().removeClass('form-invalid');
				},		
			});
			jQuery('.slug').slugify('#question_title');
			jQuery('#qbotform input[type="submit"]').on('click',function(){
				content = tinymce.get('question_description').getContent();
				$("#question_description").val(content);
			});			
			$(".updated").fadeOut(2000); 
		});
	</script>			
	<?php	
	}

}
?>