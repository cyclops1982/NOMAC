<?php
/*
 * The template for displaying Comments.
 *
 * @package NOMAC 
 * @subpackage Theme
 * @since 2011
 */


function comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type )
	{
                case '':
			echo "<li ";
			comment_class();
			echo " id=\"li-comment-";
			comment_ID();
			echo "\" >";

			echo "<div id=\"comment-";
			comment_ID();
			echo "\" >";
			
			echo "<div class=\"comment-author vcard\">";
			echo get_avatar($comment, 40);
			echo get_comment_author_link();
			echo "</div>";


			echo "<div class=\"comment-body\">";
			echo "<span class=\"comment-meta\" >";
			printf('On %s at %s :', get_comment_date(), get_comment_time());
			echo "</span>";
			echo "<span class=\"comment-meta reply\">";
			echo "reply...";
                     //comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); 
			echo "</span>";
	               	if ( $comment->comment_approved == '0' )
			{
                        	echo "<em>Comment is awaiting moderation</em>";
			}
			else
			{
				comment_text();
			}
			echo "</div>";
			echo "</div>"; // coment-ID
                        break;
                case 'pingback'  :
                case 'trackback' :
			echo "<li class=\"pingback\">Pingback:";
			comment_author_link(); 
			edit_comment_link( 'Edit', ' ' );
			echo "</li>";
                        break;
	}
}

if (comments_open() && 1 == 2)
{
	echo "\n\n<div class=\"comments\">";
	if ( post_password_required() )
	{
		echo "<p class=\"nopassword\">";
		_e( 'This post is password protected. Enter the password to view any comments.', 'twentyten' );
		echo "</p>";
		// Password protection, so no comments either.
		return;
	}

	echo "<h2>Comments</h2>";
	if (have_comments())
	{
		echo "<h3 id=\"comments-title\">";
		printf('%d Response(s) to %s', get_comments_number(), get_the_title());
		echo "</h3>";

		echo "<ol class=\"commentlist\">";
		/* Loop through and list the comments. Tell wp_list_comments()
		 * to use twentyten_comment() to format the comments.
		 * If you want to overload this in a child theme then you can
		 * define twentyten_comment() and that will be used instead.
		 * See twentyten_comment() in twentyten/functions.php for more.
		 */
		wp_list_comments( array( 'callback' => 'comment' ) );
		echo "</ol>";
	}
	else
	{
		echo "<p class=\"nocomments\">No comments yet... be the first?</>";
	}

	comment_form();

	echo "</div>";
}

?>
