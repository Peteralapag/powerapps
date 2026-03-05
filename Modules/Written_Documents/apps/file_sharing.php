<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


$username = $_SESSION['wd_username'];
$company = $_SESSION['wd_company'];
$user_level = $_SESSION['wd_userlevel'];


// echo delete_shared($archiveuser,$db);

$file_id = $_POST['file_id'];

$queryShare = "SELECT * FROM tbl_archived_memo WHERE id='$file_id'";
$shareResult = mysqli_query($db, $queryShare);    
if ( $shareResult->num_rows > 0 ) 
{
	$i=0;
	while($ROWS = mysqli_fetch_array($shareResult))  
	{
		$file_name = $ROWS['file_name'];
		$file_description = $ROWS['file_description'];
		$company = $ROWS['company'];
		$organization = $ROWS['organization'];
	}
} else {
	echo '<div style="padding:5px;color:#333;border:1px solid blue;" class="bg-info">File not found</div>';
}
?>
<style>
</style>
<div style="width:400px">
<div class="input-wrapper">
	<label for="filename" class="form-label">File Name</label>
	<input disabled class="form-control" id="filename" type="text" value="<?php echo $file_name; ?>">
</div>
<div class="input-wrapper">
	<label for="description" class="form-label">File Description</label>
	<input class="form-control" id="description" type="text" value="<?php echo $file_description; ?>">
</div>
<div class="input-wrapper">
	<label for="companytoshare" class="form-label">Company to Share</label>
	<select class="form-control " id="companytoshare">
		<?php get_company($company,$db); ?>
	</select>
</div>
<div class="input-wrapper" style="border:1px solid #f1f1f1">
	<div style="padding:5px;text-align:center" class="form-label">Organization to Share</div>
	<table style="width: 100%">
		<tr>
			<td style="width: 48.5%">
				<div class="sharingiscaring scroller"><?php echo get_department($organization,$file_id,$db); ?></div>
			</td>
			<td style="width:4%;text-align:center;padding:5px"> <i class="fa-solid fa-play"></i> </td>
			<td style="width: 48.5%">
				<div id="shareto" class="sharingiscaring scroller"></div>
			</td>
		</tr>
	</table>
</div>
<div class="input-wrapper" style="text-align:right">
	<button id="submitshare" type="button" class="btn btn-primary btn-sm" onclick="submit_share()">Proceed Sharing</button>
	<button class="btn btn-danger btn-sm" onclick="closeModal('formmodal')">Close</button>
</div>
<div class="ress bg-warning" style="display:none;"></div>
</div>
<script>
function passThis(organization,fileid)
{
	var mode = 'sharetoorganization';
	$action = '../Modules/Written_documents/actions/add_to_share.php';
	$.post($action, { mode: mode, fileid: fileid, organization: organization },
	function(data) {
		$('.ress').html(data);		
		shareto(fileid)
	});
}
function shareto()
{
	$('#shareto').load('../Modules/Written_documents/apps/share_to.php');
}
function submit_share(filename)
{
	$('#submitshare').html('<i class="fa fa-spinner fa-spin"></i> Sharing in Progress...');
	var fileid = '<?php echo $file_id ; ?>';
	var filename = '<?php echo $file_name ; ?>';
	var description = $('#description').val();
	var company = $('#companytoshare').val(); 
	rms_reloaderOn()
	$action = '../Modules/Written_documents/actions/share_process.php';
	setTimeout(function()
	{
		$.post($action, { fileid: fileid, filename: filename,  description: description, company: company },
		function(data) {
			$('.ress').html(data);
			// $('.ress').fadeIn();
			rms_reloaderOff();
			closeModal('formmodal');
		});		
	},1000);
}
function thisIsMine()
{
	const myTimeout = setTimeout(closeResults, 5000);
}
function closeResults(fileid) {
	$('.ress').fadeOut();
	$('#shareto').empty();
	$('#submitshare').html('Proceed Sharing');  
	//shareFile('<?php echo $file_id; ?>');
}
</script>
<?php
function delete_shared($archiveuser,$db)
{
	$sqldelItem = "DELETE FROM tbl_shared_temp WHERE userid='$archiveuser'";
	if ($db->query($sqldelItem) === TRUE) {}else{echo $db->error;}
}
function get_company($company,$db)
{
	$query = "SELECT * FROM tbl_company ORDER BY company ASC";
	$res = mysqli_query($db, $query);    
	if ( $res->num_rows > 0 ) 
	{
		echo '<option value="">--- SELECT ---</option>';
		while($row = mysqli_fetch_array($res))  
		{
			$_company = $row['company'];
			$selected = '';
			if($_company == $company)
			{
				$selected = 'selected';
			}
			echo '<option '.$selected.' value="'.$_company.'">'.$_company.'</option>';
		}
	} else {
		echo '<option value="">No records</option>';
	}
}
function get_department($organization,$file_id,$db)
{
	echo '<ul class="select-department">';
	$query = "SELECT * FROM tbl_wd_organization WHERE modules!='$organization' ORDER BY modules ASC";
	$res = mysqli_query($db, $query);    
	if ( $res->num_rows > 0 ) 
	{
		while($row = mysqli_fetch_array($res))  
		{
			$organization = $row['modules'];
			echo '<li onClick=passThis("'.$organization.'","'.$file_id.'")>'.$organization.'</li>';
		}
	} else {
		echo '<option value="">No records</option>';
	}
	echo '</ul>';
}
?>