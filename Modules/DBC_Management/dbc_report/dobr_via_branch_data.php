<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";

$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SUMMARY_SELECTEDBRANCH'] = $_POST['selectedbranch'] ?? '';
$_SESSION['DBC_SUMMARY_DATEFROM'] = $_POST['dateFrom'] ?? '';
$_SESSION['DBC_SUMMARY_DATETO'] = $_POST['dateTo'] ?? '';

$selectedbranch = $_POST['selectedbranch'] ?? '';
$datefrom = $_POST['dateFrom'] ?? '';
$dateto   = $_POST['dateTo'] ?? '';

$branches = [];

if ($selectedbranch == '') {
    $sql = "SELECT branch FROM dbc_tbl_branch";
} else {
    $sql = "SELECT branch FROM tbl_branch WHERE branch = ?";
}

if ($stmt = $db->prepare($sql)) {
    if ($selectedbranch != '') {
        $stmt->bind_param("s", $selectedbranch);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row['branch'];
    }
    $stmt->close();
} else {
    echo "Error: " . $db->error;
    exit;
}

/*
  OPTIMIZATION: Preload all needed data in grouped queries:
  - items list
  - beginning (previous day pcount)
  - production (dbc_fgts_pcount)
  - returns / charges / badorder / damage / rnd / complementary
  - transfer out per branch (dbc_branch_order join dbc_order_request)
  - branch received per branch (dbc_branch_order join dbc_order_request)
  - pcount at dateto (physical count)
*/

// 1) Items
$items = [];
$sql = "SELECT item_code, item_description, unit_price FROM dbc_itemlist";
$res = $db->query($sql);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $items[$r['item_code']] = [
            'desc' => $r['item_description'],
            'price' => isset($r['unit_price']) ? (float)$r['unit_price'] : 0
        ];
    }
}

// 2) Beginning = previous day pcount
$beg1 = date('Y-m-d', strtotime("$datefrom -1 day"));
$beginning = []; // [item_code] => qty
if ($stmt = $db->prepare("SELECT item_code, SUM(p_count) AS qty FROM dbc_inventory_pcount WHERE trans_date = ? GROUP BY item_code")) {
    $stmt->bind_param('s', $beg1);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $beginning[$r['item_code']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 3) Production range (dbc_fgts_pcount)
$productionMap = []; // [item_code] => qty
if ($stmt = $db->prepare("SELECT item_code, SUM(pcount) AS qty FROM dbc_fgts_pcount WHERE trans_date BETWEEN ? AND ? GROUP BY item_code")) {
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $productionMap[$r['item_code']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 4) Other types: return, charges, badorder, damage, rnd, complementary
$otherTypes = ['return','charges','badorder','damage','rnd','complementary'];
$otherMaps = []; // $otherMaps['return'][item_code] = qty
foreach ($otherTypes as $type) {
    $table = "dbc_" . $type;
    // module column name assumed 'quantity' (as used in your original functions)
    $sql = "SELECT item_code, SUM(quantity) AS qty FROM $table WHERE report_date BETWEEN ? AND ? AND status = 0 GROUP BY item_code";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param('ss', $datefrom, $dateto);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $otherMaps[$type][$r['item_code']] = (float)$r['qty'];
        }
        $stmt->close();
    } else {
        // ignore missing table silently (or log)
        $otherMaps[$type] = [];
    }
}

// 5) Transfer Out per branch per item (dbc_branch_order joined to dbc_order_request logistics='1')
$outMap = []; // [item_code][branch] => qty
$sql = "SELECT bo.item_code, bo.branch, SUM(bo.actual_quantity) AS qty
        FROM dbc_branch_order bo
        INNER JOIN dbc_order_request r ON bo.control_no = r.control_no AND r.logistics = '1'
        WHERE bo.delivery_date BETWEEN ? AND ?
        GROUP BY bo.item_code, bo.branch";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $outMap[$r['item_code']][$r['branch']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 6) Branch Received per branch per item (branch_received, branch_received_status = '1')
$branchReceivedMap = []; // [item_code][branch] => qty
$sql = "SELECT bo.item_code, bo.branch, SUM(bo.branch_received) AS qty
        FROM dbc_branch_order bo
        INNER JOIN dbc_order_request r ON bo.control_no = r.control_no AND r.logistics = '1'
        WHERE bo.delivery_date BETWEEN ? AND ? AND bo.branch_received_status = '1'
        GROUP BY bo.item_code, bo.branch";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $branchReceivedMap[$r['item_code']][$r['branch']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 7) pcount at dateto (physical count)
$pcountMap = []; // [item_code] => qty
if ($stmt = $db->prepare("SELECT item_code, SUM(p_count) AS qty FROM dbc_inventory_pcount WHERE trans_date = ? GROUP BY item_code")) {
    $stmt->bind_param('s', $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $pcountMap[$r['item_code']] = (float)$r['qty'];
    }
    $stmt->close();
}

// Done preloading - output HTML
?>
<style>
/* existing sticky css */
.sticky-column { position: -webkit-sticky; position: sticky; left: 0; background-color: #a4cfc8; z-index: 2 !important; white-space: nowrap; }
.sticky-column-1 { left: 0; z-index: 1; }
.sticky-column-1-data { left: 0; background:white !important; }
.sticky-column-2 { left: 35px; }
.sticky-column-2-data { left: 35px; background:white !important; }
.sticky-column-3 { left: 140px; }
.sticky-column-3-data { left: 140px; background:white !important; }
.sticky-column-4 { left: 478px; }
.sticky-column-4-data { left: 478px; background:white !important; }
.sticky-column-5{ left: 543px; }
.sticky-column-5-data { left: 543px; background:white !important; }
.sticky-column-6{ left: 597px; }
.sticky-column-6-data { left: 597px; background:white !important; }
.sticky-column-7{ left: 664px; }
.sticky-column-7-data { left: 664px; background:white !important; }
.sticky-header { position: -webkit-sticky; position: sticky; top: 0; background-color: #f5f5f5; z-index: 3 !important; }
</style>

<table style="width:100%" class="table table-bordered table-striped">
    <thead>
        <tr class="border-report-title">
            <th colspan="7" style="text-align:center" class="bg-success sticky-column sticky-column-1">INVENTORY</th>
            <?php foreach ($branches as $branch): ?>
                <th title="DELIVERY OUT" style="text-align:center;width:50px; background-color:#86b7fe">Out</th>
                <th title="BRANCH RECEIVED" style="text-align:center;width:50px;background:#efdeaa">In</th>
                <th title="VARIANCE" style="text-align:center;width:50px" class="bg-danger">Var.</th>
            <?php endforeach; ?>

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

            <?php foreach ($branches as $branch): ?>
                <th colspan="3" style="text-align:center;width:50px" class="bg-warning"><?php echo htmlspecialchars($branch); ?></th>
            <?php endforeach; ?>

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
    if (empty($items)) {
        $colspan = 7 + (count($branches) * 3) + 3 + 6 + 7;
        echo '<tr><td colspan="'. $colspan .'" style="text-align:center;color:#fff" class="bg-primary"><i class="fa fa-bell color-orange"></i> No Record(s) found.</td></tr>';
    } else {
        $i = 0;
        foreach ($items as $itemcode => $meta) {
            $i++;
            $unitprice = $meta['price'];
            $itemdesc = $meta['desc'];
            $beginQty = $beginning[$itemcode] ?? 0;
            $production = $productionMap[$itemcode] ?? 0;
            $GetInventoryReturn = $otherMaps['return'][$itemcode] ?? 0;

            $GetInventoryCharges = $otherMaps['charges'][$itemcode] ?? 0;
            $GetInventoryBO = $otherMaps['badorder'][$itemcode] ?? 0;
            $GetInventoryDamage = $otherMaps['damage'][$itemcode] ?? 0;
            $GetInventoryRnd = $otherMaps['rnd'][$itemcode] ?? 0;
            $GetInventoryComplementary = $otherMaps['complementary'][$itemcode] ?? 0;

            $totalOutFixed = $GetInventoryCharges + $GetInventoryBO + $GetInventoryDamage + $GetInventoryRnd + $GetInventoryComplementary;

            echo '<tr>';
            echo '<td style="text-align:center" class="sticky-column sticky-column-1-data">'.$i.'</td>';
            echo '<td style="text-align:center" class="sticky-column sticky-column-2-data">'.htmlspecialchars($itemcode).'</td>';
            echo '<td class="sticky-column sticky-column-3-data">'.htmlspecialchars($itemdesc).'</td>';
            echo '<td style="text-align:right" class="sticky-column sticky-column-4-data">'.number_format($unitprice,0).'</td>';
            echo '<td style="text-align:right;" class="sticky-column sticky-column-5-data">'.($beginQty == 0 ? '' : number_format($beginQty,0)).'</td>';
            echo '<td style="text-align:right;" class="sticky-column sticky-column-6-data">'.($production == 0 ? '' : number_format($production,0)).'</td>';
            echo '<td style="text-align:right;" class="sticky-column sticky-column-7-data">'.($GetInventoryReturn == 0 ? '' : number_format($GetInventoryReturn,0)).'</td>';

            $totalbrin = $totalbrout = $totalbrvariance = 0;
            // per branch columns
            foreach ($branches as $branch) {
                $GetInventoryOut = $outMap[$itemcode][$branch] ?? 0;
                $GetBranchReceived = $branchReceivedMap[$itemcode][$branch] ?? 0;

                $var = $GetInventoryOut - $GetBranchReceived;
                $totalbrout += $GetInventoryOut;
                $totalbrin += $GetBranchReceived;
                $totalbrvariance += $var;

                echo '<td style="text-align:center">'.($GetInventoryOut == 0 ? '' : number_format($GetInventoryOut,0)).'</td>';
                echo '<td style="text-align:center">'.($GetBranchReceived == 0 ? '' : number_format($GetBranchReceived,0)).'</td>';
                echo '<td style="text-align:center; color:red">'.($var == 0 ? '' : number_format($var,0)).'</td>';
            }

            $totalOutAll = $totalOutFixed + $totalbrout;
            $expectedstocks = ($beginQty + $production + $GetInventoryReturn) - $totalOutAll;
            $pcount = $pcountMap[$itemcode] ?? 0;
            $variance = $pcount - $expectedstocks;
            $varianceAmount = $unitprice * $variance;
            if ($variance < 0) {
                $variancestyle = 'color:#dc3545';
                $short = $variance;
                $over = 0;
            } else {
                $variancestyle = '';
                $short = 0;
                $over = $variance;
            }

            // totals columns
            echo '<td style="text-align:right">'.number_format($totalbrout,0).'</td>';
            echo '<td style="text-align:right">'.number_format($totalbrin,0).'</td>';
            echo '<td style="text-align:right">'.number_format($totalbrvariance,0).'</td>';

            echo '<td style="text-align:center">'.($GetInventoryCharges == 0 ? '' : number_format($GetInventoryCharges,0)).'</td>';
            echo '<td style="text-align:center">'.($GetInventoryBO == 0 ? '' : number_format($GetInventoryBO,0)).'</td>';
            echo '<td style="text-align:center">'.($GetInventoryDamage == 0 ? '' : number_format($GetInventoryDamage,0)).'</td>';
            echo '<td style="text-align:center">'.($GetInventoryRnd == 0 ? '' : number_format($GetInventoryRnd,0)).'</td>';
            echo '<td style="text-align:center">'.($GetInventoryComplementary == 0 ? '' : number_format($GetInventoryComplementary,0)).'</td>';

            echo '<td style="text-align:center">'.($totalOutFixed == 0 ? '' : number_format($totalOutFixed,0)).'</td>';
            echo '<td style="text-align:center">'.($totalOutAll == 0 ? '' : number_format($totalOutAll,0)).'</td>';

            echo '<td style="text-align:right">'.number_format($expectedstocks,0).'</td>';
            echo '<td style="text-align:right">'.number_format($pcount,0).'</td>';
            echo '<td style="text-align:right;'.$variancestyle.'">'.number_format($variance,0).'</td>';
            echo '<td style="text-align:right">'.number_format($varianceAmount,2).'</td>';
            echo '<td style="text-align:right">'.number_format($short,0).'</td>';
            echo '<td style="text-align:right">'.number_format($over,0).'</td>';

            echo '</tr>';
        } // end foreach items
    }
    ?>
    </tbody>
</table>
