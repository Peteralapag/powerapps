<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$encpass = new Password;
$functions = new PageFunctions;

// Define the appuser variable from the session
$appuser = isset($_SESSION['application_username']) ? $_SESSION['application_username'] : 'unknown_user';

if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_dialogbox("Warning","The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}

if($mode=='deleteaccessyes')
{
	$rowid = $_POST['rowid'];
	$application_id = $_POST['application_id'];
	$application_name = $_POST['application_name'];
	$deleteQuery = "DELETE FROM tbl_system_modules WHERE id='$rowid'";
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('			
			<script>
				var application_id = "'.$application_id.'";
				var application_name = "'.$application_name.'";				
				ListAccess(application_id,application_name);
				swal("Success", "Access has been deleted","success");
			</script>
		');
	} else {
		echo $db->error;
	}
}
if($mode=='updateappaccess')
{
	$rowid = $_POST['rowid'];
	$access = $_POST['access'];
	$application_id = $_POST['application_id'];
	$application_name = $_POST['application_name'];
	
	$queryDataUpdate = "UPDATE tbl_system_modules SET modules='$access', application_name='$application_name' WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		echo'
			<script>
				var application_id = "'.$application_id.'";
				var application_name = "'.$application_name.'";				
				ListAccess(application_id,application_name);
				swal("Success", "Update Success","success");
			</script>
		';
	} else {
		echo $db->error;
	}
}
if($mode=='addappmodules')
{
	$application_id = $_POST['application_id'];
	$application_name = $_POST['application_name'];
	$module_name = $_POST['module_name'];
	$ordering = 0;
	$datas[] = "('$application_id','$module_name','$ordering','$application_name')";
	$query = "INSERT INTO tbl_system_modules (`application_id`,`modules`,`ordering`,`application_name`)";
	$query .= "VALUES ".implode(', ', $datas);
	if ($db->query($query) === TRUE)
	{
		echo'
			<script>
				swal("Success", "Successfuly Added","success");
				$("#module_name").val();
			</script>
		';
	} else {
		echo $db->error;
	}				
}
if($mode=='changeordering')
{
	$rowid = $_POST['rowid'];
	$ordering = $_POST['ordering'];	
	$queryDataUpdate = "UPDATE tbl_system_modules SET ordering='$ordering' WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
	} else {
		echo $db->error;
	}
}
if($mode=='removermslocation')
{
	$idcode = $_POST['idcode'];
	$user_name = $_POST['username'];
	$deleteQuery = "DELETE FROM wms_user_recipient WHERE idcode='$idcode'";
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('			
			<script>
				var username = "'.$user_name.'";
				swal("Remove Success", username + " has been successfuly removed as WMS Manager","success");
			</script>
		');
	} else {
		echo $db->error;
	}
}
if($mode=='addrmslocation')
{
	$idcode = $_POST['idcode'];
	$recipient = $_POST['location'];
	$user_name = $_POST['username'];
	$query = "SELECT * FROM wms_user_recipient WHERE idcode='$idcode'";
	$results = mysqli_query($db, $query); 	
	if ( $results->num_rows > 0 ) 
	{
		$queryDataUpdate = "UPDATE wms_user_recipient SET recipient ='$recipient' WHERE idcode='$idcode'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			print_r('					
				<script>
					var username = "'.$user_name.'";
					var locastion = "'.$recipient.'";
					swal("Update Success", username + " has been successfuly updated as WMS Manager of " + locastion,"success");
				</script>
			');
		}
	} else {
		$pcountdata[] = "('$idcode','$recipient')";
		$query = "INSERT INTO WMS_user_recipient (`idcode`,`recipient`)";
		$query .= "VALUES ".implode(', ', $pcountdata);
		if ($db->query($query) === TRUE)
		{
			print_r('
					<script>
					var username = "'.$user_name.'";
					var locastion = "'.$recipient.'";
					swal("Add Success", username + " has been successfuly added as WMS Manager of " + locastion,"success");
				</script>
			');
		} else {
			echo $db->error;
		}				
	}
}
if($mode=='removepermissions')
{
	$rowid = $_POST['rowid'];
	$deleteQuery = "DELETE FROM tbl_system_permission WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('
			<script>
				swal("Delete Success","Successfuly Deleted","success");
			</script>
		');
	} else {
	  echo "Error deleting record: " . $db->error;
	}
}
if($mode=='adduser')
{
	$rowid = $_POST['rowid'];
	$firstname = ucwords($_POST['firstname']);
	$lastname = ucwords($_POST['lastname']);
	$idcode = $_POST['idcode'];
	$company = $_POST['company'];
	$cluster = $_POST['cluster'];
	$branch = $_POST['branch'];
	$department = $_POST['department'];
	$uname = $_POST['username'];
	$pass = $_POST['password'];
	$role = $_POST['role'];
	$level = $_POST['level'];
	$uservoid = $_POST['uservoid'];
	$acctname = $firstname.' '.$lastname;
	
	$username = mysqli_real_escape_string($db, $uname);
	$password = $encpass->encryptedPassword($pass,$db);
	
	$pcountdata[] = "('$idcode','$firstname','$lastname','$username','$password','$role','$level','$uservoid','$department','$branch','$cluster','$company','$acctname')";
	$query = "INSERT INTO tbl_system_user (`idcode`,`firstname`,`lastname`,`username`,`password`,`role`,`level`,`void_access`,`department`,`branch`,`cluster`,`company`,`acctname`)";
	$query .= "VALUES ".implode(', ', $pcountdata);
	if ($db->query($query) === TRUE)
	{
		$rowid = $db->insert_id;
		print_r('
			<script>
				var acctname = "'.$firstname." ".$lastname.'";
				var rowid = '.$rowid.';
				var params = "getuser";
				swal("Update Success","account has been successfuly added for " + acctname,"success");
				get_users(rowid,acctname,params);
			</script>
		');
	} else {
		echo "System Message::: ".$db->error;
	}	
}	
if($mode=='updateuser')
{
	$rowid = $_POST['rowid'];
	$firstname = ucwords($_POST['firstname']);
	$lastname = ucwords($_POST['lastname']);
	$idcode = $_POST['idcode'];
	$cluster = $_POST['cluster'];
	$branch = $_POST['branch'];
	$department = $_POST['department'];
	$uname = $_POST['username'];
	$pass = $_POST['password'];
	$role = $_POST['role'];
	$level = $_POST['level'];
	$uservoid = $_POST['uservoid'];
	
	$username = mysqli_real_escape_string($db, $uname);
	$password = $encpass->encryptedPassword($pass,$db);
	
	if($pass == '')
	{
		$update = "firstname='$firstname',lastname='$lastname',idcode='$idcode',cluster='$cluster',branch='$branch',department='$department',username='$username',role='$role',level='$level',void_access='$uservoid'";
	} else {
		$update = "firstname='$firstname',lastname='$lastname',idcode='$idcode',cluster='$cluster',branch='$branch',department='$department',username='$username',password='$password',role='$role',level='$level',void_access='$uservoid'";
	}
	
	$queryDataUpdate = "UPDATE tbl_system_user SET $update WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		print_r('
			<script>
				var acctname = "'.$firstname." ".$lastname.'";
				swal("Update Success",acctname + " account has been successfuly updated","success");
			</script>
		');
	} else {
		echo "UPDATING USER ERROR: ".$conn->error;
	}
}
if($mode=='deleteuser')
{
	$rowid = $_POST['rowid'];
	$idcode = $_POST['idcode'];
	$deleteQuery = "DELETE FROM tbl_system_user WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		$sql = "DELETE FROM tbl_system_permission WHERE idcode='$idcode'";
		if ($db->query($sql) === TRUE) {
			print_r('
				<script>
					swal("Deleted","User has been removed from the system","success");
				</script>
			');
		} 
		else {
		  echo "Error deleting record: " . $conn->error;
		}
				
	} else {
	  echo "Error deleting record: " . $db->error;
	}	
}
if($mode=='searchuser')
{
	$search = $_POST['search'];
	$query = "SELECT * FROM tbl_system_user WHERE idcode LIKE '%$search%' OR firstname LIKE '%$search%' OR lastname LIKE '%$search%' LIMIT 100";
	$results = mysqli_query($db, $query);    
	$option = '<ul class="searchlist">';
	if ( $results->num_rows > 0 ) 
	{
	    while($ROW = mysqli_fetch_array($results))  
		{
			$rowid = $ROW['id'];
			$params = 'getuser';
			$acctname = utf8_encode($ROW['firstname']." ".$ROW['lastname']);
			$option .= '<li onclick="get_users(\''.$rowid.'\',\''.$acctname.'\',\''.$params.'\');">'.$acctname.'</li>';
		}
	} else {
		echo "<li>No Records</li>";
	}
	$option .= '</ul>';
	echo $option;
}
if($mode=='searchadd')
{
	$search = $_POST['search'];
	$query = "SELECT * FROM tbl_employees WHERE idcode LIKE '%$search%' OR firstname LIKE '%$search%' OR lastname LIKE '%$search%' LIMIT 100";
	$results = mysqli_query($db, $query);    
	$option = '<ul class="searchlist">';
	if ( $results->num_rows > 0 ) 
	{
	    while($ROW = mysqli_fetch_array($results))  
		{
			$rowid = $ROW['id'];
			$params = 'getemployee';
			$acctname = utf8_encode($ROW['firstname']." ".$ROW['lastname']);
			$option .= '<li onclick="get_users(\''.$rowid.'\',\''.$acctname.'\',\''.$params.'\');">'.$acctname.'</li>';
		}
	} else {
		echo "<li>No Records</li>";
	}
	$option .= '</ul>';
	echo $option;
}
if($mode=='removepermissions')
{
	$rowid = $_POST['rowid'];
	$idcode = $_POST['idcode'];
	$deleteQuery = "DELETE FROM tbl_system_permission WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		echo '<script>userPermissions("'.$idcode.'")</script>';
	} else {
	  echo "Error deleting record: " . $db->error;
	}
}
if($mode=='savepermissions')
{
	$rowid = $_POST['rowid'];
	$column = $_POST['column'];
	$value = $_POST['permission'];
	$update = $column."='$value'";
	$queryDataUpdate = "UPDATE tbl_system_permission SET $update WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
	} else {
		echo "UPDATING PERMISSION ERROR: ".$conn->error;
	}
}
if($mode=='addpermissions')
{
	$idcode = $_POST['idcode'];
	$query = "SELECT * FROM tbl_system_user WHERE idcode='$idcode'";
	$results = mysqli_query($db, $query);    
    while($ROWS = mysqli_fetch_array($results))  
	{
		$acctname = $ROWS['firstname']." ".$ROWS['lastname'];
		$username = $ROWS['username'];
		$userlevel = $ROWS['level'];
	}
	$application = $_POST['application'];
	$module = $_POST['module'];
	$p_view = $_POST['p_view'];
	$p_read = $_POST['p_read'];
	$p_add = $_POST['p_add'];
	$p_write = $_POST['p_write'];
	$p_edit = $_POST['p_edit'];
	$p_delete = $_POST['p_delete'];
	$p_update = $_POST['p_update'];
	$p_print = $_POST['p_print'];
	$p_approver = $_POST['p_approver'];
	$p_locked = $_POST['p_locked'];

	$data[] = "('$idcode','$acctname','$username','$userlevel','$application','$module','$p_view','$p_read','$p_add','$p_write','$p_edit','$p_delete','$p_update','$p_print','$p_approver','$p_locked')";
	$query = "INSERT INTO tbl_system_permission (idcode,acctname,username,userlevel,applications,modules,p_view,p_read,p_add,p_write,p_edit,p_delete,p_update,p_print,p_approver,p_locked)";
	$query .= "VALUES ".implode(', ', $data);
	if ($db->query($query) === TRUE)
	{
		print_r('
			<script>
				var idcode = '.$idcode.';
				userPermissions(idcode);
			</script>
		');
	} else {
		echo "System Message::: ".$db->error;
	}
}
if($mode=='selectmodule')
{
	$appsid = $_POST['appsid'];
	echo $functions->GetModules($appsid,$db);
}
if($mode=='updateuser')
{
	$rowid = $_POST['rowid'];
	$firstname = ucwords($_POST['firstname']);
	$lastname = ucwords($_POST['lastname']);
	$idcode = $_POST['idcode'];
	$cluster = $_POST['cluster'];
	$branch = $_POST['branch'];
	$department = $_POST['department'];
	$uname = $_POST['username'];
	$pass = $_POST['password'];
	$role = $_POST['role'];
	$level = $_POST['level'];
	$uservoid = $_POST['uservoid'];
	
	$username = mysqli_real_escape_string($db,$uname);
	$password = $encpass->encryptedPassword($pass,$db);
	
	if($pass == '')
	{
		$update = "firstname='$firstname',lastname='$lastname',idcode='$idcode',cluster='$cluster',branch='$branch',department='$department',username='$username',role='$role',level='$level',void_access='$uservoid'";
	} else {
		$update = "firstname='$firstname',lastname='$lastname',idcode='$idcode',cluster='$cluster',branch='$branch',department='$department',username='$username',password='$password',role='$role',level='$level',void_access='$uservoid'";
	}
	
	$queryDataUpdate = "UPDATE tbl_system_user SET $update WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		print_r('
			<script>
				var acctname = "'.$firstname." ".$lastname.'";
				swal("Update Success",acctname + " account has been successfuly updated","success");
			</script>
		');
	} else {
		echo "UPDATING USER ERROR: ".$db->error;
	}
}
if($mode=='generatepassword')
{
	$lenght = $_POST['params'];
	echo $functions->GeneratePassword($lenght);
}
function addAssignment($db, $table, $colName, $rowidRaw, $idcodeRaw, $valueRaw, $appuser) {
	$rowid = mysqli_real_escape_string($db, $rowidRaw);
	$idcode = mysqli_real_escape_string($db, $idcodeRaw);
	$value = mysqli_real_escape_string($db, $valueRaw);
	$dateNow = date("Y-m-d H:i:s");

	$checkQuery = "SELECT 1 FROM $table WHERE idcode = '$idcode' AND $colName = '$value' LIMIT 1";
	$result = mysqli_query($db, $checkQuery);
	if ($result && mysqli_num_rows($result) > 0) {
		return ['status' => 'error', 'message' => ucfirst($colName) . ' assignment already exists!'];
	}

	$insertQuery = "INSERT INTO $table (idcode, $colName, date_created, created_by, rowid) VALUES ('$idcode', '$value', '$dateNow', '$appuser', '$rowid')";
	if (mysqli_query($db, $insertQuery)) {
		$newId = mysqli_insert_id($db);
		return ['status' => 'success', 'message' => ucfirst($colName) . ' added successfully!', 'rowid' => (int)$rowid, 'insert_id' => (int)$newId];
	}
	return ['status' => 'error', 'message' => 'Error: ' . mysqli_error($db)];
}

if ($mode == 'addingClusterAssignments' || $mode == 'addingBranchAssignments') {
	header('Content-Type: application/json; charset=utf-8');
	if ($mode == 'addingClusterAssignments') {
		$resp = addAssignment($db, 'tbl_system_user_add_cluster', 'cluster', $_POST['rowid'], $_POST['idcode'], $_POST['cluster'], $appuser);
	} else {
		$resp = addAssignment($db, 'tbl_system_user_add_branch', 'branch', $_POST['rowid'], $_POST['idcode'], $_POST['branch'], $appuser);
	}
	echo json_encode($resp);
	exit;
}
if ($mode == 'getAssignedClusters') {
	header('Content-Type: application/json; charset=utf-8');
	$rowid = isset($_POST['rowid']) ? mysqli_real_escape_string($db, $_POST['rowid']) : '';
	$idcode = isset($_POST['idcode']) ? mysqli_real_escape_string($db, $_POST['idcode']) : '';

	// Prefer querying by idcode if provided, otherwise try rowid
	if ($idcode !== '') {
		$q = "SELECT cluster FROM tbl_system_user_add_cluster WHERE idcode = '$idcode' ORDER BY date_created ASC";
	} elseif ($rowid !== '') {
		$q = "SELECT cluster FROM tbl_system_user_add_cluster WHERE rowid = '$rowid' ORDER BY date_created ASC";
	} else {
		echo json_encode([]);
		exit;
	}

	$res = mysqli_query($db, $q);
	$clusters = [];
	if ($res) {
		while ($r = mysqli_fetch_assoc($res)) {
			if (isset($r['cluster']) && $r['cluster'] !== '') $clusters[] = $r['cluster'];
		}
	}
	echo json_encode($clusters);
	exit;
}
if ($mode == 'deleteAssignedClusters') {
	header('Content-Type: application/json; charset=utf-8');
	$rowid = isset($_POST['rowid']) ? mysqli_real_escape_string($db, $_POST['rowid']) : '';
	$idcode = isset($_POST['idcode']) ? mysqli_real_escape_string($db, $_POST['idcode']) : '';

	if ($idcode !== '') {
		$deleteQuery = "DELETE FROM tbl_system_user_add_cluster WHERE idcode = '$idcode'";
	} elseif ($rowid !== '') {
		$deleteQuery = "DELETE FROM tbl_system_user_add_cluster WHERE rowid = '$rowid'";
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Missing identifier for deletion']);
		exit;
	}

	if (mysqli_query($db, $deleteQuery)) {
		echo json_encode(['status' => 'success', 'message' => 'All cluster assignments removed']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($db)]);
	}
	exit;
}
if ($mode == 'getAssignedBranches') {
	header('Content-Type: application/json; charset=utf-8');
	$rowid = isset($_POST['rowid']) ? mysqli_real_escape_string($db, $_POST['rowid']) : '';
	$idcode = isset($_POST['idcode']) ? mysqli_real_escape_string($db, $_POST['idcode']) : '';

	if ($idcode !== '') {
		$q = "SELECT branch FROM tbl_system_user_add_branch WHERE idcode = '$idcode' ORDER BY date_created ASC";
	} elseif ($rowid !== '') {
		$q = "SELECT branch FROM tbl_system_user_add_branch WHERE rowid = '$rowid' ORDER BY date_created ASC";
	} else {
		echo json_encode([]);
		exit;
	}

	$res = mysqli_query($db, $q);
	$branches = [];
	if ($res) {
		while ($r = mysqli_fetch_assoc($res)) {
			if (isset($r['branch']) && $r['branch'] !== '') $branches[] = $r['branch'];
		}
	}
	echo json_encode($branches);
	exit;
}
if ($mode == 'deleteAssignedBranches') {
	header('Content-Type: application/json; charset=utf-8');
	$rowid = isset($_POST['rowid']) ? mysqli_real_escape_string($db, $_POST['rowid']) : '';
	$idcode = isset($_POST['idcode']) ? mysqli_real_escape_string($db, $_POST['idcode']) : '';

	if ($idcode !== '') {
		$deleteQuery = "DELETE FROM tbl_system_user_add_branch WHERE idcode = '$idcode'";
	} elseif ($rowid !== '') {
		$deleteQuery = "DELETE FROM tbl_system_user_add_branch WHERE rowid = '$rowid'";
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Missing identifier for deletion']);
		exit;
	}

	if (mysqli_query($db, $deleteQuery)) {
		echo json_encode(['status' => 'success', 'message' => 'All branch assignments removed']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Error: ' . mysqli_error($db)]);
	}
	exit;
}

