<?php
/**
 * Question loop item for user question list in about page.
 *
 * @link https://platformpress.io
 * @since unknown
 * @package PlatformPress
 */
?>

<div class="ap-user-posts-item clearfix">
	<a class="ap-user-posts-vcount ap-tip<?php echo ap_question_best_answer_selected() ? ' answer-selected' :''; ?>" href="<?php ap_question_the_permalink(); ?>" title="<?php esc_attr_e('Votes', 'platformpress' ); ?>">
		<?php echo ap_icon('thumb-up', true ); ?>
		<?php echo ap_question_get_the_net_vote(); ?>
    </a>
    <span class="ap-user-posts-active">
		<?php ap_question_the_active_ago(); ?>
    </span>
	<a class="ap-user-posts-ccount ap-tip" href="<?php ap_question_the_permalink(); ?>" title="<?php esc_attr_e('Answers', 'platformpress' ); ?>">
		<?php printf(__('%d answers', 'platformpress' ), ap_question_get_the_answer_count() ); ?>
    </a>
    <div class="no-overflow">
		<a href="<?php ap_question_the_permalink(); ?>" class="ap-user-posts-title"><?php the_title(); ?></a>
    </div>              
</div>
