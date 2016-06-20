<?php
class platformpressFrontend extends platformpressSettings{

	function run(){
		global $wpdb, $platformpress_plugin_settings, $wp, $wp_query, $post;

		$plank_listing_url = get_permalink();


		$ip = $_SERVER['REMOTE_ADDR'];

		$this->loadStyle();

		// Parameters as array of key =&gt; value pairs
		$qid = get_query_var('qid');
		$qid = $qid;

		$action = 'plank_list';

		/* If we are on plank view page load post variable*/
		if($qid!==""){
			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'platformpress-plank');
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
			$action = 'plank_view';
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
						$table_name = 'mcl_platformpress_spam';
						$user_ID = get_current_user_id();
						$ip= $_SERVER['REMOTE_ADDR'];

						$remarkInfo		= get_post($spamObjId);
						$plankInfo	= get_post($remarkInfo->post_parent);
						$plankUrl = get_permalink($plankInfo->ID);

						$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM mcl_platformpress_spam WHERE wp_user_id="'.$user_ID.'" AND obj_id="'.$spamObjId.'"' );
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
							wp_redirect($plankUrl.'#platformpressremark-'.$remarkInfo->ID);
						}
						else{
							$url = add_query_arg(array('error'=>'Already Marked by You'),$plankUrl);
							wp_redirect($url.'#platformpressremark-'.$remarkInfo->ID);
						}
					}
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_spam.php';

				}

		break;
		case 'plank_list':
			$action = isset($_GET['action']) ? $_GET['action'] : "";
			if(($action=="add-new-plank") || ($action=="update-plank")){

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
					$plank_title 		= sanitize_text_field($_POST['plank_title']);
					$plank_description 	= wp_kses_post($_POST['plank_description']);

					// Create post object
					if((platformpress_setting_get("auto_approve_new_planks")=="1") || (is_admin())){
						$post_status = 'publish';
						$success_message = 'Plank created successfully.';
					} else{
						$post_status = 'pending';
						$success_message = 'Plank awaiting for approval.';
					}


					if($action=="add-new-plank"){
						//Add
						$my_post = array(
						  'post_type'     => 'platformpress-plank',
						  'post_title'    => $plank_title,
						  'post_content'  => $plank_description,
						  'post_status'   => $post_status,
						  'post_author'   => $user_id,
						);
						$plankId = wp_insert_post($my_post);

						$this->updateMyLocation();
						$this->nofity_new_plank($plankId);
                        $term_taxonomy_ids = wp_set_object_terms( $plankId, $plank_termID, 'topic', false );

                        if ( is_wp_error( $term_taxonomy_ids ) ) {
	                           // There was an error somewhere and the terms couldn't be set.
                               $success_message = $success_message . 'Could not set category';
                           }

					} elseif($action=="update-plank"){
						//Update
							$plankId = $_GET['post_id'];
						    $my_post = array(
							  'ID'            => $_GET['post_id'],
							  'post_title'    => $plank_title,
							  'post_content'  => $plank_description,
						  );
						  wp_update_post($my_post);

						  $success_message = 'Plank updated successfully.';
					}

					if(isset($_POST['cat']) && ($_POST['cat']!="")){
						wp_set_object_terms($plankId, intval($_POST['cat']), 'topic', true);
					}

                    if(isset($_POST['plank_category']) && ($_POST['plank_category']!="")){
					    $term_taxonomy_ids = wp_set_object_terms( $plankId, intval($_POST['plank_category']), 'topic', false );
                    }

                    if ( is_wp_error( $term_taxonomy_ids ) ) {
	                       $success_message .= '<br />Topic not set.';
                        }
                    else {
                        # code...
                        $success_message .= '<br />Topic set.';
                    }
					$this->flash_message('success', $success_message );

					if($_GET['action']=='add-new-plank'){
						wp_redirect(get_the_permalink($plankId));
						exit;
					} elseif($_GET['action']=='update-plank'){
						// Open in edit mode
						if(isset($_GET['post_id']) && ($_GET['post_id']!="") && is_numeric($_GET['post_id'])){
							$_GET['post_id'] = (int)($_GET['post_id']);
							$post = get_post($_GET['post_id']);
							$_POST['plank_title'] 		= $post->post_title;
							$_POST['plank_description'] 	= $post->post_content;
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
						$_POST['plank_title'] 		= $post->post_title;
						$_POST['plank_description'] 	= $post->post_content;
					}
				}


				require_once plugin_dir_path( __FILE__ ) . '../views/frontend_planks_add.php';

			} elseif($action=="delete-plank"){
				// Open in delete mode
				if(isset($_GET['post_id']) && ($_GET['post_id']!="") && is_numeric($_GET['post_id'])){
					$_GET['post_id'] = (int)($_GET['post_id']);
					$post = get_post($_GET['post_id']);
					//Delete if authorized well
					if($post->post_author==$this->settings['general']['user_id']){
						wp_delete_post($_GET['post_id'], true);
						$this->flash_message('success', "Plank deleted successfully." );
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
						'post_type' 	=> 'platformpress-plank',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'platformpress_views_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='remark')){
					$query = array(
						'post_type' 	=> 'platformpress-plank',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'platformpress_remarks_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='vote')){
					$query = array(
						'post_type' 	=> 'platformpress-plank',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'platformpress_plank_vote_count',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} elseif(isset($_GET['sort']) && ($_GET['sort']=='favourite')){
					$query = array(
						'post_type' 	=> 'platformpress-plank',
						'post_status'	=> 'publish',
						'meta_key' 		=> 'platformpress_plank_favorite',
						'orderby' 		=> 'meta_value_num',
						'order' 		=> 'DESC'
					);
				} else{
					$query = array(
						'post_type' 	=> 'platformpress-plank',
						'post_status'	=> 'publish',
						'order_by' 		=> array('ID'),
						'order' 		=> 'DESC',
					);
				}

				//search if search tag set
				if(isset($_GET['platformpress-search']) && (strlen($_GET['platformpress-search'])>0)){
					$keywords = $_GET['platformpress-search'];
					$query['s'] = $keywords;
				}

				$query['posts_per_page'] = $limit;
				$query['offset']		 = $offset;

				$cat = isset($_GET['cat']) ? $_GET['cat'] : "";

				if($cat!==""){
					$query['platformpress-categories'] = $cat;
				}

				query_posts($query);

				require_once plugin_dir_path( __FILE__ ) . '../views/frontend_planks_list.php';
			}

			break;
				case 'plank_view':
					$plankUrl = get_permalink(get_the_ID());
					$this->handleViewCounter(get_the_ID());
					$this->updateMyLocation();
					// If remark submitted
					if(isset($_POST['platformpressremarkcontent']) && ($_POST['platformpressremarkcontent']!="")){
						$user_id = get_current_user_id();
						$remark_content = wp_kses_post($_POST['platformpressremarkcontent']);
						if($remark_content!=="")
						{
							$this->updateMyLocation();
							// Create post object
							$my_post = array(
							  'post_type'    => 'platformpress-remark',
							  'post_title'    => 'PlatformPress Remark',
							  'post_content'  => $remark_content,
							  'post_status'   => 'publish',
							  'post_author'   => $user_id,
							  'post_parent'	  => $qid,
							);
							$post_ID = wp_insert_post($my_post);

							$my_post = array(
							  'ID'           => $post_ID,
							  'post_name'   => 'platformpress-remark-'.$post_ID,
							);
							wp_update_post($my_post);

							$this->nofity_new_remark($wpdb->insert_id);
							$this->flash_message('success', $message = 'Remarked successfully.' );
							//$url = $this->getPlankUrl($qid);
							wp_redirect($plankUrl.'#platformpressremark-'.$post_ID);
							exit;
						} else{
							$this->flash_message('error', $message = 'Please enter correct remark.' );
							wp_redirect($plankUrl);
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

						$table_name = 'mcl_platformpress_comments';
						$user_id = get_current_user_id();
						$aid=$_POST['aid'];
						$aid = (int)($aid);
						$comment_content = sanitize_text_field($_POST['platformpress-comment-content']);
						if($comment_content!=="")
						{
							$comment_id = $this->addComment($aid,$comment_content);
							$this->flash_message('success', $message = 'Commented successfully.' );
							wp_redirect($plankUrl.'#platformpresscomment-'.$comment_id);
							exit;
						} else{
							$this->flash_message('error', $message = 'Please enter your comment.' );
						}
					}
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_plank_view.php';
			break;
			case 'login':
				if(is_user_logged_in()){
					$url = $this->getBaseUrl();
					wp_redirect($url);
				} else{
					require_once plugin_dir_path( __FILE__ ) . '../views/frontend_plank_login.php';
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

				$('#platformpressform').validate({
				rules: {
					plank_title: {
						required: true,
						minlength: 3
					},
					plank_description: {
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
			jQuery('.slug').slugify('#plank_title');

		jQuery(".platformpress-alert .platformpress_flash_success").hover(function(){
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
		$css .= ".platformpress-frontend-wrap .platformpress-plank .description{".$styleSettings."}";
		$css .= "</style>";
		echo $css;
	}

	function userRemarksCount($user_id){
		global $wpdb;
		$user_id = (int)($user_id);
		$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_platformpress_remarks WHERE wp_users_id='.$user_id.'', 'OBJECT');
		return $res->counts;
	}

	function userPlanksCount($user_id){
		global $wpdb;
		$user_id = (int)($user_id);
		$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_platformpress_planks WHERE wp_users_id='.$user_id.'', 'OBJECT');
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

		require_once PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'/social/facebook_login_class.php';
		$facebook = new Facebook_Login(
		$this->settings['stored']['facebook_app_id'],
		$this->settings['stored']['facebook_app_secret'],
		$currentUrl);

		if($userData = $facebook->getUserData()){

			$userId = $this->getUserIdByLogin($userData['email']);

			if($userId!=""){
				update_user_meta($userId, '_platformpress_user_lastlogin_type','facebook');
				update_user_meta($userId, '_platformpress_user_fb_id', $userData['id']);
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
				add_user_meta($userId, '_platformpress_user_lastlogin_type','facebook',true);
				add_user_meta($userId, '_platformpress_user_fb_id', $userData['id'],true);
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

		require_once PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'/social/google/index.php';

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
					update_user_meta($userId, '_platformpress_user_lastlogin_type','google');
					update_user_meta($userId, '_platformpress_user_google_id',$user['id']);
					update_user_meta($userId, '_platformpress_user_google_pic_url',$profile_image_url);
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
					add_user_meta($userId, '_platformpress_user_lastlogin_type','google',true);
					add_user_meta($userId, '_platformpress_user_google_id',$user['id'],true);
					add_user_meta($userId, '_platformpress_user_google_pic_url',$profile_image_url,true);
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
		$table_name = 'mcl_platformpress_views';
		$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM mcl_platformpress_views WHERE ip_add="'.$ip.'" AND plank_id="'.$qid.'"' );
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
				'plank_id' 	=> $qid,
				'wp_users_id'	=> $user_ID,
				'enter_at'		=> current_time('mysql')
			), array('%s','%s','%d','%d','%s'));
			$this->setPlankViews($qid);
		}
	}


}

?>


 <script>
function goBack() {
    window.history.back();
}
</script>
