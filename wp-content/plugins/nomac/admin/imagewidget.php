<?php
function admin_nomac_imagecycle() 
{
	global $wpdb;
	$tablename = $wpdb->prefix . TABLE_IMAGECYCLE; 

	echo '<div class="wrap">';
	screen_icon('edit-pages');
	echo '<h2>NOMAC Image Cycle Configuration</h2>';
	echo '<p>Enter the image url and the url to link to. Image url is required, link url is not.</p>';

	$data = array();
	if (isset($_POST['id']) || isset($_POST['add'])) {
		$data['title'] = $_POST['title'];
		$data['link_url'] = $_POST['link_url'];
		$data['image_url'] = $_POST['image_url'];
		if (!isset($_POST['add']))
		{
			$id['id'] = $_POST['id'];
		}
	}	

	if (isset($_POST['add'])) {
		echo "Item added...";
		$wpdb->insert($tablename, $data);
	}

	if (isset($_POST['delete'])) {
		echo "Item deleted...";
		$wpdb->query("DELETE FROM $tablename WHERE ID = '".addslashes($_POST['id'])."'");
	}
	if (isset($_POST['update']))
	{
		echo "Item saved...";
		$wpdb->update($tablename, $data, $id);
	}

	$links = $wpdb->get_results("SELECT * FROM $tablename");
	foreach ($links as $link)
	{
		admin_nomac_imagecycle_outputform($link);
	}

	echo "<h3>Add a new item...</h3>";
	admin_nomac_imagecycle_outputform(null);	


	echo '</div>';

}

function admin_nomac_imagecycle_outputform($row)
{
	if (!isset($row)) {
		$row->image_url = "";
		$row->title = "";
		$row->link_url = "";
		$row->id = 0;
	}
	if (!isset($row->image_url))
	{
		$row->image_url = "";
	}
	if (!isset($row->link_url))
	{
		$row->link_url = "";
	}
	if (!isset($row->title))
	{
		$row->title = "";
	}
	if (!isset($row->id))
	{
		$row->id = 0;
	}
	
	echo '<form method="post" action="">';
	?>
	<form>
		<table>
			<tr>
				<td>Title (alt text):</td>
				<td><input type="text" name="title" value="<?php echo $row->title; ?>" size="80"/></td>
			</tr>
	
			<tr>
				<td>Image URL:</td>
				<td><input type="url" name="image_url" value="<?php echo $row->image_url; ?>" size="100" /></td>
			</tr>
			<tr>
				<td>Link URL:</td>
				<td><input type="url" name="link_url" value="<?php echo $row->link_url; ?>" size="100" /></td>
			</tr>
			<?php if ($row->id != 0) { ?>
			<tr>
				<td colspan="2">
					<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
					<input type="submit" name="delete" value="Delete" />
					<input type="submit" name="update" value="Save" />
				</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="2">
					<input type="submit" name="add" value="Add" />
				</td>
			</tr>
			<?php } ?>	
			
	
		</table>
	</form>	
	<?php
	echo '</form>';
}
?>
