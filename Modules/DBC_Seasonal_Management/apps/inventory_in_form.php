<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";

$inventory = new DBCInventory;
$functions = new DBCFunctions;

$transdate = $_POST['transdate'];
$itemcode = $_POST['itemcode'];
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr style="vertical-align:middle">
			<td style="text-align:center">ACKNOWLEDGE BY</td>
			<td style="text-align:center">UNITS (UOM)</td>
			<td style="text-align:center">ACTUAL PRODUCED</td>	
		</tr>
		<tr>
			<td style="text-align:center"><?php echo $inventory->Getdatafromtableproduction('confirmed_by',$transdate,$itemcode,$db)?></td>
			<td style="text-align:center"><?php echo $functions->GetItemListColumn('uom',$itemcode,$db)?></td>
			<td style="text-align:center"><?php echo $inventory->Getdatafromtableproduction('actual_received',$transdate,$itemcode,$db)?></td>
			
		</tr>
		
	</table>
</div>
<div id="results"></div>
<script>

