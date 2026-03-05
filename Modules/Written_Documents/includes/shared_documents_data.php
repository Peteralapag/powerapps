<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$functions = new PageFunctions;
$organization = strtolower($_POST['organization']);
$subfolder= strtolower($_POST['subfolder']);
$file_directory = $_SERVER["DOCUMENT_ROOT"]."/Data/Written_Documents/";
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];
if($user_level >= 10)
{
	if(isset($_POST['search']))
	{
		$search = $_POST['search'];
		$q = "WHERE file_name LIKE '%$search%' AND company='$company' AND shared_to='$organization' AND subfolder='$subfolder'";
	} else { 
		$q = "WHERE company='$company' AND shared_to='$organization' AND subfolder='$subfolder'"; 
	}
} else {
	if(isset($_POST['search']))
	{
		$search = $_POST['search'];
		$q = "WHERE file_name LIKE '%$search%' AND company='$company' AND shared_to='$organization' AND subfolder='$subfolder'";
	} else { 
		$q = "WHERE company='$company' AND shared_to='$organization' AND subfolder='$subfolder'"; 
	}
}
?>
<div class="tableFixHead ">
	<table id="filedata" style="width:99.9%" class="table table-hover table-striped table-bordered">
		<thead>
			<tr>
				<th style="width:30px !important">#</th>
				<th>File Name</th>
				<th>Description</th>
				<th>Date Shared</th>
				<th>Shared From</th>
				<th>Shared By</th>
			</tr>
		</thead>		
		<tbody>
	<?php
		$SQL = "SELECT * FROM tbl_shared_memo $q";
		$RESULTS = mysqli_query($db, $SQL);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			$i=0;
			while($ROWS = mysqli_fetch_array($RESULTS))  
			{
				$i++;
				$shared_id = $ROWS['shared_id'];
				$file_name = $ROWS['file_name'];
				$archiveuser = $ROWS['username'];
				$_organization = strtolower($ROWS['organization']);
				$_subfolder = strtolower($ROWS['subfolder']);
				$shared_from = $ROWS['shared_from'];
				$file_description = $ROWS['file_description'];
				$uploaded_by = $ROWS['uploaded_by'];
				$date_uploaded = date("M d, Y @ h:i A", strtotime($ROWS['date_uploaded']));
				$file_views = $ROWS['file_views'];
				$file_downloaded = $ROWS['file_downloaded'];	
				$file = basename($file_name, '.pdf');
				if (strlen($file_name) >= 40) {
					$filename = substr($file_name, 0, 25). " ... " . substr($file_name, -5);
				} else {
					$filename = $file_name;
				}
				if (strlen($file_description) >= 40) {
					$description = substr($file_description, 0, 25). " ... " . substr($file_description, -5);
				} else {
					$description = $file_description;
				}
				if(file_exists($file_directory."/".$_organization."/".$_subfolder."/".$file_name))
				{
					$file_icon = '<i class="fa-solid fa-file-pdf color-red"></i>&nbsp;&nbsp;'.$filename;
				} else {
					$file_icon = '<i class="fa-solid fa-circle-exclamation color-orange"></i>&nbsp;&nbsp;'.$filename;
				}
?>
			<tr ondblclick="openThisFile('<?php echo $shared_id; ?>','<?php echo $file_name; ?>','<?php echo $_organization; ?>')">
				<td style="text-align:center;font-weight:600"><?php echo $i; ?></td>
				<td style="position:relative" id="tooltip<?php echo $i; ?>">
					<?php echo $file_icon; ?>
					<div class="tooltips" id="tooltips<?php echo $i; ?>"><?php echo $file_name; ?></div>
				</td>
				<td><?php echo $description; ?></td> 
				<td><i class="fa-solid fa-calendar-days"></i> <?php echo $date_uploaded; ?></td>
				<td><?php echo ucwords($shared_from); ?></td>
				<td><i class="fa-solid fa-user color-dodger"></i>&nbsp;&nbsp;<?php echo $uploaded_by; ?></td>			
			</tr>
<?php } } else { ?>
			<tr>
				<td colspan="7" style="text-align:center; color:orange"><i class="fa fa-bell"></i> No Records</td>
			</tr>
<?php } ?>
		</tbody>
	</table>
</div>
<script>
$(function()
{
	$('.file-data').height($('#org_content').height());
	$('#wdheader').resize(function()
	{
		$('.file-data').height($('#org_content').height());
	});
	$('button').click(function(e)
	{
		var btn_id = $(this).attr('data-id');
		$('button').removeClass('btn-info');
		$(this).addClass('btn-info');
		$("#"+btn_id).addClass('btn-info');		
	});
	
//	var table = $('#filedatad').DataTable( {
//   });
});
</script>
