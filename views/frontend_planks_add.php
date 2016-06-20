  <!--/back-btn--><div class="platformpress-frontend-wrap">
  <div class="back-btn" style="padding-top:8%">
  <!--back-btn-->
  <a class="btn-quaseo" href="<?php echo $this->getBaseUrl(); ?>">
   <i class="fa fa-arrow-left"></i>&nbsp;&nbsp; BACK TO PLANKS</a>
  </div>
	<?php if(isset($_GET['action']) && ($_GET['action']=='update-plank')): ?>
	<h2>Update plank</h2>
	<?php else: ?>
	<h2>Add new plank</h2>
	<?php endif; ?>
	 <?php require_once(PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'platformpress-flash-messages.php'); ?>

	<?php if(!$this->isUserAllowed(get_current_user_id(),'can_add_planks')) : ?>

		<div class="no-access">
		<h4>Access not allowed</h4>
		Sorry, you are not allowed to add a plank.
		</div>

	<?php else: ?>
	<?php
	# relative current URI:
	$url = add_query_arg( NULL, NULL );
	?>
	<div class="form_plank_wrapper"><form id="platformpressform" name="platformpressform" method="post" action="<?php echo $url; ?>">

		<div class="input-row platformpress_wrap_wrapper">
		<label>Plank title</label>
		<input id="plank_title" type="text"  aria-required="true" value="<?php echo (isset($_POST['plank_title'])) ? $_POST['plank_title'] : ""; ?>" name="plank_title">
		</div>

		<br />
		<div class="input-row">
			<label>Plank description</label>
			<?php
			//Remark textarea
			platformpress_editor( array(
				'content' => ( isset( $_POST['plank_description'] ) ? $_POST['plank_description'] : '' ),
				'id' 			=> 'plank_description',
				'textarea_name' => 'plank_description',
			));
			?>
		</div>
		<br />
        <div class="input-row">
        	<label>Plank Topic</label>

            <?php
            $terms = get_terms( 'topic', array(
    		          'orderby'    => 'count',
                      'hide_empty' => 0,
                  )  );

            if ( ! is_wp_error( $terms ) ){
                echo '<select name="plank_category" id="plank_category">';
                echo '<option></option>';
                foreach ( $terms as $term ) {
                    echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';
                }
                echo '</select>';
            }

            else {
		              echo 'Categories have not been created.';
                  }
            ?>
        </div>

		<?php if((!current_user_can( 'edit_platformpress-plank' ))): ?>
		<?php if((platformpress_setting_get("auto_approve_new_planks")=="0")): ?>
		<p>Note: Your plank will reviewed before publishing.</p>
		<?php endif; ?>
		<?php endif; ?>

		<div class="input-row platformpress_submit_button">
			<input type="submit" value="Submit" id="submit" />
		</div>
	</form></div>
	<?php endif; ?>

</div>
<?php
	wp_enqueue_script(
		'jquery-validate',
		plugin_dir_url( __FILE__ ) . '../js/jquery.validate.min.js',
		array('jquery'),
		'1.10.0',
		true
	);
	wp_enqueue_script(
		'jquery-slugify',
		plugin_dir_url( __FILE__ ) . '../js/jquery.slugify.js',
		array('jquery'),
		'1.10.0',
		true
	);
	wp_enqueue_script(
		'platformpress-addques',
		plugin_dir_url( __FILE__ ) . '../js/platformpress.addques.js',
		array('jquery'),
		'1.10.0',
		true
	);
?>
