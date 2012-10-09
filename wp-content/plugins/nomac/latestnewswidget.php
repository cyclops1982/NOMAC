<?php
add_action( 'widgets_init', create_function('', 'register_widget("NomacLatestNewsWidget");'));

class NomacLatestNewsWidget extends WP_Widget {

	function NomacLatestNewsWidget() {
		parent::WP_Widget(false, $name = 'NOMAC Latest News Widget');
	}

	function widget($args, $instance)
	{
		$args = array(
			'numberposts' => 8
		);
		$posts = get_posts($args);

		if (count($posts) > 0) {
			echo '<h3>Laatste nieuws...</h3>';
			echo '<div id="latestposts"><ul>';
			foreach ($posts as $key => $post)
			{
				$categories = get_the_category($post->ID);
				$catstring = "";
				foreach ($categories as $cat)
				{
					$catstring .= '<a href="'.get_category_link($cat->cat_ID).'" title="'.$cat->cat_name.'">';
					$catstring .= $cat->cat_name;
					$catstring .= '</a>';
					$catstring .= ", ";
				}
				$catstring = rtrim($catstring, ", ");
			
				echo '<li>['.$catstring.'] ';
				echo '<a href="'.get_permalink($post->ID).'" title="'.$post->post_title.'">';
				echo $post->post_title;
				echo '</a>';
				echo '</li>';
			}
			echo '</ul></div>';

		}
	}
}
?>
