<?php
/**
 * Display question list header
 * Shows sorting, search, tags, category filter form. Also shows a comment button.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
?>

<div class="ap-list-head clearfix">
	<div class="row">
		<div class="col-md-6 col-sm-12">
			<?php ap_get_template_part('search-form'); ?>
		</div>
		<div class="col-md-6 col-sm-12">
			<form id="ap-filter" class="ap-filter clearfix">
				<?php ap_list_filters(); ?>
				<a id="ap-question-sorting-reset" href="#" title="<?php _e('Reset sorting and filter', 'platformpress'); ?>"><?php echo ap_icon('x', true); ?></a>
			</form>
			<?php
				// Hide comment button if user page
				if( !is_ap_user() ){
					ap_comment_btn();
				}
			?>
		</div>
	</div>
</div>
