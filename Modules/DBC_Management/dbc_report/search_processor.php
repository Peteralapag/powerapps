<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";

$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SUMMARY_SELECTEDCLUSTER'] = $_POST['selectedcluster'];
$_SESSION['DBC_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SUMMARY_DATETO'] = $_POST['dateTo'];

$selectedFilter = json_decode($_POST['selectedFilter'], true);
$selectedbranch = $_POST['selectedbranch'];
$selectedcluster = $_POST['selectedcluster'];
$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$response = [];

if (in_array("branch", $selectedFilter)) {
    $response['branch_page'] = "bor_branch_data.php";
}
if (in_array("cluster", $selectedFilter)) {
    $response['cluster_page'] = "bor_cluster_data.php";
}
if (in_array("allbranch", $selectedFilter)) {
    $response['allbranch_page'] = "bor_allbranch_data.php";
}

echo json_encode($response);
?>
