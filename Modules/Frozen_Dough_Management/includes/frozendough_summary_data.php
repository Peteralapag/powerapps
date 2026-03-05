<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.inventory.php";

$function = new FDSFunctions;
$inventory = new FDSInventory;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['FDS_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['FDS_TRANSDATE'];
}

$pcountChecker = $function->GetPcountDataInventoryChecker($transdate,$db);
if($pcountChecker == '1'){
	$pcountstyle = '';
	$pcounCeditable = '';
	$ButtonDisplay = 'display:none';
}
else {
	$pcountstyle = 'background-color:#f7e9d5';
	$pcounCeditable = 'contenteditable="true"';
	$ButtonDisplay = 'display:block';
}

?>
<style>
.table td {
	padding:2px 5px 2px 5px !important;
}
</style>
<div style="<?php echo $ButtonDisplay?>"><button type="button" class="btn btn-sm btn-primary" onclick="postSummary()" ><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;Post This Summary</button></div>

<table style="width: 100%" class="table table-bordered">
	<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th style="text-align:center">ITEMCODE</th>
			<th style="text-align:center">ITEM DESCRIPTION</th>
			<th style="text-align:center">BEG</th>
			<th style="text-align:center">TX-IN</th>
			<th style="text-align:center">TOTAL</th>
			
			<th style="text-align:center">TX-OUT</th>
			<th style="text-align:center">EXPTD. STKS</th>
			<th style="text-align:center">PCOUNT</th>
			<th style="text-align:center">VARIANCE</th>
			<th style="text-align:center">UNIT PRICE</th>
			<th style="text-align:center">VAR AMOUNT</th>
			<th style="text-align:center">SHORT</th>
			<th style="text-align:center">OVER</th>
			<th style="text-align:center">BKR. CHRGS</th>
			
		</tr>
	</thead>
	<tbody>	
<?PHP
	$sqlQuery = "SELECT * FROM fds_itemlist";
	$results = mysqli_query($db, $sqlQuery);    
    if ( $results->num_rows > 0 ) 
    {
    	$n=0;
    	while($RECVROW = mysqli_fetch_array($results))  
		{
			$n++;
			$itemcode = $RECVROW['item_code'];
			$itemdescription = $RECVROW['item_description'];
			
			$unitprice = $RECVROW['unit_price'];
			
			$beg1 = date('Y-m-d', strtotime($transdate. ' -1 day'));
			$beginning = $function->GetPcountBeginning('p_count',$itemcode,$beg1,$db);
			
			$t_in = $function->GetProductionData('actual_received',$itemcode,$transdate,$transdate,$db);
			$total = $beginning + $t_in;
			$charge = $function->GetProductionData('charge',$itemcode,$transdate,$transdate,$db);
			$t_out = $inventory->GetTransferOutData($itemcode,$transdate,$db);

				
			
			$expectedstock = $total - $t_out;
			
			$pcount = $inventory->GetPcountDataInventory('p_count',$itemcode,$transdate,$db);
			
			$variance = $pcount - $expectedstock;
			
			$varianceAmount = $unitprice * $variance;
			
			if($variance < 0){
				$varstyle = 'color:#dc3545';
				$short = $variance;
				$over = 0;
			}
			else {
				$varstyle = '';
				$short = 0;
				$over = $variance;
			}	
			
?>	
		<tr>
			<td style="text-align:center; height: 25px;"><?php echo $n; ?></td>
			<td style="text-align:center"><?php echo $itemcode?></td>
			<td style="text-align:center"><?php echo $itemdescription?></td>
			<td id="beginning_<?php echo $n?>" style="text-align:right"><?php echo $beginning?></td>
			<td id="tin_<?php echo $n?>" style="text-align:right"><?php echo $t_in?></td>
			<td id="total_<?php echo $n?>"style="text-align:right"><?php echo $total?></td>
			
			<td id="tout_<?php echo $n?>" style="text-align:right"><?php echo $t_out?></td>
			<td id="expectedstock_<?php echo $n?>" style="text-align:right"><?php echo $expectedstock?></td>
			<td id="pcount_<?php echo $n?>" style="text-align:right; <?php echo $pcountstyle?>" <?php echo $pcounCeditable?> onkeyup="calculateThis('<?php echo $n?>')"><?php echo $pcount?></td>
			<td id="variance_<?php echo $n?>" style="text-align:right; <?php echo $varstyle?>"><?php echo $variance?></td>
			
			<td id="unitprice_<?php echo $n?>" style="text-align:right"><?php echo number_format($unitprice,2)?></td>
			<td id="varianceamount_<?php echo $n?>" style="text-align:right"><?php echo number_format($varianceAmount,2)?></td>
			<td id="shortt_<?php echo $n?>" style="text-align:right"><?php echo $short?></td>
			<td id="over_<?php echo $n?>" style="text-align:right"><?php echo $over?></td>
			<td id="charge_<?php echo $n?>" style="text-align:right"><?php echo $charge?></td>
			
		</tr>
<?php 
		} 
	} else { 
?>
		<tr>
			<td colspan="9" style="text-align:center"><i class="fa fa-bell"></i>&nbsp;&nbsp;No Records</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<div></div>
<script>

function calculateThis(params) {
	var beginning = parseFloat($('#beginning_'+params).text());
	var tin = parseFloat($('#tin_'+params).text());
	var total = parseFloat($('#total_'+params).text());
	
	var tout = parseFloat($('#tout_'+params).text());
	var expectedstock = parseFloat($('#expectedstock_'+params).text());
	var pcount = parseFloat($('#pcount_'+params).text());
	var variance = parseFloat($('#variance_'+params).text());

	var unitprice = parseFloat($('#unitprice_'+params).text());
	var shortt = parseFloat($('#shortt_'+params).text());
	var over = parseFloat($('#over_'+params).text());
	var charge = parseFloat($('#charge_'+params).text());
	
	if(isNaN(beginning)){ beginning = 0; }
	if(isNaN(tin)){ tin = 0; }
	
	if(isNaN(tout)){ tout = 0; }
	if(isNaN(pcount)){ pcount = 0; }
	
	if(isNaN(charge)){ charge = 0; }
	
	
	var total = beginning + tin;
	var expectedstock = total - tout;
	var variance = pcount - expectedstock;
	var varianceamount = unitprice * variance;
	
	
	if(variance < 0){
		shortt = variance;
		over = 0;
	}
	else {
		shortt = 0;
		over = variance;
	}
	
	$('#variance_' + params).text(variance.toFixed(2));
	$('#varianceamount_' + params).text(varianceamount.toFixed(2));
	$('#shortt_' + params).text(shortt.toFixed(2));
	$('#over_' + params).text(over.toFixed(2));
	
}


function postSummary() {
    var mode = 'pcountposting';
    var dataToSend = [];
    var transdate = '<?php echo $transdate?>';
   
    $('table tbody tr').each(function() {
        var itemcode = $(this).find('td:nth-child(2)').text();
        var pcount = $(this).find('td:nth-child(9)').text();
        
        if (itemcode.trim() !== '') {
            var rowData = {
                itemcode: itemcode,
                pcount: pcount  
            };
            dataToSend.push(rowData);
        }
    });

    if (dataToSend.length > 0) {
        rms_reloaderOn('Loading...');
        $.post("./Modules/Frozen_Dough_Management/actions/actions.php", { mode: mode, transdate: transdate, data: JSON.stringify(dataToSend) },
            function(data) {        
                $('#smnavdata').html(data);
                rms_reloaderOff();
                viewSummary();
            }
        );
    } else {
        console.log("No item codes found. Data not saved.");
    }
}


</script>
