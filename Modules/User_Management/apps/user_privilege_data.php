<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$functions = new PageFunctions;
$idcode = $_POST['idcode'];
?>
<style>
.datathead td, .datatbody {
	font-weight:600;
	text-align:center;
}
.tableFixHeadPrivData { overflow: auto; height: calc(100vh - 335px); width:100%; }
.tableFixHeadPrivData thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.tableFixHeadPrivData table  { border-collapse: collapse;}
.tableFixHeadPrivData th, .tableFixHeadPrivData td { font-size:14px; white-space:nowrap; }
</style>
<div class="privilegeadd">
	<div class="tableFixHeadPrivData" id="tableFixHeadPrivData">
	<table style="width: 100%; min-width:1270px" class="table table-bordered">
		<thead>
			<tr class="datathead">
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
<?php
	$query = "SELECT * FROM tbl_system_permission WHERE idcode='$idcode' ORDER BY applications";
	$results = mysqli_query($db, $query);    
	$n=0;
	while($PRIVROW = mysqli_fetch_array($results))  
	{
		$n++;
		$rowid = $PRIVROW['id'];
		$idcode = $PRIVROW['idcode'];
		if($PRIVROW['p_view'] == 1) { $pview = 'checked="checked"'; } else { $pview=''; }
		if($PRIVROW['p_read'] == 1) { $pread = 'checked="checked"'; } else { $pread=''; }
		if($PRIVROW['p_add'] == 1) { $padd = 'checked="checked"'; } else { $padd=''; }
		if($PRIVROW['p_write'] == 1) { $pwrite = 'checked="checked"'; } else { $pwrite=''; }		
		if($PRIVROW['p_edit'] == 1) { $pedit = 'checked="checked"'; } else { $pedit=''; }
		if($PRIVROW['p_delete'] == 1) { $pdelete = 'checked="checked"'; } else { $pdelete=''; }
		if($PRIVROW['p_update'] == 1) { $pupdate = 'checked="checked"'; } else { $pupdate=''; }
		if($PRIVROW['p_print'] == 1) { $pprint = 'checked="checked"'; } else { $pprint=''; }
		if($PRIVROW['p_review'] == 1) { $preview = 'checked="checked"'; } else { $preview =''; }
		if($PRIVROW['p_approver'] == 1) { $papprover = 'checked="checked"'; } else { $papprover=''; }
		if($PRIVROW['p_locked'] == 1) { $plocked= 'checked="checked"'; } else { $plocked=''; }		
?>
		<tr class="datatbody">
			<td style="width: 200px">
				<input type="text" class="form-control form-control-sm" value="<?php echo $PRIVROW['applications']; ?>" readonly>
			</td>
			<td style="width:200px">
				<input type="text" class="form-control form-control-sm" value="<?php echo $PRIVROW['modules']; ?>" readonly>
			</td>
			<td>
				<label class="switch">
					<input id="p_view<?php echo $n; ?>" type="checkbox" <?php echo $pview; ?> onchange="updatePermissions('p_view','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_read<?php echo $n; ?>" type="checkbox" <?php echo $pread; ?> onchange="updatePermissions('p_read','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_add<?php echo $n; ?>" type="checkbox" <?php echo $padd; ?> onchange="updatePermissions('p_add','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_write<?php echo $n; ?>" type="checkbox" <?php echo $pwrite; ?> onchange="updatePermissions('p_write','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>			
			<td style="width: 80px">
				<label class="switch">
					<input id="p_edit<?php echo $n; ?>" type="checkbox" <?php echo $pedit; ?> onchange="updatePermissions('p_edit','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_delete<?php echo $n; ?>" type="checkbox" <?php echo $pdelete; ?> onchange="updatePermissions('p_delete','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_update<?php echo $n; ?>" type="checkbox" <?php echo $pupdate; ?> onchange="updatePermissions('p_update','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_print<?php echo $n; ?>" type="checkbox" <?php echo $pprint; ?> onchange="updatePermissions('p_print','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_review<?php echo $n; ?>" type="checkbox" <?php echo $preview; ?> onchange="updatePermissions('p_review','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_approver<?php echo $n; ?>" type="checkbox" <?php echo $papprover; ?> onchange="updatePermissions('p_approver','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width: 80px">
				<label class="switch">
					<input id="p_locked<?php echo $n; ?>" type="checkbox" <?php echo $plocked; ?> onchange="updatePermissions('p_locked','<?php echo $rowid; ?>','<?php echo $n; ?>')">
					<span class="slider round"></span>
				</label>
			</td>
			<td style="width:100px">
				<button class="btn btn-danger btn-block btn-sm w-100" onclick="removePermissions('<?php echo $rowid; ?>','<?php echo $idcode; ?>')">Remove</button>
			</td>
		</tr>
<?php } ?>	
	</tbody>	
	</table>
	</div>
</div>
<div class="presults"></div>
<script>
function removePermissions(rowid,idcode)
{
	var mode = 'removepermissions';
	$.post("../../../Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, idcode: idcode },
	function(data)
	{
		$('.presults').html(data);
		userPermissions(idcode);
	});
}
function updatePermissions(column,rowid,elemid)
{
	if( $('#' + column + "" + elemid).is(":checked") == true ) 
	{
		savePermissions(rowid,column,1)	
	} else {
		savePermissions(rowid,column,0)	
	}
}
function savePermissions(rowid,column,permission)
{
	var mode = 'savepermissions';
	$.post("./Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, column: column, permission: permission },
	function(data)
	{
		$('.presults').html(data);
	});
}
</script>