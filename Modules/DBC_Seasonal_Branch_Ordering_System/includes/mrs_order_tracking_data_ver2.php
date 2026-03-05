<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
define("MODULE_NAME", "DBC_Seasonal_Branch_Ordering_System");
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/".MODULE_NAME."/class/Class.functions.php");
$function = new FDSFunctions;

$requestid = $_POST['requestid'];
$control_no = $_POST['controlno'];

$userlevel = $_SESSION['dbc_seasonal_branch_userlevel'];
$function = new FDSFunctions;

$branch = $function->GetOrderStatusSecondTable($requestid,'branch',$db);
$mrs_no = $control_no;
$recipient = $function->GetOrderStatusSecondTable($requestid,'recipient',$db);
$trans_date = $function->GetOrderStatusSecondTable($requestid,'trans_date',$db);


?>
<style>
.mrs-wrappers {width: 8.5in;border:1px solid #aeaeae;padding:0.2in;}
.table-thth th{background: #838383;color: #fff;font-weight:normal !important;padding:5px;text-align:center;}
.table-tdth {}
.table td {padding:4px;font-size:12px;font-weight: normal;border:0 !important;}
.table-hover:hover {background: #f7f8eb;}
.tabletas th {border: 1px solid #232323;font-size:11px;font-weight: 600;padding:2px;text-align:center;}
.tabletas td {border: 1px solid #232323;font-size:11px;padding:2px;font-weight: normal;}
.form-footer td {text-align: center;font-size: 12px;}
.btn-thin {padding: 0 !important}
.approval-text {font-weight: 600;font-size:18px;}
.names {font-weight: 600;}
.dates {font-style:italic;color: #aeaeae;}
.button {padding: 2px 7px 2px 7px !important;}
.order-circle {display: flex;width:50px;height:50px;border:5px solid green;border-radius:50%;justify-content: center;align-items: center;font-size:24px}
.order-circle-gray {display: flex;width:50px;height:50px;border:5px solid #aeaeae;border-radius:50%;justify-content: center;align-items: center;font-size:24px}
.status-text {text-align:center;font-size: 11px}
.status-date {text-align:center;text-align:center;font-style:italic;color:#AEAEAE;}
.icontext-color { color: #aeaeae; }
.bar-color {border: 2px solid green}
.bar-color-gray {border: 2px solid #aeaeae}
</style>


<?php
if($function->GetOrderStatusSecondTable($requestid,"order_received",$db) == 1)
{
	$class_or = "order-circle";
	$icon_color_or = "";
	$bar_color_or = "bar-color";
} else {
	$class_or = "order-circle-gray";
	$icon_color_or = "icontext-color";
	$bar_color_or = "bar-color-gray";
}
if($function->GetOrderStatusSecondTable($requestid,"order_preparing",$db) == 1)
{
	$class_prep = "order-circle";
	$icon_color_prep = "";
	$bar_color_prep = "bar-color";
} else {
	$class_prep = "order-circle-gray";
	$icon_color_prep = "icontext-color";
	$bar_color_prep = "bar-color-gray";
}
if($function->GetOrderStatusSecondTable($requestid,"order_transit",$db) == 1)
{
	$class_trans = "order-circle";
	$icon_color_trans = "";
	$bar_color_trans = "bar-color";
} else {
	$class_trans = "order-circle-gray";
	$icon_color_trans = "icontext-color";
	$bar_color_trans = "bar-color-gray";
}
if($function->GetOrderStatusSecondTable($requestid,"order_delivered",$db) == 1)
{
	$class_del = "order-circle";
	$icon_color_del = "";
	$bar_color_del = "bar-color-gray";
} else {
	$class_del = "order-circle-gray";
	$icon_color_del = "icontext-color";
	$bar_color_del = "bar-color-gray";
}


?>
<div style="padding:10px">
	<div class="mrs-wrappers" style="margin:0 auto;margin-top:10px">
		<button type="button" class="btn btn-primary btn-sm" onclick="orderTracking('<?php echo $control_no?>')"><i class="fa fa-undo" aria-hidden="true"></i> BACK</button>
	</div>
	<div class="mrs-wrappers" style="margin:0 auto;margin-top:10px">		
		<table style="width: 100%">
			<tr>
				<th style="width:65px">Driver: </th>
				<td style="width:5px;">&nbsp;</td>
				<td><?php echo $function->GetOrderStatusVer2($requestid,"delivery_driver",$db); ?></td>
				<td style="width:100px">&nbsp;</td>
				<th style="width:100px">Plate Number: </th>
				<td style="width:5px;">&nbsp;</td>
				<td style="width:100px;text-align:center"><?php echo $function->GetOrderStatusVer2($requestid,"plate_number",$db); ?></td>
			</tr>
		</table>		
	</div>
</div>
<!-- ############################################################ -->
<div class="mrs-wrappers" style="margin:0 auto">	
	<table style="width: 100%;font-family:sans-serif" class="table">
		<tr class="table-thth">
			<th colspan="7">MATERIAL REQUISITION FORM</th>
		</tr>
		<tr>
			<td style="width:120px">Requesting Section/Branch:</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style="border-bottom:1px solid #232323 !important;width:400px"><?php echo $branch;?></td>
			<td style="width:">&nbsp;</td>
			<td>Control No.:</td>
			<td style="width:3px !important;"></td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center;color:red;font-weight:bold"><?php echo $function->GetOrderStatusVer2($requestid,"dr_number",$db)?></td>
		</tr>
		<tr>
			<td style="width:120px">&nbsp;</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style=";width:400px">&nbsp;</td>
			<td style="width:">&nbsp;</td>
			<td>Date:</td>
			<td style="width:3px !important;">&nbsp;</td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center"><?php echo $trans_date;?></td>
		</tr>
	</table>
	<table style="width: 100%" class="tabletas">
		<tr>
			<th style="width:40px;text-align:center">#</th>
			<th style="width:80px">Item Code</th>
			<th>Description</th>
			<th style="width:100px">Units (UOM)</th>
			<th style="width:75px">Quantity</th>
			<th style="width:75px;">Actual Qty</th>
			<th style="width:75px;">Inv Ending</th>
			<?php if($function->GetOrderStatusVer2($requestid,"order_transit",$db) == 1) { ?>
				<th style="width:75px;">Brch Rcvd</th>
				<th style="width:75px;">Action</th>
			<?php } ?>
		</tr>
<?php
	$sqlQueryData = "SELECT * FROM dbc_seasonal_branch_mrs_transaction WHERE branch='$branch' AND request_id='$requestid'";
	$dataResults = mysqli_query($db, $sqlQueryData);    
	if ( $dataResults->num_rows > 0 ) 
	{
		$x=0;
		while($DATAROW = mysqli_fetch_array($dataResults))  
		{
			$x++;
			$editid = $DATAROW['id'];
			$remarks = $DATAROW['remarks'];
			
			$controlno = $DATAROW['control_no'];
			
			$branchreceived = $DATAROW['branch_received'];
			$branchreceivedstatus = $DATAROW['branch_received_status'];
			
			$remarks = $function->shortenText($remarks,20);
			
			$requestid = $DATAROW['request_id'];
			
			$actionButton = '';
			$branchrcvdstyle = '';	
			$brnchrcvdeditable = '';
			if($branchreceivedstatus == '0'){
				$brnchrcvdeditable = 'contenteditable="true"';
				$branchrcvdstyle = 'background-color:#f7e9d5';
			}
			
?>		
		<tr>
			<td style="text-align:center"><?php echo $x; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['item_code']; ?></td>
			<td><?php echo $DATAROW['item_description']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['uom']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['quantity']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['wh_quantity']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['inv_ending']; ?></td>
			
			<?php if($function->GetOrderStatusVer2($requestid,"order_transit",$db) == 1) { ?>				
				
				<td id="brachrcvd<?php echo $x?>" style="text-align:center; <?php echo $branchrcvdstyle?>" <?php echo $brnchrcvdeditable?> oninput="validateNumber(this)"><?php echo $branchreceived?></td>								
				
				<td style="text-align:center">
					<?php 
						if($branchreceivedstatus == '0'){ ?>
							<button class="btn btn-sm btn-success" onclick="savemyreceived('<?php echo $x?>','<?php echo $editid?>','<?php echo $mrs_no?>','<?php echo $requestid?>')">Receive</button>
					<?php
						} else { ?>
							<span><i class="fa fa-check" aria-hidden="true"></i></span>
					<?php
						}
					?>
				</td>
			<?php } ?>
		</tr>
<?php } ?>
		<tr>
			<td colspan="10" style="padding:5px;white-space:normal">
				<span style="font-weight:600">REMARKS:</span> <?php echo $function->getOrderRemarks($mrs_no,$db); ?>
			</td>
		</tr>
<?php } else { ?>		
		<tr>
			<td colspan="10" style="text-align:center">No Items</td>
		</tr>
<?php } ?>	
	</table>
</div>


<div class="results"></div>
<script>

function validateNumber(td) {
    td.innerText = td.innerText.replace(/[^0-9]/g, '');
}

function orderTracking(controlno)
{
	var module = '<?php echo MODULE_NAME; ?>';
	$.post("./Modules/" + module + "/includes/mrs_order_tracking_data.php", { control_no: controlno },
	function(data) {		
		$('#smnavdata').html(data);
	});	
}

function savemyreceived(params,rowid,ctrlno,requestid){
	
	var mode = 'savemybranchreceived';
	var brachrcvd = $('#brachrcvd'+params).text();
	
	if(brachrcvd == ''){
		swal("System Message", "Please complete the section indicating the amount received.", "warning");
		return false;
	}
	
	rms_reloaderOn("Saving Receiving Delivery...");
	
	$.post("./Modules/<?php echo MODULE_NAME; ?>/actions/actions.php", { mode: mode, brachrcvd: brachrcvd, rowid: rowid, ctrlno: ctrlno, requestid: requestid },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});

}

function receivedDelivery(controlno)
{
	var mode = 'recieveddelivery';
	rms_reloaderOn("Receiving Delivery...");
	setTimeout(function()
	{
		$.post("./Modules/<?php echo MODULE_NAME; ?>/actions/actions.php", { mode: mode, control_no: controlno },
		function(data) {		
			$('.results').html(data);
			rms_reloaderOff();
		});
	},1000);
}
function aproveOrder(controlno)
{
	var mode = 'approveorder';
	var userlevel = '<?php echo $userlevel; ?>';
	if(userlevel >= 50)
	{		
		rms_reloaderOn("Approving...");
		setTimeout(function()
		{
			$.post("./Modules/<?php echo MODULE_NAME; ?>/actions/actions.php", { mode: mode, control_no: controlno },
			function(data) {		
				$('.results').html(data);
				rms_reloaderOff();
			});
		},1000);
	} else {
		swal("Un-Authorized", "You have unsificient priviledge.", "warning");
	}
}
function approveReview(controlno)
{
	var mode = 'approvecheckreview';
	var userlevel = '<?php echo $userlevel; ?>';
	if(userlevel >= 10 || userlevel <= 50 || userlevel >= 80)
	{		
		rms_reloaderOn("Approving...");
		setTimeout(function()
		{
			$.post("./Modules/<?php echo MODULE_NAME; ?>/actions/actions.php", { mode: mode, control_no: controlno },
			function(data) {		
				$('.results').html(data);
				rms_reloaderOff();
			});
		},1000);
	} else {
		swal("Un-Authorized", "You have unsificient priviledge.", "warning");
	}
}
$(function()
{
	
});
</script>
<script src="../Modules/<?php echo MODULE_NAME; ?>/scripts/script.js"></script>
