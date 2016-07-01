<?php 
class platformpressAdminRemarks extends platformpressSettings{

	function run(){
		$this->loadScriptAndStyle();
		
		$action = isset($_GET['action']) ? $_GET['action'] : "";
		$action = (($action=="") && isset($_GET['edit'])) ? 'edit' : $action;
		
		/* Process actions */
		switch($action){
			case 'add':
				if(isset($_POST['platformpress-submitted'])) {
					if($this->handelFormData()){
						$this->updateMyLocation();
						$params = array('page'=>'platformpress-plugin-remarks');
						$url = esc_url(add_query_arg($params,'admin.php'));
						unset($_POST);
						add_action( 'admin_notices',  printf( '<div class="updated">Remark created successfully.</div>'));
					}
				}
				
				if(isset($_GET["plank_id"])){
					$plank_id = $_GET["plank_id"];
					global $wpdb;
					$plank = $wpdb->get_row( 'SELECT plank_title FROM mcl_platformpress_planks WHERE id='.$plank_id .''
					, 'OBJECT' );
					if(!$plank_id){
						echo "Access not allowed";
						exit;
					} else{
						$_POST['plank_id'] = (int)($plank_id);
						$_POST['plank_title'] = $plank->plank_title;
					}
				}
			break;
			case 'edit':
				if(isset($_POST['platformpress-submitted'])) {
					if($this->handelFormData()){
						add_action( 'admin_notices',  printf( '<div class="updated">Remark updated successfully.</div>'));
						$params = array('page'=>'platformpress-plugin-remarks');
						$url = esc_url(add_query_arg($params,'admin.php'));
					}
				}
				$_GET['id'] = (int)($_GET['id']);	
				$_POST = $this->openRecord($_GET['id']);
			break;
			case 'delete':
				if(isset($_GET['id'])) {
					$remark_id = $_GET['id'];
					$remark_id = (int)($remark_id);
					if($this->delete($remark_id)){
						add_action( 'admin_notices',  printf( '<div class="updated">Remark deleted successfully.</div>'));
						$params = array('page'=>'platformpress-plugin-remarks');
						$url = esc_url(add_query_arg($params,'admin.php'));
						//wp_redirect($url);
					}
				}
			break;		
		}
		
		require_once plugin_dir_path( __FILE__ ) . '../views/admin_remarks.php';
	}
	
	protected function openRecord($id){
		global $wpdb;
		$res = $wpdb->get_row( 'SELECT mcl_platformpress_remarks.*,mcl_platformpress_planks.plank_title FROM mcl_platformpress_remarks 
	INNER JOIN mcl_platformpress_planks ON(mcl_platformpress_remarks.plank_id=mcl_platformpress_planks.id) WHERE mcl_platformpress_remarks.id = '.$id, 'OBJECT' );
		$data = array(
			'remark_content' 	=> $res->remark_content,
			'plank_id' 		=> $res->plank_id,
			'wp_users_id' 		=> $res->wp_users_id,
			'plank_title' 	=> $res->plank_title,
		);
		return $data;
	}
	
	protected function delete($remark_id){
		global $wpdb;
		
		$remark_id = (int)($remark_id);
		
		//Delete if spamed
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_platformpress_spam WHERE obj_id = %d AND group_name='remark'",$remark_id));
		//Delete if comment
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_platformpress_comments WHERE remark_id = %d",$remark_id));
		//Dlete remark
		$wpdb->query($wpdb->prepare("DELETE FROM mcl_platformpress_remarks WHERE id = %d",$remark_id));
		return true;
	}
	
	
	protected function handelFormData(){
		global $wpdb;
		$table_name = 'mcl_platformpress_remarks';
		if(isset($_GET['id'])){
			
			$_GET['id'] = (int)($_GET['id']);
			$remark_content = wp_kses_post($_POST['remark_content']);
			$user_id 		= (int)($_POST['wp_users_id']);
			$data = array(
				'remark_content' 	=> $remark_content,
				'wp_users_id'		=> $user_id,	
			);
			$where = array('id' => $_GET['id']);
			$format = array('%s','%d');
			$where_format = array('%d');
			$wpdb->update($table_name, $data, $where, $format, $where_format);
			return true;
		} else{
			$remark_content = wp_kses_post($_POST['remark_content']);
			$plank_id 	= (int)($_POST['plank_id']);
			$user_id 		= (int)($_POST['wp_users_id']);
			$wpdb->insert($table_name, array(
				'remark_content' 	=> $remark_content,
				'plank_id' 		=> $plank_id,
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
			$('#platformpressform').validate({
				ignore: "",
				rules: {
					remark_content: {
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
			
			jQuery('#platformpressform input[type="submit"]').on('click',function(){
				content = tinymce.get('remark_content').getContent();
				$("#remark_content").val(content);
			});	
			
			$(".updated").fadeOut(2000); 
		});
	</script>			
	<?php	
	}
	
	public function getRemark($id){
		global $wpdb;
		
		$id = (int)($id);
		$remark = $wpdb->get_row( 'SELECT * FROM mcl_platformpress_remarks WHERE id = '.$id, 'OBJECT' );
		if(!empty($remark)){
			return $remark;
		} else{
			return false;
		}
	}	

}
?>