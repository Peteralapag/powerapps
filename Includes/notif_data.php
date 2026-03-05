<style>
.notifbox {
	border:1px solid yellow;
	background:#fafeec;
	padding:5px;
	font-size: 11px;
	border-radius:5px;
	margin-bottom:5PX;
}
p {
	margin:0;
	padding:0;
	margin-bottom:5px;
	border-bottom:1px solid #f1f1f1
}
</style>
<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$username = $_SESSION['application_username'];
$sqlNotifs = "SELECT * FROM tbl_app_request WHERE requested_by='$username' AND executed=0";
$result = mysqli_query($db, $sqlNotifs);    
if ( $result->num_rows > 0 ) 
{
	while($REOWS = mysqli_fetch_array($result))  
	{
		if($REOWS['approved'] == 0)
		{
			$status = 'Status: Pending';
		}
		if($REOWS['approved'] == 1)
		{
			$status = 'Status: <span style="color: red"><strong>Access Granted</strong></span>';
		}
?>	
		<div class="notifbox">
			<p>Requested By: <?php echo $REOWS['account_name']; ?></p>
			<p><?php echo "File Name: ".str_replace("_", " ", $REOWS['file_name']); ?></p>
			<p><?php echo "Date: ".date("M. d, Y @ h:i A", strtotime($REOWS['requested_date'])); ?></p>
			<p><?php echo $status ?></p>
		</div>
<?php
	}
}


