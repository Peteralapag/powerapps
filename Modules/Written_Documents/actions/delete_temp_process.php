<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$delid = $_GET['delid'];
$sqldelItem = "DELETE FROM tbl_shared_temp WHERE id='$delid'";
if ($db->query($sqldelItem) === TRUE)
{
	print_r('
		<script>
			shareto();
		</script>
	');
}else{echo $db->error;}
?>