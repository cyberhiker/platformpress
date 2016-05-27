<?php
/**
 * Class for platformpress theme
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
 * Holds all hooks related to frontend layout/theme
 */
class PlatformPress_Theme
{
	/**
	 * Function get called on init
	 */
	public static function init_actions() {
		// Register platformpress shortcode.
		add_shortcode( 'platformpress', array( PlatformPress_BasePage_Shortcode::get_instance(), 'platformpress_sc' ) );

		// Register question shortcode.
		add_shortcode( 'question', array( PlatformPress_Question_Shortcode::get_instance(), 'platformpress_question_sc' ) );
	}

	/**
	 * PlatformPress theme function as like WordPress theme function
	 * @return void
	 */
	public static function includes_theme() {
		require_once ap_get_theme_location( 'functions.php' );
	}

	/**
	 * Add answer-seleted class in post_class
	 * @param  array $classes Post class attribute.
	 * @return array
	 * @since 2.0.1
	 */
	public static function question_answer_post_class($classes) {
		global $post;

		if ( 'question' == $post->post_type ) {
			if ( ap_question_best_answer_selected( $post->ID ) ) {
				$classes[] = 'answer-selected';
			}

			if ( ap_is_featured_question( $post->ID ) ) {
				$classes[] = 'featured-question';
			}

			$classes[] = 'answer-count-' . ap_count_answer_meta();
		} elseif ( 'answer' == $post->post_type ) {
			if ( ap_answer_is_best( $post->ID ) ) {
				$classes[] = 'best-answer';
			}
			if ( ! ap_user_can_read_answer( $post ) ) {
				$classes[] = 'no-permission';
			}
		}

		return $classes;
	}

	/**
	 * Add platformpress classess to body
	 * @param  array $classes Body class attribute.
	 * @return array
	 * @since 2.0.1
	 */
	public static function body_class($classes) {
		// Add platformpress class to body.
		if ( is_platformpress() ) {
			$classes[] = 'platformpress';
			$classes[] = 'ap-page-' . ap_current_page();
		}

		return $classes;
	}

	/**
	 * Register PlatformPress comment template
	 * @param  string $comment_template path to comment template.
	 * @return string
	 */
	public static function comment_template($comment_template) {
		global $post;
		if ( $post->post_type == 'question' || $post->post_type == 'answer' ) {
			return ap_get_theme_location( 'comments.php' );
		} else {
			return $comment_template;
		}
	}

	/**
	 * Filter wp_title
	 * @param string $title WP page title.
	 * @return string
	 */
	public static function ap_title($title) {
		if ( is_platformpress() ) {
			remove_filter('wp_title', array(
				$this,
				'ap_title',
			));

			$new_title = ap_page_title();

			if ( strpos( $title, 'platformpress_TITLE' ) !== false ) {
				$new_title = str_replace( 'platformpress_TITLE', $new_title, $title );
			} else {
				$new_title = $new_title.' | ';
			}

			$new_title = apply_filters( 'ap_title', $new_title );

			return $new_title;
		}

		return $title;
	}

	/**
	 * Filter wpseo plugin title
	 * @param  string $title Page title.
	 * @return string
	 */
	public static function wpseo_title($title) {
		if ( is_platformpress() ) {
			remove_filter('wpseo_title', array(
				$this,
				'wpseo_title',
			));

			$new_title = ap_page_title();

			if ( strpos( $title, 'platformpress_TITLE' ) !== false ) {
				$new_title = str_replace( 'platformpress_TITLE', $new_title, $title ). ' | ' . get_bloginfo( 'name' );
			} else {
				$new_title = $new_title.' | '. get_bloginfo( 'name' );
			}

			$new_title = apply_filters( 'ap_wpseo_title', $new_title );

			return $new_title;
		}

		return $title;
	}

	/**
	 * Filter the_title()
	 * @param  string $title Current page/post title.
	 * @param  string $id    Post ID.
	 * @return string
	 */
	public static function the_title($title, $id = null) {

		if ( ap_opt( 'base_page' ) == $id  ) {
			remove_filter('the_title', array(
				$this,
				'the_title',
			));
			return ap_page_title();
		}
		return $title;
	}

	/**
	 * Add feed link in wp_head
	 */
	public static function feed_link() {
		if ( is_platformpress() ) {
			echo '<link href="' . esc_url( home_url( '/feed/question-feed' ) ) . '" title="' . esc_attr__( 'Question Feed', 'platformpress' ) . '" type="application/rss+xml" rel="alternate">';
		}
	}

	/**
	 * Add default before body sidebar in PlatformPress contents
	 */
	public static function ap_before_html_body() {
		dynamic_sidebar( 'ap-before' );
	}

	/**
	 * Remove some unwanted things from wp_head
	 */
	public static function remove_head_items() {
		if ( is_platformpress() ) {
			global $wp_query;

			// Check if quesied object is set, if not then set base page object.
			if ( ! isset( $wp_query->queried_object ) ) {
				$wp_query->queried_object = get_post( ap_opt( 'base_page' ) );
			}

			$wp_query->queried_object->post_title = ap_page_title();
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
			remove_action( 'wp_head', 'rel_canonical' );
			remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
			remove_action( 'wp_head', 'feed_links', 2 );
		}
	}

	/**
	 * Add feed and links in HEAD of the document
	 */
	public static function wp_head() {
		if ( is_platformpress() ) {
			$q_feed = get_post_type_archive_feed_link( 'question' );
			$a_feed = get_post_type_archive_feed_link( 'answer' );
			echo '<link rel="alternate" type="application/rss+xml" title="'.esc_attr__( 'Question feed', 'platformpress' ).'" href="'.esc_url( $q_feed ).'" />';
			echo '<link rel="alternate" type="application/rss+xml" title="'.esc_attr__( 'Answers feed', 'platformpress' ).'" href="'.esc_url( $a_feed ).'" />';
		}

		if ( is_question() && get_query_var( 'ap_page' ) != 'base' ) {
			echo '<link rel="canonical" href="'.esc_url( get_permalink( get_question_id() ) ).'">';
			echo '<link rel="shortlink" href="'.esc_url( wp_get_shortlink( get_question_id() ) ).'" />';
		}
	}

	/**
	 * Update concal link when wpseo plugin installed
	 * @return string
	 */
	public static function wpseo_canonical() {
		if ( is_question() ) {
			return get_permalink( get_question_id() );
		}
	}

	/**
	 * Ajax callback for post actions dropdown
	 * @since 3.0.0
	 */
	public static function post_actions_dp() {
		if ( ! ap_verify_nonce('ap_ajax_nonce' ) || ! isset( $_POST['args'] ) ) {
			ap_ajax_json('something_wrong' );
		}

		$post_id = (int) $_POST['args'][0];

		global $post;
		$post = get_post( $post_id, OBJECT );
		setup_postdata( $post );

		$actions = ap_post_actions();

		$dropdown = array();
		foreach ( (array) $actions['dropdown'] as $sk => $sub ) {
			$dropdown[] = [ 'action' => $sk, 'anchor' => $sub ];
		}

		$data = array(
			'template' => 'dropdown-menu',
			'appendTo' => '#ap_post_action_'.$post_id,
			'do' => [ 'addClass' => [ '#ap_post_action_'.$post_id.' .ap-dropdown-toggle', 'ajax-disabled' ] ],
			'apData' => array(
				'id'	=> $post_id.'_dp',
				'links'			=> $dropdown,
			),
			'key' => $post_id.'Actions',
		);

		ap_ajax_json( $data );
	}

	/**
	 * Ajax callback for list filters
	 * @since 3.0.0
	 */
	public static function list_filter() {
		if ( ! ap_verify_nonce('ap_ajax_nonce' ) || ! isset( $_POST['args'] ) ) {
			ap_ajax_json('something_wrong' );
		}

		$filter = sanitize_text_field( wp_unslash( $_POST['args'][0] ) );

		if( isset( $_POST['current_filter'] ) ){
			$_GET['ap_filter'] = wp_parse_args( wp_unslash( $_POST['current_filter'] ) );
		}

		$filters = ap_get_list_filters( );
		if ( ! empty( $filters[ $filter ] ) ) {
			$apData = $filters[ $filter ];
			$apData['key'] = $filter;
			$apData['placeholder'] = sprintf( __('Filter %s', 'platformpress' ), strtolower( $apData['title'] ) );
			$elm = '#ap-filter .filter-'. esc_attr( $filter );
			$data = array(
				'template' => 'sorting',
				'appendTo' => $elm,
				'do' => [ 'addClass' => [ $elm.' .ap-dropdown-toggle', 'ajax-disabled' ] ],
				'apData' => $apData,
				'key' => $filter.'Filter',
			);
		}

		ap_ajax_json( $data );
	}
}
