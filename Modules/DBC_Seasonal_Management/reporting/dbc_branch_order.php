<?php
session_start();
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$_SESSION['DBC_SEASONAL_TABLE'] = $_POST['table'];
echo "asaaaa ". $_POST['table'];
?>
