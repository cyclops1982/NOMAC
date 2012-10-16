<?php


function admin_nomac_club() {
	global $wpdb;

	$tFreq = $wpdb->prefix . TABLE_CLUBS;

	echo '<div class="wrap">';
	screen_icon('users');
	echo '<h2>NOMAC Club Beheer</h2>';
	echo '<p>Op deze pagina worden de clubs beheerd. Je kan clubs toevoegen, verwijderen en aanpassen.</p>';


	if (isset($_POST['add'])) {
		echo "Adding club...";
		$data['Code'] = $_POST['code'];
		$data['Name'] = $_POST['name'];
		$wpdb->insert($tFreq, $data);
	}

	if (isset($_POST['delete'])) {
		echo "Deleting club...";
		$q = $wpdb->prepare("DELETE FROM $tFreq WHERE ID = %d", $_POST['id']);
		$wpdb->query($q);
	}

	if (isset($_POST['update'])) {
		echo "Updating club name....";
		$data['Name'] = $_POST['name'];
		$id['Id'] = $_POST['id'];
		$wpdb->update($tFreq, $data, $id);
	}

	$frequencies = $wpdb->get_results("SELECT Id, Code, Name FROM ".$tFreq);
	if (count($frequencies) > 0) {
		echo '<table class="wp-list-table widefat">';
		echo '<thead><tr><th>Code</th><th>Name</th><th>Action</th></tr></thead>';
		echo '<tbody>';
		foreach ($frequencies as $freq)
		{
			admin_nomac_club_outputform($freq);
		}
		admin_nomac_club_outputform(null);	
		echo '</tbody></table>';
	}
	echo "</div>";
}



function admin_nomac_club_outputform($row)
{
	if (!isset($row)) {
		$row->Code = "";
		$row->Name = "";
		$row->Id = 0;
	}

	?>
	
	<form method="post" action="">
		<tr>
			<?php if ($row->Id != 0) { ?>
				<td><?php echo $row->Code; ?></td>
			<?php } else { ?>
				<td><input type="text" name="code" value="<?php echo $row->Code; ?>" size="10"/></td>
			<?php } ?>
			<td><input type="text" name="name" value="<?php echo $row->Name; ?>" size="100" /></td>
			<?php if ($row->Id != 0) { ?>
				<td>
					<input type="hidden" name="id" value="<?php echo $row->Id; ?>" />
					<input type="submit" class="button-primary" name="update" value="Save" />
					<input type="submit" class="button-secondary" name="delete" value="Delete" />
				</td>
			<?php } else { ?>
				<td>
					<input type="submit" class="button-primary" name="add" value="Add" />
				</td>
			<?php } ?>	
		</tr>
	</form>
	<?php
}

?>