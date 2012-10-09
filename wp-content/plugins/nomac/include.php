<?php


/* Gernal functions needed for all the NOMAC customizations via plugin.
If classes are needed, they should not be added in this file, but are included in this file */


// Some defines for table names
DEFINE('TABLE_LICENSING', 'nomac_licensing');
DEFINE('TABLE_RULECHANGE', 'nomac_rulechange');
DEFINE('TABLE_CLUBS', 'nomac_clubs');
DEFINE('TABLE_CLASS', 'nomac_class');
DEFINE('TABLE_FREQUENCY', 'nomac_frequency');
DEFINE('TABLE_IMAGECYCLE', 'nomac_imagecycle');

// Capabilities
DEFINE('NOMAC_CAP_ADMIN', 'Nomac Admin');
DEFINE('NOMAC_CAP_LICENSING', 'Nomac Licensing Admin');
DEFINE('NOMAC_CAP_RULECHANGE', 'Nomac Reglementswijzigingen Admin');
DEFINE('NOMAC_CAP_IMAGECYCLE', 'Nomac Image Cycle Admin');


DEFINE('NOMAC_PLUGIN_PATH', WP_PLUGIN_DIR . "/" . basename(dirname(__FILE__)));
DEFINE('NOMAC_BASEPATH', basename(dirname(__FILE__)));




// Returns false if a table does not exist.
// PLease mind that the table is prefixed with the $wpdb->prefix by this method itself.
function table_exists($table)
{
        global $wpdb;
        return strtolower( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) ) == strtolower( $table );
}


function create_table($tablename, $sql, $option, $version) {
	if (table_exists($tablename)) 
		return;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($sql);
	add_option($option, $version);
	
	return table_exists($tablename);
}


?>
