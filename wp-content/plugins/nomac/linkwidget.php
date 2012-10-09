<?php
add_action( 'widgets_init', create_function('', 'register_widget("NomacLinkWidget");'));

class NomacLinkWidget extends WP_Widget {

	function NomacLinkWidget() {
		parent::WP_Widget(false, $name = 'NOMAC Link Widget');
	}

	function widget($args, $instance) {
		echo '<h3>Links...</h3>';
		echo '<div id="widget"><ul>';
		echo '<li>';
		echo '<a href="'. get_bloginfo('atom_url').'" title="NOMAC Nieuws RSS Feed" >';
		echo '<img src="' . plugins_url('rss_icon64x64.png', __FILE__) . '" alt="RSS icon" width="16" height="16" /> NOMAC Nieuws Feed';
		echo '</a>';
		echo '</li>';
		echo '<li><a href="' . get_bloginfo('url').'/wp-admin/" title="WordPress backend login">Admin login</a></a></li>';
		echo '</ul></div>';
	}
}
?>
