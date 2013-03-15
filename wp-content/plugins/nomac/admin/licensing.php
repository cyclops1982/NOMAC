<?php


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
			$wpdb->show_errors();
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
	global $wpdb;
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

			$statusses = Array('Aanvraag ontvangen', 'Betaling ontvangen', 'Betaling onvoldoende', 'Ter goedkeuring bij club', 'Toegekend', 'Afgewezen', 'Ingetrokken', 'Verwijderd');
			$out .= '<select name="status['.$row['Id'].']">';
			foreach ($statusses as $stat) {
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