<?php
add_action('widgets_init', create_function('', 'register_widget("NomacImageCycleWidget");'));
add_action('wp_enqueue_scripts', 'nomac_imagecycle_scripts');

class NomacImageCycleWidget extends WP_Widget {

	function NomacImageCycleWidget() {
		parent::WP_Widget(false, $name = 'NOMAC Image Cycle Widget');
	}


	function widget($args, $instance) {
	        global $wpdb;
		$tablename = $wpdb->prefix . TABLE_IMAGECYCLE;

		echo '<div class="ImageCycle">';
		$links = $wpdb->get_results("SELECT title, link_url, image_url FROM $tablename");
		foreach ($links as $row)
		{
			if (!empty($row->link_url)) {
				echo '<a href="'.$row->link_url.'" >';
			}
			echo '<img src="'.$row->image_url.'" alt="'.$row->title.'" />';
			if (!empty($row->link_url)) {
				echo '</a>';
			}

		}
		echo '</div>';

	}
}

function nomac_imagecycle_scripts() {
	$cyclesrc = plugins_url('js/jquery.cycle.all.js', __FILE__);
	wp_register_script('jquery-cycle', $cyclesrc, array('jquery'), 'v1.2.6', false);
	wp_enqueue_script('jquery-cycle');

	$jssrc = plugins_url('js/imagewidget.js', __FILE__);
	wp_register_script('ImageCycle_JS', $jssrc, array('jquery', 'jquery-cycle'), 'v0.1', false);
	wp_enqueue_script('ImageCycle_JS');
}

?>
