<?php
/**
 * Control the output of PlatformPress dashboard
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

global $questions;

$question_count = ap_total_posts_count('question');
$answer_count = ap_total_posts_count('answer');
$flagged_count = ap_total_posts_count('both', 'flag');

?>
<div id="platformpress" class="wrap">
	<?php do_action('ap_before_admin_page_title') ?>
	<h2><?php _e('PlatformPress Dashboard', 'platformpress') ?></h2>

	<div class="row ap-dash-tiles">
		<div class="ap-dash-tile col-md-6">
			<div class="ap-dash-tile-in ap-tile platformpress-stats-count">
				<ul class="clearfix">
					<li>
						<a href="<?php echo admin_url( 'edit.php?post_type=question' ); ?>">
							<strong><?php echo $question_count->publish; ?></strong>
							<span><?php _e('Questions', 'platformpress') ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo admin_url( 'edit.php?post_type=answer' ); ?>">
							<strong><?php echo $answer_count->publish; ?></strong>
							<span><?php _e('Answers', 'platformpress') ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo admin_url( 'admin.php?page=platformpress_moderate' ); ?>">
							<strong><?php echo $question_count->moderate + $answer_count->moderate. ($question_count->moderate + $answer_count->moderate > 0 ? '<i class="ap-need-att">i</i>' : ''); ?></strong>
							<span><?php _e('Moderate', 'platformpress') ?></span>
						</a>
					</li>
					<li>
						<a href="<?php echo admin_url( 'admin.php?page=platformpress_flagged' ); ?>">
							<strong><?php echo $flagged_count->total. ($flagged_count->total > 0 ? '<i class="ap-need-att">i</i>' : ''); ?></strong>
							<span><?php _e('Flagged', 'platformpress') ?></span>
						</a>
					</li>
				</ul>
			</div>
			<div class="ap-dash-tile-in ap-dash-questions">
				<h3 class="ap-dash-title"><?php _e('Latest Questions', 'platformpress') ?></h3>
				<?php
					$questions = ap_get_questions(array('sortby' => 'newest'));
					if ( ap_have_questions() ):
				?>
				<div class="ap-user-posts">
					<?php while ( ap_questions() ) : ap_the_question(); ?>
						<div class="ap-user-posts-item clearfix">
							<a class="ap-user-posts-vcount ap-tip<?php echo ap_question_best_answer_selected() ? ' answer-selected' :''; ?>" href="<?php ap_question_the_permalink(); ?>" title="<?php _e('Answers', 'platformpress'); ?>"><?php echo ap_icon('answer', true); ?><?php echo ap_question_get_the_answer_count(); ?></a>
							<span class="ap-user-posts-active"><?php ap_question_the_active_ago(); ?></span>
							<a class="ap-user-posts-ccount ap-tip" href="<?php ap_question_the_permalink(); ?>" title="<?php _e('Comments', 'platformpress'); ?>"><?php echo ap_icon('comment', true); ?><?php echo get_comments_number(); ?></a>
							<div class="no-overflow"><a href="<?php ap_question_the_permalink(); ?>" class="ap-user-posts-title"><?php the_title(); ?></a></div>
						</div>

					<?php
						endwhile;
						wp_reset_postdata();
					?>

					<?php
						else:
							_e('There is no question yet.', 'platformpress');
						endif;
					?>
				</div>
			</div>
		</div>
		<div class="ap-dash-tile col-md-6 ">
			<div class="ap-dash-tile col-md-4 clearfix">
				<?php include_once( 'sidebar.php' ); ?>
			</div>

		</div>

	</div>

</div>
