<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;

$mrs_no = "0001";
$dr_no = "000001";
$queryDataUpdate = "UPDATE dbc_seasonal_form_numbering SET mrs_number=?, dr_number=? WHERE id=1";
$stmt = $db->prepare($queryDataUpdate);
$stmt->bind_param("ss", $mrs_no, $dr_no);	   
if ($stmt->execute()) { } else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_inventory_records";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) { } else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_branch_order";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) { } else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_branch_order_remarks";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) {} else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_order_request";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) {} else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_inventory_stock";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) {} else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_receiving";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) {} else { echo $stmt->error; }
$queryDataDelete = "DELETE FROM dbc_seasonal_receiving_details";
$stmt = $db->prepare($queryDataDelete);
if ($stmt->execute()) {} else { echo $stmt->error; }
$stmt->close();
