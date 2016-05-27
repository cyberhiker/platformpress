<?php
/**
 * PlatformPress followers widget.
 * Register followers widget in WP.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Register followers widget in WP.
 */
class AP_followers_Widget extends WP_Widget {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		parent::__construct(
			'ap_followers_widget',
			__( '(PlatformPress) Followers', 'platformpress' ),
			array( 'description' => __( 'Show followers of currently displayed user.', 'platformpress' ) )
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = $instance['number'] ;
		$avatar_size = $instance['avatar_size'] ;

		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo '<div class="ap-widget-inner">';
		if(is_ap_user()){
			$followers = ap_has_users(array('user_id' => ap_get_displayed_user_id(), 'sortby' => 'followers' ));
	        if($followers->has_users()){
	            include ap_get_theme_location('widgets/followers.php');
	        }
	        else{
	            _e('No followers yet', 'platformpress');
	        }
	    }else{
	    	_e('This widget can only be used in user page.', 'platformpress');
	    }
	    echo '</div>';

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Followers', 'platformpress' );
		}
		$avatar_size 		= 30;
		$number 			= 20;

		if ( isset( $instance[ 'avatar_size' ] ) )
			$avatar = $instance[ 'avatar_size' ];

		if ( isset( $instance[ 'number' ] ) )
			$number = $instance[ 'number' ];

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'platformpress' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'avatar_size' ); ?>"><?php _e( 'Avatar size:', 'platformpress' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'avatar_size' ); ?>" name="<?php echo $this->get_field_name( 'avatar_size' ); ?>" type="text" value="<?php echo esc_attr( $avatar_size ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Numbers of user to show:', 'platformpress' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : 20;
		$instance['avatar_size'] = ( ! empty( $new_instance['avatar_size'] ) ) ? strip_tags( $new_instance['avatar_size'] ) : 30;

		return $instance;
	}
}

function ap_followers_register_widgets() {
	register_widget( 'AP_followers_Widget' );
}

add_action( 'widgets_init', 'ap_followers_register_widgets' );
