<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SEASONAL_SUMMARY_SELECTEDCLUSTER'] = $_POST['selectedcluster'];
$_SESSION['DBC_SEASONAL_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SEASONAL_SUMMARY_DATETO'] = $_POST['dateTo'];

$selectedcluster = $_POST['selectedcluster'];
$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$startDate = new DateTime($datefrom);
$endDate = new DateTime($dateto);

$interval = $startDate->diff($endDate);

$numDays = $interval->days + 1;
$dayscount = 0;

$month_name = date('F j, Y', strtotime($datefrom)). ' - ' .date('F j, Y', strtotime($dateto));
for ($day = 1; $day <= $numDays; $day++) {
    $dayscount++;
}

$formattedDate = date('F j, Y', strtotime($datefrom));

?>
<style>

.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 2 !important;
    white-space: nowrap;
}

.sticky-column-1 {
    left: 0;
    z-index: 1;
}
.sticky-column-1-data {
    left: 0;
    background:white !important;
}

.sticky-column-2 {
    left: 35px;
}
.sticky-column-2-data {
    left: 35px;
    background:white !important;
}

.sticky-column-3 {
    left: 140px;
}
.sticky-column-3-data {
    left: 140px;
    background:white !important;
}

.sticky-column-4 {
    left: 478px;
}
.sticky-column-4-data {
    left: 478px;
    background:white !important;
}

.sticky-column-5{
    left: 544px;
}
.sticky-column-5-data {
    left: 544px;
    background:white !important;
}

.sticky-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #f5f5f5;
    z-index: 3 !important;
}


</style>
<table style="width:100%" class="table table-bordered table-striped">
	<thead>
		<tr class="border-report-title">			
			<th colspan="5" style="text-align:center" class="bg-primary sticky-column sticky-column-1">INVENTORY <?php echo strtoupper($month_name)?></th>
						
			
			<th colspan="<?php echo $dayscount*3?>" style="text-align:center;color:#fff" class="bg-warning">DELIVERY OUT | BRANCH RECEIVED</th>

		</tr>
		<tr>
			<th rowspan="2" class="bg-primary sticky-column sticky-column-1 sticky-header" style="width:50px; vertical-align:middle; text-align:center">#</th>
			<th rowspan="2" class="bg-primary sticky-column sticky-column-2 sticky-header" style="width:70px; vertical-align:middle; text-align:center">ITEM CODE</th>
			<th rowspan="2" class="bg-primary sticky-column sticky-column-3 sticky-header" style="vertical-align:middle; text-align:center">ITEM NAME</th>
			<th rowspan="2" class="bg-primary sticky-column sticky-column-4 sticky-header" style="vertical-align:middle; text-align:center">PRICE</th>
			<th rowspan="2" class="bg-primary sticky-column sticky-column-5 sticky-header" style="width:70px; vertical-align:middle; text-align:center">BEG.</th>
		
		<?php
			for ($tout = 1; $tout <= $numDays; $tout++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($tout - 1) . ' days');
		        echo '<th colspan="3" style="text-align:center;width:50px" class="bg-warning">Day ' . $dateObject->format('j') . '</th>';  
			}		
		?>
		
		</tr>
		<tr>
		
		<?php
			for ($toutt = 1; $toutt <= $numDays; $toutt++)
			{
				$dateObject = new DateTime($datefrom);
		        $dateObject->modify('+' . ($toutt - 1) . ' days');
		        echo '<th style="text-align:center;width:50px" class="bg-warning">Out</th>';
		        echo '<th style="text-align:center;width:50px" class="bg-warning">In</th>';
		        echo '<th style="text-align:center;width:50px" class="bg-warning">Var.</th>';  
			}		
		?>
			
			
		</tr>
	</thead>
	<tbody>
<?php
	$sqlQuery = "SELECT * FROM dbc_seasonal_itemlist";
	$results = mysqli_query($db, $sqlQuery);
	if ($results->num_rows > 0) {
    $i = 0;
    while ($INVROW = mysqli_fetch_array($results))
    {
	    $i++;
	    $itemcode = $INVROW['item_code'];	    
	    $unitprice = $INVROW['unit_price'];
	    
		$begdate = $datefrom;
		$beg1 = date('Y-m-d', strtotime($begdate. ' -1 day'));
		$beginning = $function->GetPcountBeginning('p_count',$itemcode,$beg1,$db);			
?>	
		<tr>
			<td style="text-align:center" class="sticky-column sticky-column-1-data"><?php echo $i; ?></td> 
			<td style="text-align:center" class="sticky-column sticky-column-2-data"><?php echo $itemcode; ?></td>
			<td class="sticky-column sticky-column-3-data"><?php echo $INVROW['item_description']; ?></td>
			<td style="text-align:center" class="sticky-column sticky-column-4-data"><?php echo $INVROW['unit_price']; ?></td>
			<td style="text-align:right;" class="sticky-column sticky-column-5-data"><?php echo $beginning?></td>		
			
<?php
		
		$TotalGetInventoryOut=0;
		
		//OUT
		for ($dateLoopOut = clone $startDate; $dateLoopOut <= $endDate; $dateLoopOut->modify('+1 day')) {
			$dateout = $dateLoopOut->format('Y-m-d');	
			$GetInventoryOut = $inventory->GetTransferOutData($itemcode,$dateout,$db);	
			$GetBranchReceived = $inventory->GetBranchReceivedData($itemcode,$dateout,$db);	
			
			$var = $GetInventoryOut - $GetBranchReceived;
		
			$TotalGetInventoryOut += $GetInventoryOut;
?>
	    	<td style="text-align:right" class="psahovout" ondblclick="timeOutDetails('invout','<?php echo $itemcode?>','<?php echo $dateout?>')"><?php echo $GetInventoryOut?></td>
	    	<td style="text-align:right" class="psahovout" ondblclick="timeOutDetails('','<?php echo $itemcode?>','<?php echo $dateout?>')"><?php echo $GetBranchReceived?></td>
	    	<td style="text-align:right"><?php echo $var == 0? '': number_format($var,2)?></td>
	    	
<?php	}
		
	}

?>
		
<?php 

} else { 

?>		
		<tr>
			<td colspan="50" style="text-align:center;color:#fff" class="bg-primary"><i class="fa fa-bell color-orange"></i> No Record(s) found.</td>
		</tr>
<?php } ?>
	</tbody>
</table>

<script>

function timeOutDetails(params,itemcode,transdate){
	
	const dateStr = transdate;
	const dateObj = new Date(dateStr);
	
	const months = [
	  "January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];
		
	const year = dateObj.getFullYear();
	const month = months[dateObj.getMonth()];
	const dayOfMonth = dateObj.getDate();
	
	const dateInWords = `${month} ${dayOfMonth}, ${year}`;
	
	if(params=='invout'){
		$('#modaltitle').html("INVENTORY OUT "+dateInWords);
		$.post("./Modules/DBC_Seasonal_Management/apps/inventory_out_vs_form.php", { itemcode: itemcode, transdate: transdate },
		function(data) {		
			$('#formmodal_page').html(data);
			$('#formmodal').show();
		});
	} else {
	
		$('#modaltitle').html("INVENTORY OUT "+dateInWords);
		$.post("./Modules/DBC_Seasonal_Management/apps/inventory_out_vs_branch_rcvd_form.php", { itemcode: itemcode, transdate: transdate },
		function(data) {		
			$('#formmodal_page').html(data);
			$('#formmodal').show();
		});
	}
		
}
function timeInDetails(itemcode,transdate){
		
	const dateStr = transdate;
	const dateObj = new Date(dateStr);
	
	const months = [
	  "January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];
		
	const year = dateObj.getFullYear();
	const month = months[dateObj.getMonth()];
	const dayOfMonth = dateObj.getDate();
	
	const dateInWords = `${month} ${dayOfMonth}, ${year}`;
	
	$('#modaltitle').html("INVENTORY IN "+dateInWords);
	$.post("./Modules/DBC_Seasonal_Management/apps/inventory_in_form.php", { itemcode: itemcode, transdate: transdate },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}

</script>
