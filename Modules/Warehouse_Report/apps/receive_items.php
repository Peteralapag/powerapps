<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT'].'/Modules/Warehouse_report/class/wh_functions.class.php';
$wh_functions = new WHFunctions;
$received_by = $_SESSION['application_appnameuser'];
$date = date("Y-m-d");
?>
<div style="width:500px;margin-bottom:10px; max-height:450px; overflow:auto;">

	<table style="width: 100%" class="table">
		<tr>
			<th>ITEM CODE</th>
			<td></td>
			<td><input id="itemcode" type="text" class="form-control form-control-sm" disabled></td>
		</tr>
		<tr>
			<th>ITEM CLASS</th>
			<td></td>
			<td><input id="itemclass" type="text" class="form-control form-control-sm" disabled></td>
		</tr>
		<tr>
			<th>ITEM DESCRIPTION</th>
			<td></td>
			<td>
				<input list="itemlist" id="itemname" class="form-control form-control-sm" placeholder="Select Item" autocomplete="no" onchange="getDefaultValue(this.value)">
				<datalist id="itemlist">
					<?php echo $wh_functions->GetItemsDD($db); ?>
				</datalist>
			</td>
		</tr>
		<tr>
			<th>RECEIVED AMOUNT</th>
			<td></td>
			<td><input id="recvamount" type="number" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>UOM</th>
			<td></td>
			<td>
				<select id="uom" class="form-control form-control-sm">
					<?php echo $wh_functions->GetUOM('',$db); ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>CONVERSION</th>
			<td></td>
			<td><input id="conversion" type="text" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>P.O No.</th>
			<td></td>
			<td><input id="ponumber" type="text" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>SUPPLIER</th>
			<td></td>
			<td><input id="supplier" type="text" class="form-control form-control-sm" value="Unknown"></td>
		</tr>
		<tr>
			<th>INVOICE No.</th>
			<td></td>
			<td><input id="invoiceno" type="text" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>MRCS No.</th>
			<td></td>
			<td><input id="mrcsno" type="text" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>UNIT PRICE</th>
			<td></td>
			<td><input id="unitprice" type="number" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>EXPIRATION DATE</th>
			<td></td>
			<td><input id="expdate" type="date" class="form-control form-control-sm"></td>
		</tr>
		<tr>
			<th>DELIVERY DATE</th>
			<td></td>
			<td><input id="deldate" type="date" class="form-control form-control-sm" value="<?php echo $date; ?>"></td>
		</tr>
		<tr>
			<th>RECEIVED DATE</th>
			<td></td>
			<td><input id="recvdate" type="date" class="form-control form-control-sm" value="<?php echo $date; ?>"></td>
		</tr>
		<tr>
			<th>RECEIVED BY</th>
			<td></td>
			<td><input id="recvby" type="text" class="form-control form-control-sm" value="<?php echo $received_by; ?>" disabled></td>
		</tr>
	</table>

</div>
<div style="margin-top:20px;text-align:right">
	<button class="btn btn-success btncontrol" onclick="addReceiving()">Save</button>
	<button class="btn btn-danger btncontrol" onclick="closeModal('formodalsm')">Cancel</button>
</div>
<div class="results"></div>
<script>
function getDefaultValue(setval)
{
	var mode = 'setval';
	$.post("modules/" + sessionStorage.module + "/actions/actions.php", { mode: mode, itemname: setval },
	function(data) {
		$('.results').html(data);
	});
}
function addReceiving()
{
	var itemcode = $('#itemcode').val();
	var itemclass = $('#itemclass').val();
	var itemname = $('#itemname').val();
	var recvamount = $('#recvamount').val();
	var uom = $('#uom').val();
	var conversion = $('#conversion').val();
	var ponumber = $('#ponumber').val();
	var supplier = $('#supplier').val();
	var invoiceno = $('#invoiceno').val();
	var mrcsno = $('#mrcsno').val();
	var unitprice = $('#unitprice').val();
	var expdate = $('#expdate').val();
	var deldate = $('#deldate').val();
	var recvdate = $('#recvdate').val();
	var recvby = $('#recvby').val();

	if(itemname === '') { 
		app_alert("Item Description","Please select Item Description","warning","Ok","itemname","focus");
		return false;
	}
	if(recvamount <= 0) { 
		app_alert("Received Amount","Please received item amount","warning","Ok","recvamount","focus");
		return false;
	}
	if(uom === '') { 
		app_alert("Unit of Measures","Please select Unit of Measures","warning","Ok","uom","focus");
		return false;
	}
	if(conversion === '') { 
		app_alert("Conversion","Please enter Conversion","warning","Ok","conversion","focus");
		return false;
	}
	if(ponumber === '') { 
		app_alert("P.O. Number","Please enter P.O. Number","warning","Ok","ponumber","focus");
		return false;
	}
	if(supplier === '') { 
		app_alert("Supplier","Please enter Supplier name","warning","Ok","supplier","focus");
		return false;
	}
	if(invoiceno === '') { 
		app_alert("Invoice Number","Please Enter Invoice Number","warning","Ok","invoiceno","focus");
		return false;
	}
	if(mrcsno === '') { 
		app_alert("MRCS Number","Please Enter MRCS Number","warning","Ok","mrcsno","focus");
		return false;
	}
	if(unitprice === '') { 
		app_alert("Unit Price","Please enter Unit Price","warning","Ok","unitprice","focus");
		return false;
	}
	if(expdate === '') { 
		app_alert("Expiration Date","Please select Expiration Date","warning","Ok","expdate","focus");
		return false;
	}
	if(deldate === '') { 
		app_alert("Delivery Date","Please select Delivery Date","warning","Ok","deldate","focus");
		return false;
	}
	if(recvdate === '') { 
		app_alert("Received Date","Please select date of Item Received","warning","Ok","recvdate","focus");
		return false;
	}
	var mode = 'savereceiving';
	rms_reloaderOn("Saving! Please Wait...");
	setTimeout(function()
	{
		$.post('modules/' + sessionStorage.module + '/actions/actions.php', {
			mode: mode,
			itemcode: itemcode,
			itemclass: itemclass,
			itemname: itemname,
			recvamount: recvamount,
			uom: uom,
			conversion: conversion,
			ponumber: ponumber,
			supplier: supplier,
			invoiceno: invoiceno,
			mrcsno: mrcsno,
			unitprice: unitprice,
			expdate: expdate,
			deldate: deldate,
			recvdate: recvdate,
			recvby: recvby
		},
		function(data) {
			$('.results').html(data);
			rms_reloaderOff();
		});
	},1000);
}
</script>
