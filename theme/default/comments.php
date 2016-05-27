<?php
/**
 * This template is used for displaying comment list.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
if ( post_password_required() || (ap_opt('logged_in_can_see_comment' ) && ! is_user_logged_in()) ) {
	return;
}

?>

<?php if ( have_comments() ) : ?>
	<?php wp_list_comments(array( 'max_depth' => 0, 'callback' => 'ap_comment', 'style' => 'li' ) ); ?>
	<?php if ( get_comment_pages_count() > 1 && get_option('page_comments' ) ) : ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
			<div class="nav-previous"><?php previous_comments_link(__('&larr; Older Comments', 'platformpress' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link(__('Newer Comments &rarr;', 'platformpress' ) ); ?></div>
		</nav>
	<?php endif; ?>
<?php endif; ?>
