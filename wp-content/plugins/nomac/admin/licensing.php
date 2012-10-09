<?


function admin_nomac_licensing() {
	global $wpdb;

	$tLic = $wpdb->prefix . TABLE_LICENSING;

        echo '<div class="wrap">';
        screen_icon('users');
        echo '<h2>NOMAC Licentie Beheer</h2>';
        echo '<p>Op deze pagina worden de licenties beheerd. Het beheer kan de status van de licentie aanpassen.</p>';


	if (!isset($_SESSION['LIC_YEAR'])) {
		$_SESSION['LIC_YEAR'] = date('Y');
	}
	if (isset($_REQUEST['do'])) {
		if ( $_REQUEST['do'] == "Selecteer Jaar") {
			$_SESSION['LIC_YEAR'] = $_REQUEST['year'];
		} else if ($_REQUEST['do'] == "Statussen aanpassen") {
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
					}
				}
			}
			echo "</ul>";
		}
	}

	$yearQuery = "SELECT year FROM $tLic GROUP BY year";
	$years = $wpdb->get_results($yearQuery);
	echo '<form method="post" action="">';
	echo 'Selecteer een jaar/seizoen: ';
	echo '<select name="year">';
	foreach ($years as $y) {
		if ($_SESSION['LIC_YEAR'] == $y->year) {
			echo '<option value="'.$y->year.'" selected="selected">'.$y->year.'</option>';
		} else {
			echo '<option value="'.$y->year.'">'.$y->year.'</option>';
		}
	}
	echo '</select>';
	echo '<input class="button-secondary" type="submit" name="do" value="Selecteer Jaar" />';
	echo '</form>';	

	echo "Lijst voor het seizoen/jaar " . $_SESSION['LIC_YEAR'];
	echo "<br />";

	admin_nomac_licensinglist($_SESSION['LIC_YEAR']);
	echo "<br /><br />";
	admin_nomac_licensingtotals($_SESSION['LIC_YEAR']);
	
	echo "</div>";
}


function admin_nomac_licensinglist($year) {
	global $wpdb;
	$tLic = $wpdb->prefix . TABLE_LICENSING;
	$tClass = $wpdb->prefix . TABLE_CLASS;
	$query = "SELECT LIC.*, C.Name AS ClassName FROM $tLic AS LIC INNER JOIN $tClass AS C ON C.Code = LIC.Klasse WHERE Year = $year ORDER BY LIC.Klasse, LIC.RegistrationDate";
	$rows = $wpdb->get_results($query, ARRAY_A);

	$out  = '';

	if (count($rows) > 0) {
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
			$fullpath = NOMAC_PLUGIN_PATH . "/imagecache/" . $filename;
			if ( ! file_exists($fullpath)) {
				$fp = fopen($fullpath, "wb");
				fwrite($fp, stripslashes($row['foto']));
				fclose($fp);
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
			if (!empty($row['Email'])) {
	                        $out .= '<td><a href="mailto:' . $row['Email'] . '">' .  $driverName . '</td>';
			} else {
	                        $out .= '<td>' . $driverName . '</td>';
			}
			$path = plugins_url(NOMAC_BASEPATH . "/imagecache/" . $filename, NOMAC_BASEPATH);
			$out .= '<td><img src="'.$path.'" height="50" /></td>';
                        $out .= '<td>' . stripslashes($row['ClassName']) . '</td>';
                        $out .= '<td>' . stripslashes($row['RegistrationDate']) . '</td>';
                        $out .= '<td>';

			$statusses = Array('Aanvraag ontvangen', 'Betaling ontvangen', 'Betaling onvoldoende', 'Ter goedkeuring bij club', 'Toegekend', 'Afgewezen');
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
		
		// Create CSV file
		$exportDir = NOMAC_PLUGIN_PATH . "/imagecache/";
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
			$exportUrl = plugins_url(NOMAC_BASEPATH . "/imagecache/" . $exportFileName, NOMAC_BASEPATH);
			$out .= '<a class="button-secondary" href="'.$exportUrl.'" title="Download export">Exporteren</a>';
		}
	
        }
        else {
                $out .= "Er zijn nog geen aanmeldingen voor het seizoen $year.";
        }

        echo $out;
}



function admin_nomac_licensingtotals($year) {
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
        }
        else {
                $out .= "De totalen kunnen niet getoont worden, omdat er nog geen aanmeldingen zijn voor $year";
        }
	echo $out;
}
