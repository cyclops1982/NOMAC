<?php
/**
 * @package NOMAC Category Widget
 * @version 0.3
 */
/*
Plugin Name: NOMAC Category Widget
Plugin URI: http://www.nomac.nl
Description: The NOMAC Widgets plugin provides plugins used and created specially for the NOMAC website.
Author: Ruben d'Arco 
Version: 0.3
Author URI: http://cyclops.nettrends.nl/blog/
*/


add_action( 'widgets_init', 'category_load_widgets' );


class CategoryPages_Widget extends WP_Widget {

	function CategoryPages_Widget() {
		parent::WP_Widget(false, $name = 'NOMAC Category Widget');	
	}


	function GetCategoryPages($cat) 
	{
		global $wpdb;
		$query = "SELECT ID, post_title, guid
			FROM {$wpdb->prefix}posts
			INNER JOIN {$wpdb->prefix}postmeta m1 ON ( {$wpdb->prefix}posts.ID = m1.post_id )
			INNER JOIN {$wpdb->prefix}postmeta m2 ON ( {$wpdb->prefix}posts.ID = m2.post_id )
			WHERE
				{$wpdb->prefix}posts.post_type = 'page' 
				AND 
				{$wpdb->prefix}posts.post_status = 'publish'
				AND 
				( m2.meta_key = 'category' AND m2.meta_value = '$cat' )
			GROUP BY {$wpdb->prefix}posts.ID
			ORDER BY {$wpdb->prefix}posts.post_date DESC;";
		return $wpdb->get_results($query, OBJECT);
	}

	function PrintPosts($posts)
	{
		global $post;
		if (count($posts) > 0) 
		{
			echo "<div id=\"CategoryLinks\">";
			echo "<ul>";
			foreach ($posts as $key) {
				if (isset($post) && $post->ID == $key->ID)
				{
					echo "<li><a href=\"".$key->guid."\"> &gt; ".$key->post_title."</a></li>";
				}
				else
				{
					echo "<li><a href=\"".$key->guid."\"> ".$key->post_title."</a></li>";
				}
			}
			echo "</ul>";
			echo "</div>";
		}
	}

	function widget($args, $instance) {
		// Show related news for this category
		if (is_category()) {
			$cat = single_cat_title('', false);
			if ( ! empty($cat)) {
				$posts = $this->GetCategoryPages($cat);
				if (count($posts) > 0) {
					echo "<h2>Overige pagina's voor ".$cat.":</h2>";
					$this->PrintPosts($posts);
				}
			}
		}
		// show related pages for this post's category
		if (is_single()) {
			$cats = get_the_category();
			if (count($cats) > 0) {
				foreach ($cats as $catObj) {
					$cat = $catObj->cat_name;
					$posts = $this->GetCategoryPages($cat);
					if (count($posts) > 0)
					{
						echo "<h2>Overige pagina's voor ".$cat."</h2>";
						$this->PrintPosts($posts);
					}
				}
			}
		}		


		$custom = get_post_custom();
		if (isset($custom["category"]) && isset($custom["category"][0])) 
		{
			$cat = $custom["category"][0];
			if ( ! empty($cat)) {
				$postArgs = array('category_name' => $cat);
				$posts = get_posts($postArgs);
				
				echo "<h2>Laatste nieuws voor ".$cat.":</h2>";
				$this->PrintPosts($posts);

				echo "<h2>Overige pagina's voor ".$cat."</h2>";
				$posts = $this->GetCategoryPages($cat);
				$this->PrintPosts($posts);
			}
		}
	}
}

function category_load_widgets() {
	register_widget( 'CategoryPages_Widget' );
}



?>
