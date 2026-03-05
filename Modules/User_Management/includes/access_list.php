<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
?>
<style>
.app-left {width:300px;border:1px solid #aeaeae;padding:10px;}
.main-right {position:relative;width:400px;border:1px solid #aeaeae;box-sizing: border-box;overflow-x:hidden;overflow-y:auto}
.app-left ul {list-style-type: none;padding:0;margin:0;}
.appleft-title {border: 1px solid #aeaeae;padding: 5px 5px 5px 5px;font-size:18px;margin-bottom:10px;background:#636363;color:#fff;text-align:center;font-weight:600;}
.appleft {border: 1px solid #aeaeae;padding: 5px 5px 5px 5px;font-size:14px;margin-bottom:5px;background:#f1f1f1;cursor: pointer;}
.appleft:hover {background: #aeaeae;color:#fff;}
.access {position:absolute;width:100%;height:99%;padding: 5px;}
</style>
<table style="width: 100%">
	<tr>
		<td valign="top" class="app-left">
			<ul>
			<li class="appleft-title">APPLICATION NAME</li>
<?php
	$query = "SELECT * FROM tbl_system_applications ORDER BY application_id ASC";
	$results = mysqli_query($db, $query);    
	while($ROW = mysqli_fetch_array($results))  
	{
		$application_id = $ROW['application_id'];
		$application_name = $ROW['application_name'];
?>			
				<li class="appleft" onclick="ListAccess('<?php echo $application_id?>','<?php echo $application_name?>')"><?php echo $application_name?></li>
<?php } ?>
			</ul>
		</td>
		<td width="10px;"></td>
		<td valign="top" class="main-right">
			<div class="access" id="access"></div>
		</td>
	</tr>
	<tr>
		<td colspan="3" style="height:5px"></td>
	</tr>
	<tr>
		<td valign="top">&nbsp;</td>
		<td width="10px;">&nbsp;</td>
		<td valign="top">			
			<table style="width: 100%;border-collapse:collapse" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<input id="appid" type="hidden">
						<input id="appname" type="hidden">
						<input id="module_name" type="text" class="form-control form-control-sm">
					</td>
					<td style="width:5px"></td>
					<td>
						<button class="btn btn-secondary btn-sm w-100" onclick="addNewModules()">Add</button>
					</td>
				</tr>
			</table>			
		</td>
	</tr>
</table>
<div id="addresults"></div>
<script>
function addNewModules()
{
	var mode = 'addappmodules';
	var application_id = $('#appid').val();
	var application_name = $('#appname').val();
	var module_name = $('#module_name').val();
	if(application_id == '' && application_name == '')
	{
		swal("Add Error","Please Select Application", "error");
		return false;
	}
	if(module_name == '')
	{
		swal("Input Error","Please Enter Access name", "warning");
		return false;
	}
	$.post("./Modules/User_Management/actions/actions.php", { mode: mode, application_id: application_id, application_name: application_name, module_name: module_name },
	function(data) {
		$('#addresults').html(data);
		ListAccess(application_id,application_name);
	});
}
function ListAccess(application_id,application_name)
{
	$('#appid').val(application_id);
	$('#appname').val(application_name);
	$.post("./Modules/User_Management/includes/access_list_data.php", { application_id: application_id},
	function(data) {
		$('#access').html(data);
	});
}
</script>

