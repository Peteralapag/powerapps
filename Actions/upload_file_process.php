<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;

$file = basename($_FILES["file"]["name"]; 
echo $file;
$fullpath= $_POST['fullpath'];

$upload_folder = str_replace("../../../../",$_SERVER['DOCUMENT_ROOT']."/" ,$fullpath);

$file_date = date("ymHiA");
$file = basename($file, '.pdf');
$file_name = $file."_".$file_date.".pdf";
$file_name = str_replace("/\s+/", "_", $file_name);
$targetFilePath = $upload_folder ."/". $file_name; 
print_r('
    <script>
        alert();
    </script>
');
exit();
if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){}else{}
?>