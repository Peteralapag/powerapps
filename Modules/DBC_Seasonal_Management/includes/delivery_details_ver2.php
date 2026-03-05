<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$control_no = $_POST['controlno'];

$rowid = $_POST['rowid'];

if( $function->GetOrderStatusSecondTable($rowid,'delivery_date',$db) == '' )
{
	$date = "";
} else {
	$date = date("F d, Y", strtotime($function->GetOrderStatusSecondTable($rowid,'delivery_date',$db)));
}
$delivery_date = $function->getDeliveryMonthVer2($rowid,$db);
$delivery_year = date("Y", strtotime($function->getDeliveryDateVer2($rowid,$db)));
$delivery_month = date("m", strtotime($function->getDeliveryDateVer2($rowid,$db)));
$delivery_day = date("d", strtotime($function->getDeliveryDateVer2($rowid,$db)));



$closeStatus = $function->GetOrderStatus($control_no,'status',$db);
?>
<style>
.dr-wrapper {padding:0.25in;width:8.5in;border: 1px solid #aeaeae}
.company-wrapper td {position: relative;padding: 2px;padding-left:10px;background:#0f243f;color: #fff;font-size:18px;font-weight: 600;}
.dr-formname td {padding: 7px;text-align:center;background:#d9d9d9;color:#0f243f;font-size: 16px;padding:7px;letter-spacing:10px;}
.dr-filled td {padding:3px;}
.company-texticon {display: flex;width: 100%;align-items: center;gap: 20px;}
.dr-table-header th {
	background:#f1f1f1;font-size:12px;padding:4px;border: 1px solid #232323;font-weight:600
}
</style>
<div style="padding:10px">
	<div style="margin-top:20px;padding:10PX; width:8.5in;">	
		<table style="width: 100%">
			<tr>
				<td>
					<label style="font-weight: 600;">DELIVERY RECEIPT</label>
				</td>
				<td style="text-align:right">
				
	
				<?php 
				
					if( $function->GetOrderStatusSecondTable($rowid,'order_transit',$db) == 0) { ?>
					<!--button class="btn btn-primary btn-sm" onclick="requestReopen('<?php echo $control_no; ?>')">Request Re-Open&nbsp;&nbsp;<i class="fa-solid fa-lock-keyhole-open color-orange"></i></button-->
					<button class="btn btn-warning btn-sm btnko color-white" onclick="logisticInfo('<?php echo $rowid?>','<?php echo $control_no; ?>')">Logistic Info</button>					
					<button class="btn btn-danger btn-sm btnko" onclick="setInTransit('<?php echo $rowid?>','<?php echo $control_no?>')">Set In-Transit</button>
				<?php  } else { 
						
					if($closeStatus != 'Closed'){
						
				?>
					<!-- button class="btn btn-primary btn-sm" onclick="requestReopen('<?php echo $control_no; ?>')">Request Re-Open&nbsp;&nbsp;<i class="fa-solid fa-lock-keyhole-open color-orange"></i></button -->				
					<button class="btn btn-info btn-sm" onclick="printDR('<?php echo $rowid?>')"><i class="fa-sharp fa-solid fa-print"></i>&nbsp;&nbsp;Print DR</button>
				<?php } 
				
				}?>
				
				
				</td>
			</tr>
		</table>	
	</div>
	<div class="dr-wrapper">
		<table style="width: 100%">
			<tr class="company-wrapper">
				<td colspan="7">
					<div class="company-texticon">
						<i class="fa-solid fa-truck" style=" font-size:28px;"></i>
						<span>Rose Bakeshop</span>
					</div>
				</td>
			</tr>
			<tr class="dr-formname" style="width:8in">
				<td colspan="7">DELIVERY RECEIPT</td>
			</tr>
			<tr class="dr-filled">
				<td style="width:1in">Delivery Date:</td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:4in;border-bottom:1px solid #232323"><?php echo $date; ?></td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:0.75in">DR No.:</td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:1in;border-bottom: 1px solid #232323"><?php echo $function->GetOrderStatusSecondTable($rowid,'dr_number',$db); ?></td>
			</tr>
			<tr class="dr-filled">
				<td style="width:1in">Branch:</td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:4in;border-bottom:1px solid #232323"><?php echo $function->GetOrderStatusSecondTable($rowid,'branch',$db)?></td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:0.75in">Ref. No.:</td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:1in;border-bottom: 1px solid #232323"><?php echo $function->GetOrderStatusSecondTable($rowid,'control_no',$db)?></td>
			</tr>
		</table>	
		<table style="width: 100%;margin-top:20px">
			<tr class="dr-table-header">
				<th style="width:50px;text-align:center">#</th>
				<th style="width:80px">ITEM CODE</th>
				<th>ITEM DESCRIPTION</th>
				<th style="width:80px">UOM</th>
				<th style="width:100px">REQSTD QTY.</th>
				<th style="width:100px">PARTIAL QTY.</th>
				<th style="width:100px">BRNCH QTY.</th>
			</tr>
	<?php
//		$sqlQuery = "SELECT * FROM dbc_seasonal_branch_order WHERE control_no='$control_no' AND cancelled=0";
		
		$sqlQuery = "
		    SELECT dbo.*, dil.ordered 
		    FROM dbc_seasonal_branch_mrs_transaction dbo
		    INNER JOIN dbc_seasonal_itemlist dil ON dbo.item_code = dil.item_code
		    WHERE dbo.request_id = '$rowid' AND dbo.cancelled = 0 AND dbo.quantity <> 0
		    ORDER BY dil.ordered ASC
		";
		
		
		$results = mysqli_query($db, $sqlQuery);
		if (!$results) {
		    die('Error: ' . mysqli_error($db));
		}
		
		
		$x=0;
		while($ROWS = mysqli_fetch_array($results))  
		{
			$x++;
//			$rowid = $ROWS['id'];
			$branch = $ROWS['branch'];
			$oum = $ROWS['uom'];
			$item = $ROWS['item_description'];
			$item_code = $ROWS['item_code'];
			$quantity = $ROWS['quantity'];
			$dbc_quantity = $ROWS['wh_quantity'];
			$actual_quantity = $ROWS['actual_quantity'];
			$branchreceived = $ROWS['branch_received'];
	?>
			<tr>
				<td style="text-align:center;padding:4px;border: 1px solid #232323"><?php echo $x; ?></td>
				<td style="text-align:center;padding:4px;border: 1px solid #232323"><?php echo $item_code; ?></td>
				<td style="padding:4px;border: 1px solid #232323"><?php echo $item; ?></td>
				<td style="text-align:center; padding:4px;border: 1px solid #232323"><?php echo $oum; ?></td>
				<td style="text-align:right;padding:4px;border: 1px solid #232323"><?php echo $quantity; ?></td>
				<td style="text-align:right;padding:4px;border: 1px solid #232323"><?php echo $dbc_quantity; ?></td>
				<td style="text-align:right;padding:4px;border: 1px solid #232323"><?php echo number_format($branchreceived,2)?></td>
			</tr>
	<?php } ?>
		</table>
		<input id="ddt" type="hidden" value="<?php echo $date; ?>">	
		<input id="ddrv" type="hidden" value="<?php echo $function->GetOrderStatusSecondTable($rowid,'delivery_driver',$db); ?>">	
		<input id="pnum" type="hidden" value="<?php echo $function->GetOrderStatusSecondTable($rowid,'plate_number',$db); ?>">	
		<table style="width: 100%; margin-top:5px">
			<tr>
				<td colspan="9" style="border-bottom:2px solid #aeaeae">
					<strong>PREP. REMARKS:</strong> <span style="font-style:italic"><?php echo $function->getOrderRemarks($control_no,"preparator_remarks",$db); ?></span>
				</td>
			</tr>
			<tr>
				<td colspan="9" style="height:10px"></td>
			</tr>
			<tr>
				<td style="width:50px"><strong>Driver:</strong></td>
				<td style="width:210px;border-bottom:1px solid #232323;text-align:center"><?php echo strtoupper($function->GetOrderStatusSecondTable($rowid,'delivery_driver',$db)); ?></td>
				<td style="width:5px">&nbsp;</td>
				<td style="width:50px"><strong>Plate No.:</strong></td>
				<td style="border-bottom:1px solid #232323;width:100px;text-align:center"><?php echo $function->GetOrderStatusSecondTable($rowid,'plate_number',$db)?></td>
				<td style="width:10px;">&nbsp;</td>
				<td style="width:100px;text-align:right;"><strong>Received By: </strong>&nbsp;</td>
				<td style="border-bottom:1px solid #232323;text-align:center"><?php echo $function->GetOrderStatusSecondTable($rowid,'order_accepted_by',$db)?></td>
			</tr>
		</table>		
	</div>
</div>
<div style="padding:10px" id="setintransitresults"></div>
<script>
function printDR(rowid)
{
	$('#modaltitlePDF').html("Print Delivery Receipt");
	$.post("./Modules/DBC_Seasonal_Management/includes/printable_dr_ver2.php", { rowid: rowid },
	function(data) {		
		$('#pdfviewer_page').html(data);
		$('#pdfviewer').show();
	});
}
function requestReopen(controlno)
{	
	dialogue_confirm("Confirm","Are you sure to create request to re open this transaction?","warning","requestReopenYes",controlno,"orange");
	return false;
}
function requestReopenYes(controlno)
{
	var delivery_date = '<?php echo $delivery_date; ?>';
	var delivery_day = '<?php echo $delivery_day; ?>'
	if(delivery_date == 0)
	{
		var mode = 'reopenorderrequest';
		var recipient = $('#recipient').val();
		$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, control_no: controlno, recipient: recipient },
		function(data) {		
			$('#setintransitresults').html(data);
		});	
	} 
	else if(delivery_date == 1)
	{

		var year = '<?php echo $delivery_year; ?>';
		var month = '<?php echo $delivery_month; ?>';
		var day = '<?php echo $delivery_day; ?>';

		var mode = 'reopenclosedorderrequest';
		var recipient = $('#recipient').val();
		$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, year: year, month: month, day: day, control_no: controlno, recipient: recipient },
		function(data) {		
			$('#setintransitresults').html(data);
		});	
	}
}
/*
function printDR(controlno)
{
	$('#modaltitlePDF').html("Print Delivery Receipt");
	$.post("./Modules/DBC_Seasonal_Management/includes/printable_dr.php", { control_no: controlno },
	function(data) {		
		$('#pdfviewer_page').html(data);
		$('#pdfviewer').show();
	});
}
*/
function setInTransit(rowid,controlno)
{
	var delivery_date = '<?php echo $delivery_date; ?>';
	if(delivery_date == 0)
	{
		swal(
			"Invalid Transaction",
			"If the month of delivery is beyond the designated range, setting an item as (In-Transit) and generating a Delivery Receipt becomes impossible. Generating a Delivery Receipt for Advanced Orders is restricted to the specified month of the delivery date.",
		"warning");
		return false
	} else {
		if( $("#ddt").val() == '')
		{
			swal("Delivery Date", "Delivery Date is empty. Please Complete the Logistic Information.", "warning");
			logisticInfo(controlno);
			return false;
		}
		if( $("#ddrv").val() == '')
		{
			swal("Driver Name", "Driver Name is empty. Please Complete the Logistic Information.", "warning");
			logisticInfo(controlno);
			return false;
		}
		if( $("#pnum").val() == '')
		{
			swal("Plate Number", "Plate Number is empty. Please Complete the Logistic Information.", "warning");
			logisticInfo(controlno);
			return false;
		}
		
		
		
		var mode = 'setintransit_ver2';
		$('.btnko').prop("disabled", true);
		rms_reloaderOn("Setting Transit...");
		setTimeout(function()
		{
			$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, rowid: rowid, control_no: controlno },
			function(data) {		
				$('#setintransitresults').html(data);
				rms_reloaderOff();
			});
		},1000);
	}
}
function logisticInfo(rowid,controlno)
{
	$('#modaltitle').html("Logistic Information");
	$.post("./Modules/DBC_Seasonal_Management/apps/logistic_info.php", { rowid: rowid, control_no: controlno },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
</script>
