<div id="login_sect">
	<h3>Log in/ Register to Post Questions & Answers</h3>
	<div class="qbot-block">

		<?php if((qbot_setting_get('login_and_registeration')=="1") || is_user_logged_in()): ?>	
	
		<div class="bck-sect colm-7">
			<form name="loginform" id="loginform" action="<?php echo get_option('siteurl'); ?>/wp-login.php" method="post">
				<div class="formgroup">
					<input value="" type="text" name="log" id="user_login" class="form-control"  placeholder="Username" />
				</div>
				<div class="formgroup">
					<input value="" type="password" name="pwd" id="user_pass" class="form-control" placeholder="Password" />
				</div>
				<!--
				<div class="formgroup">
					<input name="rememberme" id="rememberme" value="forever" tabindex="90" type="checkbox"> Remember Me? <br />
				</div>
				-->
				<div class="formgroup">
					<button name="wp-submit" id="wp-submit" value="Log In" tabindex="100" class="btn btn-default" type="submit">Submit</button>
					<input name="redirect_to" value="<?php echo $_SERVER['REQUEST_URI']; ?>" type="hidden">
					<input name="testcookie" value="1" type="hidden">
				</div>
			</form>
		</div>
		
		<div class="bck-sect colm-5 register-sec">
		
			<div class="env-circle">
				<span><i class="fa fa-envelope"></i></span>
				<h4><a href="<?php echo wp_registration_url(); ?>">Register with email</a></h4>
			</div>

			<div id="social-login">
				<?php if((isset($this->settings['stored']['google_app_id']) && (trim($this->settings['stored']['google_app_id'])!="")) && 
				(isset($this->settings['stored']['google_app_secret']) && ($this->settings['stored']['google_app_secret']!=""))
				): ?>
				<?php
				$params = array('state'=>'google-login','display'=>'popup');
				$url = esc_url(add_query_arg($params));
				?>
				<a rel="nofollow" id="gplus_icon" onclick="MM_openBrWindow('<?php echo $url; ?>','google','scrollbars=yes,width=650,height=500')" href="javascript:void(0)"></a>
				<?php endif; ?>
				
				<?php if((isset($this->settings['stored']['facebook_app_id']) && (trim($this->settings['stored']['facebook_app_id'])!="")) && 
				(isset($this->settings['stored']['facebook_app_secret']) && ($this->settings['stored']['facebook_app_secret']!=""))
				): ?>
				<?php
				$params = array('state'=>'facebook-login','display'=>'popup');
				$url = esc_url(add_query_arg($params));
				?>
				<a rel="nofollow" id="face_icon" onclick="MM_openBrWindow('<?php echo $url; ?>','google','scrollbars=yes,width=650,height=500')" href="javascript:void(0)"></a>
				<?php endif; ?>
			</div><!--/social-login-->
		</div> 

		<?php else: ?>
		User registration and login is currently not allowed by site admin in plugin settings.
		<?php endif; ?>

	</div> 
</div>

<script type="text/javascript">
<!--
function MM_openBrWindow(theURL,winName,features) { //v2.0
window.open(theURL,winName,features);
}
//-->
</script>
