<?php
/**
 * Class for PlatformPress embed question shortcode
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Class for PlatformPress base page shortcode
 */
class PlatformPress_Question_Shortcode {

	protected static $instance = null;

	public static function get_instance() {

		// create an object
		null === self::$instance && self::$instance = new self;

		return self::$instance; // return the object
	}

	/**
	 * Control the output of [question] shortcode
	 * @param  string $content
	 * @return string
	 * @since 2.0.0-beta
	 */
	public function platformpress_question_sc( $atts, $content='' ) {

		ob_start();
		echo '<div id="platformpress" class="ap-eq">';

		/**
		 * ACTION: ap_before_question_shortcode
		 * Action is fired before loading PlatformPress body.
		 */
		do_action( 'ap_before_question_shortcode' );

		$questions = ap_get_question( $atts['id'] );

		if ( $questions->have_posts() ) {
			/**
			 * Set current question as global post
			 * @since 2.3.3
			 */

			while ( $questions->have_posts() ) : $questions->the_post();
				include( ap_get_theme_location( 'shortcode/question.php' ) );
			endwhile;
		}

		echo '</div>';
		wp_reset_postdata();

		return ob_get_clean();
	}

}
