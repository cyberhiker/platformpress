<?php
/**
 * PlatformPress options page
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( isset( $_POST['__nonce'] ) && wp_verify_nonce( $_POST['__nonce'], 'nonce_option_form' ) && current_user_can( 'manage_options' ) ) {
	flush_rewrite_rules();
	$options = $_POST['platformpress_opt'];

	$settings = get_option( 'platformpress_opt', array() );

	foreach ( (array) $options as $k => $opt ) {
		$value = implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $opt ) ) );
		$settings[ $k ] = wp_unslash( $value );
	}

	update_option( 'platformpress_opt', $settings );
	wp_cache_delete( 'ap_opt', 'options' );
	$_POST['platformpress_opt_updated'] = true;
}

new PlatformPress_Options_Fields();

/**
 * platformpress option navigation
 * @var array
 */
?>

<div id="platformpress" class="wrap">
    <h2 class="admin-title">
		<?php _e( 'PlatformPress Options', 'platformpress' ); ?>
        <a href="http://github.com/platformpress/platformpress" target="_blank">GitHub</a>
        <a href="https://wordpress.org/plugins/platformpress/" target="_blank">WordPress.org</a>
        <a href="https://twitter.com/platformpress_io" target="_blank">@platformpress_io</a>
        <a href="https://www.facebook.com/wp.platformpress" target="_blank">Facebook</a>
    </h2>

	<?php if ( ap_isset_post_value('platformpress_opt_updated') === true ) : ?>
		<div class="updated fade"><p><strong><?php _e( 'PlatformPress options updated', 'platformpress' ); ?></strong></p></div>
	<?php endif; // If the form has just been submitted, this shows the notification ?>

    <div class="ap-wrap">
        <div class="platformpress-options ap-wrap-left clearfix">
            <div class="option-nav-tab clearfix">
				<?php ap_options_nav(); ?>
            </div>
            <div class="ap-group-options">
				<?php ap_option_group_fields(); ?>
            </div>
        </div>
        <?php include_once( 'sidebar.php' ); ?>
    </div>

</div>
