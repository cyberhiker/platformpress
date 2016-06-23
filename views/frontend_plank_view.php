<div id="frontend_wrap" class="platformpress-frontend-wrap que-view">


  <div class="back-btn">
  <!--back-btn-->
  <a class="btn-quaseo" href="<?php echo $this->getBaseUrl(); ?>">
   <i class="fa fa-arrow-left"></i>&nbsp;&nbsp; BACK TO PLANKS</a>
  </div>
  <!--/back-btn-->


  <div class="platformpress-plank">
  <!--platformpress-plank-->

    <h1><?php echo get_the_title(); ?></h1>

	<?php
	// Plank variables
	$plankAuthoId = $post->post_author;
	$plankId 		= get_the_ID();
	$userData 			= get_userdata($plankAuthoId);
	$plankUrl 		= get_permalink();
	$resolvedRemarkId	= get_post_meta($plankId, 'platformpress_plank_resolved', true);

	//echo "<div class=\"description1\">";
    echo apply_filters('the_content', $post->post_content);
    //echo "</div>";
	?>
    <div class="platformpress-block" id="social_sec">
      <div class="bck-sect">
        <div class="user-img">
		  <?php echo platformpress_avatar($post->post_author,32); ?>
        </div>
      </div>

	  <div class="bck-sect ">

		<div class="user-info">
		  <strong>
			<?php echo ucfirst(esc_html($userData->data->display_name)); ?>
		  </strong>
		  on <?php echo date_i18n('jS F Y',strtotime($post->post_date)); ?>

			<?php if($this->settings['general']['user_id']==$plankAuthoId): ?>
				<?php $editurl = add_query_arg(array('action'=>'update-plank','post_id'=>$plankId),$this->getBaseUrl()); ?>
				<?php $deleteurl = add_query_arg(array('action'=>'delete-plank','post_id'=>$plankId),$this->getBaseUrl()); ?>
				&nbsp;&nbsp;
				<a href="<?php echo $editurl; ?>">Edit</a> |
				<a href="<?php echo $deleteurl; ?>" onclick="return confirm('Are you sure you want to delete this plank?')">Delete</a>
			<?php endif; ?>

		</div>

		<div class="analysis">
		  <ul>
			<li>
			  <i class="fa fa-thumbs-up"></i>
			  <?php $avg = get_post_meta(get_the_ID(), 'platformpress_plank_vote_count', true); ?>
			  <span title="Votes"><?php if($avg != '' ) { echo $avg; } else{ echo "0" ;} ?></span>
			</li>
			<li><i class="fa fa-heart "></i>
				<?php $count = get_post_meta(get_the_ID(), 'platformpress_plank_favorite', true); ?>
			 <span title="Favourites" id="qasefavcount"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>
            </li>
			<li>
			  <i class="fa fa-comment"></i>
				  <?php $count = get_post_meta(get_the_ID(), 'platformpress_remarks_count', true); ?>
				  <span title="Remarks" title="Remarks"><?php if($count != '' ) { echo $count; } else{ echo "0" ;} ?></span>
			</li>

			<li>
			<?php if($this->settings['general']['is_user_logged_in']): ?>
				  <a title="My favorite" onClick="platformpressMarkPlankFavorite(<?php echo $plankId; ?>)" href="javascript:void(0)" class="hear">
				  <i id="platformpress-fav-ques" class="fa fa-heart <?php echo ($this->isPlankMarkedAsFavorite($plankId)) ? "marked-fav-ques" : "" ?>"></i>
				  </a>
			<?php endif; ?>
			</li>

			<?php if($category = $this->post_categories($plankId)): ?>
				<li>
				<i class="fa fa-tag rotate"></i>
				<?php
				$cat_url = add_query_arg(array('cat'=>$category->name),$plank_listing_url);
				?>
				<a href="<?php echo $cat_url; ?>" class="cat_list"><?php echo $category->name; ?></a>
				</li>
			<?php endif; ?>

		  </ul>
		</div>

	  </div>
    </div>

  </div>
  <!--/platformpress-plank-->
  <?php if(($this->settings['stored']['social_locker']!=1)): ?>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3&appId=1642509509312261";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

  <script type="text/javascript">
    (function() {
      var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
      po.src = 'https://apis.google.com/js/platform.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
    })();
  </script>
  <div class="platformpress-social-share-plugins">
  <div class="fb-share-button" data-href="<?php echo $plankUrl; ?>" data-layout="button_count"></div>
  <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $plankUrl; ?>">Tweet</a>
  <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
 </div>
  <?php endif; ?>

  <?php require_once(PLATFORMPRESS_PLUGIN_INCLUDE_PATH.'platformpress-flash-messages.php'); ?>

	<?php $count = get_post_meta(get_the_ID(), 'platformpress_remarks_count', true); ?>

	<?php if($count<1): ?>
	<div class="platformpress-noremark">No remarks found, be first to remark on this plank</div>
	<?php endif; ?>

	<?php if($count >= 1 ): ?>
	<div class="platformpress-block ansblock">
	<div class="bck-sect"><h3><?php echo ($count>1) ? $count." Remarks" : $count." Remark"; ?></h3></div>
	<?php if(($count>1)){ ?>
    <div class="bck-sect">

		<?php
		if(isset($_GET['sort']) && ($_GET['sort']!="")){
		$sort = $_GET['sort'];
		} else{
		$sort = 'newest';
		}
		?>
		<ul class="platformpress-sort">
		<?php
		$params = array('sort'=>'newest');
		$url = esc_url(add_query_arg($params));
		?>
        <li <?php echo ($sort=='newest') ? "class=\"active\"" : "" ?>>
          <a href="<?php echo $url; ?>">Newest first</a>
        </li>
		<?php
		$params = array('sort'=>'vote');
		$url = esc_url(add_query_arg($params));
		?>
        <li <?php echo ($sort=='vote') ? "class=\"active\"" : "" ?>>
          <a href="<?php echo $url; ?>" >Most voted</a>
        </li>
		</ul>
    </div><!--/bck-sect-->
	<?php } ?>

  </div><!--/platformpress-block-->
  <?php endif; ?>


  <script type="text/javascript">
	function platformpressVoteUp(remarkId){

		jQuery(document).ready(function(){

			if(!jQuery("#platformpress-remarks-item-"+remarkId+" .vote_section li").hasClass('platformpress-voted-up')){
				vote_count = jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").text();
				jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html("<i class='fa fa-refresh fa-spin text-mutted'></i>");

				jQuery.ajax({
					method: 'POST',
					data : {remarkId:remarkId,action:'vote-up'},
					dataType : 'JSON',
					success:function(res) {
						if(res.type=='success'){
							jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html(res.vote_count);
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-up").removeClass("platformpress-voted-up");
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-down").removeClass("platformpress-voted-down");
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-up").addClass("platformpress-voted-up");
						} else{
							jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html(vote_count);
							alert(res.message);
						}
					}
				});
			}
		});

	}

    function platformpressPlankVote(plankId){

		jQuery(document).ready(function(){

			if(!jQuery("#platformpress-remarks-item-"+plankId+" .vote_section li").hasClass('platformpress-voted-up')){
				vote_count = jQuery("#platformpress-remarks-item-"+plankId+" li span.votes").text();
				jQuery("#platformpress-remarks-item-"+plankId+" li span.votes").html("<i class='fa fa-refresh fa-spin text-mutted'></i>");

				jQuery.ajax({
					method: 'POST',
					data : {plankId:plankId,action:'vote-up'},
					dataType : 'JSON',
					success:function(res) {
						if(res.type=='success'){
							jQuery("#platformpress-remarks-item-"+plankId+" li span.votes").html(res.vote_count);
							jQuery("#platformpress-remarks-item-"+plankId+" li.vote-up").removeClass("platformpress-voted-up");
							jQuery("#platformpress-remarks-item-"+plankId+" li.vote-down").removeClass("platformpress-voted-down");
							jQuery("#platformpress-remarks-item-"+plankId+" li.vote-up").addClass("platformpress-voted-up");
						} else{
							jQuery("#platformpress-remarks-item-"+plankId+" li span.votes").html(vote_count);
							alert(res.message);
						}
					}
				});
			}
		});

	}
    /*
	function platformpressVoteDown(remarkId){
		jQuery(document).ready(function(){
			if(!jQuery("#platformpress-remarks-item-"+remarkId+" .vote_section li").hasClass('platformpress-voted-down')){
				vote_count = jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").text();
				jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html("<i class='fa fa-refresh fa-spin text-mutted'></i>");
				jQuery.ajax({
					method: 'POST',
					data : {remarkId:remarkId,action:'vote-down'},
					dataType : 'JSON',
					success:function(res) {
						if(res.type=='success'){
							jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html(res.vote_count);
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-up").removeClass("platformpress-voted-up");
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-down").removeClass("platformpress-voted-down");
							jQuery("#platformpress-remarks-item-"+remarkId+" li.vote-down").addClass("platformpress-voted-down");
						} else{
							jQuery("#platformpress-remarks-item-"+remarkId+" li span.votes").html(vote_count);
							alert(res.message);
						}
					}
				});
			}
		});
	}*/

	function platformpressMarkPlankFavorite(plankId){
		jQuery(document).ready(function(){
			//if already fav plank then remove
			if(jQuery("#platformpress-fav-ques").hasClass('marked-fav-ques')){
			 var actionPerform = "remove";
			} else{
			 var actionPerform = "add";
			}
			jQuery.ajax({
				method: 'POST',
				data : {plankId:plankId,action:'mark-plank-favorite',actionPerform:actionPerform},
				dataType : 'JSON',
				success:function(res) {

					if(res.type=='success'){
						if(res.actionPerformed=='added'){
							jQuery("#platformpress-fav-ques").addClass('marked-fav-ques');
						} else{
							jQuery("#platformpress-fav-ques").removeClass('marked-fav-ques');
						}
						jQuery("#qasefavcount").text(res.fav_count);
					} else{
						alert(res.message);
					}

				}
			});
		});
	}

	function platformpressMarkPlankResolved(plankId,remarkId){
		jQuery(document).ready(function(){
			jQuery.ajax({
				method: 'POST',
				data : {plankId:plankId,remarkId:remarkId,action:'mark-plank-resolved'},
				dataType : 'JSON',
				success:function(res) {

					if(res.type=='success'){
						jQuery(".platformpress-remarks-item .mark-resolved").html('');
						jQuery("#platformpress-remarks-item-"+remarkId+" .mark-resolved").html('<a title="Marked as resolved" class="plank-resolved" href="javascript:void(0)"><i class="fa fa-check plank-resolved"></i></a>');
					} else{
						alert(res.message);
					}

				}
			});
		});
	}
	</script>

	<div class="platformpress-remarks-list">

		<!--platformpress-remarks-list-->
		<?php
		if(isset($_GET['sort']) && ($_GET['sort']=='vote')){
			$query = array(
				'post_type' 	=> 'platformpress-remark',
				'post_parent' 	=> get_the_ID(),
				'meta_key' 		=> 'platformpress_remark_vote_count',
				'orderby' 		=> 'meta_value_num',
				'order' 		=> 'DESC'
			);
		} else{
			$query = array(
				'post_type' 	=> 'platformpress-remark',
				'post_parent' 	=> get_the_ID(),
				'order_by' 		=> array('ID'),
				'order' 		=> 'DESC'
			);
		}
		query_posts($query);
		?>
		<?php while (have_posts()) : the_post(); ?>
			<?php $userData =  get_userdata($post->post_author); ?>
			<?php require('frontend_remarks_list.php'); ?>
		<?php endwhile; ?>
		<?php wp_reset_query(); ?>
	</div>
	<!-- End platformpress-remarks-list-->



	<?php if($this->settings['general']['is_user_logged_in']): ?>
		<?php if(!$this->isUserAllowed(get_current_user_id(),'can_remark_planks')){ ?>
		<div class="no-access">
			<h4>Access not allowed</h4>
			Sorry, you are not allowed to remark on planks.
		</div>
		<?php }else{ ?>
		<div class="remark-heading"><h4>Remark on this plank</h4></div>
		<div class="remark_wrapper"><form id="platformpressform" method="post" action="<?php echo $plankUrl;?>">
		<div class="input-row">
		<?php
		//Remark textarea
		platformpress_editor( array(
		'content' => ( isset( $_POST['platformpressremarkcontent'] ) ? wp_kses_data( $_POST['platformpressremarkcontent'] ) : '' ),
		'id' => 'platformpressremarkcontent',
		'textarea_name' => 'platformpressremarkcontent',
		'media_buttons' => true,
		) );
		?>
		</div>

		<div class="input-row"><input id="ans" type="submit" value="Submit" /></div>
		</form></div>
		<?php } ?>
  <?php else: //NOT is_user_logged_in ?>
  <?php require_once PLATFORMPRESS_PLUGIN_VIEW_PATH.'/_frontend_login_form.php'; ?>
  <?php endif; ?>

</div>

<?php
wp_enqueue_script(
	'jquery-validate',
	plugin_dir_url( __FILE__ ) . '../js/jquery.validate.min.js',
	array('jquery'),
	'1.10.0',
	true
);
if(($this->settings['stored']['social_locker']==1)){
	wp_enqueue_script(
		'jquery-social-locker',
		plugin_dir_url( __FILE__ ) . '../js/social-locker.js',
		array('jquery'),
		'1.10.0',
		true
	);
}
wp_enqueue_script(
	'platformpress-viewques',
	plugin_dir_url( __FILE__ ) . '../js/platformpress.viewques.js',
	array('jquery'),
	'1.10.0',
	true
);
?>
