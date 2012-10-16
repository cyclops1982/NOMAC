<?php
/*
Plugin Name: NOMAC Website
Plugin URI: http://www.nomac.nl
Description: A plugin that does all the modifications for the NOMAC website.
Author: Ruben d'Arco
Author URI: http://cyclops.nettrends.nl/blog/
Version: 0.3
*/


require_once("include.php");



/* Licensing Form and Lists */ 
require_once("licensingform.php");
require_once("licensinglist.php");
require_once("rulechangeform.php");
require_once("rulechangelist.php");
require_once("imagewidget.php");
require_once("linkwidget.php");
require_once("latestnewswidget.php");
require_once("admin/nomac.php");
require_once("admin/licensing.php");
require_once("admin/rulechange.php");
require_once("admin/imagewidget.php");
require_once("admin/frequency.php");
require_once("admin/club.php");
require_once("admin/class.php");


/* ShortCodes for the specific forms */
add_shortcode('nomac-licensing-form', 'outputNomacLicensingForm');
add_shortcode('nomac-licensing-list', 'outputNomacLicensingList');
add_shortcode('nomac-licensing-totals', 'outputNomacLicensingTotals');
add_shortcode('nomac-rulechange-form', 'outputNomacRulechangeForm');
add_shortcode('nomac-rulechange-list', 'outputNomacRulechangeList');


/* Installation / DB Creation */
register_activation_hook(__FILE__, 'nomac_add_capabilities');
register_activation_hook(__FILE__, 'nomac_licensing_install'); 
register_activation_hook(__FILE__, 'nomac_rulechange_install'); 
register_activation_hook(__FILE__, 'nomac_imagecycle_install'); 
register_deactivation_hook(__FILE__, 'nomac_remove_capabilties');

function nomac_licensing_install() {
	require_once("dbinstall.php");
	createLicensingTable();
}

function nomac_imagecycle_install() {
	require_once("dbinstall.php");
	createImageCycleTable();
}

function nomac_rulechange_install() {
	require_once("dbinstall.php");
	createRulechangeTable();
}

function nomac_add_capabilities() {
	$role = get_role('administrator');
	if ( empty($role) ) {
		die ("Sorry, we need an administrator role to activate the nomac plugin.");
	}


	$role->add_cap(NOMAC_CAP_ADMIN);
	$role->add_cap(NOMAC_CAP_LICENSING);
	$role->add_cap(NOMAC_CAP_IMAGECYCLE);
	$role->add_cap(NOMAC_CAP_RULECHANGE);
	$role->add_cap(NOMAC_CAP_FREQUENCY);
	$role->add_cap(NOMAC_CAP_CLUB);
	$role->add_cap(NOMAC_CAP_CLASS);
}


function nomac_remove_capabilities() {
	$role = get_role('administrator');
	if ( ! empty($role) ) {
		$role->remove_cap(NOMAC_CAP_ADMIN);
		$role->remove_cap(NOMAC_CAP_LICENSING);
		$role->remove_cap(NOMAC_CAP_IMAGECYCLE);
		$role->remove_cap(NOMAC_CAP_RULECHANGE);
		$role->remove_cap(NOMAC_CAP_FREQUENCY);
		$role->remove_cap(NOMAC_CAP_CLUB);
		$role->remove_cap(NOMAC_CAP_CLASS);		
    }
}






/* Admin menu/pages */ 
add_action('admin_menu', 'nomac_admin_menu');
function nomac_admin_menu() {
	add_menu_page("NOMAC Admin", "NOMAC", NOMAC_CAP_ADMIN, "NOMAC", "admin_nomac_main");
	add_submenu_page("NOMAC", "Licentie beheer", "Licentie beheer", NOMAC_CAP_LICENSING, "Licensing", "admin_nomac_licensing");
	add_submenu_page("NOMAC", "Reglementswijzigingen beheer", "Reglementswijzigingen beheer", NOMAC_CAP_RULECHANGE, "Reglementswijzigingen", "admin_nomac_rulechange");
	add_submenu_page("NOMAC", "Image Cycle", "Image Cycle", NOMAC_CAP_IMAGECYCLE, "ImageCycle", "admin_nomac_imagecycle");
	add_submenu_page("NOMAC", "Frequenties", "Frequenties", NOMAC_CAP_FREQUENCY, "Frequency", "admin_nomac_frequency");
	add_submenu_page("NOMAC", "Clubs", "Clubs", NOMAC_CAP_CLUB, "Club", "admin_nomac_club");
	add_submenu_page("NOMAC", "Klassen", "Klassen", NOMAC_CAP_CLASS, "Class", "admin_nomac_class");
}



// We need the session to be enabled when we access the admin functionality.
add_action('admin_init', 'nomac_session_start');
function nomac_session_start() {
	if (! session_id()) {
		session_name("nomac_session");
		session_start();
	}
}


add_action('admin_head', 'nomac_admin_head');
function nomac_admin_head() {
	echo '<link rel="stylesheet" type="text/css" href="' .plugins_url('admin/adminstyle.css', __FILE__). '">';
}

?>