<?php
require_once("validations.php");


function outputNomacRulechangeForm($attrs) {
	$out = "";
	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
		$out .= "The nomac rule change form needs a year parameter.";
		return $out;
	}
	$y = (int)$attrs["year"];
	
	if (isset($_REQUEST['submit'])) {
		$validateItems = rulechange_ValidateItems($y);
		if (count($validateItems) > 0) {
			$out .= '<ul class="error">';
			foreach ($validateItems as $valItem) {
				$out .= '<li>' . $valItem . '</li>';
			}
			$out .= '</ul>';
			$out .= '<br />';
			$out .= rulechange_outputForm($y);
		} else {
			$out .= rulechange_handlePost($y);
		}
	} else {	
		$out .= rulechange_outputForm($y);
	}
	return $out;
}


function rulechange_handlePost($yearOfLicense) {
	global $wpdb;
	$out  = "";
	
	$tablename = $wpdb->prefix . TABLE_RULECHANGE;
	
	$insertData = array();
	$insertData['Year'] = $yearOfLicense;
	$insertData['SubmittedByIp'] = $_SERVER["REMOTE_ADDR"];
	$insertData['SubmittedBy'] = cleanInput($_REQUEST['SubmittedBy'], true);
	$insertData['SubmitterEmail'] = cleanInput($_REQUEST['SubmitterEmail'], true);
	$insertData['Class'] = cleanInput($_REQUEST['Class'], true);
	$insertData['Page'] = cleanInput($_REQUEST['Page'], true);
	$insertData['Article'] = cleanInput($_REQUEST['Article'], true);
	$insertData['OldText'] = cleanInput($_REQUEST['OldText']);
	$insertData['NewText'] = cleanInput($_REQUEST['NewText']);
	$insertData['Comment'] = cleanInput($_REQUEST['Comment']);
	$insertData['Deleted'] = false;


/*
			Id bigint(20) unsigned not null auto_increment,
			Year YEAR(4) not null,
			SubmittedByIp varchar(64) not null,
			SubmittedOn TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			SubmitterBy varchar(200) not null,
			SubmitterEmail varchar(200) not null,
			Class varchar(10) not null,
			Page varchar(20) NULL,
			Article varchar(20) NULL,
			OldText MEDIUMTEXT,
			NewText MEDIUMTEXT,
			Comment MEDIUMTEXT,
			primary key(Id)
*/

	$insertFormat = array(	
		'%d', // year
		'%s', // SubmittedByIp
		'%s', // SubmittedBy
		'%s', // SubmitterEmail
		'%s', // Class
		'%s', // Page
		'%s', // Article
		'%s', // OldText
		'%s', // NewText
		'%s', // Comment
		'%d'); //Deleted

	if ($wpdb->insert($tablename, $insertData, $insertFormat)) {
		$out .= "Bedankt voor het insturen van een regelementswijziging! <br />";
	} else {
		$out .= '<span class="error">';
		$out .= 'Het opslaan is mislukt! Probeer het later nog eens....';
		$out .= 'Error: ' . mysql_error();
		$out .= '</span>';
	}
	return $out;
}


function rulechange_ValidateItems($y) {
	global $wpdb;

	$invalidItems = array();
	if (! RequiredIsSet('SubmittedBy') ) {
		$invalidItems[] = "'Ingediend door' is een verplicht veld.";
	} else if (! MinLength('SubmittedBy', 5)) {
		$invalidItems[] = "'Ingediend door' moet minimaal 5 letters hebben.";
	}

	if ( ! RequiredIsSet('SubmitterEmail') ) {
		$invalidItems[] = "Het e-mail adres is verplicht.";
	}

	if (RequiredIsSet('SubmitterEmail') && !filter_var($_REQUEST['SubmitterEmail'], FILTER_VALIDATE_EMAIL)) {
		$invalidItems[] = "Als je een e-mail address opgeeft, moet deze wel geldig zijn!";
	}

	if (! RequiredIsSet('Class') ) {
		$invalidItems[] = "De klasse moet gekozen worden.";
	} else {
		$class = addslashes($_REQUEST['Class']);
		$tClass = $wpdb->prefix . TABLE_CLASS;
		$row = $wpdb->get_row("SELECT * FROM $tClass WHERE Code = '".$class."'");
		if (!count($row)) {
			$invalidItems[] = "Geen geldige klasse gekozen.";
		}
	}

	if (! RequiredIsSet('NewText') ) {
		$invalidItems[] = "Nieuwe tekst is een verplicht veld.";
	}


	return $invalidItems;
}






function rulechange_outputForm($y) {
	$out  = '<form action="' . add_query_arg(array()) .'" method="post" enctype="multipart/form-data">';
	$out .= '<table class="nostyle">';
	$out .= '<tr>';
	$out .= '<th>Ingediend door *:</th>';
	if (isset($_REQUEST['SubmittedBy'])) {
		$out .= '<td><input type="text" name="SubmittedBy" size="35" value="'.$_REQUEST['SubmittedBy'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="SubmittedBy" size="35" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>E-mail adres *:</th>';
	if (isset($_REQUEST['SubmitterEmail'])) {
		$out .= '<td><input type="text" name="SubmitterEmail" size="35" value="'.$_REQUEST['SubmitterEmail'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="SubmitterEmail" size="35" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Klasse *:</th>';
	$out .= '<td>'.outputDropdown('nomac_class', 'Class').'</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Pagina:</th>';
	if (isset($_REQUEST['Page'])) {
		$out .= '<td><input type="text" name="Page" size="10" value="'.$_REQUEST['Page'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="Page" size="10" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Artikel:</th>';
	if (isset($_REQUEST['Article'])) {
		$out .= '<td><input type="text" name="Article" size="10" value="'.$_REQUEST['Article'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="Article" size="10" /></td>';
	}
	$out .= '</tr>';


	$out .= '<tr>';
	$out .= '<th>Oude tekst:</th>';
	if (isset($_REQUEST['OldText'])) {
		$out .= '<td><textarea name="OldText" rows="8" cols="60">' . $_REQUEST['OldText']. '</textarea></td>';
	} else {
		$out .= '<td><textarea name="OldText" rows="8" cols="60"></textarea></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Nieuwe tekst *:</th>';
	if (isset($_REQUEST['NewText'])) {
		$out .= '<td><textarea name="NewText" rows="8" cols="60">' . $_REQUEST['NewText']. '</textarea></td>';
	} else {
		$out .= '<td><textarea name="NewText" rows="8" cols="60"></textarea></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Uitleg:</th>';
	if (isset($_REQUEST['Comment'])) {
		$out .= '<td><textarea name="Comment" rows="8" cols="60">' . $_REQUEST['Comment']. '</textarea></td>';
	} else {
		$out .= '<td><textarea name="Comment" rows="8" cols="60"></textarea></td>';
	}
	$out .= '</tr>';

	$out .= '</table>';

	$out .= '<input type="submit" name="submit" value="Verzenden!"/>';
	$out .= '</form>';

	return $out;
}


?>