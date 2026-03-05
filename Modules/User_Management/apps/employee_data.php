<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
	if($_POST['mode'] == 'userinfo')
	{
		$rowid = $_POST['rowid'];		
		$query = "SELECT * FROM tbl_system_user WHERE id='$rowid'";
		$results = mysqli_query($db, $query);    
		$return = '<option value="">--SELECT DATABASE--</option>';
	    while($USERSROW = mysqli_fetch_array($results))  
		{
			$rowid = $USERSROW['id'];
			$idcode = $USERSROW['idcode'];
			$firstname = $USERSROW['firstname'];
			$lastname = $USERSROW['lastname'];
			$username = $USERSROW['username'];
			$password = $USERSROW['password'];
			$role = $USERSROW['role'];
			$level = $USERSROW['level'];
			$login_status = $USERSROW['login_status'];
			$void_access = $USERSROW['void_access'];
			$department = $USERSROW['department'];
			$branch = $USERSROW['branch'];
			$cluster = $USERSROW['cluster'];
			$company = $USERSROW['company'];
			if($void_access == 0)
			{
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
		}
	}
	if($_POST['mode'] == 'employeeinfo')
	{
		$rowid= $_POST['rowid'];		
		$query = "SELECT * FROM tbl_employees WHERE id='$rowid'";
		$results = mysqli_query($db, $query);    
		$return = '<option value="">--SELECT DATABASE--</option>';
	    while($USERSROW = mysqli_fetch_array($results))  
		{
			$rowid = $USERSROW['id'];
			$idcode = $USERSROW['idcode'];
			$firstname = utf8_encode($USERSROW['firstname']);
			$lastname = utf8_encode($USERSROW['lastname']);
			$username = ucwords($firstname." ".$lastname);
			$password = '';
			$role = '';
			$level = '';
			$void_access = 0;
			$department = $USERSROW['department'];
			$branch = $USERSROW['branch'];
			$cluster = $USERSROW['cluster'];
			$company = $USERSROW['company'];
			if($void_access == 0)
			{
				$checked = 'checked="checked"';
			} else {
				$checked = '';
			}
		}
	}
}
else
{
	$mode = '';
	$rowid = '';
	$firstname = '';
	$lastname = '';
	$idcode = '';
	$username = '';
	$password = '';
	$role = '';
	$level = '';
	$login_status = '';
	$void_access = '';
	$assign_cluster = '';
	$department = '';
	$branch = '';
	$checked = '';
}
?>
<style>
.generate-icon {position:absolute;top:11px;font-size:18px;right:11px;padding:0px 5px 0px 5px;border:1px solid dodgerblue;border-radius:4px;cursor:pointer;}
.generate-icon:hover {background:hotpink;color:#fff;border:1px solid red;}
.input-right {padding-right:35px;}
	#assigned_clusters {height:120px; font-size:10px; line-height:1.1; padding:8px; overflow:auto; resize:vertical;}
	#assigned_branches {height:120px; font-size:10px; line-height:1.1; padding:8px; overflow:auto; resize:vertical;}
</style>
<div id="useform">
	<table style="width: 100%" class="table">
		<tr>
			<th style="width:150px">First Name</th>
			<td><input id="firstname" type="text" class="form-control form-control-sm" value="<?php echo $firstname; ?>"></td>
		</tr>
		<tr>
			<th>Last Name</th>
			<td>
				<input id="lastname" type="text" class="form-control form-control-sm" value="<?php echo $lastname; ?>">
				<input id="idcode" type="hidden" class="form-control form-control-sm" value="<?php echo $idcode; ?>">
			</td>
		</tr>
		<tr>
			<th>Company</th>
			<td><select id="company" class="form-control form-control-sm"><?php echo $functions->GetCompany($company,$db)?>
			</select></td>
		</tr>		
		<tr>
			<th>Cluster</th>
			<td><select id="cluster" class="form-control form-control-sm">
				<?php echo $functions->GetCluster($cluster,$db)?>
			</select></td>
		</tr>
		<tr>
			<th>Branch</th>
			<td><select id="branch" class="form-control form-control-sm">
				<?php echo $functions->GetBranch($branch,$db)?>
			</select></td>
		</tr>
		<tr>
			<th>Department</th>
			<td><select id="department" class="form-control form-control-sm">
				<?php echo $functions->GetDepartment($department,$db)?>
			</select></td>
		</tr>
		<tr>
			<th style="vertical-align:top;width:150px;">
				Assign Cluster
				<br>
				<button class="btn btn-info btn-sm" style="margin-top:6px;" onclick="addCluster('<?php echo $rowid; ?>','<?php echo $idcode; ?>')">Add Cluster</button>
				<br>
				<button class="btn btn-danger btn-sm" style="margin-top:6px;" onclick="deleteAllClusters('<?php echo $rowid; ?>','<?php echo $idcode; ?>')">Delete All</button>
			</th>
			<td>
				<textarea id="assigned_clusters" class="form-control form-control-sm" readonly placeholder="No clusters assigned"></textarea>
			</td>
		</tr>
		<tr>
			<th style="vertical-align:top;width:150px;">
				Assign Branch
				<br>
				<button class="btn btn-info btn-sm" style="margin-top:6px;" onclick="addBranch('<?php echo $rowid; ?>','<?php echo $idcode; ?>')">Add Branch</button>
				<br>
				<button class="btn btn-danger btn-sm" style="margin-top:6px;" onclick="deleteAllBranches('<?php echo $rowid; ?>','<?php echo $idcode; ?>')">Delete All</button>
			</th>
			<td>
				<textarea id="assigned_branches" class="form-control form-control-sm" readonly placeholder="No branches assigned"></textarea>
			</td>
		</tr>
		<tr>
			<th>WMS LOCATION</th>
			<td>
				<button class="btn btn-success btn-sm w-100" onclick="updateManager('<?php echo $idcode; ?>')">Update Manager</button>
			</td>
		</tr>
		<tr>
			<th>Username</th>
			<td><input id="username" type="text" class="form-control form-control-sm" value="<?php echo $username; ?>" autocomplete="nope"></td>
		</tr>
		<tr>
			<th>Password</th>
			<td style="position:relative;">
				<input id="password" type="text" class="form-control input-right" placeholder="Password" value="" autocomplete="nope">
				<i class="fa-solid fa-wind-turbine generate-icon bg-info" onclick="genPass(6)"></i>
				<input id="origpassword" type="hidden" class="form-control" value="<?php echo $password; ?>">
			</td>
		</tr>
		<tr>
			<th>User Role</th>
			<td>
				<select id="role" class="form-control form-control-sm">
					<?php echo $functions->GetUserRole($role,$db)?>
				</select>
			</td>
		</tr>
		<tr>
			<th>User Level</th>
			<td><select id="level" class="form-control form-control-sm"><?php echo $functions->GetUserLevel($level,$db)?></select></td>
		</tr>
		<tr>
			<th>Active</th>
			<td>
				<label class="switch">
					<input id="uservoid" type="checkbox" <?php echo $checked; ?>>
					<span class="slider round"></span>
				</label>
			</td>
		</tr>
	</table>
	<div class="btn-group btn-block" role="group" aria-label="BAR Software & IT Solutions" style="width:100%">
	<?php if($mode == 'employeeinfo') { ?>
		<button class="btn btn-primary btn-sm" style="width:50%" onclick="saveUser('add')"><i class="fa-solid fa-floppy-disk pull-left"></i> Save</button>
	<?php } else if($mode == 'userinfo') { ?>
		<button class="btn btn-primary btn-sm" style="width:35%" onclick="saveUser('update')"><i class="fa-solid fa-pen-to-square pull-left"></i> Update</button>
		<button id="reloadPermissions" class="btn btn-warning btn-sm" style="width:37%" onclick="userPermissions('<?php echo $idcode; ?>')"><i class="fa-solid fa-bolt pull-left"></i> Permissions</button>
		<button class="btn btn-danger btn-sm" style="width:29.3%" onclick="deleteUser('<?php echo $rowid?>','<?php echo $level?>','<?php echo $userlevel?>','<?php echo $idcode?>')"><i class="fa-solid fa-xmark"></i> Delete</button>
	<?php } ?>
	</div>
</div>
<div class="results"></div>
<br>
<script>
function updateManager(idcode)
{
	$('#modaltitle').html("ADD WMS LOCATION");
	$.post("../../../Modules/User_Management/apps/add_manager.php", { idcode: idcode },
	function(data)
	{
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}

function addCluster(rowid, idcode)
{
    $('#modaltitle').html("ADD CLUSTER");
    $.post("../../../Modules/User_Management/apps/add_cluster.php", { rowid: rowid, idcode: idcode },
    function(data)
    {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });
}
function addBranch(rowid, idcode)
{
	$('#modaltitle').html("ADD BRANCH");
	$.post("../../../Modules/User_Management/apps/add_branch.php", { rowid: rowid, idcode: idcode },
	function(data)
	{
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function loadAssignedClusters(rowid, idcode)
{
	$.post("../../../Modules/User_Management/actions/actions.php", { mode: 'getAssignedClusters', rowid: rowid, idcode: idcode },
	function(data)
	{
		try {
			var resp = typeof data === 'string' ? JSON.parse(data) : data;
			if (Array.isArray(resp) && resp.length > 0) {
				var lines = resp.map(function(item, idx){ return (idx+1) + '. ' + item; });
				$('#assigned_clusters').val(lines.join('\n'));
			} else {
				$('#assigned_clusters').val('');
				$('#assigned_clusters').attr('placeholder','No clusters assigned');
			}
		} catch(e) {
			$('#assigned_clusters').val('');
		}
	});
}
function deleteAllClusters(rowid, idcode)
{
	app_confirm("Delete Assigned Clusters","Are you sure you want to delete all assigned clusters?","warning", deleteAllClustersYes, rowid, idcode);
}

function deleteAllClustersYes(rowid, idcode)
{
	var mode = 'deleteAssignedClusters';
	rms_reloaderOn('Deleting...');
	$.ajax({
		url: "../../../Modules/User_Management/actions/actions.php",
		method: 'POST',
		dataType: 'json',
		data: { mode: mode, rowid: rowid, idcode: idcode },
		success: function(resp) {
			if (resp && resp.status === 'success') {
				if (typeof showToast === 'function') showToast('success', resp.message, 2000);
				else swal('Success', resp.message, 'success');
				$('#assigned_clusters').val('');
				loadAssignedClusters(rowid, idcode);
			} else {
				var msg = (resp && resp.message) ? resp.message : 'Error deleting clusters';
				if (typeof showToast === 'function') showToast('error', msg, 3000);
				else swal('Error', msg, 'error');
			}
			rms_reloaderOff();
		},
		error: function(xhr, status, err) {
			var text = xhr.responseText || status || err;
			if (typeof showToast === 'function') showToast('error', 'Request failed: ' + text, 4000);
			else swal('Error', 'Request failed: ' + text, 'error');
			console.error('deleteAssignedClusters error:', status, err, xhr.responseText);
			rms_reloaderOff();
		}
	});
}
function userPermissions(idcode)
{
	$.post("../../../Modules/User_Management/apps/users_privilege.php", { idcode: idcode },
	function(data)
	{
		$('#contents').html(data);
	});
}

function deleteUser(rowid,level,userlevel,idcode)
{
	if(level >= userlevel)
	{
		swal('System Message',"You cannot delete equal or higher than yours","warning");
		return false;		
	} else {
		app_confirm("Delete User","Are you sure to delete this user?","warning","deleteUserYes",rowid,idcode);
	}
}
function deleteUserYes(rowid,idcode)
{
	var mode = 'deleteuser';
	rms_reloaderOn('Deleting...');
	setTimeout(function()
	{
		$.post("../../../Modules/User_Management/actions/actions.php", { mode: mode, rowid: rowid, idcode: idcode },
		function(data)
		{
			swal("Deleted","User has been removed from the system","success");
			$('.results').val(data);
			rms_reloaderOff();
			loadModules('User_Management');
		});
	},1000);
}
function genPass(params)
{
	var mode = 'generatepassword';
	$.post("../../../Modules/User_Management/actions/actions.php", { mode: mode, params: params },
	function(data)
	{
		$('#password').val(data);
	});
}
function saveUser(params)
{
	var entrymode = '<?php echo $mode; ?>';
	var rowid = '<?php echo $rowid; ?>';
	var firstname = $('#firstname').val();
	var lastname = $('#lastname').val();
	var idcode = $('#idcode').val();
	var company = $('#company').val();
	var cluster = $('#cluster').val();
	var branch = $('#branch').val();
	var department = $('#department').val();
	var username = $('#username').val();
	var password = $('#password').val();
	var origpassword = $('#origpassword').val();
	var role = $('#role').val();
	var level = $('#level').val();
	
	if(firstname === '' || firstname === 'undefined')
	{	
		app_alert("System Message","First name must be filled in","warning","Ok","firstname",'focus');
		return false;
	}
	else if(lastname === '' || lastname === 'undefined')
	{	
		app_alert("System Message","Last name must be filled in","warning","Ok","lastname",'focus');
		return false;
	}
	else if(idcode === '' || idcode === 'undefined')
	{	
		app_alert("System Message","Something is wrong! Please reload the application","warning","Ok","","");
		return false;
	}
	else if(cluster === '' || cluster === 'undefined')
	{	
		app_alert("System Message","Please select Cluster","warning","Ok","cluster",'focus');
		return false;
	}
	else if(branch === '' || branch === 'undefined')
	{	
		app_alert("System Message","Please select Branch","warning","Ok","branch",'focus');
		return false;
	}
	else if(department=== '' || department === 'undefined')
	{	
		app_alert("System Message","Please select Deparment","warning","Ok","department",'focus');
		return false;
	}
	else if(username === '' || username === 'undefined')
	{	
		app_alert("System Message","Username cannot be empty or blank","warning","Ok","username",'focus');
		return false;
	}
	if(entrymode == 'employeeinfo')
	{
		if(password === '' || password == 'undefined')
		{
			app_alert("System Message","Password cannot be empty","warning","Ok","password",'focus');
			return false;
		}
	}
	if(role === '' || role === 'undefined')
	{	
		app_alert("System Message","Please select User Role","warning","Ok","role",'focus');
		return false;
	}
	else if(level === '' || level === 'undefined')
	{	
		app_alert("System Message","Please select User Level","warning","Ok","level",'focus');
		return false;
	}
	if($('#uservoid').is(":checked") == true)
	{
		var uservoid = 0;
	} else {
		var uservoid = 1;
	}
	if(params == 'add')
	{
		rms_reloaderOn('Saving...');
		var mode = 'adduser';
	}
	if(params == 'update')
	{
		rms_reloaderOn('Updating...');
		var mode = 'updateuser';
	}
	setTimeout(function()
	{
		$.post("../../../Modules/User_Management/actions/actions.php", { mode: mode,rowid: rowid,firstname: firstname,lastname: lastname,idcode: idcode, company: company, cluster: cluster,branch: branch,department: department,username: username,password: password,role: role,level: level,uservoid: uservoid },
		function(data)
		{
			$('.results').html(data);
			rms_reloaderOff();
			loadModules('User_Management');
		});
	}, 1000);
}
$(function()
{
/*	$('#role').change(function()
	{
		if($('#level').val() == '100' || '<?php echo $_SESSION["panel_userlevel"]; ?>' == 100)
		{
			swal("System Message","You cannot demote an owner","warning");
			return false;
		}
	}); */
	if('<?php echo $mode; ?>' == 'userinfo')
	{
		$("#useform :input").attr('disabled', false);
	} 
	else if('<?php echo $mode; ?>' == 'employeeinfo')
	{
		$("#useform :input").attr('disabled', false);
	} else {
		$("#useform :input").attr('disabled', true);
	}

    // Load assigned clusters for this user when the form is shown
    if('<?php echo $rowid; ?>' !== '' && '<?php echo $idcode; ?>' !== '') {
        loadAssignedClusters('<?php echo $rowid; ?>','<?php echo $idcode; ?>');
    }
	$('#firstname,#lastname').keyup(function()
	{
		var firstname = $('#firstname').val();
		var lastname = $('#lastname').val();
		$('#username').val(firstname + " " + lastname);
	});

});
</script>

<script>
// --- Assigned Branches dynamic UI & handlers ---
(function(){
    var rowid = '<?php echo $rowid; ?>';
    var idcode = '<?php echo $idcode; ?>';
    // inject simple CSS for assigned branches if not present
    if (!document.querySelector('#assigned-branches-style')) {
        var style = document.createElement('style');
        style.id = 'assigned-branches-style';
        style.innerHTML = '#assigned_branches {height:120px; font-size:10px; line-height:1.1; padding:8px; overflow:auto; resize:vertical;}';
        document.head.appendChild(style);
    }
    // transform Assign Branch row to match Assign Cluster layout
    var $th = $("th:contains('Assign Branch')").first();
    if ($th.length) {
        $th.attr('style','vertical-align:top;width:150px;');
        $th.html('Assign Branch<br><button class="btn btn-info btn-sm" style="margin-top:6px;" onclick="addBranch(\''+rowid+'\',\''+idcode+'\')">Add Branch</button><br><button class="btn btn-danger btn-sm" style="margin-top:6px;" onclick="deleteAllBranches(\''+rowid+'\',\''+idcode+'\')">Delete All</button>');
        var $td = $th.closest('tr').find('td').first();
        $td.html('<textarea id="assigned_branches" class="form-control form-control-sm" readonly placeholder="No branches assigned"></textarea>');
    }

    window.loadAssignedBranches = function(rowidParam, idcodeParam) {
        $.post("../../../Modules/User_Management/actions/actions.php", { mode: 'getAssignedBranches', rowid: rowidParam, idcode: idcodeParam },
        function(data) {
            try {
                var resp = typeof data === 'string' ? JSON.parse(data) : data;
				if (Array.isArray(resp) && resp.length > 0) {
					var lines = resp.map(function(item, idx){ return (idx+1) + '. ' + item; });
					$('#assigned_branches').val(lines.join('\n'));
				} else {
					$('#assigned_branches').val('');
					$('#assigned_branches').attr('placeholder','No branches assigned');
				}
            } catch(e) {
                $('#assigned_branches').val('');
            }
        });
    };

    window.deleteAllBranches = function(rowidParam, idcodeParam) {
        app_confirm("Delete Assigned Branches","Are you sure you want to delete all assigned branches?","warning", deleteAllBranchesYes, rowidParam, idcodeParam);
    };

    window.deleteAllBranchesYes = function(rowidParam, idcodeParam) {
        var mode = 'deleteAssignedBranches';
        rms_reloaderOn('Deleting...');
        $.ajax({
            url: "../../../Modules/User_Management/actions/actions.php",
            method: 'POST',
            dataType: 'json',
            data: { mode: mode, rowid: rowidParam, idcode: idcodeParam },
            success: function(resp) {
                if (resp && resp.status === 'success') {
                    if (typeof showToast === 'function') showToast('success', resp.message, 2000);
                    else swal('Success', resp.message, 'success');
                    $('#assigned_branches').val('');
                    loadAssignedBranches(rowidParam, idcodeParam);
                } else {
                    var msg = (resp && resp.message) ? resp.message : 'Error deleting branches';
                    if (typeof showToast === 'function') showToast('error', msg, 3000);
                    else swal('Error', msg, 'error');
                }
                rms_reloaderOff();
            },
            error: function(xhr, status, err) {
                var text = xhr.responseText || status || err;
                if (typeof showToast === 'function') showToast('error', 'Request failed: ' + text, 4000);
                else swal('Error', 'Request failed: ' + text, 'error');
                console.error('deleteAssignedBranches error:', status, err, xhr.responseText);
                rms_reloaderOff();
            }
        });
    };

    // initial load
    if (rowid !== '' && idcode !== '') {
        loadAssignedBranches(rowid, idcode);
    }
})();
// --- end Assigned Branches ---
</script>
</script>
