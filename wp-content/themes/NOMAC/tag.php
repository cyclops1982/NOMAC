<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package NOMAC 
 * @subpackage Theme
 * @since 2011
 */


get_header(); 

echo "<div class=\"content\" role=\"main\">";


echo "<h1>";
single_tag_title( '', trie );
echo "</h1>";

/* Run the loop for the category page to output the posts.
 * If you want to overload this in a child theme then include a file
 * called loop-category.php and that will be used instead.
 */
get_template_part( 'loop', 'tag' );
echo "</div>";

get_sidebar();
get_footer(); 
?>
