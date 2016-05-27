<?php

 $clearfix_class = array('clearfix');

?>

<article id="post-0" <?php post_class($clearfix_class); ?>>
	<div class="no-questions">
		<?php _e('No question commented yet!, be the first to comment a question.', 'platformpress'); ?>		
		<?php ap_comment_btn() ?>
	</div>
</article><!-- list item -->
