<?php
/**
 * Plugin Name: Comments Loop
 * Plugin URI: http://ptahdunbar.com/plugins/comments-loop
 * Description: A WordPress widget that gives you unprecendeted control over displaying your comments loop and comment form.
 * Version: 0.1
 * Author: Ptah Dunbar
 * Author URI: http://ptahdunbar.com
 * License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
 *
 *	Copyright 2010 Ptah Dunbar (http://ptahdunbar.com/contact)
 *
 *	    This program is free software; you can redistribute it and/or modify
 *	    it under the terms of the GNU General Public License, version 2, as 
 *	    published by the Free Software Foundation.
 *
 *	    This program is distributed in the hope that it will be useful,
 *	    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	    GNU General Public License for more details.
 *
 * @package Comments_Loop
 */

require_once( 'functions.php' );

// Load up the Comments Loop and Comments Form Widget
add_action( 'widgets_init', 'register_comments_loop_and_form_widget' );

// Fix some CSS styling issues in the admin
add_action( 'admin_head-widgets.php', 'cl_widget_css' );

// Register the WordPress Loop widget
function register_comments_loop_and_form_widget() {
	register_widget( 'Comments_Loop' );
	register_widget( 'Comments_Form' );
}

// Comment reply js
if ( !is_admin() and is_singular() and get_option( 'thread_comments' ) and comments_open() )
	wp_enqueue_script( 'comment-reply' );

/**
 * Comments Loop Widget
 *
 * @since 0.1
 */
class Comments_Loop extends WP_Widget {
	
	function Comments_Loop() {
		$widget_ops = array( 'classname' => 'cmnt-loop', 'description' => __( 'Display customized Comment loops', 'comments-loop' ) );
		$control_ops = array( 'width' => 520 /*835 with no borders*/, 'height' => 350, 'id_base' => 'comments-loop' );
		$this->WP_Widget( 'comments-loop', 'Comments Loop', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		global $wpdb, $wp_query, $post, $user_ID;
				
		// local variables, soon to be populated
		global $comment, $overridden_cpage;
		
		// Will not display the comments template if not on single post or page, or if the post does not have comments.
		if ( !is_singular() OR empty($post) OR '0' == $post->comment_count )
			return false;

		// Comment author information fetched from the comment cookies.
		$commenter = wp_get_current_commenter();
		
		// The name of the current comment author escaped for use in attributes.
		$comment_author = $commenter['comment_author']; // Escaped by sanitize_comment_cookies()

		// The email address of the current comment author escaped for use in attributes.
		$comment_author_email = $commenter['comment_author_email'];  // Escaped by sanitize_comment_cookies()

		// The url of the current comment author escaped for use in attributes.
		$comment_author_url = esc_url($commenter['comment_author_url']);
		
		// allow widget to override $post->ID
		$post_id = intval($instance['post_id']) ? intval($instance['post_id']) : $post->ID;	
		
		// Grabs the comments for the $post->ID from the db.
		if ( $user_ID ) {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date_gmt", $post_id, $user_ID));
		} else if ( empty($comment_author) ) {
			$comments = get_comments( array('post_id' => $post_id, 'status' => 'approve', 'order' => 'ASC') );
		} else {
			$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date_gmt", $post_id, wp_specialchars_decode($comment_author,ENT_QUOTES), $comment_author_email));
		}
		
		// Adds the comments retrieved from the db into the main $wp_query
		$wp_query->comments = apply_filters( 'comments_array', $comments, $post_id );
		
		// keep $comments for legacy's sake
		$comments = &$wp_query->comments;
		
		// Set the comment count
		$wp_query->comment_count = count($wp_query->comments);
		
		// Update the cache
		update_comment_cache( $wp_query->comments );
		
		// Paged comments
		$overridden_cpage = FALSE;
		if ( '' == get_query_var('cpage') && get_option('page_comments') ) {
			set_query_var( 'cpage', 'newest' == get_option('default_comments_page') ? get_comment_pages_count() : 1 );
			$overridden_cpage = TRUE;
		}
		
		/**//**/// All the preliminary work is complete. Let's get down to business...
		$wp_list_comments_args = array();
		
		$wp_list_comments_args['type'] = $instance['type'];
		$wp_list_comments_args['style'] = $instance['style'];
		
		$wp_list_comments_args['avatar_size'] = (int) $instance['avatar_size'];
		$wp_list_comments_args['reply_text'] = (string) $instance['reply_text'];
		$wp_list_comments_args['login_text'] = (string) $instance['login_text'];
		
		$wp_list_comments_args['max_depth'] = (int) $instance['max_depth'];
		$wp_list_comments_args['enable_reply'] = $instance['enable_reply'];
		$wp_list_comments_args['comment_meta'] = $instance['comment_meta'];
		
		$wp_list_comments_args['callback'] = 'comments_loop_callback';
		
		$paginate_comments_links = paginate_comments_links( array('echo' => false) );
		
//		$wp_list_comments_args['page'] = (int) $instance['page'];
//		$wp_list_comments_args['per_page'] = (int) $instance['per_page'];
		$wp_list_comments_args['reverse_top_level'] = $instance['reverse_top_level'];
//		$wp_list_comments_args['reverse_children'] = (bool) $instance['reverse_children'];
		$comment_type = 'all' == $instance['type'] ? 'comment' : $instance['type'];
		
		$type_plural = 'pings' == $comment_type ? $comment_type : "{$comment_type}s";
		$type_singular = 'pings' == $comment_type ? 'ping' : $comment_type;
				
		// Check to see if post is password protected
		if ( post_password_required() ) {
			echo "<{$instance['comment_header']}>Password Protected</{$instance['comment_header']}>";
			echo '<p class="'. $post->post_type .'_password_required">This '. $post->post_type .' is password protected. Enter the password to view comments.</p>';
			do_action( "{$post->post_type}_password_required" );
			return false;
		}
		
		echo '<div id="comments-loop-'. $args['id'] .'" class="widget-comments-loop">';
		
		if ( $instance['use_default_styles'] )
			echo $args['before_widget'];
		
		// Title
		if ( $instance['use_default_styles'] and $instance['title'] )
			echo $args['before_title'] . $instance['title'] . $args['after_title'];
		elseif ( $instance['h2'] and $instance['title'] )
			echo "<{$instance['headline_tag']}>{$instance['title']}</{$instance['headline_tag']}>";
		
		// If we have comments
		if ( have_comments() ) :
			
			do_action( "before_{$comment_type}_div" );
			
			$div_id = ( 'comment' == $comment_type ) ? 'comments' : $comment_type;
			echo '<div id="'. $div_id .'">'; // div#comments
			
			$title = the_title( '&#8220;', '&#8221;', false );
			$local_comments = $comments;
			$_comments_by_type = &separate_comments( $local_comments );
			
			echo "<{$instance['comment_header']} id=\"comments-number\" class=\"comments-header\">";
			cl_comments_number( "No $type_plural to $title", "1 $type_singular to $title", "% $type_plural to $title", $instance['type'], $_comments_by_type );
			echo "</{$instance['comment_header']}>";
			
			unset( $local_comments, $_comments_by_type );
			?>

			<?php if ( $instance['enable_pagination'] and get_option( 'page_comments' ) and $paginate_comments_links ) : ?>
			<div class="comment-navigation paged-navigation">
				<?php echo $paginate_comments_links; ?>
				<?php do_action( "{$comment_type}_pagination" ); ?>
			</div><!-- .comment-navigation -->
			<?php endif; ?>
			
			<?php do_action( "before_{$comment_type}_list" ); ?>

			<<?php echo $instance['style']; ?> class="commentlist">
			<?php wp_list_comments( $wp_list_comments_args ); ?>
			</<?php echo $instance['style']; ?>>
			
			<?php do_action( "after_{$comment_type}_list" ); ?>

			<?php if ( $instance['enable_pagination'] and get_option( 'page_comments' ) and $paginate_comments_links ) : ?>
			<div class="comment-navigation paged-navigation">
				<?php echo $paginate_comments_links; ?>
				<?php do_action( "{$comment_type}_pagination" ); ?>
			</div><!-- .comment-navigation -->
			<?php endif;
			
			do_action( "after_{$comment_type}_div" );
			
		echo '</div>'; // div#comments

		endif;
		
		if ( $instance['use_default_styles'] )
			echo $args['after_widget'];
		
		echo '</div>';
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = $new_instance;
		
		// Checkboxes
		$instance['use_default_styles'] = $new_instance['use_default_styles'] ? true : false;
		$instance['enable_pagination'] = $new_instance['enable_pagination'] ? true : false;
		$instance['enable_reply'] = $new_instance['enable_reply'] ? true : false;
		$instance['reverse_top_level'] = $new_instance['reverse_top_level'] ? true : false;
//		$instance['reverse_children'] = $new_instance['reverse_children'] ? true : false;
		$instance['h2'] = $new_instance['h2'] ? true : false;
		
		$instance['comments_closed'] = esc_html( $new_instance['comments_closed'] );
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		// update $instance with $new_instance;
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array( 'max_depth' => get_option('thread_comments_depth'), 'style' => 'ul', 'type' => 'all',
			'avatar_size' => 32, 'enable_pagination' => true, 'use_default_styles' => false, 'enable_reply' => true, 'comment_header' => 'h3', 'reverse_top_level' => false, //'reverse_children' => false,
			'reply_text' => 'Reply', 'login_text' => 'Log in to Reply', 'comment_meta' => '[date] [link before="| "] [edit before="| "]',
			'comment_moderation' => 'Your comment is awaiting moderation.', 'headline_tag' => 'h2', 'h2' => false  );
		$instance = wp_parse_args( $instance, $defaults );
		$tags = array( 'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6', 'p' => 'p', 'span' => 'span', 'div' => 'div' );
		?>
		<p class="description">This widget will <em>only</em> display comments on singular templates (single post, pages, or attachments), or if they have comments.</p>
		<div class="widget-cmnt-loop" style="margin-left:0px;">
			<?php
			cl_form_text( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $instance['title'], '<code>title</code>' );
			cl_form_select( $this->get_field_id( 'type' ), $this->get_field_name( 'type' ), array( 'comment' => 'Comments', 'trackback' => 'Trackbacks', 'pingback' => 'Pingbacks', 'pings' => 'Trackbacks and Pingbacks' ), $instance['type'], '<code>comment_type</code>' );
			
			cl_form_select_n( $this->get_field_id( 'headline_tag' ), $this->get_field_name( 'headline_tag' ), $tags, $instance['headline_tag'], '<code>title_tag</code>' );
			cl_form_select_n( $this->get_field_id( 'comment_header' ), $this->get_field_name( 'comment_header' ), $tags, $instance['comment_header'], '<code>comment_header_tag</code>' );
			cl_form_select_n( $this->get_field_id( 'style' ), $this->get_field_name( 'style' ), array( 'div' => 'div', 'ol' => 'ol', 'ul' => 'ul' ), $instance['style'], '<code>comment_list_style</code>' );
			?>
		</div>
		
		<div class="widget-cmnt-loop">
			<?php
			cl_form_text( $this->get_field_id( 'comment_meta' ), $this->get_field_name( 'comment_meta' ), $instance['comment_meta'], '<code>comment_meta</code>' );
			cl_form_text( $this->get_field_id( 'comment_moderation' ), $this->get_field_name( 'comment_moderation' ), $instance['comment_moderation'], '<code>comment_moderation</code>' );
			cl_form_text( $this->get_field_id( 'reply_text' ), $this->get_field_name( 'reply_text' ), $instance['reply_text'], '<code>comment_reply_text</code>' );
			cl_form_text( $this->get_field_id( 'login_text' ), $this->get_field_name( 'login_text' ), $instance['login_text'], '<code>login_to_comment_text</code>' );
			?>
		</div>
		
		<div class="widget-cmnt-loop">
			<?php
			cl_form_smalltext( $this->get_field_id( 'max_depth' ), $this->get_field_name( 'max_depth' ), $instance['max_depth'], '<code>max_depth</code>' );
			cl_form_smalltext( $this->get_field_id( 'avatar_size' ), $this->get_field_name( 'avatar_size' ), $instance['avatar_size'], '<code>avatar_size</code>' );
			
			cl_form_checkbox( $this->get_field_id( 'h2' ), $this->get_field_name( 'h2' ), $instance['h2'], __( 'Use title as headline', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'enable_pagination' ), $this->get_field_name( 'enable_pagination' ), $instance['enable_pagination'], __( 'Enable pagination', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'enable_reply' ), $this->get_field_name( 'enable_reply' ), $instance['enable_reply'], __( 'Enable comment reply', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'reverse_top_level' ), $this->get_field_name( 'reverse_top_level' ), $instance['reverse_top_level'], __( 'Reverse the comment order', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'use_default_styles' ), $this->get_field_name( 'use_default_styles' ), $instance['use_default_styles'], __( 'Use default widget styles', 'comments-loop' ) );
			?>
		</div>
		<?php
	}
}

class Comments_Form extends WP_Widget {
	
	function Comments_Form() {
		$widget_ops = array( 'classname' => 'cmnt-form', 'description' => __( 'Display customized Comment forms', 'comments-loop' ) );
		$control_ops = array( 'width' => 520 /*835 with no borders*/, 'height' => 350, 'id_base' => 'comments-form' );
		$this->WP_Widget( 'comments-form', 'Comments Form', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		global $post;
		
		// Will not display the comments template if not on single post or page
		if ( !is_singular() OR empty($post) )
			return false;
		
		$form_args = array_merge( $args, $instance );

		cl_comment_form( $form_args );
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance = $new_instance;
		
		// Checkboxes
//		$instance['use_default_styles'] = $new_instance['use_default_styles'] ? true : false;
		$instance['req'] = $new_instance['req'] ? true : false;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		// update $instance with $new_instance;
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array( 'req' => true, 'req_str' => __( '(required)', 'comments-loop' ), 'name' => __( 'Name', 'comments-loop' ), 'email' => __( 'Mail (will not be published)', 'comments-loop' ), 'url' => __( 'Website', 'comments-loop' ),
			'must_log_in' => __( 'You must be <a href="%s">logged in</a> to post a comment.', 'comments-loop' ),
			'logged_in_as' => __( 'Logged in as <a href="%s">%s</a>. <a href="%s" title="Log out of this account">Log out &raquo;</a>', 'comments-loop' ),
			'title_reply' => __( 'Leave a Reply', 'comments-loop' ), 
			'title_reply_to' => __( 'Leave a Reply to %s', 'comments-loop' ), 
			'cancel_reply_link' => __( 'Click here to cancel reply.', 'comments-loop' ), 
			'label_submit' => __( 'Submit Comment', 'comments-loop' ),
			'title_reply_tag' => 'h3',
			'comments_closed' => 'Comments are closed.',
		);
			
		$instance = wp_parse_args( $instance, $defaults );
		$tags = array( 'h1' => 'h1', 'h2' => 'h2', 'h3' => 'h3', 'h4' => 'h4', 'h5' => 'h5', 'h6' => 'h6', 'p' => 'p', 'span' => 'span', 'div' => 'div' );
		?>
		<p class="description">This widget will <em>only</em> display the comments form on singular templates (single post, pages, or attachments).</p>
		<div class="widget-cmnt-loop" style="margin-left:0px;">
			<?php
			cl_form_text( $this->get_field_id( 'title' ), $this->get_field_name( 'title' ), $instance['title'], '<code>title</code>' );
			cl_form_text( $this->get_field_id( 'name' ), $this->get_field_name( 'name' ), $instance['name'], '<code>name</code>' );
			cl_form_text( $this->get_field_id( 'email' ), $this->get_field_name( 'email' ), $instance['email'], '<code>email</code>' );
			cl_form_text( $this->get_field_id( 'url' ), $this->get_field_name( 'url' ), $instance['url'], '<code>url</code>' );
			cl_form_text( $this->get_field_id( 'req_str' ), $this->get_field_name( 'req_str' ), $instance['req_str'], '<code>required_text</code>' );
			cl_form_text( $this->get_field_id( 'label_submit' ), $this->get_field_name( 'label_submit' ), $instance['label_submit'], '<code>label_submit</code>' );
			?>
		</div>
		
		<div class="widget-cmnt-loop">
			<?php
			cl_form_text( $this->get_field_id( 'must_log_in' ), $this->get_field_name( 'must_log_in' ), $instance['must_log_in'], '<code>must_log_in</code>' );
			cl_form_text( $this->get_field_id( 'logged_in_as' ), $this->get_field_name( 'logged_in_as' ), $instance['logged_in_as'], '<code>logged_in_as</code>' );
			cl_form_text( $this->get_field_id( 'title_reply' ), $this->get_field_name( 'title_reply' ), $instance['title_reply'], '<code>title_reply</code>' );
			cl_form_text( $this->get_field_id( 'title_reply_to' ), $this->get_field_name( 'title_reply_to' ), $instance['title_reply_to'], '<code>title_reply_to</code>' );
			cl_form_text( $this->get_field_id( 'cancel_reply_link' ), $this->get_field_name( 'cancel_reply_link' ), $instance['cancel_reply_link'], '<code>cancel_reply_link</code>' );
			cl_form_text( $this->get_field_id( 'comments_closed' ), $this->get_field_name( 'comments_closed' ), $instance['comments_closed'], '<code>comments_closed</code>' );
			?>
		</div>
		
		<div class="widget-cmnt-loop">
			<?php
			cl_form_select_n( $this->get_field_id( 'headline_tag' ), $this->get_field_name( 'headline_tag' ), $tags, $instance['headline_tag'], '<code>title_tag</code>' );
			cl_form_select_n( $this->get_field_id( 'title_reply_tag' ), $this->get_field_name( 'title_reply_tag' ), $tags, $instance['title_reply_tag'], '<code>title_reply_tag</code>' );
			cl_form_checkbox( $this->get_field_id( 'req' ), $this->get_field_name( 'req' ), $instance['req'], __( 'Require name and email', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'h2' ), $this->get_field_name( 'h2' ), $instance['h2'], __( 'Use title as headline', 'comments-loop' ) );
			cl_form_checkbox( $this->get_field_id( 'use_default_styles' ), $this->get_field_name( 'use_default_styles' ), $instance['use_default_styles'], __( 'Use default widget styles', 'comments-loop' ) );
			?>
		</div>
		<?php
	}
}
?>