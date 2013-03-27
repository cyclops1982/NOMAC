<?php
global $licensing_statusses;
$licensing_statusses = Array('Aanvraag ontvangen', 'Betaling ontvangen', 'Betaling onvoldoende', 'Ter goedkeuring bij club', 'Toegekend', 'Afgewezen', 'Ingetrokken', 'Verwijderd');

function admin_nomac_license() {
	global $wpdb;
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	echo '<div class="wrap">';
	screen_icon('users');
	echo '<h2>NOMAC Licentie Beheer</h2>';
	echo '<p>Op deze pagina worden de licenties beheerd. Het beheer kan de status van de licentie aanpassen.</p>';


	if (!isset($_SESSION['LIC_YEAR'])) {
		$_SESSION['LIC_YEAR'] = date('Y');
	}

	if (! isset($_REQUEST['do'])) {
		$_REQUEST['do'] = "";
	}



	switch($_REQUEST['do']) {
		case "Statussen aanpassen":
			echo "<ul>";
			foreach ($_REQUEST['status'] as $statkey => $statval) {
				if (is_numeric($statkey) && !empty($statval)) {
					$id = (int)$statkey;
					$where = array("Id" => $id);
					$data = array("Status" => $statval);
					if ($wpdb->update($tLic, $data, $where, array('%s'), array('%d')) == 1) {
						echo "<li>Status voor ".stripslashes($_REQUEST['DriverName'][$id])." aangepast.</li>";
					} else {
						echo "<li>Status voor ".stripslashes($_REQUEST['DriverName'][$id])." aangepassen is <b>MISLUKT!</b>.</li>";
						echo $wpdb->error();
					}
				}
			}
			echo "</ul>";
			admin_nomac_license_defaultpage();
		break;
		case "saveLicenseChange":
			admin_nomac_license_save($_SESSION['LIC_YEAR']);
		break;
		case "EditLicense":
			admin_nomac_license_editform($_SESSION['LIC_YEAR']);
			break;
		case "Selecteer Jaar":
			$_SESSION['LIC_YEAR'] = $_REQUEST['year'];
			admin_nomac_license_defaultpage();
			break;
		default:
			admin_nomac_license_defaultpage();
			break;
	}

	
	echo "</div>";
}

function admin_nomac_license_defaultpage() {
	admin_nomac_license_showyearselect($_SESSION['LIC_YEAR']);
	admin_nomac_license_list($_SESSION['LIC_YEAR']);
	admin_nomac_license_totals($_SESSION['LIC_YEAR']);
}



function admin_nomac_license_save($year) {
	global $wpdb;
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	if (!isset($_REQUEST['licenseid'])) {
		echo "Something went wrong, we need a license id. <br />";
		return;
	}
	$licId = (int)$_REQUEST['licenseid'];

	$bedrag = licensing_GetBedrag($_REQUEST['klasse']);


	$updateData = array();
	$updateData['Voornaam'] = strip_tags($_REQUEST['voornaam']);
	$updateData['Achternaam'] = strip_tags($_REQUEST['achternaam']);
	$updateData['Straat'] = strip_tags($_REQUEST['straat']);
	$updateData['HuisNr'] = strip_tags($_REQUEST['huisnr']);
	$updateData['PostCode'] = strip_tags($_REQUEST['postcode']);
	$updateData['Woonplaats'] = strip_tags($_REQUEST['woonplaats']);
	$updateData['GeboorteDatum'] = licensing_BuildDate($_REQUEST['geboortedatum']);
	$updateData['TelefoonNr'] = strip_tags($_REQUEST['telefoonnr']);
	$updateData['Email'] = strip_tags($_REQUEST['email']);
	$updateData['LidBijClub'] = strip_tags($_REQUEST['lidbijclub']);
	$updateData['Freq1'] = strip_tags($_REQUEST['freq1']);
	$updateData['Freq2'] = strip_tags($_REQUEST['freq2']);
	$updateData['Freq3'] = strip_tags($_REQUEST['freq3']);
	$updateData['Transponder'] = strip_tags($_REQUEST['transponder']);
	$updateData['Transponder2'] = strip_tags($_REQUEST['transponder2']);
	$updateData['VorigeLicentieNr'] = strip_tags($_REQUEST['vorigelicentienr']);
	$updateData['Klasse'] = strip_tags($_REQUEST['klasse']);
	$updateData['Bedrag'] = $bedrag;
	$updateData['Status'] = $_REQUEST['status'];



	if (licensing_GetBinaryContentType('foto') != NULL) {
		$updateData['foto'] = licensing_GetBinaryFile('foto');
		$updateData['fotoContentType'] = licensing_GetBinaryContentType('foto');

		$filename = $licId;
		switch($updateData['fotoContentType']) {
			case "image/jpeg": $filename = $filename . ".jpg"; break;
			case "image/pjpeg": $filename = $filename . ".jpg"; break; // An MSIE Special.
			case "image/jpg": $filename = $filename . ".jpg"; break;
			case "image/bmp": $filename = $filename . ".bmp"; break;
			case "image/gif": $filename = $filename . ".gif"; break;
			case "image/tiff": $filename = $filename . ".tiff"; break;
			case "image/png": $filename = $filename . ".png"; break;
			default: $filename = $filename . ".UNKNOWN"; break;

		}
		$exportDir = NOMAC_PLUGIN_PATH . "/imagecache/" . $year . "/";
		$imagePath = $exportDir . $filename;
		unlink($imagePath);
	}

	$updateFormat = array(	
				'Voornaam' => '%s',
				'Achternaam' => '%s',
				'Straat' => '%s',
				'HuisNr' => '%s',
				'PostCode' => '%s',
				'Woonplaats' => '%s',
				'GeboorteDatum' => '%s',
				'TelefoonNr' => '%s',
				'Email' => '%s',
				'LidBijClub' => '%s',
				'Freq1' => '%s',
				'Freq2' => '%s',
				'Freq3' => '%s',
				'Transponder' => '%s',
				'Transponder2' => '%s',
				'VorigeLicentieNr' => '%s',
				'Klasse' => '%s',
				'Bedrag' => '%d',
				'Status' => '%s'
				);

	if (isset($updateData['fotoContentType'])) {
		$updateFormat['foto'] = '%s'; //foto
		$updateFormat['fotoContentType'] = '%s';  //fotocontentype
	}

	$where = array();
	$where['Id'] = $licId;
	$wpdb->show_errors();
	$updateRes = $wpdb->update($tLic, $updateData, $where, $updateFormat, "%d");
	if ($updateRes == 0 || $updateRes == false) {
		echo 'Sorry, no update performed!<br />';
	} else {
		echo 'Update done!';
	}

	admin_nomac_license_list($year);
	admin_nomac_license_totals($year);
}

function admin_nomac_license_editform($year) {
	global $wpdb, $licensing_statusses;
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	if (!isset($_REQUEST['licenseid'])) {
		echo "Something went wrong, we need a license id. <br />";
		return;
	}
	$licId = (int)$_REQUEST['licenseid'];

	$query = "SELECT LIC.*, C.Name AS ClassName FROM $tLic AS LIC INNER JOIN $tClass AS C ON C.Code = LIC.Klasse WHERE LIC.Id = $licId";
	$lic = $wpdb->get_row($query, OBJECT);

	$out  = '<form action="' . add_query_arg(array()) .'" method="post" enctype="multipart/form-data">';
	$out .= '<table class="nostyle">';

	$out .= '<tr>';
	$out .= '<th>Voornaam *:</th>';
	$out .= '<td colspan="3"><input type="text" name="voornaam" size="35" value="'.stripslashes($lic->Voornaam).'"/></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Achternaam *:</th>';
	$out .= '<td colspan="3"><input type="text" name="achternaam" size="35" value="'.stripslashes($lic->Achternaam).'"/></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Straat *:</th>';
	$out .= '<td><input type="text" name="straat" size="35" value="'.stripslashes($lic->Straat).'"/></td>';
	$out .= '<th>Huisnr *:</th>';
	$out .= '<td colspan="3"><input type="text" name="huisnr" size="35" value="'.stripslashes($lic->HuisNr).'"/></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Woonplaats *:</th>';
	$out .= '<td><input type="text" name="woonplaats" size="35" value="'.stripslashes($lic->Woonplaats).'"/></td>';
	$out .= '<th>Postcode *:</th>';
	$out .= '<td><input type="text" name="postcode" size="10" maxlength="7" value="'.stripslashes($lic->PostCode).'" /></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Geboorte Datum *:</th>';
	$gebDate = new DateTime($lic->GeboorteDatum);
	$out .= '<td colspan="3"><input type="text" name="geboortedatum" size="8" value="'.$gebDate->format('d-m-Y').'" /> (dag-maand-jaar)</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Telefoon:</th>';
	$out .= '<td colspan="3"><input type="text" name="telefoonnr" size="15" value="'.stripslashes($lic->TelefoonNr).'"/></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>E-mail *:</th>';
	$out .= '<td colspan="3"><input type="text" name="email" size="35" value="'.stripslashes($lic->Email).'" /></td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Frequentie 1 *:</th>';
	$out .= '<td colspan="3">' . outputDropdown(TABLE_FREQUENCY, 'freq1', false, $lic->Freq1) . '</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Frequentie 2:</th>';
	$out .= '<td colspan="3">' . outputDropdown(TABLE_FREQUENCY, 'freq2', true, $lic->Freq2) . '</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Frequentie 3:</th>';
	$out .= '<td colspan="3">' . outputDropdown(TABLE_FREQUENCY, 'freq3', true, $lic->Freq3) . '</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>1<sup>e</sup> Transponder *:</th>';
	$out .= '<td><input type="text" name="transponder" size="10" value="'.stripslashes($lic->Transponder).'" /></td>';
	$out .= '<th>2<sup>e</sup> Transponder:';
	$out .= '<td><input type="text" name="transponder2" size="10" value="'.stripslashes($lic->Transponder2).'" /></td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Vorige licentie nr:</th>';
	$out .= '<td colspan="3"><input type="text" name="vorigelicentienr" size="10" value="'.stripslashes($lic->VorigeLicentieNr).'" /></td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Status:</th>';
	$out .= '<td colspan="3">';
	$out .= '<select name="status">';
	foreach ($licensing_statusses as $stat) {
		if ($lic->Status == $stat) {
			$out .= '<option value="'.$stat.'" selected="selected">'.$stat.'</option>';
		} else {
			$out .= '<option value="'.$stat.'">'.$stat.'</option>';
		}
	}
	$out .= '</select>';
	$out .= '</td>';
	$out .= '</tr>';



	$out .= '<tr>';
	$out .= '<th>Klasse *:</th>';
	$out .= '<td colspan="3">'.outputClassDropdown('klasse', false, $lic->Klasse).'</td>';
	$out .= '</tr>';

	$out .= '<tr>';
	$out .= '<th>Lid bij club:</th>';
	$out .= '<td colspan="3">'.outputDropdown(TABLE_CLUBS, 'lidbijclub', true, $lic->LidBijClub).'</td>';
	$out .= '</tr>';
	
	$out .= '<tr>';
	$out .= '<th>Foto *:</th>';
	$out .= '<td colspan="3"><input type="file" name="foto" /> (alleen selectern als er een nieuwe foto is!)</td>';
	$out .= '</tr>';

	$out .= '</table>';
	$out .= '<input type="hidden" name="do" value="saveLicenseChange" />';
	$out .= '<input type="hidden" name="licenseid" value="'.$_REQUEST['licenseid'].'" />';
	$out .= '<input type="submit" name="submit" value="Opslaan"/>';
	$out .= '</form>';

	echo $out;
}

function admin_nomac_license_showyearselect($year) {
	global $wpdb;
	$tLic = $wpdb->prefix . TABLE_LICENSING;

	$yearQuery = "SELECT year FROM $tLic GROUP BY year";
	$years = $wpdb->get_results($yearQuery);

	echo '<form method="post" action="">';
	echo 'Selecteer een jaar/seizoen: ';
	echo '<select name="year">';
	foreach ($years as $y) {
		if ($year == $y->year) {
			echo '<option value="'.$y->year.'" selected="selected">'.$y->year.'</option>';
		} else {
			echo '<option value="'.$y->year.'">'.$y->year.'</option>';
		}
	}
	echo '</select>';
	echo '<input class="button-secondary" type="submit" name="do" value="Selecteer Jaar" />';
	echo '</form>';	
}


function admin_nomac_license_list($year) {
	global $wpdb, $licensing_statusses;
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;
	$query = "SELECT LIC.*, C.Name AS ClassName FROM $tLic AS LIC INNER JOIN $tClass AS C ON C.Code = LIC.Klasse WHERE Year = $year ORDER BY LIC.Klasse, LIC.RegistrationDate";
	$rows = $wpdb->get_results($query, ARRAY_A);

	$out  = '';
	$out .= 'Lijst voor het seizoen/jaar ' . $year . '<br />';


	if (count($rows) > 0) {

		$exportDir = NOMAC_PLUGIN_PATH . "/imagecache/" . $year . "/";
		if (!is_dir($exportDir)) {
			mkdir($exportDir);
		}

		$out .= '<form method="post" action="">';
		$prevClass = "";
		foreach ($rows as $row) {
			$filename = $row['Id'];
			switch($row['fotoContentType']) {
				case "image/jpeg": $filename = $filename . ".jpg"; break;
				case "image/pjpeg": $filename = $filename . ".jpg"; break; // An MSIE Special.
				case "image/jpg": $filename = $filename . ".jpg"; break;
				case "image/bmp": $filename = $filename . ".bmp"; break;
				case "image/gif": $filename = $filename . ".gif"; break;
				case "image/tiff": $filename = $filename . ".tiff"; break;
				case "image/png": $filename = $filename . ".png"; break;
				default: $filename = $filename . ".UNKNOWN"; break;
			}
			$imagePath = $exportDir . $filename;
			if ( ! file_exists($imagePath)) {
				$fp = @fopen($imagePath, "wb");
				if ($fp === FALSE) {
					$out .= 'ERROR, COULD NOT CREATE IMAGECACHE FILE!';
					echo $out;
					return;
				} else {
					fwrite($fp, stripslashes($row['foto']));
					fclose($fp);
				}
			}


			if ($prevClass != $row['ClassName']) {
				if (!empty($prevClass)) {
					$out .= '</tbody>';	
					$out .= '</table>';
				}
				$out .= '<h3>' . $row['ClassName'] . '</h3>';
				$out .= '<table class="wp-list-table widefat fixed">';
				$out .= '<thead><tr><th>Naam</th><th>Image</th><th>Klasse</th><th>Datum/Tijd</th><th>Status</th></tr></thead>';
				$out .= '<tbody>';
				$prevClass = $row['ClassName'];
			}
			$out .= '<tr>';
			$driverName = stripslashes($row['Voornaam']) . ' ' . stripslashes($row['Achternaam']);
			$out .= '<input type="hidden" name="DriverName[' . $row['Id'] . ']" value="' . $driverName .'" />';
			$out .= '<td><a href="?page=Licensing&do=EditLicense&licenseid='. $row['Id'].'">' . $driverName . '</a></td>';
			$path = plugins_url(NOMAC_BASEPATH. "/imagecache/".$year."/".$filename);
			$out .= '<td><img src="'.$path.'" height="50" /></td>';
			$out .= '<td>' . stripslashes($row['ClassName']) . '</td>';
			$out .= '<td>' . stripslashes($row['RegistrationDate']) . '</td>';
			$out .= '<td>';

			$out .= '<select name="status['.$row['Id'].']">';
			foreach ($licensing_statusses as $stat) {
				if ($row['Status'] == $stat) {
					$out .= '<option value="" selected="selected">'.$stat.'</option>';
				} else {
					$out .= '<option value="'.$stat.'">'.$stat.'</option>';
				}
			}
			$out .= '</select>';
			$out .= '</td>';
			$out .= '</tr>';
		}
		$out .= '</tbody>';	
		$out .= '</table>';
		$out .= '<input type="submit" class="button-primary" name="do" value="Statussen aanpassen" />';
		$out .= '</form>';
		
		
		// Delete old files
		$dir = dir($exportDir);
		while (false !== ($entry = $dir->read())) {
			if (substr($entry, -4) == ".csv" || substr($entry, -4) == ".zip") {
				$delFile = $exportDir . $entry;
				unlink($delFile);
			}
		}


		// Create CSV file
		$exportFileName = $exportDir . "Drivers".date('Y-m-d').".csv";
		$fp = fopen($exportFileName, "w");
		if ($fp === FALSE) {
			$out .= "<br />ERROR: Can create export file.<br />";
		}
		fputcsv($fp, array_keys($rows[0]));
		foreach ($rows as $row) {
			unset($row['foto']);
			unset($row['fotoContentType']);
			foreach ($row as $key => $val) {
				$row[$key] = stripslashes($val);
			}
			fputcsv($fp, $row);
		}
		fclose($fp);


		// Create the zipfile for export.
		$exportFiles = array();
		$dirHandle = opendir($exportDir);
		while(false !== ($entry = readdir($dirHandle))) {
			if (filetype($exportDir . $entry) == "file" && substr($entry, -4) != ".zip") {
				$exportFiles[] = $exportDir . '/' . $entry;
			}
		}
		closedir($dirHandle);

		$zip = new ZipArchive();
		$exportFileName = "export_".date('Y-m-d').".zip";
		$zipFileName = $exportDir . '/' . $exportFileName;
		if ($zip->open($zipFileName, ZIPARCHIVE::CREATE && ZIPARCHIVE::OVERWRITE) === FALSE) {
			$out .= "<br />ERROR: Creation of export file failed.<br/>";
		}
		foreach ($exportFiles as $entry) {
			$zip->addFile($entry, basename($entry));
		}
		$zip->close();
		
		if (file_exists($zipFileName)) {
			$exportUrl = plugins_url(NOMAC_BASEPATH . "/imagecache/" . $year . "/" . $exportFileName);
			$out .= '<a class="button-secondary" href="'.$exportUrl.'" title="Download export">Exporteren</a>';
		}
	}
	else {
		$out .= "Er zijn nog geen aanmeldingen voor het seizoen $year.";
	}
	echo $out;
}



function admin_nomac_license_totals($year) {
	global $wpdb;
	$out = "";

	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	$counts = $wpdb->get_results("SELECT COUNT(LIC.Id) AS Count, C.Name AS Klasse, LIC.Status FROM $tLic AS LIC INNER JOIN $tClass AS C ON LIC.Klasse = C.Code WHERE Year = $year GROUP BY LIC.Klasse, LIC.Status");
	if (count($counts) > 0) {
		$out .= '<table class="wp-list-table widefat fixed">';
		$out .= '<thead><tr><th>Klasse</th><th>Status</th><th>Aantal</th></tr></thead><tbody>';
		foreach ($counts as $count) {
			$out .= '<tr><td>'.$count->Klasse .'</td><td>'. $count->Status. '</td><td>'.$count->Count.'</td></tr>';
		}
		$out .= '</tbody></table>';
	} else {
		$out .= "De totalen kunnen niet getoont worden, omdat er nog geen aanmeldingen zijn voor $year";
	}
	echo $out;
}


?>
