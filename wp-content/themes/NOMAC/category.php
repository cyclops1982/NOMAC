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
single_cat_title();
echo "</h1>";

$category_description = category_description();
if ( ! empty( $category_description ) )
{
	echo '<p>' . $category_description . '</p>';
}

/* Run the loop for the category page to output the posts.
 * If you want to overload this in a child theme then include a file
 * called loop-category.php and that will be used instead.
 */
get_template_part( 'loop', 'category' );
echo "</div>";

get_sidebar();
get_footer(); 
?>
