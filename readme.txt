=== Comments Loop ===
Contributors: ptahdunbar
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=11341928
Tags: widget, comments, trackbacks, pingbacks, form
Requires at least: 2.9
Tested up to: 2.9
Stable tag: 0.1

A WordPress widget that gives you unprecendeted control over displaying your comments loop and comment form.

== Description ==

The *Comments Loop* widget was written to allows users with no coding knowledge to customize their comments loop and comments form, all without editing their comments.php file.

Comments Loop is a complete widigitzation of the comments.php file, with over 30 settings to customize from reply links to what gets displayed when comments are closed. When you activate Comments Loop, you'll have two new available widgets, *Comments Loop* and *Comments Form*. Simply drag and drop them in a sidebar that's active in a **singular** template (e.g. single.php, page.php, or attachment.php).

View the [FAQ](http://wordpress.org/extend/plugins/comments-loop/faq/) section for more info on how to use *Comments Loop*.

== Frequently Asked Questions ==

= How does this widget work? =

The Comments Loop and Comments Form widgets are basically an abstraction of the comments.php file. The Comments Loop widget makes use of the `wp_list_comments()` template tag to generate the custom loops. You can see a list of arguements at the [wp_list_comments() codex page](http://codex.wordpress.org/Template_Tags/wp_list_comments). Both widgets make strategic use of [action hooks and filters](http://codex.wordpress.org/Plugin_API) for advance customization.

= What are the available shortcodes in this widget? =
The `comments_meta` in the Comments widget section may contain shortcodes.
In addition, the Comments Loop widget comes bundled with:

* `[author]` - Displays the author of the post.
* `[date]` - Displays the date the post was published.
* `[time]` - Displays the time of day the post was posted.
* `[link]` - Displays the comment's permalink.
* `[reply]` - Displays the reply link.
* `[edit]` - Displays the edit link.

All shortcodes have *before* and *after* parameters. For instance, adding a seperator before the edit link would be: `[edit before="| "]`. In addition, the `[edit]` and `[link]` shortcodes have a *label* parameter to customize the actual text (e.g. `[link label="Permalink"]`). To customize the text for the `[reply]` shortcode, use *reply_text* and *login_text*.

= What hooks are available in this widget? =

The Comments Loop has several action hooks available throughout the loop process:

* `before_$comment_type_div` - Before the opening of the `#comments` div
* `$comment_type_pagination` - Right after the pagination links
* `before_$comment_type_list` - Before the comments loop
* `before_$comment_type` - Within the loop of $comment_type, right after the opening tag.
* `after_$comment_type` - Within the loop of $comment_type, right after the closing tag.
* `after_$comment_type_list` - After the comments loop
* `after_$comment_type_div` - After the closing of the `#comments` div

**NOTE** Change `$comment_type` to be the value of the widget's `type` parameter (e.g. comment, trackback, pingback, pings).

In addition, it also has several filter hooks where you can modify the function's output:

* `cl_comment_meta` - The text of the `comment_meta` widget settings
* `cl_comment_author` - The post author
* `cl_comment_date` - The date the comment was posted
* `cl_comment_time` - The time of day the comment was posted
* `cl_comment_count` - The comment count
* `cl_comment_link` - The permalink for the comment
* `cl_comment_reply` - The reply link
* `cl_comment_edit` - The edit link to edit the comment.

The Comments Form has several action hooks available:

* `comment_form_before` - Before the `#respond` div
* `comment_form_must_log_in_after` - After the `must_log_in` text
* `comment_form_top` - After the opening form tag
* `comment_form_logged_in_after` - After the `logged_in_as` text
* `comment_form_before_fields` - Before the input fields
* `comment_form_after_fields` - After the input fields
* `comments_form` - Before the closing form tag
* `comment_form_after` - After the `#respond` div
* `comment_form_comments_closed` - After the `comments_closed` text

== Installation ==

1. Upload 'comments-loop' to the '/wp-content/plugins/' directory.
1. Activate the plugin through the *Plugins* menu in WordPress.
1. Go to *Appearance > Widgets* and place the *Comments Loop* or *Comments Form* widget where you want.

== Changelog ==
	
**0.1** _(01/29/2010)_

	* Initial release.
	
== Screenshots ==

1. View of the *Comments Loop* widget settings.
2. View of the *Comments Form* widget settings.