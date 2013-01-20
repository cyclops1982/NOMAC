<?php
require_once("validations.php");


function outputNomacLicensingForm($attrs) {
	$out = "";
	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
		$out .= "The nomac licensing form needs a year parameter.";
		return $out;
	}
	$y = (int)$attrs["year"];
	
	if (isset($_REQUEST['submit'])) {
		$validateItems = licensing_ValidateItems($y);
		if (count($validateItems) > 0) {
			$out .= '<ul class="error">';
			foreach ($validateItems as $valItem) {
				$out .= '<li>' . $valItem . '</li>';
			}
			$out .= '</ul>';
			$out .= '<br />';
			$out .= licensing_outputForm($y);
		} else {
			$bedrag = licensing_GetBedrag();
			$out .= licensing_handlePost($y, $bedrag);
		}
	} else {	
		$out .= licensing_outputForm($y);
	}


	return $out;
}


function licensing_GetBedrag() {
	global $wpdb;
	$tablename = $wpdb->prefix . TABLE_CLASS;
	$class = addslashes($_REQUEST['klasse']);
	return $wpdb->get_var("SELECT Price FROM $tablename WHERE Code = '".$class."';");
}

function licensing_handlePost($yearOfLicense, $bedrag) {
	global $wpdb;
	$out  = "";
	
	$tablename = $wpdb->prefix . TABLE_LICENSING;
	
	$insertData = array();
	$insertData['Voornaam'] = strip_tags($_REQUEST['voornaam']);
	$insertData['Achternaam'] = strip_tags($_REQUEST['achternaam']);
	$insertData['Straat'] = strip_tags($_REQUEST['straat']);
	$insertData['HuisNr'] = strip_tags($_REQUEST['huisnr']);
	$insertData['PostCode'] = strip_tags($_REQUEST['postcode']);
	$insertData['Woonplaats'] = strip_tags($_REQUEST['woonplaats']);
	$insertData['GeboorteDatum'] = licensing_BuildDate($_REQUEST['geboortedatum']);
	$insertData['TelefoonNr'] = strip_tags($_REQUEST['telefoonnr']);
	$insertData['Email'] = strip_tags($_REQUEST['email']);
	$insertData['LidBijClub'] = strip_tags($_REQUEST['lidbijclub']);
	$insertData['Freq1'] = strip_tags($_REQUEST['freq1']);
	$insertData['Freq2'] = strip_tags($_REQUEST['freq2']);
	$insertData['Transponder'] = strip_tags($_REQUEST['transponder']);
	$insertData['VorigeLicentieNr'] = strip_tags($_REQUEST['vorigelicentienr']);
	$insertData['Klasse'] = strip_tags($_REQUEST['klasse']);
	$insertData['Bedrag'] = $bedrag;
	$insertData['Status'] = 'Aanvraag ontvangen'; 
	$insertData['year'] = $yearOfLicense;
	$insertData['RegistrationDate'] = date('Y-m-d H:i:s');
	$insertData['foto'] = GetBinaryFile('foto'); 
	$insertData['fotoContentType'] = GetBinaryContentType('foto');

	$insertFormat = array(	'%s',
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%s', 
				'%d', 
				'%s', 
				'%d', 
				'%s', 
				'%s',
				'%s'); 

	if ($wpdb->insert($tablename, $insertData, $insertFormat)) {
		$out .= "Bedankt voor het aanmelden! <br />";
		$out .= 'Om de aanmelding af te ronden moet u &euro; '.$bedrag.',- overmaken <a href="/algemeen/adresgegevens">aan het eerder vermelde rekening nummer</a>.<br />';

		$email  = 'Beste ' . $_REQUEST['voornaam'] . ','."\n\n";
		$email .= 'Hartelijk dank voor je aanmelding ' . $_REQUEST['klasse'] . ".\n";
		$email .= 'Om je aanmelding definitief te maken dien je het verschuldigde bedrag van € ' . $bedrag.',- over te maken. De bankgegevens zijn te vinden op http://www.nomac.nl/algemeen/adresgegevens/.'. "\n\n";
		$email .= 'Met vriendelijke groet,'."\n";
		$email .= 'Het NOMAC Bestuur.'."\n";
		$headers = 'From: secretaris@nomac.nl' . "\n" .
			   'Reply-To: secretaris@nomac.nl' . "\n" .
		           'X-Mailer: PHP/' . phpversion();
		if (! mail($_REQUEST['email'], "Nomac Aanmelding", $email, $headers)) {
			$out .= '<br />De aanmelding is gelukt, maar de bevestigings e-mail is MISLUKT.<br />';
		} 
	} else {
		$out .= '<span class="error">';
		$out .= 'Het aanmelden is mislukt! Probeer het later nog eens....';
		$out .= 'Error: ' . mysql_error();
		$out .= '</span>';
	}
	return $out;
}

function licensing_GetBinaryContentType($name) {
	if (!isset($_FILES[$name]) || $_FILES[$name]['size'] <= 0 || $_FILES[$name]['error'] != 0) {
		return NULL;
	}
	return $_FILES[$name]['type'];
	

}

function licensing_GetBinaryFile($name) {
	if (!isset($_FILES[$name]) || $_FILES[$name]['size'] <= 0 || $_FILES[$name]['error'] != 0) {
		return NULL;
	}
	$fp = fopen($_FILES[$name]['tmp_name'], 'r');
	if ($fp) {
		$content = addslashes(fread($fp, filesize($_FILES[$name]['tmp_name'])));
		fclose($fp);
		return $content;
	}
	return NULL;
}

function licensing_BuildDate($date) {
	$firstDash = strpos($date, "-");
	$d = substr($date, 0, $firstDash);
	$m = substr($date, $firstDash + 1, strpos($date, "-", $firstDash));
	$y = substr($date, -4);
	
	return $y.'-'.$m.'-'.$d;
}

function licensing_ValidateItems($y) {
	global $wpdb;

	// ADd filter date http://au.php.net/manual/en/function.filter-var.php
	$invalidItems = array();
	if (! RequiredIsSet('voornaam') ) {
		$invalidItems[] = "Voornaam is een verplicht veld.";
	} else if (! MinLength('voornaam', 3)) {
		$invalidItems[] = "Voornaam moet minimaal 3 letters hebben.";
	}

	if (! RequiredIsSet('achternaam') ) {
		$invalidItems[] = "Achternaam is een verplicht veld.";
	} else if (! MinLength('achternaam', 3)) {
		$invalidItems[] = "Achternaam moet minimaal 3 letters hebben.";
	}

	if (! RequiredIsSet('straat') ) {
		$invalidItems[] = "Straat is een verplicht veld.";
	} else if (! MinLength('straat', 3)) {
		$invalidItems[] = "Straat moet minimaal 3 letters hebben.";
	}

	if (! RequiredIsSet('huisnr') ) {
		$invalidItems[] = "Huisnr is een verplicht veld.";
	}
	
	if (! RequiredIsSet('postcode') ) {
		$invalidItems[] = "Postcode is een verplicht veld.";
	} else if (! MinLength('postcode', 6)) {
		$invalidItems[] = "Postcode moet uit minimaal 6 tekens bestaan.";
	}

	if (! RequiredIsSet('woonplaats') ) {
		$invalidItems[] = "Woonplaats is een verplicht veld.";
	} else if (! MinLength('woonplaats',4)) {
		$invalidItems[] = "Woonplaats moet 4 letters hebben.";
	}

	if (! RequiredIsSet('geboortedatum') ) {
		$invalidItems[] = "Geboorte datum is een verplicht veld.";
	} else if (!MinLength('geboortedatum', 6)) {
		$invalidItems[] = "Een geboortedatum moet minimaal 6 tekens hebben.";
	} else if (preg_match("/^[0-9]{1,2}\-[0-9]{1,2}-[0-9]{4}/", $_REQUEST['geboortedatum']) == 0) {
		$invalidItems[] = "Geboortedatum is niet juist ingevoerd. Voorbeeld: 29-06-1982";
	} else {
		$date = $_REQUEST['geboortedatum'];
		$firstDash = strpos($date, "-");
		$day = substr($date, 0, $firstDash);
		$month = substr($date, $firstDash + 1, strpos($date, "-", $firstDash));
		$year1 = substr($date, -4);
	
		if (! checkdate($month, $day, $year1)) {
			$invalidItems[] = "Er is geen geldige geboortedatum ingevoerd. Voorbeeld: 29-06-1982";
		}
	}
	

	if (! RequiredIsSet('transponder') ) {
		$invalidItems[] = "Transponder is een verplicht veld.";
	}
	if (!MinLength('transponder', 6)) {
		$invalidItems[] = "Een transponder nummer moet minimaal 6 cijfers hebben.";
	} else if (!is_numeric($_REQUEST['transponder'])) {
		$invalidItems[] = "Een transponder mag alleen maar nummers bevatten.";
	}

	if ( ! RequiredIsSet('email') ) {
		$invalidItems[] = "Het e-mail adres is verplicht.";
	}

	if (RequiredIsSet('email') && !filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
		$invalidItems[] = "Als je een e-mail address opgeeft, moet deze wel geldig zijn!";
	}

	if (! RequiredIsSet('freq1') ) {
		$invalidItems[] = "De eerste frequentie is een verplicht veld.";
	}
	if (! RequiredIsSet('klasse') ) {
		$invalidItems[] = "De klasse moet gekozen worden.";
	} else {
		$class = addslashes($_REQUEST['klasse']);

		$tLicensing = $wpdb->prefix . TABLE_LICENSING;
		$tClass = $wpdb->prefix . TABLE_CLASS;

		$driverCount = $wpdb->get_var("SELECT count(Id) FROM $tLicensing WHERE year = $y AND Klasse = '".$class."'");
		$row = $wpdb->get_row("SELECT * FROM $tClass WHERE Code = '".$class."'");
		
		if (!empty($row->CloseDate) && $row->CloseDate <= date('Y-m-d')) {
			$invalidItems[] = "Voor de klasse $row->Name kunt u zich niet meer aanmelden. De inschrijving is gestopt op $row->CloseDate.";
		}

		if ($row->MaxDrivers > 0 && $driverCount >= $row->MaxDrivers && $row->MaxDriversCloseDate <= Date('Y-m-d')) {
			$invalidItems[] = "Er zijn al meer " . $row->MaxDrivers . " aangemeldingen. Dit betekend dat de sluitingsdatum ingaat voor de datum ". $row->MaxDriversCloseDate .". Deze datum is al voorbij en daarom kunt u zich niet meer aanmelden voor deze klasse.";
		}
	}

	if (RequiredIsSet('voornaam') && RequiredIsSet('achternaam') && RequiredIsSet('klasse')) {
		$tLicensing = $wpdb->prefix . TABLE_LICENSING;
		$q = "SELECT Id FROM $tLicensing WHERE Voornaam = '".addslashes(strip_tags($_REQUEST['voornaam']))."' AND Achternaam = '".addslashes(strip_tags($_REQUEST['achternaam']))."' AND Klasse = '".addslashes(strip_tags($_REQUEST['klasse']))."' AND year = ".$y."";
		$row = $wpdb->get_row($q);
		if ($row != NULL) {
			$invalidItems[] = "Er is al iemand met die Voornaam en Achternaam aangemeld in de klasse. Je kan je niet twee keer aanmelden.";
		}
	}


	// +Validate the photo
	if ( ! isset($_FILES['foto']) || $_FILES['foto']['error'] != 0) {
		$invalidItems[] = "De foto is verplicht.";
	} else {
		$accepted = false;
		switch($_FILES['foto']['type']) {
			case "image/jpeg":
			case "image/pjpeg":
			case "image/jpg": 
			case "image/bmp": 
			case "image/gif": 
			case "image/tiff":
			case "image/png":
				$accepted = true;
				break;
			default:
				$accepted = false;
				break;
		}
		if ( ! $accepted) {
			$invalidItems[] = "Het type foto kunnen we niet herkennen.";
		}
	}	


	return $invalidItems;
}



function licensing_outputForm($y) {
	$out  = '<form action="' . add_query_arg(array()) .'" method="post" enctype="multipart/form-data">';
	$out .= '<table class="nostyle">';

	$out .= '<tr>';
	$out .= '<th>Voornaam *:</th>';
	if (isset($_REQUEST['voornaam'])) {
		$out .= '<td colspan="3"><input type="text" name="voornaam" size="35" value="'.$_REQUEST['voornaam'].'"/></td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="voornaam" size="35" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Achternaam *:</th>';
	if (isset($_REQUEST['achternaam'])) {
		$out .= '<td colspan="3"><input type="text" name="achternaam" size="35" value="'.$_REQUEST['achternaam'].'"/></td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="achternaam" size="35" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Straat *:</th>';
	if (isset($_REQUEST['straat'])) {
		$out .= '<td><input type="text" name="straat" size="35" value="'.$_REQUEST['straat'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="straat" size="35" /></td>';
	}
	$out .= '<th>Huisnr *:</th>';
	if (isset($_REQUEST['huisnr'])) {
		$out .= '<td><input type="text" name="huisnr" size="10" value="'.$_REQUEST['huisnr'].'" /></td>';
	} else {
		$out .= '<td><input type="text" name="huisnr" size="10" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Woonplaats *:</th>';
	if (isset($_REQUEST['woonplaats'])) {
		$out .= '<td><input type="text" name="woonplaats" size="35" value="'.$_REQUEST['woonplaats'].'"/></td>';
	} else {
		$out .= '<td><input type="text" name="woonplaats" size="35" /></td>';
	}
	$out .= '<th>Postcode *:</th>';
	if (isset($_REQUEST['postcode'])) {
		$out .= '<td><input type="text" name="postcode" size="10" maxlength="7" value="'.$_REQUEST['postcode'].'" /></td>';
	} else {
		$out .= '<td><input type="text" name="postcode" size="10" maxlength="7" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Geboorte Datum *:</th>';
	if (isset($_REQUEST['geboortedatum'])) {
		$out .= '<td colspan="3"><input type="text" name="geboortedatum" size="8" value="'.$_REQUEST['geboortedatum'].'" /> (dag-maand-jaar)</td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="geboortedatum" size="8" />(dag-maand-jaar)</td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Telefoon:</th>';
	if (isset($_REQUEST['telefoonnr'])) {
		$out .= '<td colspan="3"><input type="text" name="telefoonnr" size="15" value="'.$_REQUEST['telefoonnr'].'"/></td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="telefoonnr" size="15" /></td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>E-mail *:</th>';
	if (isset($_REQUEST['email'])) {
		$out .= '<td colspan="3"><input type="text" name="email" size="35" value="'.$_REQUEST['email'].'" /></td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="email" size="35" /></td>';
	}
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Frequentie 1 *:</th>';
	$out .= '<td>'.outputDropdown(TABLE_FREQUENCY, 'freq1').'</td>';
	$out .= '<th>Frequentie 2:</th>';
	$out .= '<td>'.outputDropdown(TABLE_FREQUENCY, 'freq2').'</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Transponder *:</th>';
	if (isset($_REQUEST['transponder'])) {
		$out .= '<td colspan="3"><input type="text" name="transponder" size="10" value="'.$_REQUEST['transponder'].'" /></td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="transponder" size="10" /></td>';
	}
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Vorige licentie nr:</th>';
	if (isset($_REQUEST['vorigelicentienr'])) {
		$out .= '<td colspan="3"><input type="text" name="vorigelicentienr" size="10" value="'.$_REQUEST['vorigelicentienr'].'" /> (alleen als je een licentie had)</td>';
	} else {
		$out .= '<td colspan="3"><input type="text" name="vorigelicentienr" size="10" /> (alleen als je een licentie had)</td>';
	}
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Klasse *:</th>';
	$out .= '<td colspan="3">'.outputClassDropdown('klasse').'</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Lid bij club:</th>';
	$out .= '<td colspan="3">'.outputDropdown(TABLE_CLUBS, 'lidbijclub').'</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Foto *:</th>';
	$out .= '<td colspan="3"><input type="file" name="foto" /></td>';
	$out .= '</tr>';

	$out .= '</table>';
	$out .= '<input type="submit" name="submit" value="Akkoord en Verzenden!"/>';
	$out .= '</form>';

	return $out;
}



function outputClassDropdown($controlName, $showDefault = true) {
	global $wpdb;
	$tablename = $wpdb->prefix . TABLE_CLASS;
	
	$rows = $wpdb->get_results("SELECT Code, Name, Price FROM " . $tablename);
	$out  = "";
	$out .= '<select name="' . $controlName . '" >';	
	if ($showDefault) {
		$out .= '<option value="" selected="true">Maak een keuze</option>';
	}
	foreach ($rows as $row) {
		if (isset($_REQUEST[$controlName]) && $_REQUEST[$controlName] == $row->Code) {
			$out .= '<option selected="selected" value="' . $row->Code . '">' . $row->Name . '</option>';
		} else {
			$out .= '<option value="' . $row->Code . '">' . $row->Name . ' - (&euro; '.$row->Price.',-)</option>';
		}
	}
	$out .= '</select>';

	return $out;
}


?>
