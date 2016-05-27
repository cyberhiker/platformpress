<?php
/**
 * PlatformPress user notifications widget
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

/**
 * PlatformPress breadcrumbs widget.
 */
class PlatformPress_User_Notifications_Widget extends WP_Widget {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		parent::__construct(
			'ap_user_notifications_widget',
			__( '(PlatformPress) User Notifications', 'platformpress' ),
			array( 'description' => __( 'Show logged in user notifications', 'platformpress' ) )
		);
	}

	/**
	 * Output widget
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		global $ap_activities;
        $ap_activities = ap_get_activities( array( 'per_page' => 20, 'notification' => true, 'user_id' => ap_get_displayed_user_id() ) );

		echo $args['before_widget'];

		ap_get_template_part( 'widgets/notifications' );

		echo $args['after_widget'];
	}
}

/**
 * Register breadcrumbs widget
 */
function register_platformpress_user_notifications() {
	register_widget( 'PlatformPress_User_Notifications_Widget' );
}
add_action( 'widgets_init', 'register_platformpress_user_notifications' );
