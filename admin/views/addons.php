<?php
	/**
	 * Show lists of available extensions from PlatformPress server
     * @package     PlatformPress
     * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     * @since       0.1
	 */

	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die;
	}
	$extensions = new PlatformPress_Extensions;
?>
<div class="wrap">
	<h2>
		<?php _e('PlatformPress Extensions', 'platformpress'); ?></span>
	</h2>
	<form method="post" action="" id="plugin-filter">
		<input type="hidden" value="/platformpress/wp-admin/plugin-install.php?tab=search&amp;s=search" name="_wp_http_referer">
			<div class="wp-list-table widefat plugin-install">
				<div id="the-list">
					<?php $extensions->extensions_lists() ?>
				</div>
			</div>
	</form>
</div>
