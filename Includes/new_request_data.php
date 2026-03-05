<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$username = $_SESSION['application_username'];
$QUERYCNT = "SELECT * FROM tbl_app_request WHERE approved=0 AND executed=0";
$RESULTS = mysqli_query($db, $QUERYCNT); 
if ( $RESULTS->num_rows > 0 ) 
{
	$nr_cnt = $RESULTS->num_rows; 
} else {
	$nr_cnt = 0;
}



?>
<style>
.boxer {

}
.boxer p {
	margin:0;
	font-size: 10px;
	font-weight:normal;
}
.boxer input {
	border:0;
	width:100%;
	padding:3px;
	font-size:11px;
}
.buttones {
	font-size: 11px;
	cursor:pointer;
	width:100px;
	text-align:center;
	padding:5px;
	border-radius:5px;
	
}
</style>
<table style="width: 100%" class="table">
	<thead>
		<tr>
			<th style="text-align:center" class="bg-primary">(<?php echo $nr_cnt; ?>) NEW REQUEST</th>
		</tr>
	</thead>
	<tbody>
<?php
$sqlNotifs = "SELECT * FROM tbl_app_request WHERE approved=0 AND executed=0 ORDER BY id DESC";
$result = mysqli_query($db, $sqlNotifs);    
if ( $result->num_rows > 0 ) 
{
	while($ROWS = mysqli_fetch_array($result))  
	{
		$rowid = $ROWS['id'];
		$requested_by = $ROWS['requested_by'];

?>	
		<tr>
			<td>
				<div class="boxer">					
					<table style="width: 100%">
						<tr>
							<th style="width: 96px">User Name:</th>
							<td><input type="tect" value="<?php echo $ROWS['account_name']; ?>" disabled></td>
						</tr>
						<tr>
							<th style="width: 96px">File Name:</th>
							<td><input type="text" value="<?php echo str_replace("_"," ", $ROWS['file_name']); ?>" disabled></td>
						</tr>
						<tr>
							<th style="width: 96px">Note/Reason:</th>
							<td style="position:relative">
								<input type="text" value="<?php echo $ROWS['request_reason']; ?>" disabled>
								
							</td>
						</tr>
						<tr>
							<th style="width: 96px">Request Date:</th>
							<td style="position:relative">
								<?php echo date("F d,Y @ h:i A", strtotime($ROWS['requested_date'])); ?> 
							</td>
						</tr>
						<tr>
							<td style="text-align:right" colspan="2">
								<span class="buttones btn-success" onclick="requestActions('<?php echo $rowid; ?>','approved','1')">APPROVE</span>
								<span class="buttones btn-danger" onclick="requestActions('<?php echo $rowid; ?>','approved','2')">REJECT</span>
							</td>
						</tr>
					</table>					
				</div>
			</td>
		</tr>
<?php } } else { ?>
		<tr>
			<td style="text-align:center">No New Request</td>
		</tr>
<?PHP } ?>
	</tbody>
</table>
<script>

</script>
