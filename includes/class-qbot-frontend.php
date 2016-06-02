<?php
class qbotFrontend extends qbotSettings{

	function run(){
		global $wpdb, $qbot_plugin_settings, $wp, $wp_query, $post;
		
		$question_listing_url = get_permalink();

		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$this->loadStyle();
		
		// Parameters as array of key =&gt; value pairs
		$qid = get_query_var('qid');
		$qid = $qid;
		
		$action = 'question_list';

		/* If we are on question view page load post variable*/
		if($qid!==""){
			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'qbot-question');
				if($post==''){
					header("HTTP/1.0 404 Not Found - Archive Empty");
					$wp_query->set_404();
					require TEMPLATEPATH.'/404.php';
					exit;
				}
				$qid = $post->ID;
			} else{
				$post = get_post($qid);
			}
			setup_postdata( $GLOBALS['post'] =& $post );			
		}
		
		if(isset($_GET['action']) && $_GET['action']=='login'){
			$action = "login";
		} elseif(isset($_GET['action']) && $_GET['action']=='user-dashboard') {
			$action = "user-dashboard";
		}elseif(isset($_GET['action']) && $_GET['action']=='spam'){
			$action = "spam";
		} elseif($qid!==""){
			$action = 'question_view';
		}
		
		if(!is_user_logged_in()){
			if((isset($_GET['state'])) && ($_GET['state']=='facebook-login')){
				$this->handelFacebookLogin($qid);
				exit;
			}
			
			//if only code variable request is from google
			if((isset($_GET['code']) && (!isset($_GET['state']))) 
			|| ((isset($_GET['state'])) && ($_GET['state']=='google-login'))){
				$this->handelGoogleLogin($qid);
			}
		}
		
		switch($action){
		case 'spam':
				$action = isset($_GET['action']) ? $_GET['action'] : "";
				if(($action=="spam")){
				
					if(!is_user_logged_in()){
						$params = array('action'=>'login');
						$currentUrl = home_url(add_query_arg($params,$wp->request));
						header('Location: '.$currentUrl);
						exit;
					}
				
					if(isset($_POST['submit']) && isset($_POST['spamObjId'])){
						$spam_id = (int)($_POST['spam']);
						$spamObjId = (int)($_POST['spamObjId']);
						$spamObjName = sanitize_text_field($_POST['spamObjName']);
						$table_name = 'mcl_qbot_spam';
						$user_ID = get_current_user_id();
						$ip= $_SERVER['REMOTE_ADDR'];
						
						$answerInfo		= get_post($spamObjId);
						$questionInfo	= get_post($answerInfo->post_parent);
						$questionUrl = get_permalink($questionInfo->ID);

						$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM mcl_qbot_spam WHERE wp_user_id="'.$user_ID.'" AND obj_id="'.$spamObjId.'"' );
						if($res->counts==0){
						
							$wpdb->insert($table_name, array(
								'ip_add' 		 => $ip,
								'wp_user_id' 	 =>$user_ID,
								'obj_id'         =>$spamObjId,
								'spam_val'   	 =>$spam_id,
								'group_name'	=>$spamObjName,
								'time'	 =>current_time('mysql')
							), array('%s','%d','%d','%s','%s','%s'));
							
							//$this->flash_message('success', $message = 'Spam marked successfully' );
							wp_redirect($questionUrl.'#qbotanswer-'.$answerInfo->ID);
						}
						else{
							$url = add_query_arg(array('error'=>'Already Marked by You'),$questionUrl);
							wp_redirect($url.'#qbotanswer-'.$answerInfo->ID);
						}
					}
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_spam.php';
					
				} 
				
		break;
		case 'question_list':
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			if(($action=="add-new-question") || ($action=="update-question")){
				
				if(!is_user_logged_in()){
					$params = array('action'=>'login');
					$currentUrl = home_url(add_query_arg($params,$wp->request));
					wp_redirect($currentUrl);
					//header('Location: '.$currentUrl);
					exit;
				}
				
				if(isset($_POST) && (count($_POST)>0))
				{
					$user_id 				= get_current_user_id();
					$question_title 		= sanitize_text_field($_POST['question_title']);
					$question_description 	= wp_kses_post($_POST['question_description']);
					
					// Create post object
					if((qbot_setting_get("auto_approve_new_questions")=="1") || (is_admin())){
						$post_status = 'publish';
						$success_message = 'Question created successfully.';
					} else{
						$post_status = 'pending';
						$success_message = 'Question awaiting for approval.';
					}
					
					
					if($action=="add-new-question"){
						//Add
						$my_post = array(
						  'post_type'     => 'qbot-question',
						  'post_title'    => $question_title,
						  'post_content'  => $question_description,
						  'post_status'   => $post_status,
						  'post_author'   => $user_id,
						);
						$questionId = wp_insert_post($my_post);
						
						$this->updateMyLocation();
						$this->nofity_new_question($questionId);
					} elseif($action=="update-question"){
						//Update
							$questionId = $_GET['post_id'];
						    $my_post = array(
							  'ID'            => $_GET['post_id'],
							  'post_title'    => $question_title,
							  'post_content'  => $question_description,
						  );
						  wp_update_post($my_post);
						  $success_message = 'Question updated successfully.';
					}
					
					if(isset($_POST['cat']) && ($_POST['cat']!="")){
						wp_set_object_terms($questionId, intval($_POST['cat']), 'qbot-categories',true);
					}
					
					$this->flash_message('success', $success_message );
		
					if($_GET['action']=='add-new-question'){
						wp_redirect(get_the_permalink($questionId));
						exit;
					} elseif($_GET['action']=='update-question'){
						// Open in edit mode
						if(isset($_GET['post_id']) && ($_GET['post_id']!="") && is_numeric($_GET['post_id'])){
							$_GET['post_id'] = (int)($_GET['post_id']);
							$post = get_post($_GET['post_id']);
							$_POST['question_title'] 		= $post->post_title;
							$_POST['question_description'] 	= $post->post_content; 
						}
					}
					
				} else{
					// Open in edit mode
					if(isset($_GET['post_id']) && ($_GET['post_id']!="") && is_numeric($_GET['post_id'])){
						$_GET['post_id'] = (int)($_GET['post_id']);
						$post = get_post($_GET['post_id']);
						if($post->post_author!=get_current_user_id()){
							header("HTTP/1.0 404 Not Found - Archive Empty");
							$wp_query->set_404();
							require TEMPLATEPATH.'/404.php';
							exit;
						}
						$_POST['question_title'] 		= $post->post_title;
						$_POST['question_description'] 	= $post->post_content; 
					}
				}

					
				require_once plugin_dir_path( __FILE__ ) . '../views/frontend_questions_add.php';
				
			} elseif($action=="delete-question"){
				// Open in delete mode
				if(isset($_GET['post_id']) && ($_GET['post_id']!="") && is_numeric($_GET['post_id'])){
					$_GET['post_id'] = (int)($_GET['post_id']);
					$post = get_post($_GET['post_id']);
					//Delete if authorized well
					if($post->post_author==$this->settings['general']['user_id']){
						wp_delete_post($_GET['post_id'], true);
						$this->flash_message('success', "Question deleted successfully." );
						wp_redirect($this->getBaseUrl());
						exit;
					}
				}
			}
			
			else{
				
				$paged = get_query_var('paged');
				$paged = (int)($paged);

				$pagenum = (($paged=="") || ($paged==0)) ? 1 : $paged;
				
				$limit = 10;
				$offset = ( $pagenum - 1 ) * $limit;
				$catfilter = isset( $_GET['cat'] ) ? absint( $_GET['cat'] ) : '';
				$user_id = get_current_user_id();
				
				if(isset($_GET['sort']) && ($_GET['sort']=='view')){
					$query = array(
						'post_type' 	=> 'qbot-question',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'qbot_views_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='answer')){
					$query = array(
						'post_type' 	=> 'qbot-question',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'qbot_answers_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='vote')){
					$query = array(
						'post_type' 	=> 'qbot-question',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'qbot_question_vote_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='favourite')){
					$query = array(
						'post_type' 	=> 'qbot-question',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'qbot_question_favorite',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} else{
					$query = array(
						'post_type' 	=> 'qbot-question',
						'post_status'	=> 'publish',
						'order_by' 		=> array('ID'),
						'order' 		=> 'DESC',
					);
				}
				
				//search if search tag set
				if(isset($_GET['qbot-search']) && (strlen($_GET['qbot-search'])>0)){
					$keywords = $_GET['qbot-search'];
					$query['s'] = $keywords;
				}

				$query['posts_per_page'] = $limit;
				$query['offset']		 = $offset;
				
				$cat = isset($_GET['cat']) ? $_GET['cat'] : "";

				if($cat!==""){
					$query['qbot-categories'] = $cat;
				}
				
				query_posts($query);
				
				require_once plugin_dir_path( __FILE__ ) . '../views/frontend_questions_list.php';
			}
				
			break;
				case 'question_view':
					$questionUrl = get_permalink(get_the_ID());
					$this->handleViewCounter(get_the_ID());
					$this->updateMyLocation();
					// If answer submitted
					if(isset($_POST['qbotanswercontent']) && ($_POST['qbotanswercontent']!="")){
						$user_id = get_current_user_id();
						$answer_content = wp_kses_post($_POST['qbotanswercontent']);
						if($answer_content!=="")
						{
							$this->updateMyLocation();
							// Create post object
							$my_post = array(
							  'post_type'    => 'qbot-answer',
							  'post_title'    => 'QBOT Answer',
							  'post_content'  => $answer_content,
							  'post_status'   => 'publish',
							  'post_author'   => $user_id,
							  'post_parent'	  => $qid,
							);
							$post_ID = wp_insert_post($my_post);
							
							$my_post = array(
							  'ID'           => $post_ID,
							  'post_name'   => 'qbot-answer-'.$post_ID,
							);
							wp_update_post($my_post);
							
							$this->nofity_new_answer($wpdb->insert_id);
							$this->flash_message('success', $message = 'Answered successfully.' );
							//$url = $this->getQuestionUrl($qid);
							wp_redirect($questionUrl.'#qbotanswer-'.$post_ID);
							exit;
						} else{
							$this->flash_message('error', $message = 'Please enter correct answer.' );
							wp_redirect($questionUrl);
						}
					}
					
					//If comment submitted
					if(isset($_POST['comment'])){
					
						if(!is_user_logged_in()){
							$params = array('action'=>'login');
							$currentUrl = home_url(add_query_arg($params,$wp->request));
							header('Location: '.$currentUrl);
							exit;
						}
					
						$table_name = 'mcl_qbot_comments';
						$user_id = get_current_user_id();
						$aid=$_POST['aid'];
						$aid = (int)($aid);
						$comment_content = sanitize_text_field($_POST['qbot-comment-content']);
						if($comment_content!=="")
						{
							$comment_id = $this->addComment($aid,$comment_content);
							$this->flash_message('success', $message = 'Commented successfully.' );
							wp_redirect($questionUrl.'#qbotcomment-'.$comment_id);
							exit;
						} else{
							$this->flash_message('error', $message = 'Please enter your comment.' );
						}					
					}
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_question_view.php';
			break;
			case 'login':
				if(is_user_logged_in()){
					$url = $this->getBaseUrl();
					wp_redirect($url);
				} else{
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_question_login.php';
				}
			break;
			case 'spam':
				require_once plugin_dir_path( __FILE__ ) . '../views/frontend_spam.php';
			break;				
		}
	}

	/**
	 * Initiate the script.
	 * Calls the validation options on the comment form.
	 */
	
	
	function runjs(){ ?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {

				$('#qbotform').validate({	
				rules: {
					question_title: {
						required: true,
						minlength: 3
					},
					question_description: {
						required: true,
						minlength: 3
					},
				},
				highlight: function(element) {
					$(element).parent().parent().addClass('form-invalid');
				},		
				unhighlight: function(element) {
					$(element).parent().parent().removeClass('form-invalid');
				},		
			});
			jQuery('.slug').slugify('#question_title');
			
		jQuery(".qbot-alert .qbot_flash_success").hover(function(){
        jQuery(this).fadeOut(3000);
		
        });
		});
	</script>			
	<?php	
	}
	
	function runcss(){
		$settings = $this->settings['stored'];
		
		if(isset($settings['font']) && ($settings['font']!="")){
			wp_enqueue_style(
				'google-font-api',
				'//fonts.googleapis.com/css?family='.$settings['font'],
				array(),
				'1.0'
			);		
			$settings['font'] = str_replace('+',' ',$settings['font']);
		}
		
		$styleSettings = array();
		$styleSettings[] = ((isset($settings['font'])) && ($settings['font']!="")) ? "font-family:".$settings['font']."" : "";
		$styleSettings[] = ((isset($settings['font_bold'])) && ($settings['font_bold']!="")) ? "font-weight:blod" : "";
		$styleSettings[] = ((isset($settings['font_italic'])) && ($settings['font_italic']!="")) ? "font-style:italic" : "";
		$styleSettings[] = ((isset($settings['font_size'])) && ($settings['font_size']!="")) ? "font-size:".$settings['font_size']."px" : "";
		$styleSettings[] = ((isset($settings['line_height_size'])) && ($settings['line_height_size']!="")) ? "line-height:".$settings['line_height_size']."px" : "";
		$styleSettings[] = ((isset($settings['font_color'])) && ($settings['font_color']!="")) ? "color:#".$settings['font_color'] : "";
		$styleSettings = array_filter($styleSettings);
		$styleSettings = implode(';',$styleSettings);
		$css = "";
		$css = "<style type=\"text/css\">";
		$css .= ".qbot-frontend-wrap .qbot-question .description{".$styleSettings."}";
		$css .= "</style>";
		echo $css;
	}

	function userAnswersCount($user_id){
		global $wpdb;
		$user_id = (int)($user_id);
		$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_answers WHERE wp_users_id='.$user_id.'', 'OBJECT');
		return $res->counts;
	}
	
	function userQuestionsCount($user_id){
		global $wpdb;
		$user_id = (int)($user_id);
		$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_questions WHERE wp_users_id='.$user_id.'', 'OBJECT');
		return $res->counts;
	}

	function getUserIdByLogin($email){
		global $wpdb;
		$record = $wpdb->get_var($wpdb->prepare("SELECT ID FROM $wpdb->users WHERE user_email = %s",
		array($email)));
		return $record;
	}
	
	function handelFacebookLogin($qid){
		global $wp;
		if(isset($qid) && ($qid!="")){
			$qid = $qid;
			$params = array('qid'=>$qid,'state'=>'facebook-login');
			$currentUrl = home_url(add_query_arg($params,$wp->request));
		} else{
			$params = array('state'=>'facebook-login');
			$currentUrl = home_url(add_query_arg($params,$wp->request));
		}
		
		require_once QBOT_PLUGIN_INCLUDE_PATH.'/social/facebook_login_class.php';
		$facebook = new Facebook_Login(
		$this->settings['stored']['facebook_app_id'],
		$this->settings['stored']['facebook_app_secret'],
		$currentUrl);
		
		if($userData = $facebook->getUserData()){
		
			$userId = $this->getUserIdByLogin($userData['email']);
			
			if($userId!=""){
				update_user_meta($userId, '_qbot_user_lastlogin_type','facebook');
				update_user_meta($userId, '_qbot_user_fb_id', $userData['id']);
				wp_set_auth_cookie($userId, false, is_ssl() );
			} else{
				//Registeration code
				$password = uniqid();
				$userRecord = array(
				'user_login'    =>   $userData['email'],
				'user_email'    =>   $userData['email'],
				'user_pass'     =>   $password,
				'first_name'    =>   $userData['first_name'],
				'last_name'     =>   $userData['last_name'],
				'nickname'      =>   $userData['first_name'],
				);
				$userId = wp_insert_user($userRecord);
				add_user_meta($userId, '_qbot_user_lastlogin_type','facebook',true);
				add_user_meta($userId, '_qbot_user_fb_id', $userData['id'],true);
				$this->welcome_mail($userId,$password);
				wp_set_auth_cookie($userId, false, is_ssl() );
			}
			
			$params = array('qid'=>$qid,'state'=>'facebook-login');
			$currentUrl = home_url(add_query_arg($params,$wp->request));
			echo "<script>
			window.opener.location.reload();
			window.close();
			</script>";
			//header('Location: '.$currentUrl);
			exit;
		} else{
			$facebook->login();
		}			
		
	}
	
	function handelGoogleLogin($qid){
		global $wp;

		$qid = $qid;	
		$redirectUrl = $this->settings['general']['plugin_base_url'];
		$google_redirect_url 	= $redirectUrl;
		
		require_once QBOT_PLUGIN_INCLUDE_PATH.'/social/google/index.php';
		
		if($gClient->getAccessToken()){
		
			  $user 				= $google_oauthV2->userinfo->get();
			  
			  $user_id 				= $user['id'];
			  $user_name 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
			  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
			  $profile_url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
			  $profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
			  $_SESSION['token'] 	= $gClient->getAccessToken();

			  $userId = $this->getUserIdByLogin($email);
			  
				unset($_SESSION['token']);
				$gClient->revokeToken();
			  
			  
			  if($userId!=""){
					update_user_meta($userId, '_qbot_user_lastlogin_type','google');
					update_user_meta($userId, '_qbot_user_google_id',$user['id']);
					update_user_meta($userId, '_qbot_user_google_pic_url',$profile_image_url);
					wp_set_auth_cookie($userId, false, is_ssl() );
				} else{
					//Registeration code
					$password = uniqid();
					$userRecord = array(
					'user_login'    =>   $email,
					'user_email'    =>   $email,
					'user_pass'     =>   $password,
					'first_name'    =>   $user_name,
					'last_name'     =>   '',
					'nickname'      =>   $user_name,
					);
					$userId = wp_insert_user($userRecord);
					add_user_meta($userId, '_qbot_user_lastlogin_type','google',true);
					add_user_meta($userId, '_qbot_user_google_id',$user['id'],true);
					add_user_meta($userId, '_qbot_user_google_pic_url',$profile_image_url,true);
					$this->welcome_mail($userId,$password);
					wp_set_auth_cookie($userId, false, is_ssl() );
					unset($_SESSION['token']);
					$gClient->revokeToken();	
			}
			echo "<script>
			window.opener.location.reload();
			window.close();
			</script>";			
			//header('Location: '.$google_redirect_url);
			exit;
			
		} else{
			$authUrl = $gClient->createAuthUrl();
			wp_redirect($authUrl);
		}			
		
	}	
	
	protected function handleViewCounter($qid)
	{
		global $wpdb;
		$qid = (int)($qid);
		$user_ID = get_current_user_id();
		$ip= $_SERVER['REMOTE_ADDR'];
		$table_name = 'mcl_qbot_views';
		$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM mcl_qbot_views WHERE ip_add="'.$ip.'" AND question_id="'.$qid.'"' );
		if($res->counts==0){
			$response = $this->getMyLocation();
			if($response){
				$countryCode = $response->countryCode;
			} else{
				// if no response from api by default add "in" as indian viewer
				$countryCode = 'in';
			}
			$wpdb->insert($table_name, array(
				'ip_add' 		=> $ip,
				'ip_country_code'=>$countryCode,
				'question_id' 	=> $qid,
				'wp_users_id'	=> $user_ID,	
				'enter_at'		=> current_time('mysql')
			), array('%s','%s','%d','%d','%s'));
			$this->setQuestionViews($qid);
		}
	}	
	
	
}

?>


 <script>
function goBack() {
    window.history.back();
}
</script>