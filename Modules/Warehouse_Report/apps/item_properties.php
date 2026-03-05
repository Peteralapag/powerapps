<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
$item_code = $_POST['itemcode'];
$item_name = strtoupper($_POST['itemname']);
$class = $_POST['classes']; 
$uom = $_POST['uom'];
?>
<style>
.property-data {display: flex;margin-bottom: 10px;width: calc(100vw - 200px);flex-direction: column;}
.bar-wrapper {background:#fff;border-bottom:3px solid #aeaeae;margin-bottom: 10px;padding-bottom:10px;width:100%;}
.bar-wrapper button {color:#fff;}.bar-wrapper input[type=text] {width:300px;}
.tableFixHeadData { overflow: auto; height: calc(100vh - 220px); width:100%; }
.tableFixHeadData thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff;font-size:14px }
.tableFixHeadData table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHeadData td { font-size:14px; white-space:nowrap }
</style>
<div class="property-data">
	<div class="bar-wrapper">		
		<table style="width: 100%">
			<tr>
				<td><?php echo $item_code." - <strong>".$item_name."</strong>"; ?></td>
				<td>&nbsp;</td>
				<td style="text-align:right">
					<button class="btn btn-primary btn-sm" onclick="AddNewSupplier('new','<?php echo $item_code; ?>','<?php echo $item_name; ?>','<?php echo $class; ?>','<?php echo $uom; ?>')">Add Supplier</button>
				</td>
			</tr>
		</table>		
	</div>	
	<div class="tableFixHeadData" id="properties_data">
		
	</div>
</div>
<script>
function AddNewSupplier(mode,itemcode,itemname,classes,uom)
{
	$('#formodalsmtitle').html('ADD NEW SUPPLIER');
	$.post("modules/" + sessionStorage.module + "/apps/add_supplier.php", { mode: mode, itemcode: itemcode, itemname: itemname, classes: classes, uom: uom },
	function(data) {
		$('#formodalsm_page').html(data);
		$('#formodalsm').show();
	});
}
$(function()
{
	loadSupplier();
});
function loadSupplier()
{
	$.post('modules/' + sessionStorage.module + '/includes/item_properties_data.php', {  },
	function(data) {
		$('#properties_data').html(data);
	});

}
</script>