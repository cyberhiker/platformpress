<?php
/**
 * PlatformPress image upload modal
 * Handle image uploading and importing from url
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
?>

<div class="ap-mediam">
	<div class="ap-mediam-types">
		<div class="ap-mediam-pc ap-mediam-type clerafix">
			<?php echo ap_icon('cloud-upload', true); ?>
			<?php _e('Upload from computer', 'platformpress'); ?>
		</div>
		<div class="ap-mediam-pc ap-mediam-type clerafix">
			<?php echo ap_icon('globe', true); ?>
			<?php _e('Image from link', 'platformpress'); ?>
		</div>
	</div>
</div>
