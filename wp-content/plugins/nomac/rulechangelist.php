<?php

require_once("include.php");

function outputNomacRulechangeList($attrs) {
	global $wpdb;
	$out = "";
	
	$tRuleChange = $wpdb->prefix . TABLE_RULECHANGE;
	$tClass = $wpdb->prefix . TABLE_CLASS;


	if (! isset($attrs["year"]) || !is_numeric($attrs["year"])) {
		$out .= "The nomac rule change list needs a year parameter.";
		return $out;
	}
    $year = (int)$attrs["year"];

	
	$rows = $wpdb->get_results("SELECT RC.SubmittedBy, RC.SubmittedOn, C.Name AS Class, RC.Page, RC.Article, RC.OldText, RC.NewText, RC.Comment FROM $tRuleChange AS RC INNER JOIN $tClass AS C ON C.Code = RC.Class WHERE Year = $year AND deleted = 0 ORDER BY C.Code, RC.SubmittedOn");
	
	if (count($rows) > 0) {
		
		$class = "";
		foreach ($rows as $row) {
			if ($row->Class != $class) {
				if ($class != "") {
					$out .= "</table>";
				}
				$class = $row->Class;
				$out .= '<h3 class="pagebreak">' . stripslashes($row->Class) . '</h3>';
				$out .= '<table class="nostyle">';
			}
			$out .= '<tr>';
			$out .= '<th>Ingediend op:</th><td>' . $row->SubmittedOn . '</td>'; // 2
			$out .= '<th>Klasse:</th><td>' . stripslashes($row->Class) . '</td>';// 2
			$out .= '</tr>';
			$out .= '<tr>';
			$out .= '<th>Pagina:</th><td>' . $row->Page . '</td>'; // 2
			$out .= '<th>Artikel:</th><td>' . stripslashes($row->Article) . '</td>';// 2
			$out .= '</tr>';
			$out .= '<tr><th colspan="4">Oude tekst:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->OldText) . '</td></tr>';
			$out .= '<tr><th colspan="4">Nieuwe tekst:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->NewText) . '</td></tr>';
			$out .= '<tr><th colspan="4">Uitleg:</th></tr>';
			$out .= '<tr><td colspan="4">' . cleanOutput($row->Comment) . '</td></tr>';
			$out .= '<tr><th colspan="4" class="header">&nbsp;</th></tr>';
		}
		if ($class!= "") {
			$out .= "</table>";
		}

		
	}
	else {
		$out .= "Er zijn nog geen regelementswijzigingen voor het seizoen $year.";
	}

	return $out;
}

?>