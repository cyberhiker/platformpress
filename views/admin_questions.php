<style>
ul.suggest {
  left: 0 !important;
}
</style>
<div class="wrap">

	<?php if(($action=="") || ($action=="delete")): ?>
	<h2>Manage Questions</h2>
	<?php endif; ?>
	
	<?php if(($action=="add") || ($action=="edit")): ?>
	<h2><?php echo ($action=="add") ? "Add new Question" : "Editing question"; ?></h2>
	<?php endif; ?>
	

	<h2 class="nav-tab-wrapper">
		<?php
		$params = array('page'=>'qbot-plugin-questions');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo (($action=="") || ($action=="edit") || ($action=="delete")) ? "nav-tab-active" : ""; ?>" title="Manage all question" href="<?php echo $url; ?>">Manage Questions</a>
		<?php
		$params = array('page'=>'qbot-plugin-questions','action'=>'add');
		$url = esc_url(add_query_arg($params,'admin.php'));
		?>
		<a class="nav-tab <?php echo ($action=="add") ? "nav-tab-active" : ""; ?>" title="Create new question" href="<?php echo $url; ?>">Add new Question</a>
	</h2>

	<?php if(($action=="") || ($action=="delete")): ?>
	
	<?php
	
	qbot_flash_get();
	global $wpdb;
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 10;
	$offset = ( $pagenum - 1 ) * $limit;
	$entries = $wpdb->get_results( "SELECT ques.*,term.name as category_name,term.term_id as category_id,term.slug as category_slug FROM mcl_qbot_questions as ques LEFT JOIN mcl_qbot_term_relationships as rel ON(rel.mcl_qbot_questions_id=ques.id) LEFT JOIN ".$wpdb->prefix."term_taxonomy as tax ON(tax.term_taxonomy_id=rel.term_taxonomy_id) LEFT JOIN ".$wpdb->prefix."terms as term ON(term.term_id=tax.term_id)	
 ORDER BY id DESC LIMIT $offset, $limit" );
	echo '<div class="wrap">';
	?>
	
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col" class="manage-column column-name">Quesions</th>
				<th scope="col" class="manage-column column-name">User & post time</th>
				<th scope="col" class="manage-column column-name">Category</th>
				<th scope="col" class="manage-column column-name"  style="text-align:center">Total Answers</th>
				<th scope="col" class="manage-column column-name" style="text-align:center">Status</th>
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
					<td>
						<strong><a title="View <?php echo esc_html($entry->question_title); ?>" target="blank" href="<?php echo $this->getQuestionUrl($entry); ?>"><?php echo esc_html($entry->question_title); ?></a></strong><br />
						<?php 
						$wordlimit = 50;
						echo substr($entry->question_description,0,$wordlimit);
						echo (strlen($entry->question_description)>$wordlimit) ? "..." : "";
						?>
					</td>
					<td>
					<?php 
					$user_id = $entry->wp_users_id;
					if($user_id>0){
						$userData =  get_userdata($user_id);
						echo "<div style=\"float:left; margin-right:10px;\">";
							echo qbot_avatar($user_id, 32 );
						echo "</div>";
							echo ucfirst(esc_html($userData->data->display_name))."<br />";
							echo human_time_diff( strtotime($entry->created_at), current_time('timestamp') ) . ' ago';
					} else{
						echo "n/a";
					}
					?>
					</td>
					<td><?php 
						echo $list= $entry->category_name; 	
						?></td>
					
					<td style="text-align:center">
					
					<?php
					$qid= $entry->id; 
					$answer = $wpdb->get_row("SELECT COUNT(*) as counts FROM mcl_qbot_answers  WHERE question_id=".$qid);
					echo $answer->counts;
					?>
					
				
				</td>
				
				
				
					<td style="text-align:center">
					<?php $status_icon = ($entry->is_active==1) ? "active.png" : "delete.png"; ?>
					<img src="<?php echo QBOT_PLUGIN_IMAGES_URL; ?><?php echo $status_icon; ?>" width="16" height="16" />
					</td>
					<td>
						<?php
						$params = array('page'=>'qbot-plugin-questions','action'=>'edit','id'=>$entry->id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a href="<?php echo $url; ?>">
						<img src="<?php echo QBOT_PLUGIN_IMAGES_URL; ?>edit.png" width="16" height="16" />
						</a>
						<?php
						$params = array('page'=>'qbot-plugin-questions','action'=>'delete','id'=>$entry->id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a href="<?php echo $url; ?>">
						<img src="<?php echo QBOT_PLUGIN_IMAGES_URL; ?>trash.png" width="16" height="16" />
						</a>
						<?php
						$params = array('page'=>'qbot-plugin-answers','action'=>'add','question_id'=>$entry->id);
						$url = esc_url(add_query_arg($params,'admin.php'));
						?>
						<a title="Add your answer" href="<?php echo $url; ?>">
						<img src="<?php echo QBOT_PLUGIN_IMAGES_URL; ?>create.png" width="16" height="16" />
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

	$total = $wpdb->get_var( "SELECT COUNT(id) FROM mcl_qbot_questions" );
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
	<?php qbot_flash_get(); ?>
	
	<form novalidate="novalidate" class="validate" id="qbotform" name="createuser" method="post">
	<table class="form-table">
		<tbody>
		<tr class="form-field">
			<th scope="row"><label for="user_login">Question <span class="description">(required)</span></label></th>
			<td><input id="question_title" type="text" aria-required="true" value="<?php echo (isset($_POST['question_title'])) ? $_POST['question_title'] : ""; ?>" name="question_title"></td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="user_login">Slug <span class="description">(required)</span></label></th>
			<td><input class="slug" type="text" aria-required="true" value="<?php echo (isset($_POST['question_slug'])) ? $_POST['question_slug'] : ""; ?>" name="question_slug"><br />
			<small>SEO friendly name for question. Example: "my-question"</small>
			</td>
		</tr>
		<tr>
			<th scope="row">is question status Active?</th>
			<?php
			if(isset($_POST['is_active'])){
				if($_POST['is_active']==1){
					$isActive = "checked=\"checked\"";
				}
			} else{
				$isActive = "checked=\"checked\"";
			}
			?>
			<td><label for="qbot_is_active"><input id="qbot_is_active" type="checkbox" value="1" name="is_active" <?php echo $isActive; ?> /> Make this question status active/inactive.</label></td>
		</tr>
		<tr>
			<th scope="row"><label>Category</label></th>
			<td>
			<?php 
			$args = array('taxonomy'=>'qbot-categories','hide_empty'=>0,'exclude' => '11',);
			wp_dropdown_categories( $args ); 
			?> 
	</td></tr>
	
		<tr>
			<th scope="row">Question description</th>
			<td>
			<?php 
			//Answer textarea
			qbot_editor( array( 
				'content' => ( isset( $_POST['question_description'] ) ? wp_kses_data( $_POST['question_description'] ) : '' ),
				'id' 			=> 'question_description', 
				'textarea_name' => 'question_description',
				'media_buttons' => false,
			));
			?>
			</td>
		</tr>	

		<tr>
			<th scope="row">Question posted by</th>
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
		<input type="submit" value="<?php echo ($action=="add") ? "Create new question" : "Update question"; ?>" class="button button-primary" name="qbot-submitted">
		</p>
	</form>
	<?php endif; ?>
	
</div>