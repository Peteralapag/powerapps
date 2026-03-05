<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;

$file = basename($_FILES["file"]["name"]); 
//$file_name = $_POST['file_name'];
$fullpath= $_POST['fullpath'];

$upload_folder = str_replace("../../../../","../../../" ,$fullpath);
//$upload_folder = $fullpath;
$file_date = date("ymHiA");
$file = basename($file, '.pdf');
$file_name = $file."_".$file_date.".pdf";
$file_name = str_replace(" ","_", $file_name);
$targetFilePath = $upload_folder ."/". $file_name; 

if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {} else {}







?>