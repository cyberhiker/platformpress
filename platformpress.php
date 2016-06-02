<?php
	ob_start();
    /*
    Plugin Name: QBOT
    Plugin URI: http://www.plugmojo.com/qbot/
    Description: Plugin to integrate question & answer features for wordpress it will turn your website into a Search Engine MAGNET like Quora, Yahoo Answers.
    Author: Plugmojo
    Version: 1.0
    Author URI: http://www.plugmojo.com
    */
	if ( ! defined( 'QBOT_PLUGIN_URL' ) )
    define( 'QBOT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );	
	if ( ! defined( 'QBOT_PLUGIN_PATH' ) )
    define( 'QBOT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );	
	if ( ! defined( 'QBOT_PLUGIN_IMAGES_URL' ) )
    define( 'QBOT_PLUGIN_IMAGES_URL', QBOT_PLUGIN_URL . 'images/' );
	if ( ! defined( 'QBOT_PLUGIN_CSS_URL' ) )
    define( 'QBOT_PLUGIN_CSS_URL', QBOT_PLUGIN_URL . 'css/' );
	if ( ! defined( 'QBOT_PLUGIN_INCLUDE_PATH' ) )
    define( 'QBOT_PLUGIN_INCLUDE_PATH', QBOT_PLUGIN_PATH . 'includes/' );
	if ( ! defined( 'QBOT_PLUGIN_VIEW_PATH' ) )
    define( 'QBOT_PLUGIN_VIEW_PATH', QBOT_PLUGIN_PATH . 'views/' );

	if ( ! defined( 'QBOT_PLUGIN_DOWNLOAD_SITE' ) )
    define( 'QBOT_PLUGIN_DOWNLOAD_SITE', 'http://www.plugmojo.com/qbot/' );

	require_once QBOT_PLUGIN_INCLUDE_PATH.'qbot-installer.php';
	register_activation_hook(__FILE__,'qbot_plugin_install');
	
	require_once QBOT_PLUGIN_INCLUDE_PATH.'class-qbot-settings.php';
	require_once QBOT_PLUGIN_INCLUDE_PATH.'class-qbot.php';
	
	/**** SHORT CODES *********/
	#[qbot-frontend]
	function qbot_frontend(){
		//qbot_plugin_install();
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-qbot-frontend.php';
		$obj = new qbotFrontend();
		$obj->run();
	}

	/* Functions */
	function qbotManager() {
		$obj = new qbot_manager();
		$obj->run();
	}
	
	add_action('admin_menu', 'qbot_admin_menu');
	
	function qbot_admin_menu(){

		$icon_url = QBOT_PLUGIN_IMAGES_URL.'/geekheroicons/small-geek.png';
		add_menu_page('QBOT', __('QBOT'), 'manage_options', 'qbot-plugin','qbotAdmin',$icon_url);
		
		$icon_url = QBOT_PLUGIN_IMAGES_URL.'/geekheroicons/small-geek.png';
		
		add_submenu_page('qbot-plugin', __('Settings'),  __('Settings'), 'manage_options', 'qbot-plugin-settings', 'qbot_menu_manageSettings');
		add_submenu_page('qbot-plugin', __('QA SEO'), 'Admin Dashboard', 'manage_options', 'qbot-plugin');


	}
	
	add_filter( 'custom_menu_order', 'wpqbot_5911_submenu_order' );
	function wpqbot_5911_submenu_order( $menu_ord ) 
	{
		global $submenu;
		
		$submenuNewOrder = array();
		
		$submenuNewOrder['qbot-plugin'][] = $submenu['qbot-plugin'][3];
		$submenuNewOrder['qbot-plugin'][] = $submenu['qbot-plugin'][0];
		$submenuNewOrder['qbot-plugin'][] = $submenu['qbot-plugin'][1];
		$submenuNewOrder['qbot-plugin'][] = $submenu['qbot-plugin'][2];
		
		$submenu = array_merge($submenu,$submenuNewOrder);
		return $submenu;
	}
	
	function qbotAdmin() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-qbot-admin.php';
		$qa = new qbotAdmin();
		$qa->loadStyle();
		$qa->run();
	}
	
	function qbot_menu_manageQuestions(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-qbot-admin-questions.php';
		$qa = new qbotAdminQuestions();
		$qa->loadStyle();
		$qa->run();
		
	}
	
	function qbot_menu_manageAnswers(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-qbot-admin-answers.php';
		$qa = new qbotAdminAnswers();
		$qa->loadStyle();
		$qa->run();
	}
	
	function qbot_menu_manageSettings(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-qbot-admin-settings.php';
		$qa = new qbotAdminSettings();
		$qa->loadStyle();
		$qa->run();
	}
	
	function qbot_flash_get(){
		global $errors;
		if(isset($errors->errors))
		{
			if(isset($errors->errors['qbot_flash_success']))
			{
				$key = 'qbot_flash_success';
				$html = "<div id='close' class=\"qbot-alert ".$key."\">";
				foreach($errors->errors['qbot_flash_success'] as $key=>$val){
					$html .= $val."<br />";
				}	
				$html .= "</div>";
				echo $html;
			}	
			if(isset($errors->errors['qbot_flash_error']))
			{
				$key = 'qbot_flash_error';
				$html = "<div class=\"qbot-alert ".$key."\">";
				foreach($errors->errors['qbot_flash_error'] as $key=>$val){
					$html .= $val."<br />";
				}	
				$html .= "</div>";
				echo $html;
			}	
		}	
	}

	function qbot_flash_set($type,$message){
		$key = 'qbot_flash_'.$type;
		global $errors;
		$errors = new WP_Error();
		$errors->add($key,$message);
	}
	
	add_shortcode('qbot-frontend', 'qbot_frontend');
	add_shortcode('qbot-frontend-latest-questions', 'qbot_frontend_latest_questions');
	add_shortcode('qbot-frontend-latest-answers', 'qbot_frontend_latest_answers');
	
	function qbot_editor( $args = array() ) {
	
		$media_buttons = false;
		$drag_drop_upload = false;
		
		if(isset($args['media_buttons']) && ($args['media_buttons']==true)){
			$media_buttons = true;
			$drag_drop_upload = true;
		}

		extract( wp_parse_args( $args, array(
				'content'       => '',
				'id'            => 'qaeo-custom-content-editor',
				'textarea_name' => 'qbot_textarea',
				'rows'          => 4,
				'wpautop'       => false,
				'media_buttons' => false,
		) ) );
		
		wp_editor( $content, $id, array(
			'wpautop'       => $wpautop,
			'media_buttons' => $media_buttons,
			'textarea_name' => $textarea_name,
			'textarea_rows' => $rows,
			'tinymce' => array(
				'toolbar1' => 'bold, italic, underline,bullist,numlist,link,unlink,undo,redo',
				'theme_advanced_buttons1' => 'bold,italic,underline,|,' . 'bullist,numlist,blockquote,|,' . 'link,unlink,|,' . 'image,code,|,'. 'spellchecker,wp_fullscreen,dwqaCodeEmbed,|,',
			),
			'quicktags'     => false,
			'media_buttons' => false,
			'drag_drop_upload' => false,			
		) );
	}
	
	add_filter( 'template_include', 'portfolio_page_template', 99 );
	function portfolio_page_template( $template ) {
		if(isset($_GET['qbot-question'])){
			set_query_var('qid',$_GET['qbot-question']);
			$header = get_header();
			$template = $header.qbot_frontend().get_footer();
		}
		return $template;
	}	

	function initQBotPlugin() {
		global $wpdb;
		$settingsObj = new qbotSettings();
		
		$settings = $settingsObj->settings;
		
		if(isset($_POST['action']) && (($_POST['action']=='vote-up') || ($_POST['action']=='vote-down'))){
			$answerId = (int)($_POST['answerId']);
			$user_id = get_current_user_id();
			$ip = $_SERVER['REMOTE_ADDR']; 
			
			$questionId = wp_get_post_parent_id($answerId);
			
			switch($_POST['action']){
				case 'vote-up':
					$response = new stdClass;
					if(!is_user_logged_in()){
						$response->type = 'error';
						$response->message = 'Please login.';
						echo wp_json_encode($response);
						exit;
					}
					
					//check if already voted
					$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_qbot_votes WHERE wp_users_id='.$user_id.' AND qbot_answer_id='.$answerId.'', 'OBJECT');
					if(!empty($res)){
						//if already voted check is up vote
						if($res->is_up_vote==1){
							$response->type = 'error';
							$response->message = 'You already up voted this answer.';
							$response->vote_count = $settingsObj->setAnswerVotes($answerId);
							echo wp_json_encode($response);
							exit;
						} else{
							$sql = "DELETE FROM mcl_qbot_votes WHERE (wp_users_id=%d AND qbot_answer_id=%d)";
							$wpdb->query($wpdb->prepare($sql,array($user_id,$answerId)));
							$wpdb->insert('mcl_qbot_votes', array(
								'ip_add' 			=> $ip,
								'wp_users_id' 		=> $user_id,
								'qbot_question_id'	=> $questionId,
								'qbot_answer_id'	=> $answerId,
								'created_at'		=> current_time('mysql'),
								'is_up_vote'		=> 1,
							), array('%s','%d','%d','%s','%d'));
							$response->type = 'success';
							$response->message = 'Voted successfully.';
							$response->vote_count = $settingsObj->setAnswerVotes($answerId);
							echo wp_json_encode($response);
							exit;
						}
					} else{
						$wpdb->insert('mcl_qbot_votes', array(
							'ip_add' 			=> $ip,
							'wp_users_id' 		=> $user_id,
							'qbot_question_id'	=> $questionId,
							'qbot_answer_id'	=> $answerId,
							'created_at'		=> current_time('mysql'),
							'is_up_vote'		=> 1,
						), array('%s','%d','%d','%s','%d'));
						$response->type 		= 'success';
						$response->message 		= 'Voted successfully.';
						$response->vote_count 	= $settingsObj->setAnswerVotes($answerId);
						echo wp_json_encode($response);
						exit;
					}
				break;
				case 'vote-down':
					$response = new stdClass;
					if(!is_user_logged_in()){
						$response->type = 'error';
						$response->message = 'Please login.';
						echo wp_json_encode($response);
						exit;
					}
					
					//check if already voted
					$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_qbot_votes WHERE wp_users_id='.$user_id.' AND qbot_answer_id='.$answerId.'', 'OBJECT');
					if(!empty($res)){
						//if already voted check is up vote
						if($res->is_up_vote==1){
							//if already up voted
							$sql = "DELETE FROM mcl_qbot_votes WHERE (wp_users_id=%d AND qbot_answer_id=%d)";
							$wpdb->query($wpdb->prepare($sql,array($user_id,$answerId)));
							$wpdb->insert('mcl_qbot_votes', array(
								'ip_add' 			=> $ip,
								'wp_users_id' 		=> $user_id,
								'qbot_question_id'	=> $questionId,
								'qbot_answer_id'	=> $answerId,
								'created_at'		=> current_time('mysql'),
								'is_down_vote'		=> 1,
							), array('%s','%d','%d','%s','%d'));
							$response->type 		= 'success';
							$response->message 		= 'Voted successfully.';
							$response->vote_count 	= $settingsObj->setAnswerVotes($answerId);
							echo wp_json_encode($response);
							exit;
						} else{
							// if already down voted
							$response->type 	= 'error';
							$response->message 	= 'You already down voted this answer.';
							$response->vote_count 	= $settingsObj->setAnswerVotes($answerId);
							echo wp_json_encode($response);
							exit;
						}
					} else{
						$wpdb->insert('mcl_qbot_votes', array(
							'ip_add' 			=> $ip,
							'wp_users_id' 		=> $user_id,
							'qbot_question_id'	=> $questionId,
							'qbot_answer_id'	=> $answerId,
							'created_at'		=> current_time('mysql'),
							'is_down_vote'		=> 1,
						), array('%s','%d','%d','%s','%d'));
						$response->type 		= 'success';
						$response->message 		= 'Voted successfully.';
						$response->vote_count	= $settingsObj->setAnswerVotes($answerId);
						echo wp_json_encode($response);
						exit;
					}
				break;
			}
		}
		
		if(isset($_POST['action']) && ($_POST['action']=='mark-question-favorite')){
			global $wpdb;
			$questionId = (int)($_POST['questionId']);
			$user_id = get_current_user_id();
			
			$response = new stdClass;
			if(!is_user_logged_in()){
				$response->type = 'error';
				$response->message = 'Please login.';
				echo wp_json_encode($response);
				exit;
			}
			
			//check if already voted
			$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_favorite_questions WHERE wp_users_id='.$user_id.' AND qbot_questions_id='.$questionId.'', 'OBJECT');
			if($res->counts>0){
				$wpdb->query('DELETE FROM mcl_qbot_favorite_questions WHERE wp_users_id='.$user_id.' AND qbot_questions_id='.$questionId.'');
				$response->type = 'success';
				$response->message = 'Your favorite question removed successfully.';
				$response->actionPerformed = "removed";
				$response->fav_count = $settingsObj->setQuestionBookmarkCount($questionId);
			}
					
			else{
				$wpdb->insert('mcl_qbot_favorite_questions', array(
					'qbot_questions_id' => $questionId,
					'wp_users_id' 		 => $user_id,
					'created_at'		 => current_time('mysql'),
				), array('%d','%d','%s'));
				$response->type = 'success';
				$response->message = 'Marked as favorite successfully.';
				$response->actionPerformed = "added";
				$response->fav_count = $settingsObj->setQuestionBookmarkCount($questionId);
			}
			echo wp_json_encode($response);
			exit;
		}
		
		if(isset($_POST['action']) && ($_POST['action']=='mark-question-resolved')){
			global $wpdb;
			$questionId = (int)($_POST['questionId']);
			$answerId 	= (int)($_POST['answerId']);
			$user_id 	= get_current_user_id();
			
			$response = new stdClass;
			if(!is_user_logged_in()){
				$response->type = 'error';
				$response->message = 'Please login.';
				echo wp_json_encode($response);
				exit;
			}			
			//check if already voted
			$resolved = get_post_meta($questionId, 'qbot_question_resolved', true);
			if($resolved != ''){
				$response->type 	= 'error';
				$response->message 	= 'This question already marked as resolved.';
			} else{
				update_post_meta($questionId, 'qbot_question_resolved', $answerId);
				$response->type 	= 'success';
				$response->message 	= 'Marked as resolved successfully.';
			}
			echo wp_json_encode($response);
			exit;
		}

		$questinSupport = array('title','editor');
		
		/* Register questions post type */
		$question_labels = array(
			'name' =>'Questions',
			'singular_name' => 'Question',
			'add_new' => 'Add new',
			'add_new_item' =>'Add new question',
			'edit_item' =>'Edit question',
			'new_item' => 'New question',
			'all_items' => 'Manage Questions',
			'view_item' => 'View question',
			'search_items' => 'Search question',
			'not_found' => 'No questions found',
			'not_found_in_trash' => 'No questions found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Questions',
		);
		
		
		$question_args = array(
			'labels' => $question_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus'=>true,
			'show_in_menu' => 'qbot-plugin', 
			'query_var' => true,
			'rewrite' => array(
				'slug' => $settings['stored']['permalink_question'],
				'with_front'    => false,
			),
			'has_archive' => true, 
			'hierarchical' => true,
			'menu_icon' => '',
			'supports' => $questinSupport
		); 
		register_post_type( 'qbot-question', $question_args );
		
		
		
		/* Register Answers post type */
		$answer_labels = array(
			'name' =>'Answers',
			'singular_name' => 'Answer',
			'add_new' => 'Add new',
			'add_new_item' =>'Add new answer',
			'edit_item' =>'Edit answer',
			'new_item' => 'New answer',
			'all_items' => 'Manage Answers',
			'view_item' => 'View answer',
			'search_items' => 'Search answer',
			'not_found' => 'No answer found',
			'not_found_in_trash' => 'No answer found in Trash', 
			'parent_item_colon' => '',
			'menu_name' => 'Answers',
		);
		

		
		if((isset($_GET['post_type']) && ($_GET['post_type']=='qbot-answer')) && (!isset($_GET['parent_id']))){
			$answerCap = array('create_posts' => false);
		} else{
			//if opened in edit mode
			if(isset($_GET['post']) && is_numeric($_GET['post'])){
				
				$answerCap = array();
			} else{
				$answerCap = array();
			}
		}
		
		
		
		$answer_args = array(
			'labels' => $answer_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true, 
			'show_in_menu' => 'qbot-plugin', 
			'query_var' => true,
			'rewrite' => true,
			'has_archive' => false, 
			'hierarchical' => false,
			'menu_icon' => '',
			'supports' => array('editor'),
			'capabilities' => $answerCap,
			'map_meta_cap' => true,
		); 
		register_post_type( 'qbot-answer', $answer_args );
		
		qbot_update_rewrite_rules();
	}
	
	add_action( 'init', 'initQBotPlugin', 0 );	
	
	add_filter( 'page_template', 'qbot_wpa3396_page_template' );
	function qbot_wpa3396_page_template( $page_template ){
		global $page,$post;
		if(isset($post) && isset($post->post_content)){
			if(strpos($post->post_content, '[qbot-frontend]' ) !== false){
				$page_template = plugin_dir_path( __FILE__ ) . 'views/frontend_template.php';
				return $page_template;
			}
		}
		return $page_template;
	}
	
	function qbot_avatar($userId,$size) {
		$avatar = "";
		$userId = (int)($userId);
		$userData =  get_userdata($userId);
		$alt = ucfirst($userData->data->display_name);

		$loginType = get_user_meta($userId, '_qbot_user_lastlogin_type', true);
		
		//Show user avatar in admin according to social medta filter
		if(isset($_GET['filter-user']) && ($_GET['filter-user']=='facebook')){
			$loginType = 'facebook';
		}
		if(isset($_GET['filter-user']) && ($_GET['filter-user']=='google')){
			$loginType = 'google';
		}
		

		
		switch($loginType){
			case 'facebook':
				$fbId = get_user_meta($userId, '_qbot_user_fb_id', true);
				$avatar = 'http://graph.facebook.com/'.$fbId.'/picture?width='.($size+30).'&height='.($size+30).'';
				$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			break;
			case 'google':
				$googlePic = get_user_meta($userId, '_qbot_user_google_pic_url', true);
				$avatar = $googlePic;
				$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			break;
		}
		
		if($avatar==""){
			$avatar = get_avatar($userId,$size);
		}
		return $avatar;
		
	}
	

	function qbot_prefix_register_query_var( $vars ) {
		$vars[] = 'qid';
		return $vars;
	}
	add_filter( 'query_vars', 'qbot_prefix_register_query_var' );
	
		
	function safely_add_stylesheet(){
		wp_enqueue_style('qbot-style',QBOT_PLUGIN_CSS_URL.'/style.css',array(),'1.0');	
	}
	add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );	

	function qbot_get_header(){
		global $wpdb, $post;
		$qid = get_query_var('qid');
		$qid = $qid;

		if(isset($post->post_parent) && ($post->post_parent>0) && ($post->post_type=='qbot-answer')){
			wp_redirect(get_the_permalink($post->post_parent));
			exit;
		}
		
		
		/* If we are on question view page load post variable*/
		if($qid!==""){
		
			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'qbot-question');
				if($post!=''){
				$qid = $post->ID;
				}
			} else{
				$post = get_post($qid);
			}
			if($post!='' && $post->post_type='qbot-question'){
				setup_postdata( $GLOBALS['post'] =& $post );
				$title = $post->post_title.' ';
			}
			
			//set queried object id 
			global $wp_query;
			$wp_query->queried_object_id = $qid;
			
			/*Remove All in one seo plugin footprint*/
			remove_action('wp_head','jetpack_og_tags'); // JetPack
			if (defined('WPSEO_VERSION')) { // Yoast SEO
				global $wpseo_front;
				remove_action('wp_head',array($wpseo_front,'head'),1);
				add_filter( 'wpseo_canonical', 'disable_qbot_stuff' );
			}
			
			if (defined('AIOSEOP_VERSION')) { // All-In-One SEO
				global $aiosp;
				remove_action('wp_head',array($aiosp,'wp_head'));
				add_filter( 'aioseop_canonical_url', 'disable_qbot_stuff' );
				add_filter('aioseop_title_page','disable_qbot_stuff');				
			}
			
			remove_action('wp_head','rel_canonical');
			remove_action('wp_head','index_rel_link');
			remove_action('wp_head','start_post_rel_link');
			remove_action('wp_head','adjacent_posts_rel_link_wp_head');
			
			remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
			remove_action( 'wp_head', 'feed_links', 2 ); // Display the links to the general feeds: Post and Comment Feed
			remove_action( 'wp_head', 'rsd_link' ); // Display the link to the Really Simple Discovery service endpoint, EditURI link
			remove_action( 'wp_head', 'wlwmanifest_link' ); // Display the link to the Windows Live Writer manifest file.
			remove_action( 'wp_head', 'index_rel_link' ); // index link
			remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // prev link
			remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
			remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 ); // Display relational links for the posts adjacent to the current post.
			remove_action( 'wp_head', 'wp_generator' ); // Display the XHTML generator that is generated on the wp_head hook, WP version
			/*Remove All in one seo plugin footprint*/	
			
		}		
		
	}
	
	add_action( 'get_header', 'qbot_get_header' );	
	
	function qbot_wp_title($title, $sep)
	{
		global $wpdb, $post;
		$qid = get_query_var('qid');

		if(isset($post->post_parent) && ($post->post_parent>0) && ($post->post_type=='qbot-answer')){
			wp_redirect(get_the_permalink($post->post_parent));
			exit;
		}
		
		/* If we are on question view page load post variable*/
		if($qid!==""){
			
			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'qbot-question');
				if($post!=''){
				$qid = $post->ID;
				}
			} else{
				$post = get_post($qid);
			}
			if($post!='' && $post->post_type='qbot-question'){
				setup_postdata( $GLOBALS['post'] =& $post );
				$title = $post->post_title.' ';
			}
		}
		return $title;
	}
	
	add_filter( 'wp_title', 'qbot_wp_title', 10,3);

	function qbot_text_after_title( $post ) 
	{
		if($post->post_type=='qbot-answer')
		{
			//Remove media button
			add_action('admin_head','z_remove_media_controls');
 
			//in case of edit answer
			if(isset($_GET['post']) && is_numeric($_GET['post'])){
				$entry = get_post($_GET['post']);
				$_GET['parent_id'] = $entry->post_parent;
			}
			
			if(isset($_GET['parent_id']) && is_numeric($_GET['parent_id']) && (($_GET['parent_id'])>0)){
				echo '<div class="after-title-help postbox">';
				$entry = get_post($_GET['parent_id']);
				echo "<input type=\"hidden\" name=\"parent_id\" value=\"".$entry->ID."\" />";
				echo "<input type=\"hidden\" name=\"post_title\" value=\"QBOT Answer\" />";
				echo "<h3>Question: ".$entry->post_title."</h3>";
				echo '<div class="inside">';
				echo "<p>".$entry->post_content."</p>";
				$user_id = $entry->post_author;
				if($user_id>0){
					$userData =  get_userdata($user_id);
					echo "<div style=\"float:left; margin-right:10px;\">";
						echo qbot_avatar($user_id, 32 );
					echo "</div>";
						echo "Question posted by ".ucfirst($userData->data->display_name)."<br />";
						echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
				} else{
					echo "n/a";
				}
				echo "</div>";
				echo "</div>";
			}
		}

	}
	add_action( 'edit_form_after_title', 'qbot_text_after_title' );	
	
	
	// ADD NEW COLUMN 
	function qbot_array_insert( &$array, $element, $position = null ) {
	
		if ( is_array( $element ) ) {
			$part = $element;
		} else {
			$part = array( $position => $element );
		}

		$len = count( $array );

		$firsthalf = array_slice( $array, 0, $len / 2 );
		$secondhalf = array_slice( $array, $len / 2 );

		$array = array_merge( $firsthalf, $part, $secondhalf );
		
		
		return $array;
	}
	
	function qbot_columns_head( $defaults ) { 
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'qbot-answer' ) {
			global $submenu;
			$defaults = array(
				'cb'            => '<input type="checkbox">',
				'answer'          => 'Answer',
				'date'			=> 'Date',
				'answerauthor'        => 'Author',
				'in-response-to' => 'In Response to question',
			);
		}
		if ( $_GET['post_type'] == 'qbot-question' ) {
			$defaults['questionauthor'] =  'Author';
			$defaults['stat'] =  'Stat';
			$defaults['action'] =  'Add answer';
		}
		return $defaults;  
	} 
	add_filter( 'manage_posts_columns', 'qbot_columns_head' ); 
	
function qbot_question_columns_content( $column_name, $post_ID ) {  
	switch ( $column_name ) {
		case 'questionauthor':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$user_id = $entry->post_author;
			if($user_id>0){
				$userData =  get_userdata($user_id);
				echo "<div style=\"float:left; margin-right:10px;\">";
					echo qbot_avatar($user_id, 32 );
				echo "</div>";
					echo ucfirst($userData->data->display_name)."<br />";
					echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
			} else{
				echo "n/a";
			}		
			break;
		case 'stat':
			$settings = new qbotSettings();
			$avg = get_post_meta(get_the_ID(), 'qbot_answers_count', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Answers <br />";
			$avg = get_post_meta(get_the_ID(), 'qbot_question_vote_count', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Votes <br />";
			$count = get_post_meta($post_ID, 'qbot_views_count', true);
			echo ($count != '' ) ? $count : "0";
			echo ' Views <br />';
			$avg = get_post_meta(get_the_ID(), 'qbot_question_favorite', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Favorite <br />";
			break;
		case 'action':
			echo '<br /><a style="margin-left:30px;" title="Add answer to this question" href="post-new.php?post_type=qbot-answer&parent_id='.$post_ID.'"><img src="'.QBOT_PLUGIN_IMAGES_URL.'/create.png" /></a><br>';
			break;
	}
} 


add_action( 'manage_qbot-question_posts_custom_column', 'qbot_question_columns_content', 10, 2 );  
		
function qbot_answer_columns_content( $column_name, $post_ID ) {  
	switch ( $column_name ) {
		case 'answerauthor':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$user_id = $entry->post_author;
			if($user_id>0){
				$userData =  get_userdata($user_id);
				echo "<div style=\"float:left; margin-right:10px;\">";
					echo qbot_avatar($user_id, 32 );
				echo "</div>";
					echo ucfirst($userData->data->display_name)."<br />";
					echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
			} else{
				echo "n/a";
			}		
		break;	
		case 'answer':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$content = $entry->post_content;
			$content = apply_filters('the_content', $content);
			$content = str_replace(']]>', ']]&gt;', $content);			
			$html = substr($content,0,150);
			if($entry->post_status!='trash'){
				$html .= '<div class="row-actions" style="visibility:visible">
				<span class="edit">
				<a title="Edit this item" href="'.get_edit_post_link($post_ID).'">Edit</a> | </span>
				<span class="trash"><a href="'.get_delete_post_link($post_ID).'" title="Move this item to the Trash" class="submitdelete">Trash</a> | </span>
				</div>';
			}
			echo $html;
			break;
		case 'in-response-to':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$entry = get_post($entry->post_parent);
			$html = "<a href=\"".get_permalink($entry->ID)."\">".$entry->post_title."</a>";
			echo $html;
		break;		
	}
} 
add_action( 'manage_qbot-answer_posts_custom_column', 'qbot_answer_columns_content', 10, 2 ); 
	
	//save post callback
	function qbot_after_save_post($post_id) {
		$post_id = (int)($post_id);
		$post_type = get_post_type($post_id);
		if($post_type=='qbot-question'){
			//Set views
			$count_key = 'qbot_views_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}
			
			//Set question vote count (thumb up of all answer votes)
			$count_key = 'qbot_question_vote_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}		
			
			//Set favorite (bookmarked)
			$count_key = 'qbot_question_favorite';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}	

			//Set answers count in this questions
			$count_key = 'qbot_answers_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}
		}
		
		// if answer posted
		if($post_type=='qbot-answer'){
			//Set answers count in this questions
			$parent_id = wp_get_post_parent_id($post_id);
			global $wpdb;
			$sql = "SELECT COUNT(*) as counts FROM {$wpdb->posts} AS P WHERE P.post_parent = ".$parent_id." AND P.post_type = 'qbot-answer' AND P.post_status = 'publish'";
			$obj = $wpdb->get_row($sql,OBJECT);
			$counts = $obj->counts;
			$count_key = 'qbot_answers_count';
			$count = get_post_meta($parent_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($parent_id, $count_key);
				add_post_meta($parent_id, $count_key, $counts);
			} else{
				update_post_meta($parent_id, $count_key, $counts);
			}
			
			//When answer saved, set 0, if no vote counter set
			$settingsObj = new qbotSettings();
			$count_key = 'qbot_answer_vote_count';
			$count = $settingsObj->setAnswerVotes($post_id); //get count of votest
			if($count==''){
				$count = 0;
				delete_post_meta($parent_id, $count_key);
				add_post_meta($parent_id, $count_key, $counts);
			}
			
		}
		
	}
	add_action( 'save_post', 'qbot_after_save_post' );
	
	
	function qbot_post_updated($post_ID, $post_after, $post_before){
		if($post_after->post_type=='qbot-answer'){
			//When answer saved, set 0, if no vote counter set
			$settingsObj = new qbotSettings();
			$count_key = 'qbot_answer_vote_count';
			$count = $settingsObj->setAnswerVotes($post_after->ID); //get count of votest
		}
	}
	add_action( 'post_updated', 'qbot_post_updated', 10, 3 );
	
	
	function qbot_after_delete_post( $pid ) {
		$post_type = get_post_type($pid);
		if(($post_type=='qbot-question') || ($post_type=='qbot-answer')){
			global $wpdb;
			
			//If deleteing question remove all answers
			if($post_type=='qbot-question'){
				$args = array( 
					'post_parent' => $pid,
					'post_type' => 'qbot-answer'
				);
				$posts = get_posts($args);
				if (is_array($posts) && count($posts) > 0) {
					foreach($posts as $post){
						wp_delete_post($post->ID, true);
					}
				}
			}
			
			
			//Delete fav
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_qbot_favorite_questions WHERE qbot_questions_id = %d', $pid ) );
			//Delete spam
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_qbot_spam WHERE obj_id = %d', $pid ) );
			//Delete views
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_qbot_views WHERE question_id = %d', $pid ) );
			//Delete vote
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_qbot_votes WHERE qbot_question_id = %d', $pid ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_qbot_votes WHERE qbot_answer_id = %d', $pid ) );
			
			//if answer deleted reset , up votes 
			if($post_type=='qbot-answer'){
				$settingsObj = new qbotSettings();
				$settingsObj->setAnswerVotes($pid);
			}
			
		}
		
	}
	
	add_action( 'admin_init', 'qbot_delete_init' );
	function qbot_delete_init() {
		if ( current_user_can( 'delete_posts' ) )
		add_action( 'delete_post', 'qbot_after_delete_post', 10 );	
	}

	add_action( 'admin_head', 'showhiddencustomfields' );

	function showhiddencustomfields() {
		global $post;
		if(isset($post) && (($post->post_type=='qbot-question') || ($post->post_type=='qbot-answer')))
		{
			?>
			<script>
			jQuery(document).ready(function(){
				 jQuery(".wp-editor-tabs #content-tmce").trigger("click");
			});
			</script>
			<?php
			echo "<link rel='stylesheet' media='screen,projection' type='text/css' href='" . QBOT_PLUGIN_CSS_URL . "dashboard.css'>";
		}	
	}
	
	//Rewrite rule 
	function qbot_update_rewrite_rules() {
		global $wpdb;
		$settingsObj = new qbotSettings();
		$settings = $settingsObj->settings;
	
		$var1 = '^'.$settings['stored']['permalink_question'].'/([^/]+)?';
		$var2 = 'index.php?page_id='.$settings['stored']['plugin_page_id'].'&qid=$matches[1]';
		add_rewrite_rule($var1,$var2,'top');
		
		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);							
	}
	
	//settings function
	function qbot_setting_save($option_name,$new_value){
		$option_name = 'qbot_'.$option_name;
		if ( get_option( $option_name ) !== false ) {
			update_option( $option_name, $new_value );
		} else{
			$deprecated = null;
			$autoload = 'yes';
			add_option( $option_name, $new_value, $deprecated, $autoload );		
		}
	}
	function qbot_setting_get($option_name){
		$option_name = 'qbot_'.$option_name;
		return get_option( $option_name );
	}
	
	function qbot_setting_get_all($default='no'){
		$settings = array(
			'question_style'				=> '0',
			'font_color'					=> '',
			'line_height_size'				=> '',
			'font_size'						=> '',
			'font_italic'					=> '0',
			'font_bold'						=> '0',
			'font'							=> '',
			
			'notification_new_answer'		=> '<p>Hey Admin,</p>
			<p>New answer created on your question.</p>
			<p>Regards,</p><p>QBOT Team</p>',
			'notification_new_question'		=> '<p>Hey Admin,</p>
<p>The user {question_author} has posted this question on {question_title_url}</p>
<p>You might wanna check it out.</p>
<p>Regards,</p>
<p>QBOT Team</p>',
			'notify_new_question'			=> '0',
			'notify_user'					=> '0',
			
			'permalink_question'			=> 'question',
			
			'social_locker'					=> '0',
			
			'facebook_app_id'				=> '',
			'facebook_app_secret'			=> '',
			'google_app_id'					=> '',
			'google_app_secret'				=> '',
			
			'auto_approve_new_answers'		=> '1',
			'auto_approve_new_questions'	=> '1',
			'disble_negative_rating'		=> '0',
			'plugin_page_id'				=> '',
			'login_and_registeration'		=> '1',
		);
		
		//if settings not default pick stored settings
		if($default=='no'){
			foreach($settings as $key=>$val){
				$val = qbot_setting_get($key);
				if($val!=""){
					$settings[$key] = $val;
				}
			}
		}
		return $settings;
	}
	
	# NEW CODES 22 JULY 2015 EVENING
	function qbot_add_qbot_user_role() {
		add_role('qbot_qbot_user',
			'QBOT User',
			array(
				'read' => true,
				'edit_posts' => false,
				'delete_posts' => false,
				'publish_posts' => false,
				'upload_files' => true,
			)
		);
	}
	
	add_filter('pre_option_default_role', function($default_role){
		return 'qbot_qbot_user'; // This is changed
	});
	
	function disable_qbot_stuff($data) {
		return false;
	}
	
	function qbot_set_meta_tags(){
		if(is_page()) {
			global $page;
			$description = '';
			echo "\n";
			echo '<!-- QBOT plugin meta -->' . "\n";
			$recentpost =  get_page($page->ID);
			if($recentpost->post_type=='qbot-question')
			{
				echo '<meta name="title" content="'.strip_tags($recentpost->post_title).'" />' . "\n";
				$content = $recentpost->post_content;
				$acontent = strip_tags($content);
				$content = str_replace("\r", ' ', $acontent);
				$content = str_replace("\n", ' ', $content);
				$content = str_replace("\t", '', $content);
				$description = $content;
				$description = substr($description,0,150);
				echo '<meta name="description" content="'. $description .'" />' . "\n";
				echo '<link rel="canonical" href="'.get_the_permalink().'" />';
			}
		} //is page end
	}
	add_action('wp_head','qbot_set_meta_tags', 1);	
?>