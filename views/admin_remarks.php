<div class="wrap">

	<?php if(($action=="") || ($action=="delete")): ?>
	<h2>Manage Answers</h2>
	<?php endif; ?>

	<?php if(($action=="add") || ($action=="edit")): ?>
	<h2><?php echo ($action=="add") ? "Add new remark" : "Editing remark"; ?></h2>
	<?php endif; ?>
	
	<h2 class="nav-tab-wrapper">
		<?php
		$params = array('page'=>'platformpress-plugin-remarks');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo (($action=="") || ($action=="add") || ($action=="edit") || ($action=="delete")) ? "nav-tab-active" : ""; ?>" title="Manage all remarks" href="<?php echo $url; ?>">Manage remarks</a>
	</h2>

	<?php if(($action=="") || ($action=="delete")): ?>
	<?php
	platformpress_flash_get();
	global $wpdb;
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 10;
	$offset = ( $pagenum - 1 ) * $limit;
	$entries = $wpdb->get_results( "SELECT mcl_platformpress_remarks.*,mcl_platformpress_planks.plank_title,mcl_platformpress_planks.plank_slug FROM mcl_platformpress_remarks 
	INNER JOIN mcl_platformpress_planks ON(mcl_platformpress_remarks.plank_id=mcl_platformpress_planks.id)
	ORDER BY id DESC LIMIT $offset, $limit" );
	echo '<div class="wrap">';
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name">Answer</th>
				<th scope="col" class="manage-column column-name">User & post time</th>
				<th scope="col" class="manage-column column-name"></th>
			</tr>
		</thead>

		<tbody>
			<?php if( $entries ) { ?>

				<?php
				$count = 1;
				$class = '';
				foreach( $entries as $entry ) {
					$class = ( $count % 2 == 0 ) ? ' class="alternate"' : '';
				?>

				<tr <?php echo $class; ?>>
					<td style="width:40%">
						<strong><a title="View <?php echo esc_html($entry->plank_title); ?>" target="blank" href="<?php echo $this->getQuestionUrl($entry); ?>"><?php echo esc_html($entry->plank_title); ?></a></strong><br />
						<strong>Answer:</strong> 
						<?php 
						$wordlimit = 50;
						echo substr($entry->remark_content,0,$wordlimit);
						echo (strlen($entry->remark_content)>$wordlimit) ? "..." : "";
						?>
					</td>
					<td>
					<?php 
					$user_id = $entry->wp_users_id;
					if($user_id>0){
						$userData =  get_userdata($user_id);
						echo "<div style=\"float:left; margin-right:10px;\">";
							echo platformpress_avatar($user_id, 32 );
						echo "</div>";
							echo ucfirst(esc_html($userData->data->display_name))."<br />";
							echo human_time_diff( strtotime($entry->created_at), current_time('timestamp') ) . ' ago';
					} else{
						echo "n/a";
					}
					?>
					</td>					
					<td>
						<?php
						$params = array('page'=>'platformpress-plugin-remarks','action'=>'edit','id'=>$entry->id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a href="<?php echo $url; ?>">
						<img src="<?php echo PLATFORMPRESS_PLUGIN_IMAGES_URL; ?>edit.png" width="16" height="16" />
						</a>
						<?php
						$params = array('page'=>'platformpress-plugin-remarks','action'=>'delete','id'=>$entry->id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a href="<?php echo $url; ?>">
						<img src="<?php echo PLATFORMPRESS_PLUGIN_IMAGES_URL; ?>trash.png" width="16" height="16" />
						</a>
						<?php
						$params = array('page'=>'platformpress-plugin-remarks','action'=>'add','plank_id'=>$entry->plank_id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a title="Add your remark" href="<?php echo $url; ?>">
						<img src="<?php echo PLATFORMPRESS_PLUGIN_IMAGES_URL; ?>create.png" width="16" height="16" />
						</a>
					</td>
				</tr>

				<?php
					$count++;
				}
				?>

			<?php } else { ?>
			<tr>
				<td colspan="2">No posts yet</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>

	<?php

	$total = $wpdb->get_var( "SELECT COUNT(id) FROM mcl_platformpress_remarks" );
	$num_of_pages = ceil( $total / $limit );
	$page_links = paginate_links( array(
		'base' => add_query_arg( 'pagenum', '%#%' ),
		'format' => '',
		'prev_text' => __( '&laquo;', 'aag' ),
		'next_text' => __( '&raquo;', 'aag' ),
		'total' => $num_of_pages,
		'current' => $pagenum
	) );

	if ( $page_links ) {
		echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
	}

	echo '</div>';
	?>
	<?php endif; ?>
	

	<?php if(($action=="add") || ($action=="edit")): ?>
	
	<?php platformpress_flash_get(); ?>
	
	
	<form class="validate" id="platformpressform" name="createuser" method="post">
	<table class="form-table">
		<tbody>

		<tr>
			<th scope="row">Question</th>
			<td><h3><?php echo esc_html($_POST['plank_title']); ?></h3></td>
			<input type="hidden" name="plank_id" value="<?php echo (int)($_GET["plank_id"]); ?>" />
		</tr>
		
		<tr>
			<th scope="row">Answer description</th>
			<td>
			<?php 
			//Answer textarea
			platformpress_editor( array( 
				'content' => ( isset( $_POST['remark_content'] ) ? wp_kses_data( $_POST['remark_content'] ) : '' ),
				'id' 			=> 'remark_content', 
				'textarea_name' => 'remark_content',
				'media_buttons' => false,
			));
			?>
			</td>
		</tr>	

		<tr>
			<th scope="row">Answered by</th>
			<td>
			<?php
			$args = array(
			'name'=> 'wp_users_id',
			'selected'=> (isset($_POST['wp_users_id'])) ? $_POST['wp_users_id'] : "",
			);		
			wp_dropdown_users( $args ); 
			?> 
			</td>
		</tr>
		
		
			
		
		</tbody></table>
		<p class="submit">
		<input type="submit" value="<?php echo ($action=="add") ? "Create new remark" : "Update remark"; ?>" class="button button-primary" name="platformpress-submitted">
		</p>
	</form>
	<?php endif; ?>
	
</div><!--/wrap-->