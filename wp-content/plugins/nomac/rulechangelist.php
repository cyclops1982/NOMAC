<?php

function outputNomacRulechangeList($attrs) {
	global $wpdb;
	$out = "";
	
	$tLic = $wpdb->prefix . TABLE_RULECHANGE;

	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
		$out .= "The nomac rule change list needs a year parameter.";
		return $out;
	}
    $year = (int)$attrs["year"];
	
	$rows = $wpdb->get_results("SELECT LIC.Voornaam, LIC.Achternaam, C.Name AS Klasse, LIC.LidBijClub, LIC.RegistrationDate, LIC.Status FROM $tLic AS LIC INNER JOIN $tClass AS C ON C.Code = LIC.Klasse WHERE Year = $year ORDER BY LIC.Klasse, LIC.RegistrationDate");
	
	if (count($rows) > 0) {
		$prevClass = "";
		$out .= '<table>';
		foreach ($rows as $row) {
			if ($prevClass != $row->Klasse) {
				$out .= '<tr><th colspan="4" class="header">' . $row->Klasse .'</th></tr>';
				$out .= '<tr><th>Naam</th><th>Klasse</th><th>Club</th><th>Status</th></tr>';
				$prevClass = $row->Klasse;
			}
			$out .= '<tr>';
			$out .= '<td>' . stripslashes($row->Voornaam) . ' ' . stripslashes($row->Achternaam) . '</td>';
			$out .= '<td>' . stripslashes($row->Klasse) . '</td>';
			$out .= '<td>' . stripslashes($row->LidBijClub) . '</td>';
			$out .= '<td>' . stripslashes($row->Status) . '</td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
	}
	else {
		$out .= "Er zijn nog geen aanmeldingen voor het seizoen $year.";
	}

	return $out;
}

?>