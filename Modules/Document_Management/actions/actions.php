<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;

if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
$date_now = date("Y-m-d H:i:s");
if($mode == 'checkrequesttype')
{
	$username = $_POST['username']; 
	$filename = $_POST['filename']; 	
	if($_POST['rtype'] == 3)
	{
		$type = "Delete";
	}
	if($_POST['rtype'] == 2)
	{
		$type = "Rename";
	}
	if($_POST['rtype'] != 1) 
	{	
		$checkPolicy = "SELECT * FROM tbl_document_properties WHERE username='$username' AND file_name='$filename'";
		$pRes = mysqli_query($db, $checkPolicy);    
		if ( $pRes->num_rows > 0 ) 
		{
			
		} else {
			print_r('
				<script>
					swal("Request Denied", "You cannot '.$type.' the file that is not yours", "warning");
					$("#rtype").prop("selectedIndex", 0);

				</script>				
			');
		}
	}
}
if($mode == 'deleterequest')
{
	$rowid = $_POST['rowid'];
	$deleteQuery = "DELETE FROM tbl_app_request WHERE id='$rowid'";	
	if ($db->query($deleteQuery) === TRUE)
	{
		print_r('
    		<script>
    			closeModal("formmodal");
    			swal("System Message", "Request has been successfuly deleted", "success");
    		</script>
		');
	} else {
		print_r('
			<script>
				swal("System Message", "'.$db-error.'","warning");
			</script>
		');
	}
}
if($mode == 'submitrequest')
{
	$applications = $_POST['application'];
	$modules = $_POST['module'];
	$file_name = $_POST['file_name'];
	$account_name = $_POST['account_name'];
	$requested_by = $_POST['requested_by'];
	$type = $_POST['type'];
	$request_reason = $_POST['request_reason'];
	
	if($type == 1) { $request_type = "Print / Download:"; }
	if($type == 2) { $request_type = "Rename File:"; }
	if($type == 3) { $request_type = "Delete File:"; }
	
	$reason = $request_reason." ".$request_type;
	
	$QUERY = "SELECT * FROM tbl_app_request WHERE requested_by='$requested_by' AND applications='$applications' AND modules='$modules' AND file_name='$file_name' AND approved=0";
	$RESULTS = mysqli_query($db, $QUERY);    
    if ( $RESULTS->num_rows > 0 ) 
    {
    	print_r('
    		<script>
    			swal("Pending Request", "We see that you still have a pending Access Request", "warning");
    		</script>
    	');
    } else {
    	$queryInsert = "INSERT INTO tbl_app_request (`applications`,`modules`,`file_name`,`account_name`,`requested_by`,`request_type`,`requested_date`,`request_reason`)";
		$queryInsert .= "VALUES('$applications','$modules','$file_name','$account_name','$requested_by','$type','$date_now','$reason')";
		if ($db->query($queryInsert) === TRUE)
		{
			print_r('
	    		<script>
	    			closeModal("formmodal");
	    			swal("System Message", "Request has been successfuly created", "success");
	    		</script>
    		');
		} else {
			print_r('
	    		<script>
		    		closeModal("formmodal");
	    			swal("System Message", "'.$db-error.'","warning");
	    		</script>
    		');
		}
    }
}
