<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header();

echo "<div class=\"content\" role=\"main\">";

echo "<h1>";
printf('Zoek resultaat voor: <span>%s</span>', get_search_query());
echo "</h1>";
	
	/* Run the loop for the search to output the results.
	 * If you want to overload this in a child theme then include a file
	 * called loop-search.php and that will be used instead.
	 */
	 get_template_part( 'loop', 'search' );

echo "</div>";

get_sidebar(); 
get_footer();
 ?>
