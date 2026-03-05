<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$application_id = $_POST['application_id'];
$query = "SELECT * FROM tbl_system_modules WHERE application_id='$application_id' ORDER BY ordering ASC";
$results = mysqli_query($db, $query);    
?>
<style>
.lamesako {
	font-size: 14px !important;
}
.lamesako td input {
	border:0 !important;
	font-size:14px
}
.lamesako td {
	padding:0 !important
}
.orderinginput input {
 text-align:center
}
</style>
<table style="width: 100%" class="table table-bordered lamesako">
<?php
$x=0;
while($ROWS = mysqli_fetch_array($results))  
{
	$x++;
	$rowid = $ROWS['id'];
	$modulez = $ROWS['modules'];
	
?>	
			<tr>
				<td>
					<input id="access<?php echo $x?>" type="text" class="form-control form-control-sm" value="<?php echo $ROWS['modules']; ?>" onkeyup="updateAccess('<?php echo $x?>','<?php echo $rowid?>',this.value)">
				</td>
				<td style="width:50px" class="orderinginput">
					<input id="val<?php echo $x;?>" type="text" class="form-control form-control-sm" value="<?php echo $ROWS['ordering']; ?>" onkeyup="ordering('<?php echo $x?>','<?php echo $rowid?>')">					
				</td>
				<td style="padding:0 !important">
					<button class="btn btn-danger btn-sm" onclick="deleteAccess('<?php echo $rowid?>','<?php echo $modulez?>')"><i class="fa-solid fa-trash"></i></button>
				</td>
			</tr>
<?php } ?>
</table>
<div class="accessresults"></div>
<script>
function deleteAccess(rowid,modules)
{
	app_confirm("Delete","Are you sure to delete " + modules + "?","warning","deleteAccessYes",rowid,"red");
	return false;
}
function deleteAccessYes(rowid)
{
	var mode = "deleteaccessyes";
	var application_id = $('#appid').val();
	var application_name = $('#appname').val();
	$.post("./Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, application_id: application_id, application_name: application_name },
	function(data) {
		$('.accessresults').html(data);
	});
}
function updateAccess(num,rowid,access)
{
    var mode = 'updateappaccess';
	var access = $('#access' + num).val();
	var application_id = $('#appid').val();
	var application_name = $('#appname').val();
	if (event.key === 'Enter')
	{
	    $.post("./Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, access: access, application_id: application_id, application_name: application_name },
		function(data) {
			$('.accessresults').html(data);
		});
    }
}
function ordering(num,rowid)
{
    var mode = 'changeordering';
    var ordering = $('#val' + num).val();
    $.post("./Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, ordering: ordering },
	function(data) {
		$('.accessresults').html(data);
	});

}
</script>
