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

	if ( is_day() )
	{
		printf( 'Dag archief: <span>%s</span>', get_the_date() );
	}
	else if ( is_month() ) 
	{
		printf( 'Maand archief: <span>%s</span>', get_the_date('F Y'));
	}
	else if ( is_year() )
	{
		printf( 'Jaar archief: <span>%s</span>', get_the_date('Y'));
	}
	else
	{
		echo "Archief";
	}
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
get_template_part( 'loop', 'archive' );
echo "</div>";

get_sidebar();
get_footer();
?>
