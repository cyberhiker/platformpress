<?php
/**
 * Best answer template *
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

if ( ap_have_answers() ) {
	echo '<div id="ap-best-answer">';
		echo '<h3 class="ap-bestans-label"><span>' . __('Best answer', 'platformpress' ) .'</span></h3>';
	while ( ap_have_answers() ) : ap_the_answer();
		include(ap_get_theme_location('answer.php' ) );
	endwhile ;
	echo '</div>';
}
