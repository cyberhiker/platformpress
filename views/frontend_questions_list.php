<?php
$totalQuestions 	= $wp_query->found_posts;
?>

<div id="frontend_wrap" class="qbot-frontend-wrap">

  <div id="qbot_top" class="qbot-block">
   <!--qbot_top-->
    <div class="bck-sect">
      <a href="<?php echo $question_listing_url; ?>"><h2>All Questions</h2></a>
	  <?php 
	  if(isset($_GET['cat']) && $_GET['cat']!=""){
		echo "<h4>Displaying results for category \"".$_GET['cat']."\"</h4>";
	  }
	  ?>
    </div>
    <div class="bck-sect">
		<?php $url = add_query_arg(array('action'=>'add-new-question'),get_permalink()); ?>
		<a id="post_ques" href="<?php echo $url; ?>"> Post a Question</a>
    </div>
    <!--/qbot_top-->
  </div>
  
    <?php require_once(QBOT_PLUGIN_INCLUDE_PATH.'qbot-flash-messages.php'); ?>	

  
  <?php if($totalQuestions>0){ ?>
  <div class="qbot-main-search">
	<form role="search" method="get" class="search-form" action="<?php echo $question_listing_url; ?>">
		<label>
			<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span>
			<input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search …', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="qbot-search" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
		</label>
		<input type="submit" class="" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>" />
	</form>  
	</div>
	<?php } ?>
  
	<?php if($totalQuestions>0){ ?>
	<?php
	if(isset($_GET['sort']) && ($_GET['sort']!="")){
	$sort = $_GET['sort'];
	} else{
	$sort = 'newest';
	}
	
	if(isset($_GET['filter']) && ($_GET['filter']!="")){
	$filter = $_GET['filter'];
	} else{
	$filter = '';
	}
	
	?>
  <ul class="qbot-sort">
	<?php if($filter!=="private"): //don't show sorting option in case of private questions ?>
    <?php
	$params = array('sort'=>'newest');
	$url = esc_url(add_query_arg($params));
	?>
    <li <?php echo ($sort=='newest') ? "class=\"active\"" : "" ?>>
      <a href="<?php echo $url; ?>">Newest first</a>
    </li>
    <?php
	$params = array('sort'=>'view');
	$url = esc_url(add_query_arg($params));
	?>
    <li <?php echo ($sort=='view') ? "class=\"active\"" : "" ?>>
      <a href="<?php echo $url; ?>">View</a>
    </li>
    <?php
	$params = array('sort'=>'answer');
	$url = esc_url(add_query_arg($params));
	?>
    <li <?php echo ($sort=='answer') ? "class=\"active\"" : "" ?>>
      <a href="<?php echo $url; ?>" >Most Answered</a>
    </li>
    <?php
	$params = array('sort'=>'vote');
	$url = esc_url(add_query_arg($params));
	?>
	
    <li <?php echo ($sort=='vote') ? "class=\"active\"" : "" ?>>
      <a href="<?php echo $url; ?>" >Vote</a>
    </li>
	   <?php
	$params = array('sort'=>'favourite');
	$url = esc_url(add_query_arg($params));
	?>
    <li <?php echo ($sort=='favourite') ? "class=\"active\"" : "" ?>>
      <a href="<?php echo $url; ?>" >Most Favourite</a>
    </li>
   <?php
	$params = array('filter'=>'private');
	$url = esc_url(add_query_arg($params));
	?>
	<?php endif; ?>
	
  </ul>
  <?php } else{ ?>
  <h3>No result</h3>
  <?php } ?>

	
  
  <div class="qbot-questions-list">
    <?php while (have_posts()) : the_post(); ?>
	<?php
	// Question variables
	$questionAuthoId = $post->post_author;
	$questionId 		= get_the_ID();
	$userData 			= get_userdata($questionAuthoId);				
	$questionUrl 		= get_permalink();
	$resolvedAnswerId	= get_post_meta($questionId, 'qbot_question_resolved', true);
	?>	
    <div class="qbot-questions-item">
      <div class="ques">
        <h3><a href="<?php echo $questionUrl; ?>"><?php echo esc_html(get_the_title()); ?></a></h3>
        <p>
			<?php 
			$wordlimit = 256;
			echo strip_tags(substr(get_the_content(),0,$wordlimit));
			echo (strlen(get_the_content())>$wordlimit) ? "......" : "";
			?>	
		</p>
						
        <div id="social_sec" class="qbot-block">
          <div class="bck-sect">
            <div class="user-img">
				<?php echo qbot_avatar($questionAuthoId,32); ?>
            </div>
          </div>
          <div class="bck-sect ">
		  
            <div class="user-info">
              <strong>
				<?php echo ucfirst(esc_html($userData->data->display_name)); ?>
			  </strong>
			  on <?php echo date_i18n('jS F Y',strtotime($post->post_date)); ?>

			<?php if($this->settings['general']['user_id']==$questionAuthoId): ?>
				<?php $editurl = add_query_arg(array('action'=>'update-question','post_id'=>$questionId),$this->getBaseUrl()); ?>
				<?php $deleteurl = add_query_arg(array('action'=>'delete-question','post_id'=>$questionId),$this->getBaseUrl()); ?>
				&nbsp;&nbsp;
				<a href="<?php echo $editurl; ?>">Edit</a> | 
				<a href="<?php echo $deleteurl; ?>" onclick="return confirm('Are you sure you want to delete this question?')">Delete</a>
			<?php endif; ?>			  
			  
			  
            </div>
           
            <div class="analysis">
              <ul>
				<li><i class="fa fa-eye"></i>
				  <?php $avg = get_post_meta(get_the_ID(), 'qbot_views_count', true); ?>
				  <span title="Views"><?php if($avg != '' ) { echo $avg; } else{ echo "0" ;} ?></span>
                </li>
                <li>
                  <i class="fa fa-thumbs-up"></i>
				  <?php $avg = get_post_meta(get_the_ID(), 'qbot_question_vote_count', true); ?>
				  <span title="Votes"><?php if($avg != '' ) { echo $avg; } else{ echo "0" ;} ?></span>
                </li>
				<li><i class="fa fa-heart "></i>
					<?php $count = get_post_meta(get_the_ID(), 'qbot_question_favorite', true); ?>
				 <span title="Favourites" id="qasefavcount"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>
				</li>
				<li>
				  <i class="fa fa-comment"></i>
				  <?php $count = get_post_meta(get_the_ID(), 'qbot_answers_count', true); ?>
				  <span title="Answers"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>
				</li>
				
				<?php if($category = $this->post_categories($questionId)): ?>
					<li>
					<i class="fa fa-tag rotate"></i>
					<?php
					$cat_url = add_query_arg(array('cat'=>$category->name),$question_listing_url);
					?>					
					<a href="<?php echo $cat_url; ?>" class="cat_list"><?php echo esc_html($category->name); ?></a>
					</li>
				<?php endif; ?>
		
					<?php
					$attachmentId = get_post_meta($questionId, 'qbot_question_attachment', true);
					?>
					<?php if(($attachmentId!=='') && is_numeric($attachmentId) && ($attachmentId>0)): ?>
					<li>
                    <div class="dowlod-btn">
					<?php 
					$attachment_url = get_permalink().'?action=download&attachmentId='.$attachmentId;
					?>
					<a title="Download attachment" href="<?php echo $attachment_url ?>"><i class="fa fa-paperclip"></i> Download attachment</a>
                    </div>
					</li>
					<?php endif; ?>
              </ul>
            </div>
            
          </div>
        </div>
        <p>
		
        <?php
		$answer = $this->getLatestAnswer($questionId);
		// if this question have answer, show latest answer
		if($answer){
			$userData =  get_userdata($answer->post_author);
			echo "answered by ";?>
				<strong><?php echo ucfirst(esc_html($userData->data->display_name)); ?></strong>
				<?php
			//echo ucfirst($userData->data->display_name)." ";
			echo "<span class=\"qbot-smallfont\">". human_time_diff( strtotime($answer->post_date), current_time('timestamp') ) . ' ago</span>';		
		}
		?>
        </p>
      </div>
    </div>
	<?php endwhile; ?>
	<?php wp_reset_query(); ?>
  </div>
  
  
<?php
if(isset($totalQuestions)){
bootstrap_pagination($totalQuestions,2);
}
?> 

 
<?php if(!$this->settings['general']['is_user_logged_in']): ?> 
<?php require_once QBOT_PLUGIN_VIEW_PATH.'/_frontend_login_form.php'; ?>
<?php endif; ?>

</div> 



<?php

function bootstrap_pagination($total = '', $range = 2)
{	
	$showitems = ($range * 2)+1;

	global $paged;
	if(empty($paged)) $paged = 1;
	
	$pages  = ceil($total/10);

	if(1 != $pages)
	{
		echo "<div id='qbot_pagenation'><ul>";
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged - 1)."'>Previous</a></li>";
		if($paged > 1 && $showitems < $pages) echo "<li><a href='".get_pagenum_link(1)."'>First</a></li>";

		for ($i=1; $i <= $pages; $i++)
		{
		if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
		{
		echo ($paged == $i)? "<li><a id='current' href='javascript:void(0)'>".$i."</a></li>":"<li><a href='".get_pagenum_link($i)."'>".$i."</a></li>";
		}
		}

		if ($paged < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($paged + 1)."'>Next</a></li>";
		if ($paged < $pages-1 && $paged+$range-1 < $pages && $showitems < $pages) echo "<li><a href='".get_pagenum_link($pages)."'>Last</a></li>";
		echo "</ul></div>\n";
	}
}

?>