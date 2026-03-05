<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$md_application = $_SESSION['md_application'];
$root = $_SERVER['DOCUMENT_ROOT'];
$fullpath = str_replace("../","", $_POST['fullpath']);
$module = $_POST['module'];
$oldfilename = $_POST['oldfilename'];
$filename = strtoupper($_POST['filename']);
$file_date = date("ymdHiA");
$date = date("Y-m-d H:i:s");

$ext = explode('.', $oldfilename);
$extension = end($ext);
$extension = strtolower($extension);

$new_filename = $filename."_".$file_date.".".$extension;
$new_filename = str_replace(" ", "_", $new_filename);

if(file_exists($root."/".$fullpath.$oldfilename))
{
	if(@rename($root."/".$fullpath."/".$oldfilename, $root."/".$fullpath.$new_filename) === true)
	{	
		$queryDataUpdate = "UPDATE tbl_document_properties SET file_name='$new_filename', date_updated='$date' WHERE file_name='$oldfilename'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			echo $functions->ExecuteAccess($md_application,$module,$oldfilename,$date,$db);
			print_r('
				<script>
					swal("Success","The file '.$oldfilename.' has been successfuly renamed to '.$new_filename.'", "success");
					closeModal("formmodal");
				</script>
			');			
		} else {
			print_r('				
				<script>
					swal("Renaming Error","DB Error:" + '.$db-error.', "warning");
				</script>
			');
		}
	} else {
		print_r('
			<script>
				swal("Renaming Error","Maybe you have invalid character in text or the files does not exists", "warning");
			</script>
		');
	}
} else {
	print_r('
		<script>
			swal("Renaming Error","File does not exists", "warning");
		</script>
	');
}