<div id="login_sect">
	<h3>Login / Register to Post Planks & Remarks</h3>
	<div class="platformpress-block">

		<?php if((platformpress_setting_get('login_and_registeration')=="1") || is_user_logged_in()): ?>

		<div class="bck-sect colm-7">
			<h4><a href="<?php echo wp_login_url(); ?>">Login</a></h4>
		</div>

		<div class="bck-sect colm-5 register-sec">
			<h4><a href="<?php echo wp_registration_url(); ?>">Register</a></h4>
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
