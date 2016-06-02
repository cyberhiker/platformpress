<?php 
class qbotSettings{

	var $settings = array();
	var $currentUrl = '';
	
	function __construct(){
		$storedSettings = array();
		$storedSettings = $this->loadStoredSettings();
		$this->settings['stored'] = $storedSettings;

		$generalSettings = array();
		$generalSettings = $this->loadGeneralSettings();
		$this->settings['general'] = $generalSettings;
		
		$this->currentUrl = $this->getCurrentUrl();
	}
		
	function getCurrentUrl(){
		global $wp;
		$current_url = home_url(add_query_arg(array(),$wp->request));
		return $current_url;
	}

	function loadStoredSettings(){
		$settings = qbot_setting_get_all($default='no');
		return $settings;
	}
	
	function loadgeneralSettings(){
		$settings = array();
		$settings['is_user_logged_in'] =  is_user_logged_in();
		$settings['user_id'] =  get_current_user_id();
		
		$pluginPageId = $this->settings['stored']['plugin_page_id'];
		$pluginPageId = (int)($pluginPageId);
		$permalink = get_permalink($pluginPageId); 
		$settings['plugin_base_url'] =  $permalink;
		
		return $settings;		
	}	

	function getSetting($type,$key){
		$value = "";
		switch($type){
			case 'stored':
			$value = isset($this->settings['stored'][$key]) ? $this->settings['stored'][$key] : "";
			break;
			case 'general':
			$value = isset($this->settings['general'][$key]) ? $this->settings['general'][$key] : "";
			break;
		}
		return $value;
	}
	
	function isQuestionMarkedAsFavorite($questionId){
		global $wpdb;
		$user_id = get_current_user_id();
		$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_favorite_questions WHERE wp_users_id='.$user_id.' AND qbot_questions_id='.$questionId.'', 'OBJECT');
		if($res->counts>0){
			return true;
		} else{
			return false;
		}
	}
	
	function isUserAllowed($userId,$cap){
		// In free version no authorization restriction, user always allowed
		return true;
	}
	
	public function getUserIP()
	{
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];

		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}

		return $ip;
	}	
	
	function getMyLocation(){
		$myIp = $this->get_the_user_ip();
		$response = $this->ipToGeoData($myIp);
		//if no geo data, try with tempraory ip of users ISP
		if(!$response){
			$myIp = $_SERVER['SERVER_ADDR'];
			$response = $this->ipToGeoData($myIp);
		} else{
			return $response;
		}
	}
	
	function updateMyLocation(){
		$userId = get_current_user_id();
		if($userId!=""){
			//check from db;
			$res = $this->getUserLocation($userId);
			// if user have no location entery then pick location and store cache
			if(!$res){
				$response = $this->getMyLocation();	
				
				if($response){
					global $wpdb;
					$wpdb->insert('mcl_qbot_users', array(
						'wp_users_id' 	=> $userId,
						'country_code' 	=> $response->countryCode,
						'country_name'	=> '',
						'region_name'	=> '',
						'city_name'		=> '',
						'zipcode'		=> '',
						'lati'			=> '',
						'longi'			=> '',
					), array('%d','%s','%s','%s','%s','%s','%d','%d'));
				}	
			}// if entery not exist already
		}
	}
	
	function ipToGeoData($ip){
		global $wpdb;
		$sql = 'SELECT t.country_code FROM mcl_ip2location_db1 as t WHERE INET_ATON("'.$ip.'") <= ip_to LIMIT 1';
		$res = $wpdb->get_row($sql, 'OBJECT');
		if(!empty($res)){
			$response = new stdClass;
			$response->countryCode = $res->country_code;
			return $response;
		} else{
			return false;
		}
	}
	
	function getUserLocation($userId){
		global $wpdb;
		$userId = (int)($userId);
		$res = $wpdb->get_row('SELECT * FROM mcl_qbot_users WHERE wp_users_id='.$userId.' AND country_code!=""', 'OBJECT');
		if($res){
			return $res;
		} else{
			return false;
		}
	}// get user details

	function get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	function getQuestionUrl($question){
		if(is_object($question)){
			$qid = $question->question_slug;
		} else{
			global $wpdb;
			$res = $wpdb->get_row('SELECT question_slug FROM mcl_qbot_questions WHERE id='.$question.'', 'OBJECT');
			$qid = $res->question_slug;
		}
		
		if(($this->settings['stored']['seo_friendly_urls']==1) && ($this->settings['stored']['permalink_question']!='')){
			$params = array('qid'=>$qid);
			$url = site_url($this->settings['stored']['permalink_question'].'/'.$qid.'/');
		} else{
			$params = array('qid'=>$qid);
			$url = esc_url(add_query_arg($params,$this->getBaseUrl()));
		}
		return $url;
	}
	
	function getBaseUrl(){
		$basePageId = $this->settings['stored']['plugin_page_id'];
		$url = get_permalink($basePageId);
		return $url;
	}
	
	function countByDate($date,$type){
		global $wpdb;
		switch($type){
			case 'questions':
			$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM '.$wpdb->posts.' WHERE DATE(post_date) = "'.$date.'" AND post_status="publish" AND post_type="qbot-question"', 'OBJECT' );
			return $res->counts;
			break;
			case 'answers':
			$res = $wpdb->get_row( 'SELECT COUNT(*) as counts FROM '.$wpdb->posts.' WHERE DATE(post_date) = "'.$date.'" AND post_status="publish" AND post_type="qbot-answer"', 'OBJECT' );
			return $res->counts;
			break;
			case 'userRegistered':
			$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM '.$wpdb->users.' WHERE DATE(user_registered) = "'.$date.'"', 'OBJECT' );
			return $res->counts;
			break;
			case 'spam':
			$res = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_spam WHERE DATE(time) = "'.$date.'"', 'OBJECT' );
			return $res->counts;
			break;
			default:
			return 'N/A';
			break;
		}
	}

	public function nofity_new_question( $questionId ) {
		#send notification to admin if setting enabled from admin;
		$questionId = (int)($questionId);
		$isQuestuionNotificationEnabled = ($this->settings['stored']['notify_new_question']==1) ? true : false;
		if(!$isQuestuionNotificationEnabled){
			return false;
		}
	
		require_once QBOT_PLUGIN_INCLUDE_PATH.'class-qbot-admin-questions.php';
		$qbotAdminQuestions = new qbotAdminQuestions();
		$post 	= get_post($questionId);
		
		$questionUserData = get_userdata($post->post_author);
		
		$subject = "Notification - New question posted";
		$message = $this->settings['stored']['notification_new_question'];
		
		$question_author 		= $questionUserData->data->display_name;
		$question_url 			= get_permalink($post->ID);
		$question_title 		= $post->post_title;
		$question_title_url 	= "<a href='".$question_url."'>".$question_title."</a>";
		$question_content 		= $post->post_content;

		$site_name 				= get_bloginfo( 'name' );
		
		$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		$subject = str_replace( '{site_description}', get_bloginfo( 'description' ), $subject );
		$subject = str_replace( '{site_url}', site_url(), $subject );
		
		$subject = str_replace( '{question_title}', $question_title, $subject );
		$subject = str_replace( '{question_author}', $question_author, $subject );

		$message = str_replace( '{site_name}', get_bloginfo( 'name' ), $message );
		$message = str_replace( '{site_description}', get_bloginfo( 'description' ), $message );
		$message = str_replace( '{site_url}', site_url(), $message );
		
		$message = str_replace( '{question_author}', $question_author, $message );
		$message = str_replace( '{question_content}', $question_content, $message );
		
		$message = str_replace( '{question_title}', $question_title, $message );
		$message = str_replace( '{question_title_url}', $question_title_url, $message );
		
		$from_email = $questionUserData->data->user_email;
		
		#Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$to_email = get_bloginfo( 'admin_email' );
		if($from_email) {
			$headers .= 'From: ' . $from_email . "\r\n";
		}


		wp_mail($to_email, $subject, $message, $headers );
	}	
	
	public function nofity_new_answer( $answer_id ) {
	
		$answer_id = (int)($answer_id);
		#send notification to question author if setting enabled from admin;
		$isAnswerNotificationEnabled = ($this->settings['stored']['notify_user']==1) ? true : false;
		if(!$isAnswerNotificationEnabled){
			return false;
		}
		
		$answer 	= get_post($answer_id);
		$questionId = $answer->post_parent;
		$question 	= get_post($questionId);
		
		$questionUserData = get_userdata($question->post_author);
		$answerUserData = get_userdata($answer->post_author);
		
		$subject = "Notification - New answer posted";
		$message = $this->settings['stored']['notification_new_answer'];
		
		$question_author 		= $questionUserData->data->display_name;
		$question_url 			= get_permalink($question->ID);
		$question_title 		= $question->post_title;
		$question_title_url 	= "<a href='".$question_url."'>".$question_title."</a>";
		$question_content 		= $question->post_content;

		$answer_author 			= $answerUserData->data->display_name;
		$answer_content 		= $answer->post_content;
		
		$site_name 				= get_bloginfo( 'name' );
		
		
		$subject = str_replace( '{site_name}', get_bloginfo( 'name' ), $subject );
		$subject = str_replace( '{site_description}', get_bloginfo( 'description' ), $subject );
		$subject = str_replace( '{site_url}', site_url(), $subject );
		
		$subject = str_replace( '{question_title}', $question_title, $subject );
		$subject = str_replace( '{question_author}', $question_author, $subject );
		$subject = str_replace( '{answer_author}', $answer_author, $subject );

		$message = str_replace( '{site_name}', get_bloginfo( 'name' ), $message );
		$message = str_replace( '{site_description}', get_bloginfo( 'description' ), $message );
		$message = str_replace( '{site_url}', site_url(), $message );
		
		$message = str_replace( '{question_author}', $question_author, $message );
		$message = str_replace( '{answer_author}', $answer_author, $message );
		$message = str_replace( '{question_content}', $question_content, $message );
		$message = str_replace( '{answer_content}', $answer_content, $message );
		
		$message = str_replace( '{question_title}', $question_title, $message );
		$message = str_replace( '{question_title_url}', $question_title_url, $message );
		
		$to_email = $questionUserData->data->user_email;
		
		#Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$from_email = get_bloginfo( 'admin_email' );
		if($from_email) {
			$headers .= 'From: ' . $from_email . "\r\n";
		}
		wp_mail($to_email, $subject, $message, $headers );
	}
	
	public function welcome_mail($user_id,$password) {
	
		$userData = get_userdata($user_id);
		
		$to_name 		= $userData->data->display_name;
		$site_name 		= get_bloginfo( 'name' );
		$to_email 		= $userData->data->user_email;
		$subject		= "Welcome - ".$site_name;
		
		$message = "";
		$message .= "Hello ".$to_name.",<br /><br />";
		$message .= "Your QBOT account has been created for ".$site_name."<br />";
		$message .= "Login credentials are:<br />";
		$message .= "Username: ".$userData->data->user_login."<br />";
		$message .= "Password: ".$password."<br />";
		$message .= "<a href='".$this->getBaseUrl()."'>Click here</a> to view new questions.";
		$message .= "<br /><br />";
		$message .= "Thanks & regards,<br />";
		$message .= $site_name;
		
		#Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$from_email = get_bloginfo( 'admin_email' );
		if($from_email) {
			$headers .= 'From: ' . $from_email . "\r\n";
		}
		echo $to_email;	
		wp_mail($to_email, $subject, $message, $headers );
	}	
	
	function getUserProfileUrl($user_id){
		$url = add_query_arg(array('action'=>'user-dashboard','uid'=>$user_id),$this->getBaseUrl());
		return $url;
	}
	
	public function loadStyle(){
	
		wp_enqueue_style(
			'qbot-main-style',
			plugin_dir_url( __FILE__ ) . '../css/style.css',
			array(),
			'1.0'
		);
		
		wp_enqueue_style(
		'font-awesome', 
		plugin_dir_url( __FILE__ ) . '../font-awesome-4.3.0/css/font-awesome.min.css'
		); 
		

		//add_action('wp_footer', array($this,'runjs'));
		// apply dynamicly added question description style
		add_action('wp_footer', array($this,'runcss'));
	}
	
	function flash_message( $type, $message = '' ) {
		if(session_id() == ''){ session_start(); }
		$_SESSION['qbot_flash_messages'][$type] = $message;
	}

	function get_flash_messages() {
		if(session_id() == ''){ session_start(); }
		$return = '';
		if ( isset( $_SESSION['qbot_flash_messages'] ) && is_array( $_SESSION['qbot_flash_messages'] ) ) {
			foreach( $_SESSION['qbot_flash_messages'] as $type => $message ) {
				$key = 'qbot_flash_'.$type;
				$html = "<div id='close' class=\"qbot-alert ".$key."\">";
				$html .= $message;
				$html .= "</div>";
				$return .= $html;
			}
		}
		if ( strlen( $return ) > 0 )
			return $return;

		return false;
	}

	function clean_flash_messages( $type = false ) {
		if(session_id() == ''){ session_start(); }
		
		if ( ! $type ){
			$_SESSION['qbot_flash_messages'] = array();
		}
		else{
			unset( $_SESSION['qbot_flash_messages'][$type]);
		}
	}

	function setQuestionViews($postID) {
		$count_key = 'qbot_views_count';
		$count = get_post_meta($postID, $count_key, true);
		if($count==''){
			$count = 0;
			delete_post_meta($postID, $count_key);
			add_post_meta($postID, $count_key, '0');
		}else{
			$count++;
			update_post_meta($postID, $count_key, $count);
		}
	}
	
	function setAnswerVotes($postID) {
		global $wpdb;
		$upVoteObj = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_votes WHERE qbot_answer_id='.$postID.' AND is_up_vote=1', 'OBJECT');
		$count = ($upVoteObj->counts);
		$count_key 		= 'qbot_answer_vote_count';
		$storedCount 	= get_post_meta($postID, $count_key, true);
		if($storedCount==''){
			add_post_meta($postID, $count_key, $count);
		}else{
			//update thumb-up votes count for each answer
			update_post_meta($postID, $count_key, $count);
		}
		
		//update thumb-up votes of all the answers in question to show most voted question
		$questionId = wp_get_post_parent_id($postID);
		$sql = "
		SELECT COUNT(*) as counts FROM mcl_qbot_votes 
		INNER JOIN {$wpdb->posts} AS P ON(mcl_qbot_votes.qbot_answer_id=P.ID) 
		WHERE P.post_status='publish' AND qbot_question_id=".$questionId." AND is_up_vote=1";
		$upVoteObj = $wpdb->get_row($sql, 'OBJECT');

		$qaAvgCount = (($upVoteObj->counts));
		$count_key = 'qbot_question_vote_count';
		$storedCount = get_post_meta($questionId, $count_key, true);
		if($storedCount==''){
			delete_post_meta($questionId, $count_key);
			add_post_meta($questionId, $count_key, $qaAvgCount);
		}else{
			update_post_meta($questionId, $count_key, $qaAvgCount);
		}
		$count = ($count<1) ? '0' : $count;
		return $count;
	}
	
	/* Bookmark this question */
	function setQuestionBookmarkCount($postID){
		global $wpdb;
		$obj = $wpdb->get_row('SELECT COUNT(*) as counts FROM mcl_qbot_favorite_questions WHERE qbot_questions_id='.$postID, 'OBJECT');
		$counts = $obj->counts;
		update_post_meta($postID, 'qbot_question_favorite', $counts);
		return $counts;
	}
	
	function getLatestAnswer($question_id){
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->posts} AS P WHERE P.post_parent = ".$question_id." AND P.post_type = 'qbot-answer' AND P.post_status = 'publish' ORDER BY ID DESC";
		$obj = $wpdb->get_row($sql,OBJECT);
		if($obj!==null){
			return $obj;
		} else{
			return false;
		}
	}
	
	function post_categories($questionId){
		global $wpdb;
		$sql = "
		SELECT term.term_id,term.name,term.slug FROM 
		{$wpdb->term_relationships} AS catr, {$wpdb->term_taxonomy} AS termtax, {$wpdb->terms} AS term 
		WHERE
		catr.object_id=".$questionId."
		AND catr.term_taxonomy_id=termtax.term_taxonomy_id 
		AND termtax.term_id=term.term_id";
		$categories = $wpdb->get_row($sql);
		if($categories==''){
			return false;
		} else{
		return $categories;
		}
	}
	
}
?>