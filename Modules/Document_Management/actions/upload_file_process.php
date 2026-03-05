<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$file = basename($_FILES["file"]["name"]); 
$fullpath = $_POST['fullpath'];
$upload_folder = str_replace("../../../../","../../../" ,$fullpath);
$file_date = date("ymdHiA");
$upload_date = date("Y-m-d H:i:s");
if($_POST['subfolder'] == 'Archived')
{
    $file = basename($file, '.pdf');
    $file_name = $file."_".$file_date.".pdf";
} 
else if($_POST['subfolder'] == 'Raw')
{

    $ext = explode('.', $file);
    $extension = end($ext);
    $file_name = $file."_".$file_date.".".$extension;
}
$file_name = str_replace(" ","_", $file_name);
$targetFilePath = $upload_folder ."/". $file_name; 

$username = $_SESSION['md_username'];
$author = $_SESSION['md_appnameuser'];
$upload_type = $_POST['subfolder'];

$queryInsert = "INSERT INTO tbl_document_properties (`upload_type`,`file_name`,`username`,`author`,`upload_date`)";
$queryInsert .= "VALUES('$upload_type','$file_name','$username','$author','$upload_date')";
if ($db->query($queryInsert) === TRUE)
{
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {} else {}
} else {
    echo $db->error;
}

?>