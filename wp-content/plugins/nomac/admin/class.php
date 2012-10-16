<?php


function admin_nomac_class() {
	global $wpdb;

	$tablename = $wpdb->prefix . TABLE_CLASS;

	echo '<div class="wrap">';
	screen_icon('users');
	echo '<h2>NOMAC Club Beheer</h2>';
	echo '<p>Op deze pagina worden de klasses beheerd. Je kan klasses toevoegen, verwijderen en aanpassen.</p>';


	if (isset($_POST['add'])) {
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';
		echo 'Adding klasse...';
		$data['Code'] = $_POST['code'];
		$data['Name'] = $_POST['name'];
		$data['CloseDate'] = $_POST['closedate'];
		$data['MaxDrivers'] = $_POST['maxdrivers'];
		$data['MaxDriversCloseDate'] = $_POST['maxdriversclosedate'];
		$data['Price'] = $_POST['price'];
		$wpdb->insert($tablename, $data);
	}

	if (isset($_POST['delete'])) {
		echo "Deleting klasse...";
		$q = $wpdb->prepare("DELETE FROM $tablename WHERE ID = %d", $_POST['id']);
		$wpdb->query($q);
	}

	if (isset($_POST['update'])) {
		echo '<pre>';
		print_r($_POST);
		echo '</pre>';

		echo "Updating klasse name....";
		$data['Name'] = $_POST['name'];
		$data['CloseDate'] = $_POST['closedate'];
		$data['MaxDrivers'] = $_POST['maxdrivers'];
		$data['MaxDriversCloseDate'] = $_POST['maxdriversclosedate'];
		$data['Price'] = $_POST['price'];
		$id['Id'] = $_POST['id'];
		$wpdb->update($tablename, $data, $id);
	}

	$frequencies = $wpdb->get_results("SELECT Id, Code, Name, CloseDate, MaxDrivers, MaxDriversCloseDate, Price FROM ".$tablename);
	if (count($frequencies) > 0) {
		echo '<table class="wp-list-table widefat">';
		echo '<thead><tr><th>Code</th><th>Name</th><th>Close Date</th><th>Max Drivers</th><th>Max Drivers Close Date</th><th>Price</th><th>Action</th><th></th></tr></thead>';
		echo '<tbody>';
		foreach ($frequencies as $freq)
		{
			admin_nomac_class_outputform($freq);
		}
		admin_nomac_class_outputform(null);	
		echo '</tbody></table>';
	}
	echo "</div>";
}



function admin_nomac_class_outputform($row)
{
	if (!isset($row)) {
		$row->Id = 0;
		$row->Code = "";
		$row->Name = "";
		$row->CloseDate = "";
		$row->MaxDrivers = 0;
		$row->MaxDriversCloseDate = "";
		$row->Price = 100;
	}
	?>
	<form method="post" action="">
		<tr>
			<?php if ($row->Id != 0) { ?>
				<td><?php echo $row->Code; ?></td>
			<?php } else { ?>
				<td><input type="text" name="code" value="<?php echo $row->Code; ?>" size="10"/></td>
			<?php } ?>
			<td><input type="text" name="name" value="<?php echo $row->Name; ?>" size="70" /></td>
			<td><input type="text" name="closedate" value="<?php echo $row->CloseDate; ?>" size="10" /></td>
			<td><input type="text" name="maxdrivers" value="<?php echo $row->MaxDrivers; ?>" size="3" /></td>
			<td><input type="text" name="maxdriversclosedate" value="<?php echo $row->MaxDriversCloseDate; ?>" size="10" /></td>
			<td><input type="text" name="price" value="<?php echo $row->Price; ?>" size="3" /></td>
			<?php if ($row->Id != 0) { ?>
				<td>
					<input type="hidden" name="id" value="<?php echo $row->Id; ?>" />
					<input type="submit" class="button-primary" name="update" value="Save" />
				</td>
				<td>
					<input type="hidden" name="id" value="<?php echo $row->Id; ?>" />
					<input type="submit" class="button-secondary" name="delete" value="Delete" />
				</td>
			<?php } else { ?>
				<td colspan="2">
					<input type="submit" class="button-primary" name="add" value="Add" />
				</td>
			<?php } ?>	
		</tr>
	</form>
	<?php
}

?>