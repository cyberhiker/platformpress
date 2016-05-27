<?php
/**
 * Control the output of reputation page
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
    <h2>
		<?php esc_attr_e( 'PlatformPress Reputation', 'platformpress' ); ?>
		<a class="add-new-h2" href="#" data-button="ap-new-reputation"><?php esc_attr_e( 'New reputation', 'platformpress' ); ?></a>
    </h2>
    <form id="platformpress-reputation-table" method="get">
		<input type="hidden" name="page" value="<?php echo sanitize_text_field( @$_GET['page'] ); ?>" />
		<?php $reputation_table->display() ?>
    </form>
</div>
