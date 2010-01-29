<?php

/**
 * fixes some CSS styling bugs
 *
 * @since 0.1
 **/
function cl_widget_css() {
	?>
	<style type="text/css" media="screen">
		.widget-cmnt-loop .mutlifat { height: 7em !important; }
		.widget-cmnt-loop textarea { font-size: 12px; }
		.widget-cmnt-loop .small-text { width: 50px; float: right; }
		
		.widget-cmnt-loop { float: left; width: 165px; margin-left: 15px; }
		
		.widget-cmnt-loop input[type="checkbox"], .widget-cmnt-loop input[type="radio"] { margin-right: 4px; }
		
		.wl-form-radio { margin-bottom: 3px !important; }
		.widget-cmnt-loop p label { font-size: 10px; }
		
		.widget-control-actions { clear: both; }
	</style>
	<?php
}

/* Helper Functions */

/**
 * Displays the text input
 *
 * @since 0.1
 **/
function cl_form_text( $id, $name, $value = false, $label = '' ) {
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	echo '<input class="code widefat" type="text" id="'. $id .'" name="'. $name .'" value="'. esc_attr($value) .'" />';
	echo '</p>';
}

/**
 * Displays the textarea input
 *
 * @since 0.1
 **/
function cl_form_bigtext( $id, $name, $value = false, $label = '' ) {
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	echo '<textarea class="widefat code" cols="16" rows="2" id="'. $id .'" name="'. $name .'">';
	echo esc_attr($value);
	echo '</textarea>';
	echo '</p>';
}

/**
 * Displays the small text input
 *
 * @since 0.1
 **/
function cl_form_smalltext( $id, $name, $value = false, $label = '' ) {
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	echo '<input class="code small-text" type="text" id="'. $id .'" name="'. $name .'" value="'. esc_attr($value) .'" />';
	echo '</p>';
}

/**
 * Displays the checkbox input
 *
 * @since 0.1
 **/
function cl_form_checkbox( $id, $name, $value, $label ) {
	echo '<p><label for="'. $id .'"><input type="checkbox" id="'. $id .'" name="'. $name .'"'. checked( $value, true, false ) .' />'. $label .'</label></p>';
}

/**
 * Displays the radio input
 *
 * @since 0.1
 **/
function cl_form_radio( $id, $name, $options, $value, $label = '' ) {
	echo '<p class="wl-form-radio">'. $label .'</p>';
	foreach ( $options as $_value => $_name ) :
		echo '<p><label for="'. $_value .'"><input type="radio" id="'. $_value .'" name="'. $name .'" value="'. $_value .'" '. checked( $value, $_value, false ) .'/>'. $_name .'</label></p>';
	endforeach;
}

/**
 * Displays the select input
 *
 * @since 0.1
 **/
function cl_form_select( $id, $name, $options, $value, $label = '' ) {
	$all = ( 'all' == $value OR null == $value ) ? ' selected="selected"' : null;
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	?>
	<select class="widefat" name="<?php echo $name; ?>" id="<?php echo $id; ?>">
	<?php
	if ( stripos( $label, 'meta_compare' ) ) {
		echo '<option value="all"'. $all .'> </option>';
	} else {
		echo '<option value="all"'. $all .'>All</option>';
	}
	?>
	<?php foreach ( $options as $option_value => $option_name ) : $selected = ( $value == $option_value ) ? ' selected="selected"' : null; ?>
		<option<?php echo $selected; ?> value="<?php echo $option_value; ?>"><?php echo $option_name; ?></option>
	<?php endforeach; ?>
	</select>
	<?php
	echo '</p>';
}

/**
 * Displays the select input
 *
 * @since 0.1
 **/
function cl_form_select_n( $id, $name, $options, $value, $label = '' ) {
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	?>
	<select class="widefat" name="<?php echo $name; ?>" id="<?php echo $id; ?>">
	<?php foreach ( $options as $option_value => $option_name ) : $selected = ( $value == $option_value ) ? ' selected="selected"' : null; ?>
		<option<?php echo $selected; ?> value="<?php echo $option_value; ?>"><?php echo $option_name; ?></option>
	<?php endforeach; ?>
	</select>
	<?php
	echo '</p>';
}

/**
 * Displays the multi-select input
 *
 * @since 0.1
 **/
function cl_form_multi_select( $id, $name, $options, $value, $label = '' ) {
	$value = (array) $value;
	$all = ( 'all' == $value[0] OR null == $value ) ? ' selected="selected"' : null;
	echo '<p>';
	wl_form_label( array('id' => $id, 'label' => $label) );
	?>
	<select class="widefat mutlifat" multiple="multiple" size="4" name="<?php echo $name; ?>[]" id="<?php echo $id; ?>">
		<option value="all"<?php echo $all; ?>>All</option>
	<?php foreach ( $options as $option_value => $options_name ) : $selected = ( in_array( $option_value, $value ) OR in_array( $option_name, $value ) ) ? ' selected="selected"' : null; ?>
		<option<?php echo $selected; ?> value="<?php echo $option_value; ?>"><?php echo $options_name; ?></option>
	<?php endforeach; ?>
	</select>
	<?php
	echo '</p>';
}

/**
 * Displays the label
 *
 * @since 0.1
 **/
function cl_form_label( $args = array() ) {
	extract( $args );
	$id = $id ? ' for="'. $id .'"' : null;
	$title = $title ? ' title="'. $title .'"' : null;
	echo '<label'. $id . $title . '>'. $label .'</label>';
}

/**
 * Custom Loop callback for wp_list_comments()
 *
 * @since 0.1
 **/
function comments_loop_callback( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$GLOBALS['comment_depth'] = $depth;
	
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	} ?>
	
	<<?php echo $tag ?> <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?> id="comment-<?php comment_ID() ?>">
	
	<?php do_action( "before_{$args['type']}" ); ?>
	
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
	<?php endif; ?>
	
	<div class="comment-meta commentmetadata">
		<?php cl_comment_author_avatar( $comment, $args ); ?>
		<?php echo cl_comment_meta( $args['comment_meta'] ); ?>
	</div>
	
	<div class="comment-content">
		<?php if ( '0' == $comment->comment_approved ) : ?>
		<p class="comment-moderation"><?php _e( $args['comment_moderation'], 'comments-loop' ); ?></p>
		<?php endif; ?>
		
		<?php comment_text(); ?>
	</div>
	
	<?php if ( $args['enable_reply'] ): ?>
	<div class="reply">
		<?php comment_reply_link( array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth'])) ) ?>
	</div>	
	<?php endif ?>
	
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif;
	
	do_action( "after_{$args['type']}" );
}

/**
 * Display the language string for the number of comments the current post has.
 *
 * @since 0.71
 * @uses $id
 * @uses apply_filters() Calls the 'comments_number' hook on the output and number of comments respectively.
 *
 * @param string $zero Text for no comments
 * @param string $one Text for one comment
 * @param string $more Text for more than one comment
 * @param string $type Comment Type
 * @param array $comments Comments by type
 */
function cl_comments_number( $zero = false, $one = false, $more = false, $type = 'all', $comments ) {
	if ( 'all' == $type ) {
		$number = count( $comments['comment'] ) + count( $comments['pings'] );
	} else {
		$number = count($comments[$type]);
	}

	if ( $number > 1 )
		$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments') : $more);
	elseif ( $number == 0 )
		$output = ( false === $zero ) ? __('No Comments') : $zero;
	else // must be one
		$output = ( false === $one ) ? __('1 Comment') : $one;

	echo apply_filters('comments_number', $output, $number);
}

/**
 * Outputs a complete commenting form for use within a template.
 * Most strings and form fields may be controlled through the $args array passed
 * into the function, while you may also choose to use the comments_form_default_fields
 * filter to modify the array of default fields if you'd just like to add a new
 * one or remove a single field. All fields are also individually passed through
 * a filter of the form comments_form_field_$name where $name is the key used
 * in the array of fields.
 *
 * @since 3.0 
 * @param array $args Options for strings, fields etc in the form
 * @param mixed $post_id Post ID to generate the form for, uses the current post if null
 * @return void
 */
function cl_comment_form( $form_args = array(), $post_id = null ) {
	global $user_identity, $id;
		
	if ( null === $post_id )
		$post_id = $id;
	else
		$id = $post_id;
	
	$commenter = wp_get_current_commenter();
	
	$req = $form_args['req'];
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$req_str  = ( $req ? ' ' . $args['req_str'] : '' );
	
	$args = array( 'fields' => apply_filters( 'comment_form_default_fields', array( 'author' => '<input type="text" name="author" id="author" value="' . esc_attr( $commenter['comment_author'] ) . '" size="22" tabindex="1"' . $aria_req . ' /> <label for="author">' . $form_args['name'] . $req_str . '</label>', 
																					    'email'  => '<input type="text" name="email" id="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="22" tabindex="2"' . $aria_req . ' /> <label for="email">' . $form_args['email'] . $req_str . '</label>', 
																					    'url'    => '<input type="text" name="url" id="url" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="22" tabindex="3" /> <label for="url">' . $form_args['url'] . '</label>' ) ),
						'comment_field' => '<p><textarea name="comment" id="comment" cols="58" rows="10" tabindex="4"></textarea></p>', 
						'must_log_in' => '<p class="must_log_in">' .  sprintf( $form_args['must_log_in'], wp_login_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>', 
						'logged_in_as' => '<p class="logged_in_as">' . sprintf( $form_args['logged_in_as'], admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( $post_id ) ) ) ) . '</p>', 
						'id_form' => 'commentform', 
						'id_submit' => 'submit', 
						'title_reply' => $form_args['title_reply'], 
						'title_reply_to' => $form_args['title_reply_to'], 
						'cancel_reply_link' => $form_args['cancel_reply_link'], 
						'label_submit' => $form_args['label_submit'],
	);
	
//	$args = wp_parse_args( $args, apply_filters( 'comment_form_defaults', $defaults ) );
	?>
		<?php if ( comments_open() ) : ?>				
			<?php echo '<div id="comments-form-'. $form_args['id'] .'" class="widget-comments-form">'; ?>
				<?php do_action( 'comment_form_before' ); ?>
				
				<div id="respond">
					
				<?php
				if ( $form_args['use_default_styles'] )
					echo $form_args['before_widget'];
					
				// Title
				if ( $form_args['use_default_styles'] and $form_args['title'] )
					echo $form_args['before_title'] . $form_args['title'] . $form_args['after_title'];
				elseif ( $form_args['h2'] and $form_args['title'] )
					echo "<{$form_args['headline_tag']}>{$form_args['title']}</{$form_args['headline_tag']}>";
				
				echo "<{$form_args['title_reply_tag']} class=\"title-reply\">";
				comment_form_title( $args['title_reply'], $args['title_reply_to'] );
				echo "</{$form_args['title_reply_tag']}>";
				?>
				
				<div class="cancel-comment-reply">
					<span class="cancel-comment-reply-link"><?php cancel_comment_reply_link( $args['cancel_reply_link'] ); ?></span>
				</div>
				
				<?php if ( get_option( 'comment_registration' ) && !is_user_logged_in() ) : ?>
					<?php echo $args['must_log_in']; ?>
					<?php do_action( 'comment_form_must_log_in_after' ); ?>
				<?php else : ?>
					<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="<?php echo esc_attr( $args['id_form'] ); ?>">
						<?php do_action( 'comment_form_top' ); ?>
						<?php if ( is_user_logged_in() ) : ?>
							<?php echo $args['logged_in_as']; ?>
							<?php do_action( 'comment_form_logged_in_after', $commenter, $user_identity ); ?>
						<?php else : ?>
							<?php
							do_action( 'comment_form_before_fields' );
							foreach ( (array) $args['fields'] as $name => $field ) {
								echo '<p>' . apply_filters( "comment_form_field_{$name}", $field ) . "</p>\n";
							}
							do_action( 'comment_form_after_fields' );
							?>
						<?php endif; ?>
						<?php echo apply_filters( 'comment_form_field_comment', $args['comment_field'] ); ?>
						<p>
							<input name="submit" type="submit" id="<?php echo esc_attr( $args['id_submit'] ); ?>" tabindex="<?php echo ( count( $args['fields'] ) + 2 ); ?>" value="<?php echo esc_attr( $args['label_submit'] ); ?>" />
							<?php comment_id_fields(); ?>
						</p>
						<?php do_action( 'comments_form', $post_id ); ?>
					</form>
				<?php endif; ?>
				
				<?php
				if ( $form_args['use_default_styles'] )
					echo $form_args['after_widget'];
				?>
				
				</div>
				<?php do_action( 'comment_form_after' );
			echo '</div>';

		else : // comments are closed
		
			echo '<!-- If comments are closed. -->';
			if ( $form_args['comments_closed'] )
				echo '<p class="comments-closed">'. $form_args['comments_closed'] .'</p>';

			do_action( 'comment_form_comments_closed' );
		endif;
}

/**
 * Process any shortcodes applied to $content
 *
 * @since 0.1
 **/
function cl_comment_meta( $content ) {
	$content = preg_replace( '/\[(.+?)\]/', '[comment_$1]', $content );
	return apply_filters( 'cl_comment_meta', do_shortcode( $content ) );
}

/**
 * Displays the author's avatar and the author's name/link
 *
 * @since 0.1
 **/
function cl_comment_author_avatar( $comment, $args ) { ?>
	<div class="comment-author vcard">
	<?php if ( $args['avatar_size'] != 0 )
		echo get_avatar( $comment, $args['avatar_size'] );
	?>
		<cite class="fn"><?php echo cl_comment_author(); ?></cite>
	</div>
	<?php
}

/**
 * Returns the author's name/link microformatted
 *
 * @since 0.1
 **/
function cl_comment_author( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '' );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );
	
	$author = esc_html( get_comment_author() );
	$url = esc_url( get_comment_author_url() );

	/* Display link and cite if URL is set. Also, properly cites trackbacks/pingbacks. */
	if ( $url )
		$output = '<cite class="fn" title="' . $url . '"><a href="' . $url . '" title="' . $author . '" class="url" rel="external nofollow">' . $author . '</a></cite>';
	else
		$output = '<cite class="fn">' . $author . '</cite>';

	$output = '<span class="comment-author vcard">' . apply_filters( 'get_comment_author_link', $output ) . '</span>';

	return apply_filters( 'cl_comment_author', $before . $output . $after );
}

/**
 * Displays the comment date
 *
 * @since 0.1
 */
function cl_comment_date( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '' );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );
	
	$output = '<span class="published"><abbr class="comment-date" title="' . get_comment_date(get_option('date_format')) . '">' . get_comment_date() . '</abbr>';
	
	return apply_filters( 'cl_comment_date', $before . $output . $after );
}

/**
 * Displays the comment time
 *
 * @since 0.1
 */
function cl_comment_time( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '' );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );
	
	$output = '<span class="comment-time"><abbr title="' . get_comment_date( __( 'g:i a', 'comments-loop' ) ) . '">' . get_comment_time() . '</abbr></span>';
	
	return apply_filters( 'cl_comment_time', $before . $output . $after );
}

/**
 * Displays the comment count
 *
 * @since 0.1
 **/
function cl_comment_count( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '' );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );
	
	global $comment_count;
	
	if ( !isset($comment_count) )
		$comment_count = 1;
	
	$comment_type = get_comment_type();
	
	$output = "<span class=\"$comment_type-count\">$comment_count</span>";
	
	$comment_count++;
	
	return apply_filters( 'cl_comment_count', $before . $output . $after );
}

/**
 * Displays a list of comma seperated tags
 *
 * @since 0.1
 **/
function cl_comment_link( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '', 'label' => __( 'Permalink', 'comments-loop' ) );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );

	$output = '<span class="comment-permalink"><a href="' . esc_url(get_comment_link()) . '" title="' . sprintf( __( 'Permalink to %1$s %2$s', 'comments-loop' ), get_comment_type(), get_comment_ID() ) . '">' . $label . '</a></span>';

	return apply_filters( 'cl_comment_link', $before . $output . $after );
}

/**
 * Comment Reply link
 *
 * @since 0.1
 */
function cl_comment_reply( $atts = array() ) {
	$defaults = array(
		'reply_text' => __( 'Reply', 'comments-loop' ),
		'login_text' => __( 'Log in to reply.', 'comments-loop' ),
		'depth' => $GLOBALS['comment_depth'],
		'max_depth' => get_option( 'thread_comments_depth' ),
		'before' => '',
		'after' => ''
	);
	$args = shortcode_atts( $defaults, $args );

	if ( !get_option( 'thread_comments' ) || 'comment' !== get_comment_type() )
		return '';

	return get_comment_reply_link( $args );
}

/**
 * Comment Edit link
 *
 * @since 0.1
 **/
function cl_comment_edit( $atts = array() ) {
	$defaults = array( 'before' => '', 'after' => '', 'label' => __( 'Edit', 'comments-loop' ) );
	$args = shortcode_atts( $defaults, $atts );
	extract( $args, EXTR_SKIP );
	
	$edit_link = get_edit_comment_link( get_comment_ID() );

	if ( !$edit_link )
		return '';

	$output = '<span class="comment-edit"><a href="' . $edit_link . '" title="' . $label . '">' . $label . '</a></span>';
	
	return apply_filters( 'cl_comment_edit', $before . $output . $after );
}

// Shortcodes
add_shortcode( 'comment_author', 'cl_comment_author' );
add_shortcode( 'comment_date', 'cl_comment_date' );
add_shortcode( 'comment_time', 'cl_comment_time' );
add_shortcode( 'comment_count', 'cl_comment_count' );
add_shortcode( 'comment_link', 'cl_comment_link' );
add_shortcode( 'comment_reply', 'cl_comment_reply' );
add_shortcode( 'comment_edit', 'cl_comment_edit' );
?>