<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;

	$md_application = $_SESSION['md_application'];
	$root = $_SERVER['DOCUMENT_ROOT'];
	$app = $_POST['application'];
	$path = $_POST['path'];
	$module = $_POST['module'];
	$author = $_POST['author'];
	$file = $_POST['filename'];
	$username = $_POST['username'];
	$file_date = date("Y-m-d H:i:s");
	$date_deleted = date("ymdhisA");

	$ext = explode('.', $file);
    $extension = end($ext);
    $file = pathinfo($file, PATHINFO_FILENAME);
		
	$main_file = $file.".".$extension;
	$filename = $file."_".$date_deleted.".".$extension;
	
	if(file_exists($root."/".$path."/".$main_file))
	{
		if(@copy($root."/".$path.$main_file, $root.'/Recycle_bin/'.$filename) === true )
		{
			$queryInsert = "INSERT INTO tbl_document_bin (`application`,`path`,`modules`,`author`,`real_file_name`,`file_name`,`username`,`date_deleted`)";
			$queryInsert .= "VALUES('$app','$path','$module','$author','$main_file','$filename','$username','$file_date')";
			if ($db->query($queryInsert) === TRUE)
			{
				/* CHECK AND DELETE DOCUMENT PROPERTIES TABLE */
				$checkDP = "SELECT * FROM tbl_document_properties WHERE username='$username' AND file_name='$main_file'";
				$DPresult = mysqli_query($db, $checkDP);    
				if ( $DPresult->num_rows > 0 ) 
				{
					$deleteDP = "DELETE FROM tbl_document_properties WHERE file_name='$main_file' AND username='$username'";	
					if ($db->query($deleteDP) === TRUE) { } else { }
				}
				echo $functions->ExecuteAccess($md_application,$module,$main_file,$file_date,$db);
				unlink($root."/".$path."/".$main_file);
			} else {
			    print_r('
			    	<script>
			    		swal("System Error", '.$db-error.', "warning");
			    	</script>
			    ');
			}
			
		} else {
			print_r('
		    	<script>
		    		swal("File Not found", "The file " + '.$main_file.' + "does not exists.", "warning");
		    	</script>
		    ');
			exit();			
		}
		
	} else {
		print_r('
	    	<script>
	    		swal("File Not found", "The file " + '.$main_file.' + "does not exists.", "warning");
	    	</script>
	    ');
		exit();
	}
	

