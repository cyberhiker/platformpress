<?php
/**
 * Class for PlatformPress base page shortcode
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Class for PlatformPress base page shortcode
 */
class PlatformPress_BasePage_Shortcode {

	protected static $instance = null;

	public static function get_instance() {

		// create an object
		null === self::$instance && self::$instance = new self;

		return self::$instance; // return the object
	}

	/**
	 * Control the output of [platformpress] shortcode
	 *
	 * @param  array  $atts  {
	 *     Attributes of the shortcode.
	 *
	 *     $categories 			slug of question_category
	 *     $tags 				slug of question_tag
	 *     $tax_relation 		taxonomy relation, see here http://codex.wordpress.org/Taxonomies
	 *     $tags_operator 		operator for question_tag taxnomomy
	 *     $categories_operator operator for question_category taxnomomy
	 *     $hide_list_head 		Hide list head?
	 *     $sortby 				Sort by.
	 *  }
	 * @param  string $content
	 * @return string
	 * @since 2.0.0
	 * @since  3.0.0 Added new attribute `hide_list_head` and `attr_sortby`.
	 */
	public function platformpress_sc( $atts, $content = '' ) {
		global $questions, $ap_shortcode_loaded;

		// Check if PlatformPress shortcode already loaded.
		if ( true === $ap_shortcode_loaded ) {
			return __('PlatformPress shortcode cannot be nested.', 'platformpress' );
		}

		$ap_shortcode_loaded = true;

		$this->attributes($atts, $content );

		ob_start();
		echo '<div id="platformpress">';

			/**
			 * Action is fired before loading PlatformPress body.
			 */
			do_action( 'ap_before' );

			// Include theme file.
			ap_page();

			// Linkback to author.
			if ( ! ap_opt( 'author_credits' ) ) {
				echo '<div class="ap-cradit">' . __( 'Question and answer is powered by', 'platformpress' ). ' <a href="https://platformpress.io" traget="_blank">PlatformPress.io</a>' . '</div>';
			}
		echo '</div>';
		wp_reset_postdata();
		$ap_shortcode_loaded = false;
		return ob_get_clean();
	}

	/**
	 * Get attributes from shortcode and set it as query var.
	 * @since 3.0.0
	 */
	public function attributes( $atts, $content ) {
		global $wp;

		if ( isset( $atts['categories'] ) ) {
			$categories = explode( ',', str_replace( ', ', ',', $atts['categories'] ) );
			$wp->set_query_var( 'ap_categories', $categories );
		}

		if ( isset( $atts['tags'] ) ) {
			$tags = explode( ',', str_replace( ', ', ',', $atts['tags'] ) );
			$wp->set_query_var( 'ap_tags', $tags );
		}

		if ( isset( $atts['tax_relation'] ) ) {
			$tax_relation = $atts['tax_relation'];
			$wp->set_query_var( 'ap_tax_relation', $tax_relation );
		}

		if ( isset( $atts['tags_operator'] ) ) {
			$tags_operator = $atts['tags_operator'];
			$wp->set_query_var( 'ap_tags_operator', $tags_operator );
		}

		if ( isset( $atts['categories_operator'] ) ) {
			$categories_operator = $atts['categories_operator'];
			$wp->set_query_var( 'ap_categories_operator', $categories_operator );
		}

		// Load specefic PlatformPress page.
		if ( isset( $atts['page'] ) ) {
			set_query_var( 'ap_page', $atts['page'] );
			$_GET['ap_page'] = $atts['page'];
		}

		if ( isset( $atts['hide_list_head'] ) ) {
			set_query_var( 'ap_hide_list_head', (bool) $atts['hide_list_head'] );
			$_GET['ap_hide_list_head'] = $atts['hide_list_head'];
		}

		// Sort by.
		if ( isset( $atts['sortby'] ) ) {
			set_query_var( 'ap_sortby',  ap_sanitize_unslash( $atts['sortby'] ) );
			$_GET['ap_sortby'] = $atts['sortby'];
		}
	}

}
