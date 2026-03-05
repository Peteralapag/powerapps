<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Warehouse_Management/class/Class.functions.php";
$function = new WMSFunctions;

$idcode = $_POST['idcode'];
$queryWUL = "SELECT * FROM wms_user_recipient WHERE idcode='$idcode'";
$resultWUL = mysqli_query($db, $queryWUL); 	
if ( $resultWUL->num_rows > 0 ) 
{
	$queryUser = "SELECT su.*, wul.recipient
		FROM tbl_system_user su
		INNER JOIN wms_user_recipient wul ON su.idcode = wul.idcode
		WHERE su.idcode = '$idcode'";
	$resultsUser = $db->query($queryUser);
	while ($UROWS = mysqli_fetch_array($resultsUser)) {
	    $u_sername = $UROWS['firstname'] . " " . $UROWS['lastname'];
	    $recipient = $UROWS['recipient'];
	}
} else {
	$UserQuery = "SELECT * FROM tbl_system_user WHERE idcode='$idcode'";
	$userResults = mysqli_query($db, $UserQuery);    
	if ( $userResults->num_rows > 0 ) 
	{
	    while($UROWS = mysqli_fetch_array($userResults))  
		{
			$u_sername = $UROWS['firstname'] . " " . $UROWS['lastname'];
		    $recipient = '';
		}
	}
}
?>
<style>
.add-manager-wrapper {
	width:300px;
	padding-bottom:10px;
}
</style>
<div class="add-manager-wrapper">
	<table style="width: 100%">
		<tr>
			<th>Manager</th>
			<td><?php echo $u_sername; ?></td>
		</tr>
		<tr>
			<td colspan="2" style="height:10px"></td>
		</tr>
		<tr>
			<th colspan="2" style="height:10px">HANDLED RECIPIENT</th>
		</tr>
		<tr>
			<td colspan="2">
				<select id="location" class="form-control">
					<?php echo $function->GetRecipient($recipient,$db); ?>
				</select>
			</td>
		</tr>
	</table>
	<div style="margin-top:10px;text-align:right">
		<button class="btn btn-success" onclick="updateManagers('<?php echo $idcode; ?>')">Update Location</button>
		<button class="btn btn-danger" onclick="removeManager('<?php echo $idcode; ?>')">Remove</button>
	</div>
</div>
<div class="resultas"></div>
<script>
function removeManager(idcode)
{
	var mode = 'removermslocation';
	var username = '<?php echo $u_sername; ?>';
	$.post("./Modules/User_Management/actions/actions.php",
	{ 
		mode: mode,
		idcode: idcode,
		username: username
	},
	function(data) {		
		$('.resultas').html(data);
		$('#formmodal').hide();
	});
}
function updateManagers(idcode)
{
	var mode = 'addrmslocation';
	var username = '<?php echo $u_sername; ?>';
	var location = $('#location').val();
	if(location === '')
	{
		swal("Location", "Please select WMS Manager Location", "warning");
		return false;
	}
	$.post("./Modules/User_Management/actions/actions.php",
	{ 
		mode: mode,
		idcode: idcode,
		username: username,
		location: location
	},
	function(data) {		
		$('.resultas').html(data);
		rms_reloaderOff();
	});
}
</script>