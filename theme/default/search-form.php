<?php
/**
 * Template for search form.
 * Different from WP default searchfrom.php. This only search for question and answer.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
?>
<form id="ap-search-form" class="ap-search-form" action="<?php echo ap_get_link_to('search' ); ?>">
	<button class="ap-btn ap-search-btn" type="submit"><?php _e('Search', 'platformpress' ); ?></button>
	<div class="ap-search-inner no-overflow">
	    <input name="ap_s" type="text" class="ap-search-input ap-form-input" placeholder="<?php _e('Search questions...', 'platformpress' ); ?>" value="<?php echo sanitize_text_field( get_query_var('ap_s' ) ); ?>" />
    </div>

</form>
