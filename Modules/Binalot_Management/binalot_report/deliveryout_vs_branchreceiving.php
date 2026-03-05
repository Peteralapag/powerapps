<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.inventory.php";

$function = new BINALOTFunctions;
$inventory = new BINALOTInventory;

$_SESSION['BINALOT_SUMMARY_SELECTEDCLUSTER'] = $_POST['selectedcluster'];
$_SESSION['BINALOT_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['BINALOT_SUMMARY_DATETO'] = $_POST['dateTo'];

$selectedcluster = $_POST['selectedcluster'];
$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$branches = [];

if ($selectedcluster == '') {
    $sql = "SELECT branch FROM binalot_tbl_branch";
} else {
    $sql = "SELECT branch FROM tbl_branch WHERE location = ?";
}

if ($stmt = $db->prepare($sql)) {
    if ($selectedcluster != '') {
        $stmt->bind_param("s", $selectedcluster);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row['branch'];
    }
    
    $stmt->close();
} else {
    echo "Error: " . $db->error;
}

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
    left: 543px;
}
.sticky-column-5-data {
    left: 543px;
    background:white !important;
}

.sticky-column-6{
    left: 597px;
}
.sticky-column-6-data {
    left: 597px;
    background:white !important;
}
.sticky-column-7{
    left: 664px;
}
.sticky-column-7-data {
    left: 664px;
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
            <th colspan="7" style="text-align:center" class="bg-success sticky-column sticky-column-1">INVENTORY</th>
            <?php
            foreach ($branches as $branch) {
                echo '<th title="DELIVERY OUT" style="text-align:center;width:50px; background-color:#86b7fe">Out</th>';
                echo '<th title="BRANCH RECEIVED" style="text-align:center;width:50px;background:#efdeaa">In</th>';
                echo '<th title="VARIANCE" style="text-align:center;width:50px" class="bg-danger">Var.</th>';
            }
            ?>
            
            <th title="DELIVERY OUT" style="text-align:center;width:50px; background-color:#86b7fe">Out</th>
            <th title="BRANCH RECEIVED" style="text-align:center;width:50px;background:#efdeaa">In</th>
            <th title="VARIANCE" style="text-align:center;width:50px" class="bg-danger">Var.</th>
            
            <th colspan="6" style="text-align:center" class="bg-success">OUT(QTY)</th>
            
            
            <th colspan="7" title="EXPECTED STOCK" class="bg-danger" style="vertical-align:middle"></th>
            
        </tr>
        <tr>
            <th class="bg-success sticky-column sticky-column-1 sticky-header" style="width:50px; vertical-align:middle; text-align:center">#</th>
            <th class="bg-success sticky-column sticky-column-2 sticky-header" style="width:70px; vertical-align:middle; text-align:center">ITEM CODE</th>
            <th class="bg-success sticky-column sticky-column-3 sticky-header" style="vertical-align:middle; text-align:center">ITEM NAME</th>
            <th class="bg-success sticky-column sticky-column-4 sticky-header" style="vertical-align:middle; text-align:center">PRICE</th>
            <th title="BEGINNING" class="bg-success sticky-column sticky-column-5 sticky-header" style="width:70px; vertical-align:middle; text-align:center">BEG.</th>
            <th title="PRODUCTION" class="bg-success sticky-column sticky-column-6 sticky-header" style="width:70px; vertical-align:middle; text-align:center">PROD.</th>
            <th title="RETURN FROM BRANCHES" class="bg-success sticky-column sticky-column-7 sticky-header" style="width:70px; vertical-align:middle; text-align:center">RET.</th>
            <?php
            foreach ($branches as $branch) {
                echo '<th colspan="3" style="text-align:center;width:50px" class="bg-warning">' . htmlspecialchars($branch) . '</th>';
            }
            ?>
            
            <th colspan="3" style="text-align:center;width:50px" class="bg-warning">TOTAL</th>
            
            <th title="CHARGES">CHRGS.</th>
            <th title="BAD ORDER">BO</th>
            
            <th title="DAMAGE">DAM.</th>
            <th title="RESEARCH and DEVELOPMENT">RND</th>
            <th title="COMPLEMENTARY">COMPL.</th>
            <th title="TOTAL">TOTAL</th>
            
            <th title="TOTAL OUT" class="bg-danger" style="vertical-align:middle">TOTAL OUT</th>
			<th title="EXPECTED STOCK" class="bg-danger" style="vertical-align:middle">EXPTD. STKS</th>
			<th title="PHYSICAL COUNT" class="bg-danger" style="vertical-align:middle">PHY. COUNT</th>
			<th title="VARIANCES" class="bg-danger" style="vertical-align:middle">VARIANCES</th>
			<th title="VARIANCE AMOUNT" class="bg-danger" style="vertical-align:middle">VAR. AMOUNT</th>
			<th title="SHORT" class="bg-danger" style="vertical-align:middle">SHORT</th>
			<th title="OVER" class="bg-danger" style="vertical-align:middle">OVER</th>
            
        </tr>
    </thead>
    <tbody>
    <?php
    $sqlQuery = "SELECT * FROM binalot_itemlist";
    $results = mysqli_query($db, $sqlQuery);
    if ($results && $results->num_rows > 0) {
        $i = 0;
        while ($INVROW = mysqli_fetch_array($results)) {
            $i++;
            $itemcode = $INVROW['item_code'];	    
            $unitprice = $INVROW['unit_price'];
            $begdate = $datefrom;
            $beg1 = date('Y-m-d', strtotime($begdate. ' -1 day'));
            $beginning = $function->GetPcountBeginning('p_count', $itemcode, $beg1, $db);
            
            $production = $inventory->GetInventoryProductionRange($datefrom,$dateto,$itemcode,$db);
            $GetInventoryReturn = $inventory->GetInventoryOtherDataRange('return','quantity',$datefrom,$dateto,$itemcode, $db);
            
            $GetInventoryCharges = $inventory->GetInventoryOtherDataRange('charges', 'quantity', $datefrom, $dateto, $itemcode, $db);
            $GetInventoryBO = $inventory->GetInventoryOtherDataRange('badorder', 'quantity', $datefrom, $dateto, $itemcode, $db);
            $GetInventoryDamage = $inventory->GetInventoryOtherDataRange('damage', 'quantity', $datefrom, $dateto, $itemcode, $db);
            $GetInventoryRnd = $inventory->GetInventoryOtherDataRange('rnd', 'quantity', $datefrom, $dateto, $itemcode, $db);
            $GetInventoryComplementary = $inventory->GetInventoryOtherDataRange('complementary', 'quantity', $datefrom, $dateto, $itemcode, $db);
            
            $totalOut = $GetInventoryCharges + $GetInventoryBO + $GetInventoryDamage + $GetInventoryRnd + $GetInventoryComplementary;
            
            ?>	
            <tr>
                <td style="text-align:center" class="sticky-column sticky-column-1-data"><?php echo $i; ?></td> 
                <td style="text-align:center" class="sticky-column sticky-column-2-data"><?php echo htmlspecialchars($itemcode); ?></td>
                <td class="sticky-column sticky-column-3-data"><?php echo htmlspecialchars($INVROW['item_description']); ?></td>
                <td style="text-align:right" class="sticky-column sticky-column-4-data"><?php echo number_format($unitprice,0)?></td>
                <td style="text-align:right;" class="sticky-column sticky-column-5-data"><?php echo $beginning == 0? '': number_format($beginning,0)?></td>
                <td style="text-align:right;" class="sticky-column sticky-column-6-data"><?php echo $production == 0? '': number_format($production,0)?></td>
                <td style="text-align:right;" class="sticky-column sticky-column-7-data"><?php echo $GetInventoryReturn == 0? '': number_format($GetInventoryReturn,0)?></td>


                <?php
                $totalbrin = $totalbrout = $totalbrvariance = 0;
                foreach ($branches as $branch) {
                    $GetInventoryOut = $inventory->GetTransferOutDataVsbrchreceivingRange($branch,$datefrom,$dateto,$itemcode,$db);
                    $GetBranchReceived = $inventory->GetBranchReceivedDataRange($branch,$datefrom,$dateto,$itemcode,$db);	
			
					$var = $GetInventoryOut - $GetBranchReceived;
					
					$totalbrout += $GetInventoryOut;
					$totalbrin += $GetBranchReceived;
					$totalbrvariance += $var;
                ?>
                
                    <td style="text-align:center"><?php echo $GetInventoryOut == 0? '': number_format($GetInventoryOut,0)?></td>
                    <td style="text-align:center"><?php echo $GetBranchReceived == 0? '': number_format($GetBranchReceived,0)?></td>
                    <td style="text-align:center; color:red"><?php echo $var == 0? '': number_format($var,0)?></td>

                <?php 
                	}
                	
					$totalOutAll = $totalOut + $totalbrout; 
					
					$expectedstocks = ($beginning+$production+$GetInventoryReturn)-$totalOutAll;
					
					$pcountdate = $dateto;
					$pcount = $inventory->GetPcountDataInventory('p_count',$itemcode,$pcountdate,$db);
					
					$variance = $pcount - $expectedstocks;
					$varianceAmount = $unitprice * $variance;
					
					if($variance < 0){
						$variancestyle = 'color:#dc3545';
						$short = $variance;
						$over = 0;
					}
					else {
						$variancestyle = '';
						$short = 0;
						$over = $variance;
					}
                ?>
                
                <td style="text-align:right"><?php echo number_format($totalbrout,0)?></td>
                <td style="text-align:right"><?php echo number_format($totalbrin,0)?></td>
                <td style="text-align:right"><?php echo number_format($totalbrvariance,0)?></td>
                
                <td style="text-align:center"><?php echo $GetInventoryCharges == 0? '': number_format($GetInventoryCharges,0)?></td>
				<td style="text-align:center"><?php echo $GetInventoryBO == 0? '': number_format($GetInventoryBO,0)?></td>
                <td style="text-align:center"><?php echo $GetInventoryDamage == 0? '': number_format($GetInventoryDamage,0)?></td>
                <td style="text-align:center"><?php echo $GetInventoryRnd == 0? '': number_format($GetInventoryRnd,0)?></td>
                <td style="text-align:center"><?php echo $GetInventoryComplementary == 0? '': number_format($GetInventoryComplementary,0)?></td>
                
                <td style="text-align:center"><?php echo $totalOut == 0? '': number_format($totalOut,0)?></td>

				<td style="text-align:center"><?php echo $totalOutAll == 0? '': number_format($totalOutAll,0)?></td>
				
				<td style="text-align:right"><?php echo number_format($expectedstocks,0); ?></td>
				<td style="text-align:right"><?php echo number_format($pcount,0)?></td>
				<td style="text-align:right;<?php echo $variancestyle?>"><?php echo number_format($variance,0); ?></td>
				<td style="text-align:right"><?php echo number_format($varianceAmount,2); ?></td>
				<td style="text-align:right"><?php echo number_format($short,0); ?></td>
				<td style="text-align:right"><?php echo number_format($over,0); ?></td>
       
            </tr>
            <?php			
        }
    } else { 
        $colspan = 5 + (count($branches) * 3);
        ?>		
        <tr>
            <td colspan="<?php echo $colspan; ?>" style="text-align:center;color:#fff" class="bg-primary">
                <i class="fa fa-bell color-orange"></i> No Record(s) found.
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
