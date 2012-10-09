<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package NOMAC 
 * @subpackage Theme
 * @since 2011
 */

get_header();

echo '<div class="content" role="main">';
	
/* Queue the first post, that way we know
 * what date we're dealing with (if that is the case).
 */
if ( have_posts() ) 
{
	the_post();
	echo "<h1>";
	echo 'Auteur: ';
	the_author();
	echo "</h1>";

	/* Since we called the_post() above, we need to
	 * rewind the loop back to the beginning that way
	 * we can run the loop properly, in full.
	 */
	rewind_posts();

	/* Run the loop for the archives page to output the posts.
	 * If you want to overload this in a child theme then include a file
	 * called loop-archives.php and that will be used instead.
	 */
}
get_template_part( 'loop', 'author' );
echo "</div>";

get_sidebar();
get_footer();
?>
