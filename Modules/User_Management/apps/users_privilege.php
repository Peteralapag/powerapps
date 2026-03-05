<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$functions = new PageFunctions;
$idcode = $_POST['idcode'];
?>
<style>
.copyto { display:none; }
.copyto button {width:50%;float:left;}
.privilegeadd {margin-top:px;width:100%; font-weight:;text-align:center;font-size:14px }
.privilegeadd td select, input {width:200px;}
.tables td {padding:5px;}
#privilegeadd { display:nones; }
.copy-wrapper {
	position:absolute;
	display:none;
	width:100%;
	min-height:50px;
	max-height:300px;
	background:#fff;
	z-index:999;
	background:#fff;
	border-radius:0px 0px 10px 10px;
	border:1px solid #aeaeae;
	overflow:hidden;
	overflow-y:auto;
}
.tableFixHeadPrivH  { overflow-y: auto; width:100% }
.tableFixHeadPrivH thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.tableFixHeadPrivH table  { border-collapse: collapse;}
.tableFixHeadPrivH th, .tableFixHeadPrivH td { font-size:14px; white-space:nowrap; } 
</style>
<table style="width: 100%;margin-bottom:10px">
	<tr>
		<td style="width:200px">
			<div class="btn-group btn-block" role="group" aria-label="BAR Software & IT Solutions">	
				<!-- button class="btn btn-warning" style="width:45%" onclick="addPrivilege()"><i class="fa-sharp fa-solid fa-plus pull-left"></i> Add Privilege</button -->
				<button class="btn btn-primary btn-sm" style="width:55%" onclick="showCopyTo()"><i class="fa-solid fa-clone pull-left"></i> Copy Privilege To</button>
			</div>
		</td>
		<td style="width:10px;"></td>
		<td class="copyto" style="width:250px;position:relative">
			<input id="searchcopyto" type="text" class="form-control form-control-sm" placeholder="Recipient's username" aria-label="Recipient's username">
			<input id="id_code" type="hidden">
			<div class="copy-wrapper" id="copywrapper">
				<div class="employee-data" id="copysearchdata"></div>
			</div>
		</td>
		<td style="width:2px;"></td>
		<td class="copyto" style="width:100px;">
			<div class="btn-group btn-block" role="group" aria-label="BAR Software & IT Solutions">
				<button class="btn btn-success btn-sm" type="button" onclick="copyToThisUser('<?php echo $idcode; ?>')"><i class="fa-solid fa-floppy-disk"></i></button>
				<button class="btn btn-danger btn-sm" type="button" onclick="hideCopyTo()"><i class="fa-sharp fa-solid fa-circle-xmark"></i></button>
			</div>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<div class="privilegeadd" id="privilegeadd">
	<div class="tableFixHeadPrivH">
		<table style="width: 100%; min-width:1270px" class="table table-bordered">
			<thead>
				<tr>
					<th style="width: 200px !important">APPLICATION</th>
					<th style="width: 200px !important">MODULES</th>
					<th style="width: 80px">VIEW</th>
					<th style="width: 80px">READ</th>
					<th style="width: 80px">ADD</th>
					<th style="width: 80px">WRITE</th>
					<th style="width: 80px">EDIT</th>
					<th style="width: 80px">DELETE</th>
					<th style="width: 80px">UPDATE</th>
					<th style="width: 80px">PRINT</th>
					<th style="width: 80px">REVIEW</th>
					<th style="width: 80px">APPROV</th>
					<th style="width: 80px">LOCKED</th>
					<th style="text-align:center;width: 100px">ACTION</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="width:200px !important">
						<select id="application" class="form-control form-control-sm w-100" onchange="selectModules(this.value)">
							<?php echo $functions->GetApplication('',$db); ?>
						</select>
					</td>
					<td style="width:200px !important">
						<select id="modules" class="form-control form-control-sm w-100"></select>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_view" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_read" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_add" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_write" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_edit" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_delete" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_update" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_print" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_review" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_approver" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width: 80px">
						<label class="switch">
							<input id="p_locked" type="checkbox">
							<span class="slider round"></span>
						</label>
					</td>
					<td style="width:100px">
						<button class="btn btn-primary btn-sm w-100" onclick="addPermissions('<?php echo $idcode; ?>')">Save</button>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<hr>
</div>
</div>
<div class="copyresults"></div>
<div id="privilege_data"></div>
<script>
function viewUsersList()
{
	$('#contents').load('');
	$.post("../Modules/User_Management/apps/users_list.php", { },
	function(data)
	{
		$('#contents').html(data);				
	});

}
function copyToThisUser(idcode)
{
	var mode = 'copytothisuser';
	var useridcode = $('#id_code').val();	
	
	if(idcode === useridcode)
	{
		swal("System Message","You cannot copy Privilege to self","warning");
		return false;
	}
	$.post("./actions/actions.php", { mode: mode, useridcode: useridcode, myidcode: idcode },
	function(data)
	{
		$('.copyresults').html(data);				
	});
}
function get_copytouser(idcode,acctname,params)
{
	var mode = 'searchcopyuser';
	$('#searchcopyto').val(acctname);	
	$('#id_code').val(idcode);	
}
$(function()
{
	$('#searchcopyto').keyup(function()
	{
		$('#copywrapper').slideDown();
		var mode = 'searchcopyuser';
		var search = $('#searchcopyto').val();
		$.post("./actions/actions.php", { mode: mode, search: search },
		function(data)
		{
			$('#copysearchdata').html(data);				
		});			
	});
	
	var idcode = '<?php echo $idcode; ?>';
	$.post("./Modules/User_Management/apps/user_privilege_data.php", { idcode: idcode },
	function(data)
	{
		$('#privilege_data').html(data);
	});
});
function addPermissions(idcode)
{
	var mode = 'addpermissions';
	var application = $('#application').find(":selected").text();
	var module = $("#modules").val();
	
	if($('#p_view').is(":checked") == true) { var p_view = 1; } else { var p_view = 0; }
	if($('#p_read').is(":checked") == true) { var p_read = 1; } else { var p_read = 0; }
	if($('#p_add').is(":checked") == true) { var p_add = 1; } else { var p_add = 0; }
	if($('#p_write').is(":checked") == true) { var p_write = 1; } else { var p_write = 0; }
	if($('#p_edit').is(":checked") == true) { var p_edit = 1; } else { var p_edit = 0; }
	if($('#p_delete').is(":checked") == true) { var p_delete = 1; } else { var p_delete = 0; }
	if($('#p_update').is(":checked") == true) { var p_update = 1; } else { var p_update = 0; }
	if($('#p_print').is(":checked") == true) { var p_print = 1; } else { var p_print = 0; }
	if($('#p_approver').is(":checked") == true) { var p_approver = 1; } else { var p_approver = 0; }
	if($('#p_locked ').is(":checked") == true) { var p_locked = 1; } else { var p_locked = 0; }
	if(application === '' || application == '--SELECT APPS--')
	{
		app_alert("System Message","Please select Application","warning");
		return false;
	}
	else if(module === '' || module == null || module == '--SELECT MODULE--')
	{
		app_alert("System Message","Please select Module","warning");
		return false;
	}
	
	rms_reloaderOn('Updating...');
	setTimeout(function()
	{
		$.post("./Modules/User_Management/actions/actions.php", { mode: mode, idcode: idcode, application: application, module: module, p_view: p_view, p_read: p_read, p_add: p_add, p_write: p_write, p_edit: p_edit, p_delete: p_delete, p_update: p_update, p_print: p_print, p_approver: p_approver, p_locked: p_locked},
		function(data)
		{
			$('.results').html(data);
			rms_reloaderOff();
		});
	}, 1000);
}
function selectModules(appsid)
{
	var mode = 'selectmodule';
	$.post("./Modules/User_Management/actions/actions.php", { mode: mode, appsid: appsid },
	function(data)
	{
		$('#modules').html(data);
	});
}
function addPrivilege()
{
	$('#privilegeadd').slideToggle();
	if($('#privilegeadd').is(':visible'))
	{
		$('#tableFixHeadPrivData').css('height', '100vh');
	} else {
		$('#tableFixHeadPrivData').css('height', 'calc(100vh - 335px)');
	}
}
function showCopyTo()
{
	$('.copyto').fadeIn();
}
function hideCopyTo()
{
	$('.copyto').fadeOut();
	$('#searchcopyto').val('');	
	$('#id_code').val('');
}
</script>