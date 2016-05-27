<?php
/**
 * PlatformPress participants question
 * Widget for showing participants button
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

class PlatformPress_Participants_Widget extends WP_Widget {
	/**
	 * Initialize the class
	 */
	public function __construct() {
		parent::__construct(
			'PlatformPress_Participants_Widget',
			__( '(PlatformPress) Participants', 'platformpress' ),
			array( 'description' => __( 'Show question participants', 'platformpress' ) )
		);

	}

	public function widget( $args, $instance ) {
		$title 			= apply_filters( 'widget_title', $instance['title'] );
		$avatar_size 	= $instance['avatar_size'];

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		ap_get_all_parti( $avatar_size );
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title 			= isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$avatar_size 	= isset( $instance[ 'avatar_size' ] ) ? $instance[ 'avatar_size' ] : 30;

		?>
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'platformpress' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e( 'Avatar size:', 'platformpress' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" type="text" value="<?php echo esc_attr( $avatar_size ); ?>">
        </p>
		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['avatar_size'] = ( ! empty( $new_instance['avatar_size'] ) ) ? (int) $new_instance['avatar_size'] : 30;

		return $instance;
	}
}

function ap_participants_register_widgets() {
	register_widget( 'PlatformPress_Participants_Widget' );
}

add_action( 'widgets_init', 'ap_participants_register_widgets' );
