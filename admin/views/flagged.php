<?php
/**
 * Control the output of flagged page
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

?>
<div class="wrap">
	<div id="apicon-users" class="icon32"><br/></div>
	<h2><?php esc_attr_e( 'Flagged question & answer', 'platformpress' ); ?></h2>
	<?php do_action( 'ap_after_admin_page_title' ) ?>
	<form id="flagged-filter" method="get">
		<input type="hidden" name="page" value="<?php echo sanitize_text_field( $_REQUEST['page'] ); ?>" />
		<?php $flagged_table->views() ?>
		<?php $flagged_table->advanced_filters(); ?>
		<?php $flagged_table->display() ?>
	</form>
</div>
