<?php
/**
 * This class will login with facebook
 * You can use this class as you want, you can modify it but do not sell it. Help others
 * @author Halmagean Daniel halmageandaniel@yahoo.com
 * 
 */
class Facebook_Login
{

	public $auth_url           = 'https://www.facebook.com/dialog/oauth?';
	public $redirect_uri       = '';  
	public $app_id             = '';
	public $app_secret		   = ''; 
	
	/**
	 * set up the app id, app secret and redirect uri 
	 */
	public function __construct($app_id = '',$app_secret = '',$redirect_uri = '')
	{
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
		$this->redirect_uri = $redirect_uri;
	}
	/**
	 * Get the code in order to request for the token. 
	 * This will redirect to facebook in order to allow the user to give accept for login.
	 * If $mode variable is FALSE the function will return the url to be used. Just in case that user cannot use header function
	 * This is step 1
	 */
	public function login($mode = TRUE)
	{
		$url = $this->auth_url . 'client_id=' .$this->app_id. '&redirect_uri=' .$this->redirect_uri. '&scope=email&display=popup';
		if($mode === TRUE)
		{
			wp_redirect($url);
			exit;
		}
		else
		{
			return $url;	
		}
	}
	
	/**
	 * get the token.
	 * This function is called when the facebook redirects to user to us after he logged into FB.
	 * This is step 2 and final one :D
	 */
	public function getUserData()
	{
		#get the code from url if exists
		$code = (isset($_GET['code']))? $_GET['code']:'';
		if(!empty($code))
		{
			$token_url = 'https://graph.facebook.com/oauth/access_token?client_id=' .$this->app_id. '&redirect_uri=' .$this->redirect_uri. '&client_secret=' .$this->app_secret. '&code='.$code;
			#get the token. Token is returned as a string in a text document, in our case..the browser.
			
			#response example: access_token=SOME_VERY_LARGE_HASH&expires=5173856
			#we will use @ in order to force the file_get_contents function not to throw an ugly erorr 
			$response = @file_get_contents($token_url);
			if($response === FALSE)
			{
				echo 'Login failed. This "code" was already used for getting the token. Get another code (resend user to login page)';exit;
			}
			#make from string ex: &token=1&expires=123 into array
			parse_str($response,$response);
			#get the token if exists
			$token = (isset($response['access_token']))? $response['access_token']:'';
			#call for user data, the returned data is a json	
			$graph_url = 'https://graph.facebook.com/me?fields=id,first_name,last_name,email,picture&access_token='.$token;
			#userData is an array
			$userData = json_decode(@file_get_contents($graph_url),TRUE);
			if(!is_array($userData) || empty($userData))
			{
				echo 'Getting user data failed.The token was not properly received.';exit;
			}
			#make things with this data
			return $userData;
		}
		return FALSE;
	}
	
}
?>