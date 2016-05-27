<?php
/**
 * PlatformPress product license *
 * Handle licences of PlatformPress products.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	// Load updater.
	include( dirname( __FILE__ ) . '/updater.php' );
}

class AP_License{
	public function __construct() {
		add_action( 'ap_admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'ap_plugin_updater' ), 0 );
	}

	/**
	 * Show license menu if license field is registered.
	 */
	public function menu() {
		$fields = ap_product_license_fields();
		if ( ! empty( $fields ) ) {
			$count = ' <span class="update-plugins count"><span class="plugin-count">'.number_format_i18n( count($fields ) ).'</span></span>';
			add_submenu_page( 'platformpress', __( 'Licenses', 'platformpress' ), __( 'Licenses', 'platformpress' ).$count, 'manage_options', 'platformpress_licenses', array( $this, 'display_plugin_licenses' ) );
		}
	}

	/**
	 * Display license page.
	 */
	public function display_plugin_licenses() {
		include_once( 'views/licenses.php' );
	}

	public static function ap_product_license() {

		if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['ap_licenses_nonce'], 'ap_licenses_nonce' ) ) {
			return;
		}

		$licenses = get_option( 'platformpress_license', array() );
		$fields = ap_product_license_fields();

		if ( empty($fields ) ) {
			return;
		}

		if ( isset( $_POST['save_licenses'] ) ) {
			foreach ( $fields as $slug => $prod ) {
				if ( isset( $_POST['ap_license_'.$slug] ) && $licenses[$slug]['key'] != sanitize_text_field( $_POST['ap_license_'.$slug] ) ) {

					$licenses[$slug] = array(
						'key' => trim(sanitize_text_field( wp_unslash( $_POST['ap_license_'.$slug] ) ) ),
						'status' => false,
					);
					update_option( 'platformpress_license', $licenses );
				}
			}
		}

		foreach ( $fields as $slug => $prod ) {

			// Data to send in our API request.
			$api_params = array(
				'license' 	=> $licenses[$slug]['key'],
				'item_name' => urlencode( $prod['name'] ),
				'url'       => home_url(),
			);

			// Check if activate is clicked.
			if ( isset( $_POST['ap_license_activate_'.$slug] ) ) {

				$api_params['edd_action'] = 'activate_license';

				// Call the custom API.
				$response = wp_remote_post( 'https://platformpress.io', array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

				// Make sure the response came back okay.
				if ( ! is_wp_error( $response ) ) {
					// Decode the license data.
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

					$licenses[$slug]['status'] = sanitize_text_field( $license_data->license );
					update_option( 'platformpress_license', $licenses );
				}
			}

			// Check if deactivate is clicked.
			if ( isset( $_POST['ap_license_deactivate_'.$slug] ) ) {
				$api_params['edd_action'] = 'deactivate_license';

				// Call the custom API.
				$response = wp_remote_post( 'https://platformpress.io', array( 'timeout' => 15, 'sslverify' => true, 'body' => $api_params ) );

				// Make sure the response came back okay.
				if ( ! is_wp_error( $response ) ) {
					// Decode the license data.
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );
					$licenses[$slug]['status'] = sanitize_text_field( $license_data->license );
					update_option( 'platformpress_license', $licenses );
				}
			}
		}
	}

	/**
	 * Initiate product updater.
	 */
	public function ap_plugin_updater() {
		$fields = ap_product_license_fields();
		$licenses = get_option( 'platformpress_license', array() );

		if ( ! empty($fields ) ) {
			foreach ( $fields as $slug => $prod ) {
				if ( isset( $licenses[ $slug ] ) && ! empty($licenses[ $slug ]['key'] ) ) {
					new EDD_SL_Plugin_Updater( 'https://platformpress.io', __FILE__, array(
							'version' 	=> ! empty( $prod['version'] ) ? $prod['version'] : '',
							'license' 	=> $licenses[ $slug ]['key'],
							'item_name' => ! empty( $prod['name'] ) ? $prod['name'] : '',
							'author' 	=> ! empty($prod['author'] ) ? $prod['author'] : '',
						)
					);
				}
			}
		}
	}

}

/**
 * PlatformPress product licenses
 * @return array
 * @since 2.4.5
 */
function ap_product_license_fields() {
	return apply_filters( 'platformpress_license_fields', array() );
}
