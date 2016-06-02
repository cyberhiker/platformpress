<div class="wrap">

	<?php if(($action=="")): ?>
	<h2>General Settings</h2>
	<?php endif; ?>
	
	<?php if(($action=="permalink-settings")): ?>
	<h2>Permalink Settings</h2>
	<?php endif; ?>	

	<h2 class="nav-tab-wrapper">
		<?php
		$params = array('page'=>'platformpress-plugin-settings');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo (($action=="")) ? "nav-tab-active" : ""; ?>" title="Basic settings" href="<?php echo $url; ?>">General settings</a>
		<?php
		$params = array('page'=>'platformpress-plugin-settings','action'=>'permalink-settings');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo ($action=="permalink-settings") ? "nav-tab-active" : ""; ?>" title="Permalink Settings" href="<?php echo $url; ?>">Permalink Settings</a>
		
		<?php
		$params = array('page'=>'platformpress-plugin-settings','action'=>'notification-settings');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo ($action=="notification-settings") ? "nav-tab-active" : ""; ?>" title="Notification Settings" href="<?php echo $url; ?>">Notification Settings</a>
		
	</h2>

	<?php platformpress_flash_get(); ?>
	
	<?php if(($action=="")): ?>	
	<form novalidate="novalidate" class="validate" id="platformpressform" name="platformpresssettings" method="post">
	<table class="form-table">
		<tbody>

		<tr>
			<th scope="row">Questions listing page</th>
			<td>
				<label for="remark_rating">
				<?php $setting = ((isset($_POST['plugin_page_id'])) && ($_POST['plugin_page_id']!='')) ? $_POST['plugin_page_id']: ""; ?>
					<?php
					wp_dropdown_pages(
							array(
								 'name' => 'plugin_page_id',
								 'echo' => 1,
								 'show_option_none' => __( '&mdash; Select &mdash;' ),
								 'option_none_value' => '0',
								 'selected' => $setting
							)
						);
					?>	
				</label>
				<p class="description">Select a page where you want to show PLATFORMPRESSs frontend</p>
			</td>
		</tr>

		<tr>
			<th scope="row">User login/registeration</th>
			<td>
				<input type="hidden" value="0" name="login_and_registeration">
				<label for="login_and_registeration">
				<?php $setting = ((isset($_POST['login_and_registeration'])) && ($_POST['login_and_registeration']==1)) ? "checked": ""; ?>
				<input id="login_and_registeration" type="checkbox" value="1" name="login_and_registeration" <?php echo $setting; ?>>
				Allow user login/register
				</label>
				<p class="description">Enabling this feature is required to allow users to post planks and remarks</p>
			</td>
		</tr>	
		
		<tr>
			<th scope="row">Disable negative ratings</th>
			<td>
				<input type="hidden" value="0" name="disble_negative_rating">
				<label for="disble_negative_rating">
				<?php $setting = ((isset($_POST['disble_negative_rating'])) && ($_POST['disble_negative_rating']==1)) ? "checked": ""; ?>
				<input id="disble_negative_rating" type="checkbox" value="1" name="disble_negative_rating" <?php echo $setting; ?>>
				Disable the negative (Thumb Down) feature
				</label>
				<p class="description">By default remarks include the thumb up and thumb down feature</p>
			</td>
		</tr>	

		<tr>
			<th scope="row">Approve Questions</th>
			<td>
				<input type="hidden" value="0" name="auto_approve_new_planks">
				<label for="auto_approve_new_planks">
				<?php $setting = ((isset($_POST['auto_approve_new_planks'])) && ($_POST['auto_approve_new_planks']==1)) ? "checked": ""; ?>
				<input id="auto_approve_new_planks" type="checkbox" value="1" name="auto_approve_new_planks" <?php echo $setting; ?>>
				Auto Approve new planks
				</label>
				<p class="description">All planks would be posted immediately upon submission</p>
			</td>
		</tr>
					
		</tbody></table>
		<p class="submit">
		<input type="submit" value="Save Settings" class="button button-primary" name="platformpress-submitted">
		</p>
	</form>
	<?php elseif(($action=="permalink-settings")): ?>
	
		<table width="100%">
		<tr>
		<td width="50%" valign="top">	
			<form novalidate="novalidate" class="validate" id="platformpressform" name="platformpresssettings" method="post">
			<table class="form-table">
				<tbody>
				
				<tr class="form-field">
					<th scope="row">Question view permalink</th>
					<td>
						<?php $setting = (isset($_POST['permalink_plank'])) ? $_POST['permalink_plank']: ""; ?>
						<input type="text" name="permalink_plank" value="<?php echo $setting; ?>" />
						<p class="description">e.g. http:://www.example.com/plank/your-plank</p>
					</td>
				</tr>

				</tbody>
			</table>
			
				<p class="submit">
				<input type="submit" value="Save" class="button button-primary" name="platformpress-submitted">
				</p>	
			
			</form>	
		</td>
		</tr>
		</table>
		
	<?php elseif(($action=="notification-settings")): ?>
	
		<table width="100%">
		<tr>
		<td width="50%" valign="top">	
			<form novalidate="novalidate" class="validate" id="platformpressform" name="platformpresssettings" method="post">
			<table class="form-table">
				<tbody>

				<tr>
					<th scope="row">New plank notification</th>
					<td>
						<div>
							<input type="hidden" value="0" name="notify_new_plank">
							<label for="notify_new_plank">
							<?php $setting = ((isset($_POST['notify_new_plank'])) && ($_POST['notify_new_plank']==1)) ? "checked": ""; ?>
							<input id="notify_new_plank" type="checkbox" value="1" name="notify_new_plank" <?php echo $setting; ?>>
							Notify admin when new plank created by any user.
							</label>
						</div>
					</td>
				</tr>	
				
				<tr class="form-field">
					<th scope="row">Notify new plank</th>
					<td>
						<?php $setting = (isset($_POST['notification_new_plank'])) ? $_POST['notification_new_plank']: ""; ?>
						<?php 
						//Answer textarea
						platformpress_editor(array(
							'content' 		=> $setting,
							'id' 			=> 'notification_new_plank', 
							'textarea_name' => 'notification_new_plank',
							'media_buttons' => false,
						));
						?>						
						<p class="description">Notification template, send this notification to admin, when new plank created.</p>
						
						<div>
							<h3>User following shortcode in plank email</h3>
							<ul>
							<li>{site_name}</li>
							<li>{site_description}</li>
							<li>{site_url}</li>
							<li>{plank_author}</li>
							<li>{plank_content}</li>
							<li>{plank_title}</li>
							<li>{plank_title_url}</li>
							</ul>
						</div>
						
					</td>
				</tr>
				
				<tr>
					<th scope="row">New remark notification</th>
					<td>
						<div>
							<input type="hidden" value="0" name="notify_user">
							<label for="notify_user">
							<?php $setting = ((isset($_POST['notify_user'])) && ($_POST['notify_user']==1)) ? "checked": ""; ?>
							<input id="notify_user" type="checkbox" value="1" name="notify_user" <?php echo $setting; ?>>
							Notify plank author when new remark posted to user's plank.
							</label>
						</div>
					</td>
				</tr>	
				
				
				<tr class="form-field">
					<th scope="row">Notify new remark</th>
					<td>
						<?php $setting = (isset($_POST['notification_new_remark'])) ? $_POST['notification_new_remark']: ""; ?>

						<?php 
						//Answer textarea
						platformpress_editor(array(
							'content' 		=> $setting,
							'id' 			=> 'notification_new_remark', 
							'textarea_name' => 'notification_new_remark',
							'media_buttons' => false,
						));
						?>						
						<p class="description">Notification template notify to plank author, when someone post remark</p>

						<div>
							<h3>User following shortcode in plank email</h3>
							<ul>
							<li>{site_name}</li>
							<li>{site_description}</li>
							<li>{site_url}</li>
							<li>{plank_author}</li>
							<li>{plank_content}</li>
							<li>{plank_title}</li>
							<li>{plank_title_url}</li>
							<li>{remark_author}</li>
							<li>{remark_content}</li>
							</ul>
						</div>
						
					</td>
				</tr>
				
				</tbody>
			</table>
			
				<p class="submit">
				<input type="submit" value="Save" class="button button-primary" name="platformpress-submitted">
				</p>	
			
			</form>	
		</td>
		</tr>
		</table>		
		
	
	<?php endif; ?>
</div><!--/wrap-->