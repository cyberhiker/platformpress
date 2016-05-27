<?php
/**
 * PlatformPress breadcrumbs widget
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
class PlatformPress_Breadcrumbs_Widget extends WP_Widget {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		parent::__construct(
			'ap_breadcrumbs_widget',
			__( '(PlatformPress) Breadcrumbs', 'platformpress' ),
			array( 'description' => __( 'Show current platformpress page navigation', 'platformpress' ) )
		);
	}

	/**
	 * Output widget
	 * @param  array $args     Widget arguments.
	 * @param  array $instance Widget instance.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		ap_breadcrumbs();
		echo $args['after_widget'];
	}
}

/**
 * Register breadcrumbs widget
 */
function register_platformpress_breadcrumbs() {
	register_widget( 'PlatformPress_Breadcrumbs_Widget' );
}
add_action( 'widgets_init', 'register_platformpress_breadcrumbs' );
