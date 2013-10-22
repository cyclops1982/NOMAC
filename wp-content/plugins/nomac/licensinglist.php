<?php

function outputNomacLicensingList($attrs) {
	global $wpdb;
	$out = "";
	
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
                $out .= "The nomac licensing form needs a year parameter.";
                return $out;
        }
        $year = (int)$attrs["year"];
	
	$rows = $wpdb->get_results("SELECT LIC.Voornaam, LIC.Achternaam, C.Name AS Klasse, LIC.LidBijClub, LIC.RegistrationDate, LIC.Status 
								FROM $tLic AS LIC 
								INNER JOIN $tClass AS C ON C.Code = LIC.Klasse 
								WHERE Year = $year AND LIC.Status <> 'Verwijderd' AND LIC.Status <> 'Ingetrokken'
								ORDER BY LIC.Klasse, LIC.RegistrationDate");
	
	if (count($rows) > 0) {
		$prevClass = "";
		$out .= '<table>';
		foreach ($rows as $row) {
			if ($prevClass != $row->Klasse) {
				$out .= '<tr><th colspan="4" class="header" id="'.$row->Klasse.'">' . $row->Klasse .'</th></tr>';
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

function outputNomacLicensingTotals($attrs) {
	global $wpdb;
	$out = "";

	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
                $out .= "The totals list needs a year parameter.";
                return $out;
        }
        $year = (int)$attrs["year"];

	$counts = $wpdb->get_results("SELECT COUNT(LIC.Id) AS Count, C.Name AS Klasse 
									FROM $tLic AS LIC 
									INNER JOIN $tClass AS C ON LIC.Klasse = C.Code 
									WHERE Year = $year AND LIC.Status <> 'Verwijderd' AND LIC.Status <> 'Ingetrokken'
									GROUP BY LIC.Klasse");
	if (count($counts) > 0) {
		$out .= '<table>';
		$out .= '<tr><th>Klasse</th><th>Aantal</th></tr>';
		foreach ($counts as $count) {
			$out .= '<tr><td>'.$count->Klasse .'</td><td>'.$count->Count.'</td></tr>';
		}
		$out .= '</table>';
	}
	else {
		$out .= "De totalen kunnen niet getoont worden, omdat er nog geen aanmeldingen zijn voor $year";
	}
	return $out;

}
?>
