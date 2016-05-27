<?php
	/**
	 * Display answer in user page
	 *
     * @package     PlatformPress
     * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
     * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
     * @since       0.1
	 */
	if(!ap_user_can_view_post(get_the_ID()))
	return;

	global $post;
?>


<div class="ap-answer-post clearfix">
	<a class="ap-vote-count ap-tip" href="#" title="<?php _e('Total votes', 'platformpress') ?>"><span><?php echo ap_net_vote() ?></span><?php _e('Votes', 'platformpress') ?></a>
	<div class="ap-ans-content no-overflow">

		<a class="ap-answer-link" href="<?php echo get_permalink() ?>"><?php echo ap_truncate_chars(strip_tags(get_the_content()), 150) ?></a>
		<ul class="ap-display-question-meta ap-ul-inline">
			<?php echo ap_display_answer_metas(); ?>
		</ul>
	</div>
</div>
