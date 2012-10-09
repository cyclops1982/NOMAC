<?php
/**
 * The template for displaying all pages.
 *
 * @package NOMAC
 * @subpackage Theme
 * @since 2011
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

		echo "<h1>";
		the_title();
		echo "</h1>";
		echo '<p class="postmeta">Date/Time: ';
		the_time("Y/m/d H:i");
		$cat = get_the_category(get_the_ID());
		if (count($cat) > 0)
		{
			 echo ' | Categorie&euml;n: ';
                	the_category(', ');
		}

		echo ' | Auteur: <a href="'.get_author_posts_url(get_the_author_meta('ID')).'" title="View all posts from '.get_the_author().'">'.get_the_author().'</a>';
		echo '</p>';
	
		the_content();
		
		wp_link_pages( 	
			array( 
			'before' => '<div class="page-link">Pages:', 
			'after' => '</div>'));
		edit_post_link( 'Edit', '<span class="edit-link">', '</span>' );
			
		echo "</div>";

		comments_template( '', true );
	}
}

echo "</div>";

get_sidebar();
get_footer();


?>
