<style>
.view_user_wrap:hover {
	background: #0091cd;
	border-color: #0073aa;
	box-shadow: inset 0 1px 0 rgba(120,200,230,.6);
	color: #fff;
}

.view_user_wrap {
	border-color: #0073aa;
	-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.5), 0 1px 0 rgba(0,0,0,.15);
	box-shadow: inset 0 1px 0 rgba(120,200,230,.5), 0 1px 0 rgba(0,0,0,.15);
	border-width: 1px;
	border-style: solid;
	-webkit-appearance: none;
	-webkit-border-radius: 3px;
	border-radius: 3px;
	white-space: nowrap;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	float: right;
	font-size: 13px;
	background-color: #00A0D2;
	padding: 2px 15px;
	color: #fff;
	text-decoration: none;
	margin-bottom: 15px;
	text-align: center;
}

.activities_wrap h2 { padding: 9px 0px 4px 0; }
.qbot-feature-list ul li{min-height:80px;}
</style>

<div class="wrap">
	<h2>QBOT Plugin Dashboard <small>( <a target="blank" href="<?php echo $this->getBaseUrl(); ?>">Visit frontend</a> )</small></h2>


  <div class="welcome-panel qbot-activation-key-panel" id="welcome-panel">
    <div class="welcome-panel-content">
	
		<h1>Basic version</h1>
		
		<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">
		<!-- <h3>You are using Free version, some features are locked</h3>-->
		<div class="welcome-panel-column qbot-feature-list">
		  <ul>
			<li>
				<h4>
				<a class="qbot-admin-dashboard qbot-manage-question" href="edit.php?post_type=qbot-question">Manage Questions</a>
				</h4>
				<p>View/ Edit/ Delete Posted Questions</p>
			</li>
			<li>
				<h4>
				<a class="qbot-admin-dashboard qbot-manage-answer" href="edit.php?post_type=qbot-answer">Manage Answers</a>
				</h4>
				<p>View/ Edit/ Delete/ Respond to Posted Answers</p>
			</li>
			<li>
				<h4>
				<a class="qbot-admin-dashboard qbot-manage-settings" href="admin.php?page=qbot-plugin-settings">Settings</a>
				</h4>
				<p>Manage QBOT's extensive features from this section</p>
			</li>
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-categories0">Category Management</span></h4>
				<p>Keep things organized. Give your users the ability to post questions into admin specified categories</p>
			</li>
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-question-style0" >Design Management</span>
				</h4>
				<p>Manage fonts, colors and background of QBOT to match your website design</p>
			</li>
		  </ul>
		</div>
		<div class="welcome-panel-column qbot-feature-list">
		  <ul>
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-shortcode0" >Manage Shortcodes</span>
				</h4>
				<p>Use Shortcodes to include QABOT into other sections of your website</p>
			</li>
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-spam0" >Spam Manager</span>
				</h4>
				<p>Keep spam at bay. Manage flagged questions/ answers</p>
			</li>
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-comments0" >Manage Sub Answers</span>
				</h4>
				<p>View/ Edit/ Delete/ Respond to Posted Sub Answers</p>
			</li>
			
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-categories0" >Widgets</span>
				</h4>
				<p>Add QBOT widget to specific sections of your website</p>
			</li>
			
			<li>
				<h4><span class="qbot-admin-dashboard qbot-manage-sociallogin0" >Share & Learn</span>
				</h4>
				<p>Social Lock the top answers, pushing your visitors to share your page for the correct answer. Social Sharing = Higher Rankings</p>
			</li>			
			
		  </ul>
		</div>
		<div class="welcome-panel-column welcome-panel-last qbot-feature-list">
			<ul>
				<li>
					<h4><span class="qbot-admin-dashboard qbot-manage-sociallogin0" >Social Login</span>
					</h4>
					<p>Allow users to register via Facebook/ G+. User data and email address captured from logins are stores into your database increasing your email list organically</p>
				</li>			
				<li>
					<h4><span class="qbot-admin-dashboard qbot-analytics0" >Analytics</span>
					</h4>
					<p>Use a multi feature analytic system to track top questions, contributors, and countries, and download users in CSV.</p>
				</li>
				<li>
					<h4><span class="qbot-admin-dashboard qbot-userroles0" >User Roles</span>
					</h4>
					<p>Control user access to certain questions or answers</p>
				</li>			
			</ul>
		</div>
		</div>
		<br /><br />
		</div>		

		
	</div>
  </div>


<?php
	global $wpdb;
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 10;
	$offset = ( $pagenum - 1 ) * $limit;
	$entries = $wpdb->get_results( "SELECT * FROM $wpdb->users ORDER BY id ASC LIMIT $offset, $limit" );
	echo '<div class="wrap">';
	?>

	<div class="activities_wrap">
	  <h2>Last 7 days activities 
		<!--
		<a class="view_user_wrap" href="#">To view full stat Download pro version</a>
		-->
	  </h2>
	</div>

<table class="widefat">
  <thead>
    <tr>
      <th scope="col" class="manage-column column-name">Date</th>
      <th scope="col" class="manage-column column-name" style="text-align:center">User registered</th>
      <th scope="col" class="manage-column column-name" style="text-align:center">Question asked</th>
      <th scope="col" class="manage-column column-name" style="text-align:center">Answered</th>
      <th scope="col" class="manage-column column-name" style="text-align:center">Spam counts</th>
    </tr>
  </thead>
  <tbody>
  
	<?php
	$date = date('Y-m-d');
	$end_date = date('Y-m-d', strtotime('-7 days'));
	$count = 1;
	?>
	
    <?php while (strtotime($date) >= strtotime($end_date)) { ?>
    <?php
				if($date==date('Y-m-d')){
					$printDate='Today';
				}elseif($date==date('Y-m-d', strtotime('-1 days'))){
					$printDate='Yesterday';
				} else{
					$printDate= date('d M Y',strtotime($date));
				}
				
				$class = '';
				$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				?>
    <tr <?php echo $class; ?>>
      <td><?php echo $printDate; ?></td>
      <td align="center"><?php echo $this->countByDate($date,'userRegistered'); ?></td>
      <td align="center"><?php echo $this->countByDate($date,'questions'); ?></td>
      <td align="center"><?php echo $this->countByDate($date,'answers'); ?></td>
      <td align="center"><?php echo $this->countByDate($date,'spam'); ?></td>
    </tr>
    <?php
					$count++;
					?>
    <?php $date = date ("Y-m-d", strtotime("-1 day", strtotime($date))); ?>
    <?php } ?>
  </tbody>
</table>

</div>