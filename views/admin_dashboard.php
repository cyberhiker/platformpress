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
.platformpress-feature-list ul li{min-height:80px;}
</style>

<div class="wrap">
	<h2>PlaformPress Plugin Dashboard <small>( <a target="blank" href="<?php echo $this->getBaseUrl(); ?>">Visit frontend</a> )</small></h2>


  <div class="welcome-panel platformpress-activation-key-panel" id="welcome-panel">
    <div class="welcome-panel-content">
		<div class="welcome-panel-content">
		<div class="welcome-panel-column-container">
		<div class="welcome-panel-column platformpress-feature-list">
		  <ul>
			<li>
				<h4>
				<a class="platformpress-admin-dashboard platformpress-manage-plank" href="edit.php?post_type=platformpress-plank">Manage Planks</a>
				</h4>
				<p>View/ Edit/ Delete Posted Planks</p>
			</li>
			<li>
				<h4>
				<a class="platformpress-admin-dashboard platformpress-manage-remark" href="edit.php?post_type=platformpress-remark">Manage Remarks</a>
				</h4>
				<p>View/ Edit/ Delete/ Respond to Posted Remarks</p>
			</li>
			<li>
				<h4>
				<a class="platformpress-admin-dashboard platformpress-manage-settings" href="admin.php?page=platformpress-plugin-settings">Settings</a>
				</h4>
				<p>Manage PlatformPress's extensive features from this section</p>
			</li>
			<li>
				<h4><span class="platformpress-admin-dashboard platformpress-manage-plank-style0">Category Management</span>
                </h4>
				<p>Keep things organized. Give your users the ability to post planks into admin specified categories</p>
			</li>
		  </ul>
		</div>

		<br /><br />
		</div>


	</div>
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
      <th scope="col" class="manage-column column-name" style="text-align:center">Plank asked</th>
      <th scope="col" class="manage-column column-name" style="text-align:center">Remarked</th>
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
      <td align="center"><?php echo $this->countByDate($date,'planks'); ?></td>
      <td align="center"><?php echo $this->countByDate($date,'remarks'); ?></td>
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
