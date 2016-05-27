<?php
/**
 * This template is used for displaying comment form.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */
$subscribed = false;
if( is_object( $ap_comment ) ){
	$subscribed = ap_is_user_subscribed( $ap_comment->comment_post_ID, 'comment', $ap_comment->user_id );
}

?>
<div class="ap-avatar ap-pull-left">
	<?php echo get_avatar( get_current_user_id(), 30 ); ?>
</div>
<div class="ap-comment-inner no-overflow">
	<textarea placeholder="<?php _e('Your comment..', 'platformpress' ); ?>" class="ap-form-control autogrow" id="ap-comment-textarea" aria-required="true" rows="3" name="content"><?php echo isset( $ap_comment, $ap_comment->comment_content ) ? $ap_comment->comment_content : ''; ?></textarea>

    <div class="ap-comment-footer clearfix">
        <label>
            <input type="checkbox" value="1" name="notify" <?php checked( $subscribed, true ); ?> />
			<?php _e('Notify me of follow-up comments', 'platformpress' ); ?>
        </label>
		<button type="submit" class="ap-comment-submit ap-btn"><?php _e( 'Submit', 'platformpress' ); ?></button>
		<a data-action="cancel-comment" class="ap-comment-cancel" href="#"><?php _e('Cancel', 'platformpress' ); ?></a>
    </div>
</div>
