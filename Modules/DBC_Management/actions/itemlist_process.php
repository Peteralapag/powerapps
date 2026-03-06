<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$mode = $_POST['mode'];
$limit = $_POST['limit'];
$rowid = $_POST['rowid'];
$supplier_id = $_POST['supplier'];
$recipient = $_POST['recipient'];
$item_location = $_POST['item_location'];
$item_code = $_POST['item_code'];
$category = $_POST['category'];
$item_description = $_POST['item_description'];
$uom = $_POST['uom'];
//$conversion = $_POST['conversion'];
$yieldperbatch = $_POST['yieldperbatch'];
$active = $_POST['active'];
$unit_price = $_POST['unit_price'];
$app_user = $_SESSION['dbc_username'];
$date_user = date("Y-m-d H:i:s");

if($mode == 'add')
{
	$query = "SELECT * FROM wms_itemlist WHERE item_description='$item_description'";
	$checkRes = mysqli_query($db, $query);    
    if ( $checkRes->num_rows === 0 ) 
    {
    	$column = "`recipient`,`item_location`,`item_code`,`category`,`item_description`,`uom`,`yield_perbatch`,`added_by`,`date_added`,`active`,`unit_price`,`supplier_id`";    	
    	$insert = "'$recipient','$item_location','$item_code','$category','$item_description','$uom','$yieldperbatch','$app_user','$date_user','$active','$unit_price','$supplier_id'";
		$queryInsert = "INSERT INTO wms_itemlist ($column)";
		echo $queryInsert;
		$queryInsert .= "VALUES($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
			print_r('
				<script>
					load_data("'.$limit.'");
					swal("Success", "Item has been successfully added", "success");
					closeModal("formmodal");
				</script>
			');
		} else {
			print_r('
				<script>
					swal("Warning", "'.$db->error.'", "warning");
				</script>
			');
		}
	} else {
		print_r('
			<script>
				swal("Warning", "Item is already exists.", "warning");
			</script>
		');
	}
}
if($mode == 'edit') {
	$update = "recipient='$recipient',item_location='$item_location',item_code='$item_code',category='$category',item_description='$item_description',uom='$uom',yield_perbatch='$yieldperbatch',updated_by='$app_user',date_updated='$date_user',active='$active',unit_price='$unit_price',supplier_id='$supplier_id'";
	$queryDataUpdate = "UPDATE wms_itemlist SET $update WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		print_r('
			<script>
				load_data("'.$limit.'");
				swal("Success", "Item has been successfully updated", "success");
			</script>
		');		
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}
}

