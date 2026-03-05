<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$approved_by = $_SESSION['application_appnameuser'];
$date = date("Y-m-d H:i:s");
$rowid = $_POST['rowid'];
$column = $_POST['column'];
$value = $_POST['value'];
$date = date('Y-m-d H:i:s');

if($value == 2){
	$reasonRejection = $_POST['reasonRejection'];
	$q = ", executed = '1', executed_date='$date', reject_reason='$reasonRejection', ";
}
else{
	$q = ',';	
}

$queryDataUpdate = "UPDATE tbl_app_request SET $column='$value' $q approved_by='$approved_by', approved_date='$date' WHERE id='$rowid'";
if ($db->query($queryDataUpdate) === TRUE)
{
	print_r('
		<script>
			new_request();
			rejected_request();
			approved_request();
		</script>
	');
} else {
	echo $db->error;
}	
mysqli_close($db);
