<?php
/**
 * The template for displaying attachments.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */



get_header();

echo "<div class=\"content\" role=\"main\">";

if ( have_posts() ) 
{
	while ( have_posts() ) 
	{
		the_post();

		echo "<div id=\"post-";
		the_ID();
		echo "\" ";
		post_class();
		echo " >\n";

		echo "<h1>Attachment: ";
		the_title();
		echo "</h1>";

		echo '<p class="postmeta">Date/Time: ';
		the_time("Y/m/d H:i");
		echo ' | Auteur: <a href="'.get_author_posts_url(get_the_author_meta('ID')).'" title="View all posts from '.get_the_author().'">'.get_the_author().'</a>';
		echo '</p>';
		
		if ( wp_attachment_is_image() ) 
		{
			$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) );
			foreach ( $attachments as $k => $attachment ) {
				if ( $attachment->ID == $post->ID )
				{
					break;
				}
			}
			
			$k++;
			// If there is more than 1 image attachment in a gallery
			if ( count( $attachments ) > 1 ) 
			{
				if ( isset( $attachments[ $k ] ) )
				{
					// get the URL of the next image attachment
					$next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );
				}
				else
				{
					// or get the URL of the first image attachment
					$next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID );
				}
			} else {
				// or, if there's only 1 image attachment, get the URL of the image
				$next_attachment_url = wp_get_attachment_url();
			}
			
			echo '<p class="attachment"><a href="' . $next_attachment_url . '" title="'.esc_attr( get_the_title()) .'" rel="attachment">';

			$attachment_size = apply_filters( 'twentyten_attachment_size', 900 );
			echo wp_get_attachment_image( $post->ID, array( $attachment_size, 9999 ) ); // filterable image width with, essentially, no limit for image height.
			echo '</a></p>';
			echo '<a href="'.wp_get_attachment_url() .'" title="'.esc_attr( get_the_title() ) .'" rel="attachment">'.basename( get_permalink() ).'</a>';
		}
		else
		{
			echo '<br />';
			echo '<a href="' . wp_get_attachment_url() . '" title="' . esc_attr( get_the_title() ) . '" rel="attachment">' . basename( wp_get_attachment_url() ) . '</a>';
		}
		echo '</div>';
	} //endwhile
} // end ifhasposts

echo "</div>";

get_sidebar();
get_footer();

?>
