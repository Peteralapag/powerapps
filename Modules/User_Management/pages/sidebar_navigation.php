<?php
require '../../../init.php';
?>
<style>
.users-data {
	font-size:14px;
}
.employee-data{
	font-size:14px;
}
</style>
<table style="width: 100%">
	<tr>
		<td>
			<div class="btn-group" role="group" style="width:100%;">
				<button type="button" class="btn btn-primary w-100 color-white" onclick="addUser('add')">Add User <i class="fa-solid fa-plus float-end"></i></button>
				<button type="button" class="btn btn-info w-100 color-white" onclick="viewAllUser()"> User' List <i class="fa-solid fa-eye float-end"></i></button>
			</div>
		</td>
	</tr>
	<tr>
		<td style="height:10px"></td>
	</tr>	
	<tr>
		<td>
			<div class="input-group mb-3" style="position:relative">
				<div class="input-group mb-3">
					<input type="hidden" id="searchmode" value="">
					<input id="searchuser" type="text" class="form-control form-control-sm" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2" autocomplete="off">
					<button class="btn btn-outline-secondary" type="button" id="button-addon2" onclick="clearSearch()">&times</button>
				</div>
				<div id="searchbox" class="search-box" style="z-index:999">
					<div class="employee-data" id="employeedata"></div>
				</div>
			</div>
		</td>
	</tr>
</table>
<div class="users-data" id="usersdata"></div>
<script>
function viewAllUser()
{
	loadModules('User_Management');
}
function get_users(rowid,acctname,params)
{
	if(params == 'getuser') { var mode = 'userinfo'; }
	if(params == 'getemployee') { var mode = 'employeeinfo'; }
	$('#searchuser').val(acctname);	
	$.post("../../../Modules/User_Management/apps/employee_data.php", { mode: mode, rowid: rowid },
	function(data)
	{
		$("#useform :input").attr('disabled', false);
		$('#usersdata').html(data);	
		$('#searchbox').slideUp();			
	});
}
function clearSearch()
{
	$("#useform :input").attr('disabled', true);
	$('#searchuser,#searchmode').val('');
	load_employee_data();
}
$(function()
{
	if('<?php echo DB_HOST; ?>' != '')
	{
		$('#searchuser').keyup(function()
		{
			if($('#searchmode').val() == 'add')
			{
				var mode = 'searchadd';
			} else {
				var mode = 'searchuser';
			}
			var search = $('#searchuser').val();
			$('#searchbox').slideDown();
			$.post("../../../Modules/User_Management/actions/actions.php", { mode: mode, search: search },
			function(data)
			{
				$('#employeedata').html(data);				
			});	
		});
		load_employee_data();
	} else {
		$(".input-group,.btn-group :input").attr("disabled", true);
		$('#usersdata').html('<i class="fa-solid fa-triangle-exclamation"></i> No Database selected');
	}
});
function addUser(params)
{
	$("#useform :input").attr('disabled', false);
	$('#searchmode').val(params);
	$('#searchuser').val('');
	$('#searchbox').slideUp();
	add_employee_data();
}
function add_employee_data()
{
	$.post("../../../Modules/User_Management/apps/employee_data.php", { },
	function(data)
	{
		$('#usersdata').html(data);				
	});
}
function load_employee_data()
{
	$.post("../../../Modules/User_Management/apps/employee_data.php", { },
	function(data)
	{
		$('#usersdata').html(data);				
	});
}
</script>
