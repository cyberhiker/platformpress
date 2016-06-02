<?php 
class qbotAdminAnswers extends qbotSettings{

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
						$params = array('page'=>'qbot-plugin-answers');
						$url = esc_url(add_query_arg($params,'admin.php'));
						unset($_POST);
						add_action( 'admin_notices',  printf( '<div class="updated">Answer created successfully.</div>'));
					}
				}
				
				if(isset($_GET["question_id"])){
					$question_id = $_GET["question_id"];
					global $wpdb;
					$question = $wpdb->get_row( 'SELECT question_title FROM mcl_qbot_questions WHERE id='.$question_id .''
					, 'OBJECT' );
					if(!$question_id){
						echo "Access not allowed";
						exit;
					} else{
						$_POST['question_id'] = (int)($question_id);
						$_POST['question_title'] = $question->question_title;
					}
				}
			break;
			case 'edit':
				if(isset($_POST['qbot-submitted'])) {
					if($this->handelFormData()){
						add_action( 'admin_notices',  printf( '<div class="updated">Answer updated successfully.</div>'));
						$params = array('page'=>'qbot-plugin-answers');
						$url = esc_url(add_query_arg($params,'admin.php'));
					}
				}
				$_GET['id'] = (int)($_GET['id']);	
				$_POST = $this->openRecord($_GET['id']);
			break;
			case 'delete':
				if(isset($_GET['id'])) {
					$answer_id = $_GET['id'];
					$answer_id = (int)($answer_id);
					if($this->delete($answer_id)){
						add_action( 'admin_notices',  printf( '<div class="updated">Answer deleted successfully.</div>'));
						$params = array('page'=>'qbot-plugin-answers');
						$url = esc_url(add_query_arg($params,'admin.php'));
						//wp_redirect($url);
					}
				}
			break;		
		}
		
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_answers.php';
	}
	
	protected function openRecord($id){
		global $wpdb;
		$res = $wpdb->get_row( 'SELECT mcl_qbot_answers.*,mcl_qbot_questions.question_title FROM mcl_qbot_answers 
	INNER JOIN mcl_qbot_questions ON(mcl_qbot_answers.question_id=mcl_qbot_questions.id) WHERE mcl_qbot_answers.id = '.$id, 'OBJECT' );
		$data = array(
			'answer_content' 	=> $res->answer_content,
			'question_id' 		=> $res->question_id,
			'wp_users_id' 		=> $res->wp_users_id,
			'question_title' 	=> $res->question_title,
		);
		return $data;
	}
	
	protected function delete($answer_id){
		global $wpdb;
		
		$answer_id = (int)($answer_id);
		
		//Delete if spamed
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_spam WHERE obj_id = %d AND group_name='answer'",$answer_id));
		//Delete if comment
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_comments WHERE answer_id = %d",$answer_id));
		//Dlete answer
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_qbot_answers WHERE id = %d",$answer_id));
		return true;
	}
	
	
	protected function handelFormData(){
		global $wpdb;
		$table_name = 'mcl_qbot_answers';
		if(isset($_GET['id'])){
			
			$_GET['id'] = (int)($_GET['id']);
			$answer_content = wp_kses_post($_POST['answer_content']);
			$user_id 		= (int)($_POST['wp_users_id']);
			$data = array(
				'answer_content' 	=> $answer_content,
				'wp_users_id'		=> $user_id,	
			);
			$where = array('id' => $_GET['id']);
			$format = array('%s','%d');
			$where_format = array('%d');
			$wpdb->update($table_name, $data, $where, $format, $where_format);
			return true;
		} else{
			$answer_content = wp_kses_post($_POST['answer_content']);
			$question_id 	= (int)($_POST['question_id']);
			$user_id 		= (int)($_POST['wp_users_id']);
			$wpdb->insert($table_name, array(
				'answer_content' 	=> $answer_content,
				'question_id' 		=> $question_id,
				'wp_users_id'		=> $user_id,	
				'created_at'		=> current_time('mysql')
			), array('%s','%d','%d','%s'));
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
	
	
	function runjs(){ ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#qbotform').validate({
				ignore: "",
				rules: {
					answer_content: {
						required: true,
						minlength: 3,
						highlight: function(element) {
							$(element).parent().parent().parent().parent().addClass('form-invalid');
						}
					},
				},
				unhighlight: function(element) {
					$(element).parent().parent().parent().parent().removeClass('form-invalid');
				},		
			});
			
			jQuery('#qbotform input[type="submit"]').on('click',function(){
				content = tinymce.get('answer_content').getContent();
				$("#answer_content").val(content);
			});	
			
			$(".updated").fadeOut(2000); 
		});
	</script>			
	<?php	
	}
	
	public function getAnswer($id){
		global $wpdb;
		
		$id = (int)($id);
		$answer = $wpdb->get_row( 'SELECT * FROM mcl_qbot_answers WHERE id = '.$id, 'OBJECT' );
		if(!empty($answer)){
			return $answer;
		} else{
			return false;
		}
	}	

}
?>