<?php
	ob_start();
    /*
    Plugin Name: PlatformPress
    Plugin URI: https://github.com/cyberhiker/platformpress
    Description: Plugin to create and manage a political platform. 
    Author: Chris Burton
    Version: 0.1-alpha
    */
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_URL' ) )
    define( 'PLATFORMPRESS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_PATH' ) )
    define( 'PLATFORMPRESS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_IMAGES_URL' ) )
    define( 'PLATFORMPRESS_PLUGIN_IMAGES_URL', PLATFORMPRESS_PLUGIN_URL . 'images/' );
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_CSS_URL' ) )
    define( 'PLATFORMPRESS_PLUGIN_CSS_URL', PLATFORMPRESS_PLUGIN_URL . 'css/' );
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_INCLUDE_PATH' ) )
    define( 'PLATFORMPRESS_PLUGIN_INCLUDE_PATH', PLATFORMPRESS_PLUGIN_PATH . 'includes/' );
	if ( ! defined( 'PLATFORMPRESS_PLUGIN_VIEW_PATH' ) )
    define( 'PLATFORMPRESS_PLUGIN_VIEW_PATH', PLATFORMPRESS_PLUGIN_PATH . 'views/' );

	if ( ! defined( 'PLATFORMPRESS_PLUGIN_DOWNLOAD_SITE' ) )
    define( 'PLATFORMPRESS_PLUGIN_DOWNLOAD_SITE', 'https://github.com/cyberhiker/platformpress/' );

	require_once PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'platformpress-installer.php';
	register_activation_hook(__FILE__,'platformpress_plugin_install');

	require_once PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'class-platformpress-settings.php';
	require_once PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'class-platformpress.php';

	/**** SHORT CODES *********/
	#[platformpress-frontend]
	function platformpress_frontend(){
		//platformpress_plugin_install();
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-frontend.php';
		$obj = new platformpressFrontend();
		$obj->run();
	}

	/* Functions */
	function platformpressManager() {
		$obj = new platformpress_manager();
		$obj->run();
	}

	add_action('admin_menu', 'platformpress_admin_menu');

	function platformpress_admin_menu(){

		$icon_url = PLATFORMPRESS_PLUGIN_IMAGES_URL.'/geekheroicons/small-geek.png';
		add_menu_page('PlatformPress', __('PLATFORMPRESS'), 'manage_options', 'platformpress-plugin','platformpressAdmin',$icon_url);

		$icon_url = PLATFORMPRESS_PLUGIN_IMAGES_URL.'/geekheroicons/small-geek.png';

		add_submenu_page('platformpress-plugin', __('Settings'),  __('Settings'), 'manage_options', 'platformpress-plugin-settings', 'platformpress_menu_manageSettings');
        add_submenu_page('platformpress-plugin', __('Categories'),  __('Categories'), 'manage_options', 'platformpress-plugin-categories', 'platformpress_menu_manageCategories');
        add_submenu_page('platformpress-plugin', __('QA SEO'), 'Admin Dashboard', 'manage_options', 'platformpress-plugin');


	}

	add_filter( 'custom_menu_order', 'wpplatformpress_5911_submenu_order' );
	function wpplatformpress_5911_submenu_order( $menu_ord )
	{
		global $submenu;

		$submenuNewOrder = array();

		$submenuNewOrder['platformpress-plugin'][] = $submenu['platformpress-plugin'][3];
		$submenuNewOrder['platformpress-plugin'][] = $submenu['platformpress-plugin'][0];
		$submenuNewOrder['platformpress-plugin'][] = $submenu['platformpress-plugin'][1];
		$submenuNewOrder['platformpress-plugin'][] = $submenu['platformpress-plugin'][2];

		$submenu = array_merge($submenu,$submenuNewOrder);
		return $submenu;
	}

	function platformpressAdmin() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-admin.php';
		$qa = new platformpressAdmin();
		$qa->loadStyle();
		$qa->run();
	}

	function platformpress_menu_managePlanks(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-admin-planks.php';
		$qa = new platformpressAdminPlanks();
		$qa->loadStyle();
		$qa->run();

	}

	function platformpress_menu_manageRemarks(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-admin-remarks.php';
		$qa = new platformpressAdminRemarks();
		$qa->loadStyle();
		$qa->run();
	}

	function platformpress_menu_manageSettings(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-admin-settings.php';
		$qa = new platformpressAdminSettings();
		$qa->loadStyle();
		$qa->run();
	}

    function platformpress_menu_manageCategories(){
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-platformpress-admin-categories.php';
		$qa = new platformpressAdminCategories();
		$qa->loadStyle();
		$qa->run();
	}

	function platformpress_flash_get(){
		global $errors;
		if(isset($errors->errors))
		{
			if(isset($errors->errors['platformpress_flash_success']))
			{
				$key = 'platformpress_flash_success';
				$html = "<div id='close' class=\"platformpress-alert ".$key."\">";
				foreach($errors->errors['platformpress_flash_success'] as $key=>$val){
					$html .= $val."<br />";
				}
				$html .= "</div>";
				echo $html;
			}
			if(isset($errors->errors['platformpress_flash_error']))
			{
				$key = 'platformpress_flash_error';
				$html = "<div class=\"platformpress-alert ".$key."\">";
				foreach($errors->errors['platformpress_flash_error'] as $key=>$val){
					$html .= $val."<br />";
				}
				$html .= "</div>";
				echo $html;
			}
		}
	}

	function platformpress_flash_set($type,$message){
		$key = 'platformpress_flash_'.$type;
		global $errors;
		$errors = new WP_Error();
		$errors->add($key,$message);
	}

	add_shortcode('platformpress-frontend', 'platformpress_frontend');
	add_shortcode('platformpress-frontend-latest-planks', 'platformpress_frontend_latest_planks');
	add_shortcode('platformpress-frontend-latest-remarks', 'platformpress_frontend_latest_remarks');

	function platformpress_editor( $args = array() ) {

		$media_buttons = false;
		$drag_drop_upload = false;

		if(isset($args['media_buttons']) && ($args['media_buttons']==true)){
			$media_buttons = true;
			$drag_drop_upload = true;
		}

		extract( wp_parse_args( $args, array(
				'content'       => '',
				'id'            => 'qaeo-custom-content-editor',
				'textarea_name' => 'platformpress_textarea',
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
		if(isset($_GET['platformpress-plank'])){
			set_query_var('qid',$_GET['platformpress-plank']);
			$header = get_header();
			$template = $header.platformpress_frontend().get_footer();
		}
		return $template;
	}

	function initQBotPlugin() {
		global $wpdb;
		$settingsObj = new platformpressSettings();

		$settings = $settingsObj->settings;

		if(isset($_POST['action']) && (($_POST['action']=='vote-up') || ($_POST['action']=='vote-down'))){
			$remarkId = (int)($_POST['remarkId']);
			$user_id = get_current_user_id();
			$ip = $_SERVER['REMOTE_ADDR'];

			$plankId = wp_get_post_parent_id($remarkId);

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
					$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_platformpress_votes WHERE wp_users_id='.$user_id.' AND platformpress_remark_id='.$remarkId.'', 'OBJECT');
					if(!empty($res)){
						//if already voted check is up vote
						if($res->is_up_vote==1){
							$response->type = 'error';
							$response->message = 'You already up voted this remark.';
							$response->vote_count = $settingsObj->setRemarkVotes($remarkId);
							echo wp_json_encode($response);
							exit;
						} else{
							$sql = "DELETE FROM mcl_platformpress_votes WHERE (wp_users_id=%d AND platformpress_remark_id=%d)";
							$wpdb->query($wpdb->prepare($sql,array($user_id,$remarkId)));
							$wpdb->insert('mcl_platformpress_votes', array(
								'ip_add' 			=> $ip,
								'wp_users_id' 		=> $user_id,
								'platformpress_plank_id'	=> $plankId,
								'platformpress_remark_id'	=> $remarkId,
								'created_at'		=> current_time('mysql'),
								'is_up_vote'		=> 1,
							), array('%s','%d','%d','%s','%d'));
							$response->type = 'success';
							$response->message = 'Voted successfully.';
							$response->vote_count = $settingsObj->setRemarkVotes($remarkId);
							echo wp_json_encode($response);
							exit;
						}
					} else{
						$wpdb->insert('mcl_platformpress_votes', array(
							'ip_add' 			=> $ip,
							'wp_users_id' 		=> $user_id,
							'platformpress_plank_id'	=> $plankId,
							'platformpress_remark_id'	=> $remarkId,
							'created_at'		=> current_time('mysql'),
							'is_up_vote'		=> 1,
						), array('%s','%d','%d','%s','%d'));
						$response->type 		= 'success';
						$response->message 		= 'Voted successfully.';
						$response->vote_count 	= $settingsObj->setRemarkVotes($remarkId);
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
					$res = $wpdb->get_row('SELECT is_up_vote,is_down_vote FROM mcl_platformpress_votes WHERE wp_users_id='.$user_id.' AND platformpress_remark_id='.$remarkId.'', 'OBJECT');
					if(!empty($res)){
						//if already voted check is up vote
						if($res->is_up_vote==1){
							//if already up voted
							$sql = "DELETE FROM mcl_platformpress_votes WHERE (wp_users_id=%d AND platformpress_remark_id=%d)";
							$wpdb->query($wpdb->prepare($sql,array($user_id,$remarkId)));
							$wpdb->insert('mcl_platformpress_votes', array(
								'ip_add' 			=> $ip,
								'wp_users_id' 		=> $user_id,
								'platformpress_plank_id'	=> $plankId,
								'platformpress_remark_id'	=> $remarkId,
								'created_at'		=> current_time('mysql'),
								'is_down_vote'		=> 1,
							), array('%s','%d','%d','%s','%d'));
							$response->type 		= 'success';
							$response->message 		= 'Voted successfully.';
							$response->vote_count 	= $settingsObj->setRemarkVotes($remarkId);
							echo wp_json_encode($response);
							exit;
						} else{
							// if already down voted
							$response->type 	= 'error';
							$response->message 	= 'You already down voted this remark.';
							$response->vote_count 	= $settingsObj->setRemarkVotes($remarkId);
							echo wp_json_encode($response);
							exit;
						}
					} else{
						$wpdb->insert('mcl_platformpress_votes', array(
							'ip_add' 			=> $ip,
							'wp_users_id' 		=> $user_id,
							'platformpress_plank_id'	=> $plankId,
							'platformpress_remark_id'	=> $remarkId,
							'created_at'		=> current_time('mysql'),
							'is_down_vote'		=> 1,
						), array('%s','%d','%d','%s','%d'));
						$response->type 		= 'success';
						$response->message 		= 'Voted successfully.';
						$response->vote_count	= $settingsObj->setRemarkVotes($remarkId);
						echo wp_json_encode($response);
						exit;
					}
				break;
			}
		}

		if(isset($_POST['action']) && ($_POST['action']=='mark-plank-favorite')){
			global $wpdb;
			$plankId = (int)($_POST['plankId']);
			$user_id = get_current_user_id();

			$response = new stdClass;
			if(!is_user_logged_in()){
				$response->type = 'error';
				$response->message = 'Please login.';
				echo wp_json_encode($response);
				exit;
			}

			//check if already voted
			$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_platformpress_favorite_planks WHERE wp_users_id='.$user_id.' AND platformpress_planks_id='.$plankId.'', 'OBJECT');
			if($res->counts>0){
				$wpdb->query('DELETE FROM mcl_platformpress_favorite_planks WHERE wp_users_id='.$user_id.' AND platformpress_planks_id='.$plankId.'');
				$response->type = 'success';
				$response->message = 'Your favorite plank removed successfully.';
				$response->actionPerformed = "removed";
				$response->fav_count = $settingsObj->setPlankBookmarkCount($plankId);
			}

			else{
				$wpdb->insert('mcl_platformpress_favorite_planks', array(
					'platformpress_planks_id' => $plankId,
					'wp_users_id' 		 => $user_id,
					'created_at'		 => current_time('mysql'),
				), array('%d','%d','%s'));
				$response->type = 'success';
				$response->message = 'Marked as favorite successfully.';
				$response->actionPerformed = "added";
				$response->fav_count = $settingsObj->setPlankBookmarkCount($plankId);
			}
			echo wp_json_encode($response);
			exit;
		}

		if(isset($_POST['action']) && ($_POST['action']=='mark-plank-resolved')){
			global $wpdb;
			$plankId = (int)($_POST['plankId']);
			$remarkId 	= (int)($_POST['remarkId']);
			$user_id 	= get_current_user_id();

			$response = new stdClass;
			if(!is_user_logged_in()){
				$response->type = 'error';
				$response->message = 'Please login.';
				echo wp_json_encode($response);
				exit;
			}
			//check if already voted
			$resolved = get_post_meta($plankId, 'platformpress_plank_resolved', true);
			if($resolved != ''){
				$response->type 	= 'error';
				$response->message 	= 'This plank already marked as resolved.';
			} else{
				update_post_meta($plankId, 'platformpress_plank_resolved', $remarkId);
				$response->type 	= 'success';
				$response->message 	= 'Marked as resolved successfully.';
			}
			echo wp_json_encode($response);
			exit;
		}

		$questinSupport = array('title','editor');

		/* Register planks post type */
		$plank_labels = array(
			'name' =>'Planks',
			'singular_name' => 'Plank',
			'add_new' => 'Add new',
			'add_new_item' =>'Add new plank',
			'edit_item' =>'Edit plank',
			'new_item' => 'New plank',
			'all_items' => 'Manage Planks',
			'view_item' => 'View plank',
			'search_items' => 'Search Planks',
			'not_found' => 'No planks found',
			'not_found_in_trash' => 'No planks found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Planks',
		);


		$plank_args = array(
			'labels' => $plank_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus'=>true,
			'show_in_menu' => 'platformpress-plugin',
			'query_var' => true,
			'rewrite' => array(
				'slug' => $settings['stored']['permalink_plank'],
				'with_front'    => false,
			),
			'has_archive' => true,
			'hierarchical' => true,
			'menu_icon' => '',
			'supports' => $questinSupport
		);
		register_post_type( 'platformpress-plank', $plank_args );



		/* Register Remarks post type */
		$remark_labels = array(
			'name' =>'Remarks',
			'singular_name' => 'Remark',
			'add_new' => 'Add Remarks',
			'add_new_item' =>'Add new remark',
			'edit_item' =>'Edit remark',
			'new_item' => 'New remark',
			'all_items' => 'Manage Remarks',
			'view_item' => 'View remark',
			'search_items' => 'Search Remarks',
			'not_found' => 'No remarks found',
			'not_found_in_trash' => 'No remarks found in Trash',
			'parent_item_colon' => '',
			'menu_name' => 'Remarks',
		);



		if((isset($_GET['post_type']) && ($_GET['post_type']=='platformpress-remark')) && (!isset($_GET['parent_id']))){
			$remarkCap = array('create_posts' => false);
		} else{
			//if opened in edit mode
			if(isset($_GET['post']) && is_numeric($_GET['post'])){

				$remarkCap = array();
			} else{
				$remarkCap = array();
			}
		}



		$remark_args = array(
			'labels' => $remark_labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => 'platformpress-plugin',
			'query_var' => true,
			'rewrite' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'menu_icon' => '',
			'supports' => array('editor'),
			'capabilities' => $remarkCap,
			'map_meta_cap' => true,
		);
		register_post_type( 'platformpress-remark', $remark_args );

		platformpress_update_rewrite_rules();
	}

	add_action( 'init', 'initQBotPlugin', 0 );

	add_filter( 'page_template', 'platformpress_wpa3396_page_template' );
	function platformpress_wpa3396_page_template( $page_template ){
		global $page,$post;
		if(isset($post) && isset($post->post_content)){
			if(strpos($post->post_content, '[platformpress-frontend]' ) !== false){
				$page_template = plugin_dir_path( __FILE__ ) . 'views/frontend_template.php';
				return $page_template;
			}
		}
		return $page_template;
	}

	function platformpress_avatar($userId,$size) {
		$avatar = "";
		$userId = (int)($userId);
		$userData =  get_userdata($userId);
		$alt = ucfirst($userData->data->display_name);

		$loginType = get_user_meta($userId, '_platformpress_user_lastlogin_type', true);

		//Show user avatar in admin according to social medta filter
		if(isset($_GET['filter-user']) && ($_GET['filter-user']=='facebook')){
			$loginType = 'facebook';
		}
		if(isset($_GET['filter-user']) && ($_GET['filter-user']=='google')){
			$loginType = 'google';
		}



		switch($loginType){
			case 'facebook':
				$fbId = get_user_meta($userId, '_platformpress_user_fb_id', true);
				$avatar = 'http://graph.facebook.com/'.$fbId.'/picture?width='.($size+30).'&height='.($size+30).'';
				$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			break;
			case 'google':
				$googlePic = get_user_meta($userId, '_platformpress_user_google_pic_url', true);
				$avatar = $googlePic;
				$avatar = "<img alt='{$alt}' src='{$avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
			break;
		}

		if($avatar==""){
			$avatar = get_avatar($userId,$size);
		}
		return $avatar;

	}


	function platformpress_prefix_register_query_var( $vars ) {
		$vars[] = 'qid';
		return $vars;
	}
	add_filter( 'query_vars', 'platformpress_prefix_register_query_var' );


	function safely_add_stylesheet(){
		wp_enqueue_style('platformpress-style',PLATFORMPRESS_PLUGIN_CSS_URL.'/style.css',array(),'1.0');
	}
	add_action( 'wp_enqueue_scripts', 'safely_add_stylesheet' );

	function platformpress_get_header(){
		global $wpdb, $post;
		$qid = get_query_var('qid');
		$qid = $qid;

		if(isset($post->post_parent) && ($post->post_parent>0) && ($post->post_type=='platformpress-remark')){
			wp_redirect(get_the_permalink($post->post_parent));
			exit;
		}


		/* If we are on plank view page load post variable*/
		if($qid!==""){

			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'platformpress-plank');
				if($post!=''){
				$qid = $post->ID;
				}
			} else{
				$post = get_post($qid);
			}
			if($post!='' && $post->post_type='platformpress-plank'){
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
				add_filter( 'wpseo_canonical', 'disable_platformpress_stuff' );
			}

			if (defined('AIOSEOP_VERSION')) { // All-In-One SEO
				global $aiosp;
				remove_action('wp_head',array($aiosp,'wp_head'));
				add_filter( 'aioseop_canonical_url', 'disable_platformpress_stuff' );
				add_filter('aioseop_title_page','disable_platformpress_stuff');
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

	add_action( 'get_header', 'platformpress_get_header' );

	function platformpress_wp_title($title, $sep)
	{
		global $wpdb, $post;
		$qid = get_query_var('qid');

		if(isset($post->post_parent) && ($post->post_parent>0) && ($post->post_type=='platformpress-remark')){
			wp_redirect(get_the_permalink($post->post_parent));
			exit;
		}

		/* If we are on plank view page load post variable*/
		if($qid!==""){

			if(!is_numeric($qid)){
				$post =  get_page_by_path($qid,OBJECT, 'platformpress-plank');
				if($post!=''){
				$qid = $post->ID;
				}
			} else{
				$post = get_post($qid);
			}
			if($post!='' && $post->post_type='platformpress-plank'){
				setup_postdata( $GLOBALS['post'] =& $post );
				$title = $post->post_title.' ';
			}
		}
		return $title;
	}

	add_filter( 'wp_title', 'platformpress_wp_title', 10,3);

	function platformpress_text_after_title( $post )
	{
		if($post->post_type=='platformpress-remark')
		{
			//Remove media button
			add_action('admin_head','z_remove_media_controls');

			//in case of edit remark
			if(isset($_GET['post']) && is_numeric($_GET['post'])){
				$entry = get_post($_GET['post']);
				$_GET['parent_id'] = $entry->post_parent;
			}

			if(isset($_GET['parent_id']) && is_numeric($_GET['parent_id']) && (($_GET['parent_id'])>0)){
				echo '<div class="after-title-help postbox">';
				$entry = get_post($_GET['parent_id']);
				echo "<input type=\"hidden\" name=\"parent_id\" value=\"".$entry->ID."\" />";
				echo "<input type=\"hidden\" name=\"post_title\" value=\"PLATFORMPRESS Remark\" />";
				echo "<h3>Plank: ".$entry->post_title."</h3>";
				echo '<div class="inside">';
				echo "<p>".$entry->post_content."</p>";
				$user_id = $entry->post_author;
				if($user_id>0){
					$userData =  get_userdata($user_id);
					echo "<div style=\"float:left; margin-right:10px;\">";
						echo platformpress_avatar($user_id, 32 );
					echo "</div>";
						echo "Plank posted by ".ucfirst($userData->data->display_name)."<br />";
						echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
				} else{
					echo "n/a";
				}
				echo "</div>";
				echo "</div>";
			}
		}

	}
	add_action( 'edit_form_after_title', 'platformpress_text_after_title' );


	// ADD NEW COLUMN
	function platformpress_array_insert( &$array, $element, $position = null ) {

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

	function platformpress_columns_head( $defaults ) {
		if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'platformpress-remark' ) {
			global $submenu;
			$defaults = array(
				'cb'            => '<input type="checkbox">',
				'remark'          => 'Remark',
				'date'			=> 'Date',
				'remarkauthor'        => 'Author',
				'in-response-to' => 'In Response to plank',
			);
		}
		if ( $_GET['post_type'] == 'platformpress-plank' ) {
			$defaults['plankauthor'] =  'Author';
			$defaults['stat'] =  'Stat';
			$defaults['action'] =  'Add remark';
		}
		return $defaults;
	}
	add_filter( 'manage_posts_columns', 'platformpress_columns_head' );

function platformpress_plank_columns_content( $column_name, $post_ID ) {
	switch ( $column_name ) {
		case 'plankauthor':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$user_id = $entry->post_author;
			if($user_id>0){
				$userData =  get_userdata($user_id);
				echo "<div style=\"float:left; margin-right:10px;\">";
					echo platformpress_avatar($user_id, 32 );
				echo "</div>";
					echo ucfirst($userData->data->display_name)."<br />";
					echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
			} else{
				echo "n/a";
			}
			break;
		case 'stat':
			$settings = new platformpressSettings();
			$avg = get_post_meta(get_the_ID(), 'platformpress_remarks_count', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Remarks <br />";
			$avg = get_post_meta(get_the_ID(), 'platformpress_plank_vote_count', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Votes <br />";
			$count = get_post_meta($post_ID, 'platformpress_views_count', true);
			echo ($count != '' ) ? $count : "0";
			echo ' Views <br />';
			$avg = get_post_meta(get_the_ID(), 'platformpress_plank_favorite', true);
			echo ($avg != '' ) ? $avg : "0";
			echo " Favorite <br />";
			break;
		case 'action':
			echo '<br /><a style="margin-left:30px;" title="Add remark to this plank" href="post-new.php?post_type=platformpress-remark&parent_id='.$post_ID.'"><img src="'.PLATFORMPRESS_PLUGIN_IMAGES_URL.'/create.png" /></a><br>';
			break;
	}
}


add_action( 'manage_platformpress-plank_posts_custom_column', 'platformpress_plank_columns_content', 10, 2 );

function platformpress_remark_columns_content( $column_name, $post_ID ) {
	switch ( $column_name ) {
		case 'remarkauthor':
			$post_ID = (int)($post_ID);
			$entry = get_post($post_ID);
			$user_id = $entry->post_author;
			if($user_id>0){
				$userData =  get_userdata($user_id);
				echo "<div style=\"float:left; margin-right:10px;\">";
					echo platformpress_avatar($user_id, 32 );
				echo "</div>";
					echo ucfirst($userData->data->display_name)."<br />";
					echo human_time_diff( strtotime($entry->post_date), current_time('timestamp') ) . ' ago';
			} else{
				echo "n/a";
			}
		break;
		case 'remark':
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
add_action( 'manage_platformpress-remark_posts_custom_column', 'platformpress_remark_columns_content', 10, 2 );

	//save post callback
	function platformpress_after_save_post($post_id) {
		$post_id = (int)($post_id);
		$post_type = get_post_type($post_id);
		if($post_type=='platformpress-plank'){
			//Set views
			$count_key = 'platformpress_views_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}

			//Set plank vote count (thumb up of all remark votes)
			$count_key = 'platformpress_plank_vote_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}

			//Set favorite (bookmarked)
			$count_key = 'platformpress_plank_favorite';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}

			//Set remarks count in this planks
			$count_key = 'platformpress_remarks_count';
			$count = get_post_meta($post_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($post_id, $count_key);
				add_post_meta($post_id, $count_key, '0');
			}
		}

		// if remark posted
		if($post_type=='platformpress-remark'){
			//Set remarks count in this planks
			$parent_id = wp_get_post_parent_id($post_id);
			global $wpdb;
			$sql = "SELECT COUNT(*) as counts FROM {$wpdb->posts} AS P WHERE P.post_parent = ".$parent_id." AND P.post_type = 'platformpress-remark' AND P.post_status = 'publish'";
			$obj = $wpdb->get_row($sql,OBJECT);
			$counts = $obj->counts;
			$count_key = 'platformpress_remarks_count';
			$count = get_post_meta($parent_id, $count_key, true);
			if($count==''){
				$count = 0;
				delete_post_meta($parent_id, $count_key);
				add_post_meta($parent_id, $count_key, $counts);
			} else{
				update_post_meta($parent_id, $count_key, $counts);
			}

			//When remark saved, set 0, if no vote counter set
			$settingsObj = new platformpressSettings();
			$count_key = 'platformpress_remark_vote_count';
			$count = $settingsObj->setRemarkVotes($post_id); //get count of votest
			if($count==''){
				$count = 0;
				delete_post_meta($parent_id, $count_key);
				add_post_meta($parent_id, $count_key, $counts);
			}

		}

	}
	add_action( 'save_post', 'platformpress_after_save_post' );


	function platformpress_post_updated($post_ID, $post_after, $post_before){
		if($post_after->post_type=='platformpress-remark'){
			//When remark saved, set 0, if no vote counter set
			$settingsObj = new platformpressSettings();
			$count_key = 'platformpress_remark_vote_count';
			$count = $settingsObj->setRemarkVotes($post_after->ID); //get count of votest
		}
	}
	add_action( 'post_updated', 'platformpress_post_updated', 10, 3 );


	function platformpress_after_delete_post( $pid ) {
		$post_type = get_post_type($pid);
		if(($post_type=='platformpress-plank') || ($post_type=='platformpress-remark')){
			global $wpdb;

			//If deleteing plank remove all remarks
			if($post_type=='platformpress-plank'){
				$args = array(
					'post_parent' => $pid,
					'post_type' => 'platformpress-remark'
				);
				$posts = get_posts($args);
				if (is_array($posts) && count($posts) > 0) {
					foreach($posts as $post){
						wp_delete_post($post->ID, true);
					}
				}
			}


			//Delete fav
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_platformpress_favorite_planks WHERE platformpress_planks_id = %d', $pid ) );
			//Delete spam
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_platformpress_spam WHERE obj_id = %d', $pid ) );
			//Delete views
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_platformpress_views WHERE plank_id = %d', $pid ) );
			//Delete vote
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_platformpress_votes WHERE platformpress_plank_id = %d', $pid ) );
			$wpdb->query( $wpdb->prepare( 'DELETE FROM mcl_platformpress_votes WHERE platformpress_remark_id = %d', $pid ) );

			//if remark deleted reset , up votes
			if($post_type=='platformpress-remark'){
				$settingsObj = new platformpressSettings();
				$settingsObj->setRemarkVotes($pid);
			}

		}

	}

	add_action( 'admin_init', 'platformpress_delete_init' );
	function platformpress_delete_init() {
		if ( current_user_can( 'delete_posts' ) )
		add_action( 'delete_post', 'platformpress_after_delete_post', 10 );
	}

	add_action( 'admin_head', 'showhiddencustomfields' );

	function showhiddencustomfields() {
		global $post;
		if(isset($post) && (($post->post_type=='platformpress-plank') || ($post->post_type=='platformpress-remark')))
		{
			?>
			<script>
			jQuery(document).ready(function(){
				 jQuery(".wp-editor-tabs #content-tmce").trigger("click");
			});
			</script>
			<?php
			echo "<link rel='stylesheet' media='screen,projection' type='text/css' href='" . PLATFORMPRESS_PLUGIN_CSS_URL . "dashboard.css'>";
		}
	}

	//Rewrite rule
	function platformpress_update_rewrite_rules() {
		global $wpdb;
		$settingsObj = new platformpressSettings();
		$settings = $settingsObj->settings;

		$var1 = '^'.$settings['stored']['permalink_plank'].'/([^/]+)?';
		$var2 = 'index.php?page_id='.$settings['stored']['plugin_page_id'].'&qid=$matches[1]';
		add_rewrite_rule($var1,$var2,'top');

		global $wp_rewrite;
		$wp_rewrite->flush_rules(false);
	}

	//settings function
	function platformpress_setting_save($option_name,$new_value){
		$option_name = 'platformpress_'.$option_name;
		if ( get_option( $option_name ) !== false ) {
			update_option( $option_name, $new_value );
		} else{
			$deprecated = null;
			$autoload = 'yes';
			add_option( $option_name, $new_value, $deprecated, $autoload );
		}
	}
	function platformpress_setting_get($option_name){
		$option_name = 'platformpress_'.$option_name;
		return get_option( $option_name );
	}

	function platformpress_setting_get_all($default='no'){
		$settings = array(
			'plank_style'				=> '0',
			'font_color'					=> '',
			'line_height_size'				=> '',
			'font_size'						=> '',
			'font_italic'					=> '0',
			'font_bold'						=> '0',
			'font'							=> '',

			'notification_new_remark'		=> '<p>Hey Admin,</p>
			<p>New remark created on your plank.</p>
			<p>Regards,</p><p>PLATFORMPRESS Team</p>',
			'notification_new_plank'		=> '<p>Hey Admin,</p>
<p>The user {plank_author} has posted this plank on {plank_title_url}</p>
<p>You might wanna check it out.</p>
<p>Regards,</p>
<p>PLATFORMPRESS Team</p>',
			'notify_new_plank'			=> '0',
			'notify_user'					=> '0',

			'permalink_plank'			=> 'plank',

			'social_locker'					=> '0',

			'facebook_app_id'				=> '',
			'facebook_app_secret'			=> '',
			'google_app_id'					=> '',
			'google_app_secret'				=> '',

			'auto_approve_new_remarks'		=> '1',
			'auto_approve_new_planks'	=> '1',
			'disble_negative_rating'		=> '0',
			'plugin_page_id'				=> '',
			'login_and_registeration'		=> '1',
		);

		//if settings not default pick stored settings
		if($default=='no'){
			foreach($settings as $key=>$val){
				$val = platformpress_setting_get($key);
				if($val!=""){
					$settings[$key] = $val;
				}
			}
		}
		return $settings;
	}

	# NEW CODES 22 JULY 2015 EVENING
	function platformpress_add_platformpress_user_role() {
		add_role('platformpress_platformpress_user',
			'PLATFORMPRESS User',
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
		return 'platformpress_platformpress_user'; // This is changed
	});

	function disable_platformpress_stuff($data) {
		return false;
	}

	function platformpress_set_meta_tags(){
		if(is_page()) {
			global $page;
			$description = '';
			echo "\n";
			echo '<!-- PLATFORMPRESS plugin meta -->' . "\n";
			$recentpost =  get_page($page->ID);
			if($recentpost->post_type=='platformpress-plank')
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
	add_action('wp_head','platformpress_set_meta_tags', 1);
?>
