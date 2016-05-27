<?php
/**
 * Political Platform Development Plugin.
 *
 * The most advance community question and answer system for WordPress
 *
 * @contributor Rahul Aryan <support@rahularyan.com>
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1

 *
 * @wordpress-plugin
 * Plugin Name:       PlatformPress
 * Plugin URI:        http://platformpress.io
 * Description:       A political platform devevelopment tool for WordPress
 * Version:           1.0-alpha
 * Contributor:       Rahul Aryan
 * Contributor URI:   http://anspress.io
 * Text Domain:       platformpress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: platformpress/platformpress
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Check if using required PHP version.
if ( version_compare(PHP_VERSION, '5.5.0' ) < 0 ) {
	function ap_admin_php_version__error() {
		$class = 'notice notice-error';
		$message = '<strong>'. __('PlatformPress is not running!', 'platformpress' ) .'</strong><br />';
		$message .= sprintf( __( 'Irks! At least PHP version 5.5 is required to run PlatformPress. Current PHP version is %s. Please comment hosting provider to update your PHP version.', 'platformpress' ), PHP_VERSION );
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
	add_action( 'admin_notices', 'ap_admin_php_version__error' );
	return;
}

if ( ! class_exists( 'PlatformPress' ) ) {

	/**
	 * Main PlatformPress class.
	 */
	class PlatformPress
	{
		/**
		 * PlatformPress version
		 * @var string
		 */
	    private $_plugin_version = '3.0.0-alpha.2';

	    /**
	     * Class instance
	     * @var object
	     */
	    public static $instance = null;

	    /**
	     * PlatformPress hooks
	     * @var object Register all PlatformPress hooks
	     */
	    public $platformpress_hooks;

	    /**
	     * PlatformPress ajax
	     * @var object Register all PlatformPress ajax hooks
	     */
	    public $platformpress_ajax;
	    public $admin_ajax;

	    /**
	     * PlatformPress pages
	     * @var array All PlatformPress pages
	     */
	    public $pages;

	    /**
	     * PlatformPress users pages
	     * @var array PlatformPress user pages
	     */
	    public $user_pages;

	    /**
	     * PlatformPress user
	     * @var object PlatformPress users loop
	     */
	    public $users;

	    /**
	     * PlatformPress menu
	     * @var array PlatformPress menu
	     */
	    public $menu;

	    /**
	     * PlatformPress question loop
	     * @var object PlatformPress question query loop
	     */
	    public $questions;

	    /**
	     * PlatformPress answers loop
	     * @var object Answer query loop
	     */
	    public $answers;

	    /**
	     * PlatformPress form
	     * @var object PlatformPress form
	     */
	    public $form;

	    /**
	     * PlatformPress reputation
	     * @var object
	     */
	    public $reputations;

		/**
		 * The array of actions registered with WordPress.
		 * @since    1.0.0
		 * @var array The actions registered with WordPress to fire when the plugin loads.
		 */
		protected $actions;

		/**
		 * The array of filters registered with WordPress.
		 * @since    1.0.0
		 * @var array The filters registered with WordPress to fire when the plugin loads.
		 */
		protected $filters;

		/**
		 * Filter object.
		 * @var object
		 */
		public $platformpress_query_filter;

		/**
		 * Post type object.
		 * @var object
		 * @since 2.0.1
		 */
		public $platformpress_cpt;

		/**
		 * PlatformPress form object
		 * @var object
		 */
	    public $platformpress_forms;

	    public $platformpress_reputation;
	    public $platformpress_bp;
	    public $third_party;
	    public $history_class;
	    public $mention_hooks;
	    public $views_class;
	    public $bad_words_class;

		/**
		 * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
		 *
		 * @return instance
		 */
		public static function instance() {
		    if ( ! isset( self::$instance ) && ! (self::$instance instanceof self) ) {

		        self::$instance = new self();
		        self::$instance->setup_constants();
		        self::$instance->actions = array();
		        self::$instance->filters = array();

		        add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

		        add_action( 'bp_loaded', array( self::$instance, 'bp_include' ) );

		        global $ap_classes;
		        $ap_classes = array();

		        self::$instance->includes();

		        self::$instance->ajax_hooks();
		        self::$instance->site_include();

		        self::$instance->platformpress_forms 		= new PlatformPress_Process_Form();
		        self::$instance->platformpress_query_filter 	= new PlatformPress_Query_Filter();
		        self::$instance->platformpress_cpt 			= new PlatformPress_PostTypes();
		        self::$instance->platformpress_reputation 	= new AP_Reputation();

				/*
                 * ACTION: platformpress_loaded
                 * Hooks for extension to load their codes after PlatformPress is leaded
				 */
				do_action( 'platformpress_loaded' );

		        self::$instance->setup_hooks();
		    }

		    return self::$instance;
		}

		/**
		 * Setup plugin constants.
		 * @since  2.0.1
		 */
		private function setup_constants() {

		    $constants = array(
				'DS' 						=> DIRECTORY_SEPARATOR,
				'AP_VERSION' 				=> $this->_plugin_version,
				'AP_DB_VERSION' 			=> 19,
				'platformpress_DIR' 				=> plugin_dir_path( __FILE__ ),
				'platformpress_URL' 				=> plugin_dir_url( __FILE__ ),
				'platformpress_WIDGET_DIR' 		=> plugin_dir_path( __FILE__ ).'widgets'.DIRECTORY_SEPARATOR,
				'platformpress_THEME_DIR' 		=> plugin_dir_path( __FILE__ ).'theme',
				'platformpress_THEME_URL' 		=> plugin_dir_url( __FILE__ ).'theme',
				'platformpress_VOTE_META' 		=> '_ap_vote',
				'platformpress_SUBSCRIBER_META' 	=> '_ap_subscriber',
				'platformpress_CLOSE_META' 		=> '_ap_close',
				'platformpress_FLAG_META' 		=> '_ap_flag',
				'platformpress_VIEW_META' 		=> '_views',
				'platformpress_UPDATED_META' 	=> '_ap_updated',
				'platformpress_ANS_META' 		=> '_ap_answers',
				'platformpress_SELECTED_META' 	=> '_ap_selected',
				'platformpress_BEST_META' 		=> '_ap_best_answer',
				'platformpress_PARTI_META' 		=> '_ap_participants',
			);

		    foreach ( $constants as $k => $val ) {
		        if ( ! defined( $k ) ) {
		            define( $k, $val );
		        }
		    }
		}

		/**
		 * Include required files.
		 * @since 2.0.1
		 */
		private function includes() {
		    global $ap_options;

		    require_once platformpress_DIR.'includes/class/form.php';
		    require_once platformpress_DIR.'includes/class/validation.php';
		    require_once platformpress_DIR.'includes/class/roles-cap.php';
		    require_once platformpress_DIR.'includes/class/activity.php';

		    require_once platformpress_DIR.'includes/common-pages.php';
		    require_once platformpress_DIR.'includes/class-user.php';
		    require_once platformpress_DIR.'includes/class-theme.php';
		    require_once platformpress_DIR.'admin/platformpress-admin.php';
		    require_once platformpress_DIR.'admin/ajax.php';
		    require_once platformpress_DIR.'includes/options.php';
		    require_once platformpress_DIR.'includes/functions.php';
		    require_once platformpress_DIR.'includes/hooks.php';
		    require_once platformpress_DIR.'includes/ajax-hooks.php';

		    require_once platformpress_DIR.'includes/question-loop.php';
		    require_once platformpress_DIR.'includes/answer-loop.php';

		    require_once platformpress_DIR.'includes/post_types.php';
		    require_once platformpress_DIR.'includes/query_filter.php';
		    require_once platformpress_DIR.'includes/post_status.php';
		    require_once platformpress_DIR.'includes/meta.php';
		    require_once platformpress_DIR.'includes/vote.php';
		    require_once platformpress_DIR.'includes/view.php';
		    require_once platformpress_DIR.'includes/theme.php';
		    require_once platformpress_DIR.'includes/form.php';
		    require_once platformpress_DIR.'includes/participants.php';
		    require_once platformpress_DIR.'includes/activity-hooks.php';
		    require_once platformpress_DIR.'includes/shortcode-basepage.php';

		    require_once platformpress_DIR.'includes/process-form.php';
		    require_once platformpress_DIR.'includes/comment-form.php';
		    require_once platformpress_DIR.'includes/answer-form.php';
		    require_once platformpress_DIR.'widgets/search.php';
		    require_once platformpress_DIR.'widgets/subscribe.php';
		    require_once platformpress_DIR.'widgets/question_stats.php';
		    require_once platformpress_DIR.'widgets/related_questions.php';
		    require_once platformpress_DIR.'widgets/questions.php';
		    require_once platformpress_DIR.'widgets/breadcrumbs.php';
		    require_once platformpress_DIR.'widgets/followers.php';
		    require_once platformpress_DIR.'widgets/user_notification.php';
		    require_once platformpress_DIR.'widgets/users.php';
		    require_once platformpress_DIR.'includes/rewrite.php';
		    require_once platformpress_DIR.'includes/reputation.php';
		    require_once platformpress_DIR.'includes/bad-words.php';

		    require_once platformpress_DIR.'includes/user.php';
		    require_once platformpress_DIR.'includes/users-loop.php';
		    require_once platformpress_DIR.'includes/deprecated.php';
		    require_once platformpress_DIR.'includes/user-fields.php';
		    require_once platformpress_DIR.'includes/subscriber.php';
		    require_once platformpress_DIR.'includes/follow.php';
		    require_once platformpress_DIR.'includes/notification.php';
		    require_once platformpress_DIR.'widgets/user.php';
		    require_once platformpress_DIR.'widgets/comment-form.php';
		    require_once platformpress_DIR.'includes/3rd-party.php';
		    require_once platformpress_DIR.'includes/flag.php';

		    require_once platformpress_DIR.'includes/subscriber-hooks.php';
		    require_once platformpress_DIR.'includes/shortcode-question.php';
		    require_once platformpress_DIR.'includes/mention.php';
		    require_once platformpress_DIR.'includes/akismet.php';
		    require_once platformpress_DIR.'includes/comments.php';
		    //require_once platformpress_DIR.'includes/api.php';
		}

		/**
		 * Load translations.
		 * @since 2.0.1
		 */
		public function load_textdomain() {
		    $locale = apply_filters( 'plugin_locale', get_locale(), 'platformpress' );
		    $loaded = load_textdomain( 'platformpress', trailingslashit( WP_LANG_DIR ).'platformpress'.'/'.'platformpress'.'-'.$locale.'.mo' );

		    if ( $loaded ) {
		        return $loaded;
		    } else {
		        load_plugin_textdomain( 'platformpress', false, basename( dirname( __FILE__ ) ).'/languages/' );
		    }
		}

		/**
		 * Register ajax hooks
		 */
		public function ajax_hooks() {
			// Load ajax hooks only if DOING_AJAX defined.
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		    	self::$instance->platformpress_ajax = new PlatformPress_Ajax( );
		    	self::$instance->admin_ajax = new PlatformPress_Admin_Ajax( );
			}
		}

		/**
		 * Include all public classes
		 */
		public function site_include() {
		    self::$instance->platformpress_hooks 	= PlatformPress_Hooks::init();
	    	self::$instance->history_class 		= new PlatformPress_Activity_Hook( );
	    	self::$instance->mention_hooks 		= new AP_Mentions_Hooks( );
	    	self::$instance->views_class 		= new AP_Views( );
	    	self::$instance->bad_words_class 	= new AP_Bad_words( );
		}

		/**
		 * Include BuddyPress hooks and files
		 */
	    public function bp_include() {
	        if ( ! class_exists( 'BuddyPress' ) ) {
	            return;
	        }

	        require_once platformpress_DIR.'includes/bp.php';
	        self::$instance->platformpress_bp = new PlatformPress_BP();
	    }

		/**
		 * Add a new action to the collection to be registered with WordPress.
		 *
		 * @since    2.4
		 *
		 * @param string            $hook          The name of the WordPress action that is being registered.
		 * @param object            $component     A reference to the instance of the object on which the action is defined.
		 * @param string            $callback      The name of the function definition on the $component.
		 * @param int      Optional $priority      The priority at which the function should be fired.
		 * @param int      Optional $accepted_args The number of arguments that should be passed to the $callback.
		 */
		public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		    $this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
		}

		/**
		 * Add a new filter to the collection to be registered with WordPress.
		 *
		 * @since    2.4
		 *
		 * @param string            $hook          The name of the WordPress filter that is being registered.
		 * @param object            $component     A reference to the instance of the object on which the filter is defined.
		 * @param string            $callback      The name of the function definition on the $component.
		 * @param int      Optional $priority      The priority at which the function should be fired.
		 * @param int      Optional $accepted_args The number of arguments that should be passed to the $callback.
		 */
		public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1) {
		    $this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
		}

		/**
		 * A utility function that is used to register the actions and hooks into a single
		 * collection.
		 *
		 * @since    2.4
		 *
		 * @param array             $hooks         The collection of hooks that is being registered (that is, actions or filters).
		 * @param string            $hook          The name of the WordPress filter that is being registered.
		 * @param object            $component     A reference to the instance of the object on which the filter is defined.
		 * @param string            $callback      The name of the function definition on the $component.
		 * @param int      Optional $priority      The priority at which the function should be fired.
		 * @param int      Optional $accepted_args The number of arguments that should be passed to the $callback.
		 * @param integer           $priority
		 * @param integer           $accepted_args
		 *
		 * @return type The collection of actions and filters registered with WordPress.
		 */
		private function add($hooks, $hook, $component, $callback, $priority, $accepted_args) {
		    $hooks[] = array(
				'hook' => $hook,
				'component' => $component,
				'callback' => $callback,
				'priority' => $priority,
				'accepted_args' => $accepted_args,
			);

		    return $hooks;
		}

		/**
		 * Register the filters and actions with WordPress.
		 */
		private function setup_hooks() {
		    foreach ( $this->filters as $hook ) {
		        add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		    }

		    foreach ( $this->actions as $hook ) {
		        add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		    }
		}
	}
}

/**
 * Run PlatformPress thingy
 * @return object
 */
if ( ! function_exists('platformpress' ) ) {
	function platformpress() {
		return PlatformPress::instance();
	}
}

if ( ! class_exists( 'PlatformPress_Init' ) ) {
	class PlatformPress_Init{
		public static function load_platformpress() {
			/*
             * ACTION: before_loading_platformpress
             * Action before loading PlatformPress.
             * @since 2.4.7
			 */
			do_action( 'before_loading_platformpress' );
			platformpress();
		}

		/**
		 * Delete a cpt posts. Used by PlatformPress uninstaller.
		 * @param  string $type Accepted args question or answer.
		 * @since  3.0.0
		 */
		public static function delete_cpt( $type = 'question' ) {
			global $wpdb;
			$count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM $wpdb->posts WHERE post_type = '%s'", $type ) );

			$deleted = 0;

			while ( $deleted <= $count ) {
				$question_IDS = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = '%s' LIMIT 50", $type ) );

				foreach ( (array) $question_IDS as $ID ) {
					wp_delete_post( $ID, true );
					$deleted++;
				}
			}
		}

		/**
		 * Plugin un-installation hook, called by WP while removing PlatformPress
		 */
		public static function platformpress_uninstall() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}

			check_admin_referer( 'bulk-plugins' );

			if ( ! ap_opt( 'db_cleanup' ) ) {
				return;
			}

			global $wpdb;

			// Remove question CPT.
			SELF::delete_cpt();

			// Removes answer CPT.
			SELF::delete_cpt( 'answer' );

			// remove tables
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ap_meta" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ap_activity" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ap_activitymeta" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ap_notifications" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}ap_subscribers" );

			// remove option
			delete_option( 'platformpress_opt' );
			delete_option( 'ap_reputation' );

			// Remove user roles
			AP_Roles::remove_roles();
		}

		/**
		 * Before activation redirect
		 * @param  string $plugin Plugin base name.
		 */
		public static function activation_redirect($plugin) {
			if ( $plugin == plugin_basename( __FILE__ ) ) {
				add_option('platformpress_do_installation_redirect', true );
			}
		}

		/**
		 * Creating table whenever a new blog is created
		 * @param  integer $blog_id Blog id.
		 * @param  integer $user_id User id.
		 * @param  string  $domain  Domain.
		 * @param  string  $path    Path.
		 * @param  integer $site_id Site id.
		 * @param  array   $meta    Site meta.
		 */
		public static function create_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {
			if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
				switch_to_blog( $blog_id );
				AP_Activate::get_instance( true );
				restore_current_blog();
			}
		}

		/**
		 * Deleting the table whenever a blog is deleted
		 * @param  array $tables Table names.
		 * @return array
		 */
		public static function drop_blog_tables( $tables, $blog_id ) {
			if ( empty( $blog_id ) || 1 == $blog_id || $blog_id != $GLOBALS['blog_id'] ) {
				return $tables;
			}

			global $wpdb;

			$tables[] 	= $wpdb->prefix . 'ap_meta';
			$tables[] 	= $wpdb->prefix . 'ap_meta';
			$tables[] 	= $wpdb->prefix . 'ap_activity';
			$tables[] 	= $wpdb->prefix . 'ap_activitymeta';
			$tables[] 	= $wpdb->prefix . 'ap_notifications';
			$tables[]	= $wpdb->prefix . 'ap_subscribers';
			return $tables;
		}

		/**
		 * Redirect to about PlatformPress page after activating PlatformPress.
		 * @since 3.0.0
		 */
		public static function redirect_to_about_page() {
			if ( get_option( 'platformpress_do_installation_redirect' ) ) {
				delete_option( 'platformpress_do_installation_redirect' );
				exit( wp_redirect( admin_url( 'admin.php?page=platformpress_about' ) ) );
			}
		}
	}
}


add_action( 'plugins_loaded', [ 'PlatformPress_Init', 'load_platformpress' ] );
add_action( 'activated_plugin', [ 'PlatformPress_Init', 'activation_redirect' ] );
add_action( 'wpmu_new_blog', [ 'PlatformPress_Init', 'create_blog' ], 10, 6 );
add_filter( 'wpmu_drop_tables', [ 'PlatformPress_Init', 'drop_blog_tables' ], 10, 2 );
add_filter( 'admin_init', [ 'PlatformPress_Init', 'redirect_to_about_page' ] );
//add_action( 'rest_api_init', ['PlatformPress_API', 'register'] );

/*
 * Dashboard and Administrative Functionality
 */
if ( is_admin() ) {
	add_action( 'plugins_loaded', [ 'PlatformPress_Admin', 'get_instance' ] );
}


/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
require_once dirname(__FILE__ ).'/activate.php';
register_activation_hook( __FILE__, [ 'AP_Activate', 'get_instance' ] );
register_uninstall_hook( __FILE__, [ 'PlatformPress_Init', 'platformpress_uninstall' ] );
