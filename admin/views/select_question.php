<?php
/**
 * Control the output of question select
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

?>
<div id="ap-admin-dashboard" class="wrap">
	<?php do_action('ap_before_admin_page_title') ?>

	<h2><?php _e('Select a question for new answer', 'platformpress') ?></h2>
	<p><?php _e('Slowly type for question suggestion and then click select button right to question title.', 'platformpress') ?></p>

	<?php do_action('ap_after_admin_page_title') ?>

	<div class="ap-admin-container">
		<form class="question-selection">
			<input type="text" name="question_id" class="ap-select-question" id="select-question-for-answer" />
			<input type="hidden" name="is_admin" value="true" />
		</form>
		<div id="similar_suggestions">
		</div>
	</div>

</div>
