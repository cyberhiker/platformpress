<?php

/**
 * Edit page
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

if($editing_post->post_type == 'question')
	ap_edit_question_form();
elseif($editing_post->post_type == 'answer')
	ap_edit_answer_form($editing_post->post_parent);
