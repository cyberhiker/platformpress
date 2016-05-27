<?php
/**
 * This file contains theme script, styles and other theme related functions.
 * This file can be overridden by creating a platformpress directory in active theme folder.
 *
 * @package     PlatformPress
 * @copyright   Copyright (c) 2013, Rahul Aryan; Copyright (c) 2016, Chris Burton
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Enqueue scripts.
 */
add_action( 'wp_enqueue_scripts', 'ap_scripts_front', 1 );
function ap_scripts_front() {
	if ( ! is_platformpress() && ap_opt('load_assets_in_platformpress_only' ) ) {
		return;
	}

	ap_enqueue_scripts();

	$custom_css = '
        #platformpress .ap-q-cells{
            margin-'.(is_rtl()? 'right' : 'left').': '.(ap_opt( 'avatar_size_qquestion' ) + 10).'px;
        }
        #platformpress .ap-a-cells{
            margin-'.(is_rtl()? 'right' : 'left').': '.(ap_opt( 'avatar_size_qanswer' ) + 10).'px;
        }#platformpress .ap-comment-content{
            margin-'.(is_rtl()? 'right' : 'left').': '.(ap_opt( 'avatar_size_qcomment' ) + 15).'px;
        }';

	wp_add_inline_style( 'ap-theme-css', $custom_css );
	do_action( 'ap_enqueue' );
	wp_enqueue_style( 'ap-overrides', ap_get_theme_url( 'css/overrides.css' ), array( 'ap-theme-css' ), AP_VERSION );

	echo '<script type="text/javascript">';
		echo 'var ajaxurl = "'.admin_url( 'admin-ajax.php' ).'",';
		echo 'ap_nonce 	= "'.wp_create_nonce( 'ap_ajax_nonce' ).'",';
	    echo 'ap_max_tags = "'.ap_opt( 'max_tags' ).'",';
	    echo 'disable_hover_card = "'.(ap_opt( 'disable_hover_card' ) ? true : false).'";';
	    echo 'disable_q_suggestion = "'. ap_disable_question_suggestion( ) .'";';
	    echo 'var apMentions = '.json_encode( ap_search_mentions() ).';
	    	var cachequeryMentions = [], itemsMentions,
	    	at_config = {
		      at: "@",
		      data: apMentions,
		      headerTpl: \'<div class="atwho-header">Member List<small>↑&nbsp;↓&nbsp;</small></div>\',
		      insertTpl: "@${login}",
		      displayTpl: \'<li data-value="${login}">${name} <small>@${login}</small></li>\',
		      limit: 50,
		      callbacks: {
			    remoteFilter: function (query, render_view) {
                    var thisVal = query,
                    self = jQuery(this);
                    if( !self.data("active") && thisVal.length >= 2 ){
                        self.data("active", true);
                        itemsMentions = cachequeryMentions[thisVal]
                        if(typeof itemsMentions == "object"){
                            render_view(itemsMentions);
                        }else
                        {
                            if (self.xhr) {
                                self.xhr.abort();
                            }
                            self.xhr = jQuery.getJSON(ajaxurl+"?ap_ajax_action=search_mentions&action=ap_ajax&ap_ajax_nonce="+ap_nonce,{
                                term: thisVal
                            }, function(data) {
                                cachequeryMentions[thisVal] = data
                                render_view(data);
                            });
                        }
                        self.data("active", false);
                    }
                }
			  }
		 };
	    ';
	echo '</script>';

	wp_localize_script('platformpress-js', 'aplang', array(
		'password_field_not_macthing' => __( 'Password not matching', 'platformpress' ),
		'password_length_less' => __( 'Password length must be 6 or higher', 'platformpress' ),
		'not_valid_email' => __( 'Not a valid email', 'platformpress' ),
		'username_less' => __( 'Username length must be 4 or higher', 'platformpress' ),
		'username_not_avilable' => __( 'Username not available', 'platformpress' ),
		'email_already_in_use' => sprintf( __( 'Email already in use. %sDo you want to reset your password?%s', 'platformpress' ), '<a href="'.wp_lostpassword_url().'">', '</a>' ),
		'loading' => __( 'Loading', 'platformpress' ),
		'sending' => __( 'Sending request', 'platformpress' ),
		'adding_to_fav' => __( 'Adding question to your favorites', 'platformpress' ),
		'voting_on_post' => __( 'Sending your vote', 'platformpress' ),
		'requesting_for_closing' => __( 'Requesting for closing this question', 'platformpress' ),
		'sending_request' => __( 'Submitting request', 'platformpress' ),
		'loading_comment_form' => __( 'Loading comment form', 'platformpress' ),
		'submitting_your_question' => __( 'Sending your question', 'platformpress' ),
		'submitting_your_answer' => __( 'Sending your answer', 'platformpress' ),
		'submitting_your_comment' => __( 'Sending your comment', 'platformpress' ),
		'deleting_comment' => __( 'Deleting comment', 'platformpress' ),
		'updating_comment' => __( 'Updating comment', 'platformpress' ),
		'loading_form' => __( 'Loading form', 'platformpress' ),
		'saving_labels' => __( 'Saving labels', 'platformpress' ),
		'loading_suggestions' => __( 'Loading suggestions', 'platformpress' ),
		'uploading_cover' => __( 'Uploading cover', 'platformpress' ),
		'saving_profile' => __( 'Saving profile', 'platformpress' ),
		'sending_message' => __( 'Sending message', 'platformpress' ),
		'loading_conversation' => __( 'Loading conversation', 'platformpress' ),
		'loading_new_message_form' => __( 'Loading new message form', 'platformpress' ),
		'loading_more_conversations' => __( 'Loading more conversations', 'platformpress' ),
		'searching_conversations' => __( 'Searching conversations', 'platformpress' ),
		'loading_message_edit_form' => __( 'Loading message form', 'platformpress' ),
		'updating_message' => __( 'Updating message', 'platformpress' ),
		'deleting_message' => __( 'Deleting message', 'platformpress' ),
		'uploading' => __( 'Uploading', 'platformpress' ),
		'error' => ap_icon( 'error' ),
		'warning' => ap_icon( 'warning' ),
		'success' => ap_icon( 'success' ),
		'not_valid_response' => __( 'Something went wrong in server side, not a valid response.', 'platformpress' ),
	));

	wp_localize_script('ap-site-js', 'apoptions', array(
			'ajaxlogin' => ap_opt( 'ajax_login' ),
		));
}

if ( ! function_exists( 'ap_comment' ) ) :
	function ap_comment($comment) {
	    $GLOBALS['comment'] = $comment;
	    include ap_get_theme_location( 'comment.php' );
	}
endif;

add_action( 'widgets_init', 'ap_widgets_positions' );
function ap_widgets_positions() {

	register_sidebar(array(
		'name' => __( 'AP Before', 'platformpress' ),
		'id' => 'ap-before',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown before platformpress body.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP Lists Top', 'platformpress' ),
		'id' => 'ap-top',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown before questions list.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP Sidebar', 'platformpress' ),
		'id' => 'ap-sidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in PlatformPress sidebar.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP Question Sidebar', 'platformpress' ),
		'id' => 'ap-qsidebar',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in question page sidebar.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP Category Page', 'platformpress' ),
		'id' => 'ap-category',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in category listing page.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP Tag page', 'platformpress' ),
		'id' => 'ap-tag',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in tag listing page.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));

	register_sidebar(array(
		'name' => __( 'AP User', 'platformpress' ),
		'id' => 'ap-user',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown in user page.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name' => __( 'AP Activity', 'platformpress' ),
		'id' => 'ap-activity',
		'before_widget' => '<div id="%1$s" class="ap-widget-pos %2$s">',
		'after_widget' => '</div>',
		'description' => __( 'Widgets in this area will be shown PlatformPress activity page.', 'platformpress' ),
		'before_title' => '<h3 class="ap-widget-title">',
		'after_title' => '</h3>',
	));
}
