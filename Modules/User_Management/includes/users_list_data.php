<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
if(isset($_POST['search']))
{
	$search = $_POST['search'];
	$q = "WHERE firstname LIKE '%$search%' OR lastname LIKE '%$search%' OR username LIKE '%$search%'";
} 
else
{
	$q = '';
}
?>
<head>
<style type="text/css">
</style>
</head>
<div class="tableFixHead ">
	<table id="filedata" style="width:100%" class="table table-hover table-striped table-bordered">
		<thead>
			<tr class="table-header-cells">
				<th style="width:50px;text-align:center">#</th>
				<th>ACCOUNT NAME</th>
				<th>CLUSTER</th>
				<th>DEPARTMENT</th>
				<th>BRANCH</th>
				<th>USERNAME</th>
				<th>ROLE</th>
				<th>LEVEL</th>
				<th>STATUS</th>		
			</tr>
		</thead>
		<tbody>
<?php
	$query = "SELECT * FROM tbl_system_user $q";
	$results = mysqli_query($db, $query);    
	$cnt=0;
	while($ROW = mysqli_fetch_array($results))  
	{
		$cnt++;
		$rowid = $ROW['id'];
		$acctname = $ROW['firstname']." ".$ROW['lastname'];
		$params = '';
?>
			<tr class="table-body-cells" onclick="get_users('<?php echo $rowid; ?>','<?php echo $acctname; ?>','getuser');">
				<td style="width:50px;text-align:center"><?php echo $cnt; ?></td>
				<td><?php echo $ROW['firstname']." ".$ROW['lastname']; ?></td>
				<td><?php echo $ROW['cluster']; ?></td>
				<td><?php echo $ROW['department']; ?></td>
				<td><?php echo $ROW['branch']; ?></td>
				<td><?php echo $ROW['username']; ?></td>
				<td><?php echo $ROW['role']; ?></td>
				<td style="text-align:center"><?php echo $ROW['level']; ?></td>
				<td style="text-align:center"><?php if($ROW['void_access']==1) { echo "Void"; } else { echo "Active"; } ?></td>		
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<script>
$(document).ready(function(){  
	$('#user_datas').DataTable({
		"lengthMenu": [[20, 50, 100, 500,  -1], [20, 50, 100, 500, "All"]]
	}); 
 });
</script>