<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
require $_SERVER['DOCUMENT_ROOT'].'/Modules/Warehouse_report/class/wh_functions.class.php';
$wh_functions = new WHFunctions;

if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
$date_now = date("Y-m-d H:i:s");
if($mode == 'saveorder')
{
	$$rowid = $POST['POSTid'];
	$date_issued = $POST['date_issued'];
	$itemcode = $POST['itemcode'];
	$itemname = $POST['itemname'];
	$quantity = $POST['quantity'];
	$itemclass = $POST['itemclass'];
	$uom = $POST['uom'];
	$conversion = $POST['conversion'];
	$mrsno = $POST['mrsno'];
	$delivered_to = $POST['delivered_to'];
	$delivery_date = $POST['delivery_date'];
	$expiration_date = $POST['expiration_date'];
	$prepared_by = $POST['prepared_by'];
	$stock = $wh_functions->GetStockAmount($item_code,$db);
	$new_stock = ($received_amount + $stock);

	$query = "INSERT INTO rpt_item_receiving (`item_code`,`item_description`,`class`,`received_amount`,`uom`,`conversion`,`po_number`,`supplier`,`invoice_no`,`mrcs_no`,`unit_price`,`expiration_date`,`delivery_date`,`received_by`,`date_received`)";
	$query .= "VALUES('$item_code','$item_description','$class','$received_amount','$uom','$conversion','$po_number','$supplier','$invoice_no','$mrcs_no','$unit_price','$expiration_date','$delivery_date','$received_by','$date_received')";	
	if ($db->query($query) === TRUE)
	{
		$last_id = $db->insert_id;
		updateStock($last_id,$item_code,$new_stock,$db);
		print_r('
			<script>
//				closeModal("formodalsm");
				swal("Success", "'.$supplier.' has been added successfuly","success");
				loadReceivingData();
			</script>
		');
	} else {
		print_r('
			<script>
				swal("Saving Fail", "'.$db->error.'","warning");
			</script>
		');
	}		
}
if($mode == 'deletereceiving')
{
	$rowid = $_POST['rowid'];
	$deleteQuery = "DELETE FROM rpt_item_receiving WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('
    		<script>
    			swal("System Message", "Receiving Item has been successfuly deleted", "success");
    			loadReceivingData();
    		</script>
		');
	} else {
		print_r('
			<script>
				swal("System Message", "'.$db-error.'","warning");
			</script>
		');
	}
}
if($mode == 'updatethisreceiving')
{
	$rowid = $_POST['rowid'];
	$column = $_POST['column'];
	$value = $_POST['value'];
	$queryDataUpdate = "UPDATE rpt_item_receiving SET $column = '$value' WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{ 
	
	} else {
		print_r('
			<script>
				swal("Updating Fail", "'.$db->error.'","warning");
			</script>
		');
	}
}
if($mode == 'savereceiving')
{
	$item_code = $_POST['itemcode'];
	$class = $_POST['itemclass'];
	$item_description = $_POST['itemname'];
	$received_amount = $_POST['recvamount'];
	$uom = $_POST['uom'];
	$conversion = $_POST['conversion'];
	$po_number = $_POST['ponumber'];
	$supplier = $_POST['supplier'];
	$invoice_no = $_POST['invoiceno'];
	$mrcs_no = $_POST['mrcsno'];
	$unit_price = $_POST['unitprice'];
	$expiration_date = $_POST['expdate'];
	$delivery_date = $_POST['deldate'];
	$date_received = $_POST['recvdate'];
	$received_by = $_POST['recvby'];

	$stock = $wh_functions->GetStockAmount($item_code,$db);
	$new_stock = ($received_amount + $stock);

	$query = "INSERT INTO rpt_item_receiving (`item_code`,`item_description`,`class`,`received_amount`,`uom`,`conversion`,`po_number`,`supplier`,`invoice_no`,`mrcs_no`,`unit_price`,`expiration_date`,`delivery_date`,`received_by`,`date_received`)";
	$query .= "VALUES('$item_code','$item_description','$class','$received_amount','$uom','$conversion','$po_number','$supplier','$invoice_no','$mrcs_no','$unit_price','$expiration_date','$delivery_date','$received_by','$date_received')";	
	if ($db->query($query) === TRUE)
	{
		$last_id = $db->insert_id;
		updateStock($last_id,$item_code,$new_stock,$db);
		print_r('
			<script>
				closeModal("formodalsm");
				swal("Success", "'.$supplier.' has been added successfuly","success");
				loadReceivingData();
			</script>
		');
	} else {
		print_r('
			<script>
				swal("Saving Fail", "'.$db->error.'","warning");
			</script>
		');
	}		
}
function updateStock($last_id,$item_code,$new_stock,$db)
{
	$queryDataUpdate = "UPDATE rpt_warehouse SET on_hand='$new_stock', trans_id='$last_id' WHERE item_code='$item_code'";
	if ($db->query($queryDataUpdate) === TRUE)
	{ 
	} else {
		echo $db->error;
	}
}
if($mode == 'setval')
{
	$item_name = $_POST['itemname'];	
	$query = "SELECT * FROM rpt_item_records WHERE item_description='$item_name'";
	$results = mysqli_query($db, $query); 
	while($ROW = mysqli_fetch_array($results))  
	{
		$item_code = $ROW['item_code'];
		$item_class = $ROW['class'];
		$conversion = $ROW['conversion'];
		print_r('
			<script>
				$("#itemcode").val("'.$item_code.'");
				$("#itemclass").val("'.$item_class.'");
				$("#conversion").val("'.$conversion.'");
			</script>
		');
	}
}
if($mode == 'savesupplier')
{
	$params = $_POST['params'];
	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	$itemname = $_POST['itemname'];
	$itemclass = $_POST['itemclass'];
	$uom = $_POST['uom'];
	$conversion = $_POST['conversion'];
	$unitprice = $_POST['unitprice'];
	$supplier = $_POST['supplier'];
	$added_by = $_SESSION['application_appnameuser'];
	
	if($params == 'save')
	{
		$query = "SELECT * FROM rpt_item_records WHERE item_code='$itemcode' AND supplier='$supplier'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows === 0 ) 
		{
			$query = "INSERT INTO rpt_item_records (`item_code`,`class`,`item_description`,`uom`,`conversion`,`supplier`,`unit_price`,`added_by`,`date_added`)";
			$query .= "VALUES('$itemcode','$itemclass','$itemname','$uom','$conversion','$supplier','$unitprice','$added_by','$date_now')";	
			if ($db->query($query) === TRUE)
			{
				print_r('
					<script>
						swal("Success", "'.$supplier.' has been added successfuly","success");
						inventoryList();
					</script>
				');
			} else {
				print_r('
					<script>
						swal("Saving Fail", "'.$db->error.'","warning");
					</script>
				');
			}			
		} else {
			print_r('
				<script>
					swal("Item Exists", "'.$itemname.' is already exist","warning");
				</script>
			');
		}
	}
	if($params == 'update')
	{
		$queryDataUpdate = "UPDATE rpt_item_records 
		SET item_code='$itemcode',class='$itemclass',item_description='$itemname',uom='$uom',conversion='$conversion',supplier='$supplier',unit_price='$unitprice' WHERE id='$rowid'";
		if ($db->query($queryDataUpdate) === TRUE)
		{ 
			print_r('
				<script>
					swal("Success", "'.$itemname.' has been updated successfuly","success");
					closeModal("formodalsm");
					inventoryList();
				</script>
			');
		} else {
			print_r('
				<script>
					swal("Saving Fail", "'.$db->error.'","warning");
				</script>
			');
		}
	}
}
if($mode == 'deletesupplier')
{
	$rowid = $_POST['rowid'];
	$deleteQuery = "DELETE FROM rpt_item_records WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('
    		<script>
    			swal("System Message", "Supplier has been successfuly deleted", "success");
    			loadSupplier();
    		</script>
		');
	} else {
		print_r('
			<script>
				swal("System Message", "'.$db-error.'","warning");
			</script>
		');
	}
}
