<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$username = $_SESSION['wd_username'];
$company = $_SESSION['wd_company'];
$user_level = $_SESSION['wd_userlevel'];

echo '<ul class="select-department">';
$showTemp = "SELECT * FROM tbl_shared_temp WHERE userid='$username'";
$tmpResult = mysqli_query($db, $showTemp);    
while($LISTTEMP = mysqli_fetch_array($tmpResult))  
{
	$share_id = $LISTTEMP['id'];
	$organization = $LISTTEMP['organization'];
	echo '<li ondblclick=removeThis('.$share_id.')>'.$organization.'</li>';
}
echo '</ul>';
?>
<div id="rem"></div>
<script>
function removeThis(shareid)
{
	$('#rem').load('../Modules/Written_documents/actions/delete_temp_process.php?delid='+shareid);
}
</script>
