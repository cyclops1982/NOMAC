<?php
function admin_nomac_rulechange() {
	global $wpdb;

	$tRuleChange = $wpdb->prefix . TABLE_RULECHANGE;

    echo '<div class="wrap">';
    screen_icon('users');
    echo '<h2 class="noprint">NOMAC reglemenswijzigingen Beheer</h2>';
    echo '<p class="noprint">Op deze pagina worden de reglemenswijzigingen beheerd.</p>';


	if (!isset($_SESSION['LIC_YEAR'])) {
		$_SESSION['LIC_YEAR'] = date('Y');
	}
	if (isset($_REQUEST['do'])) {
		if ( $_REQUEST['do'] == "Selecteer Jaar") {
			$_SESSION['LIC_YEAR'] = $_REQUEST['year'];
		} else if ($_REQUEST['do'] == "Aangevinkte items verwijderen") {
			echo "<ul>";
			foreach ($_REQUEST['delete'] as $statkey => $statval) {
				if (is_numeric($statkey) && !empty($statval)) {
					$id = (int)$statkey;
					$where = array("Id" => $id);
					$data = array("Deleted" => 1);
					if ($wpdb->update($tRuleChange, $data, $where, array('%s'), array('%d')) == 1) {
						echo "<li>Reglemenswijziging verwijderd.</li>";
					} else {
						echo "<li>Verwijderen MISLUK!</li>";
					}
				}
			}
			echo "</ul>";
		}
	}

	$yearQuery = "SELECT year FROM $tRuleChange GROUP BY year";
	$years = $wpdb->get_results($yearQuery);
	echo '<form method="post" class="noprint" action="">';
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

	echo "Lijst voor het seizoen/jaar " . $_SESSION['LIC_YEAR'];
	echo "<br />";

	echo '</form>';	
	admin_nomac_rulechangelist($_SESSION['LIC_YEAR']);
	echo "</div>";

}


function admin_nomac_rulechangelist($year) {
	global $wpdb;
	$out = "";
	
	$tRuleChange = $wpdb->prefix . TABLE_RULECHANGE;
	$tClass = $wpdb->prefix . TABLE_CLASS;

	$rows = $wpdb->get_results("SELECT RC.Id, RC.SubmittedBy, RC.SubmitterEmail, RC.SubmittedOn, C.Name AS Class, RC.Page, RC.Article, RC.OldText, RC.NewText, RC.Comment FROM $tRuleChange AS RC INNER JOIN $tClass AS C ON C.Code = RC.Class WHERE Year = $year AND RC.Deleted=0 ORDER BY C.Code, RC.SubmittedOn");
	

	if (count($rows) > 0) {
		$i = 0;
		$out .= '<form method="post" action="">';		
		foreach ($rows as $row) {
			if ($row->Class != $class) {
				$i=0;
				$class = $row->Class;
				$out .= '<h3 class="pagebreak">' . stripslashes($row->Class) . '</h3>';
				
			} else {
				$out .= '<br />';
			}
			$i++;

			$out .= '<table class="ruletable">';
			$out .= '<tr><th>Voorstel nummer:</th><th colspan="3">' . $i . '</th></tr>'; 
			$out .= '<tr class="noprint"><td colspan="1">Verwijderen:</td><td colspan="3"><input type="checkbox" name="delete['.$row->Id.']" /></td></tr>'; //4
			$out .= '<tr>';
			$out .= '<td>Ingediend door:</td><td>' . stripslashes($row->SubmittedBy) . '</td>'; // 2
			$out .= '<td>E-mail:</td><td>' . stripslashes($row->SubmitterEmail) . '</td>';// 2
			$out .= '</tr>';
			$out .= '<tr>';
			$out .= '<td>Ingediend op:</td><td>' . $row->SubmittedOn . '</td>'; // 2
			$out .= '<td>Klasse:</td><td>' . stripslashes($row->Class) . '</td>';// 2
			$out .= '</tr>';
			$out .= '<tr>';
			$out .= '<td>Pagina:</td><td>' . $row->Page . '</td>'; // 2
			$out .= '<td>Artikel:</td><td>' . stripslashes($row->Article) . '</td>';// 2
			$out .= '</tr>';
			$out .= '<tr><th colspan="4"><br />Oude tekst:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->OldText) . '</td></tr>';
			$out .= '<tr><th colspan="4"><br />Nieuwe tekst:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->NewText) . '</td></tr>';
			$out .= '<tr><th colspan="4"><br />Uitleg:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->Comment) . '</td></tr>';
			$out .= '<tr class="printonly"><th colspan="4"><br />Stemming/uitslag</th></tr>';
			$out .= '<tr class="printonly"><td>Voorstel teruggetrokken:</td><td>Stemmen voor:</td><td>Stemmen tegen:</td><td>Stemmen onthouden:</td></tr>';
			$out .= '<tr class="printonly"><td>Ja/Nee</td><td></td><td></td><td></td></tr>';
			$out .= '<tr class="printonly"><td colspan="4">Aanpassingen op het voorstel/het uiteindelijke voorstel:</td></tr>';
			$out .= '<tr class="printonly"><td colspan="4"><br/><br /><br /><br/><br/><br/><br/<br/></td></tr>';
			$out .= "</table>";
		}
		
		$out .= '<p class="submit noprint"><input type="submit" class="button-primary noprint" name="do" value="Aangevinkte items verwijderen" /></p>';
		$out .= '</form>';

	}
	else {
		$out .= "Er zijn nog geen regelementswijzigingen voor het seizoen $year.";
	}

	echo $out;
}
?>
