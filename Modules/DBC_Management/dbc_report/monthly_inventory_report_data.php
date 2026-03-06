<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SUMMARY_DATETO']   = $_POST['dateTo'];

$datefrom = $_POST['dateFrom'];
$dateto   = $_POST['dateTo'];

$startDate = new DateTime($datefrom);
$endDate   = new DateTime($dateto);
$interval  = $startDate->diff($endDate);
$numDays   = $interval->days + 1;

// build date range array (string Y-m-d)
$dateRange = [];
for ($loop = clone $startDate; $loop <= $endDate; $loop->modify('+1 day')) {
    $dateRange[] = $loop->format('Y-m-d');
}

$month_name = date('F j, Y', strtotime($datefrom)). ' - ' .date('F j, Y', strtotime($dateto));
$formattedDate = date('F j, Y', strtotime($datefrom));

/*
  Preload all required datasets with grouped queries:
  - beginning (previous day pcount)
  - pcount on dateto (physical count)
  - inventory in (dbc_fgts_pcount) grouped by item_code, trans_date
  - other data tables (return, damage, complementary, badorder, charges, rnd) grouped by item_code, report_date
  - transfer out (dbc_branch_order) grouped by item_code, delivery_date (only logistics=1 control_no)
*/

// 1) Beginning: previous day
$beg1 = date('Y-m-d', strtotime("$datefrom -1 day"));
$beginning = []; // [item_code] => beginning qty
$stmt = $db->prepare("SELECT item_code, SUM(p_count) AS qty FROM dbc_inventory_pcount WHERE trans_date = ? GROUP BY item_code");
if ($stmt) {
    $stmt->bind_param('s', $beg1);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $beginning[$r['item_code']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 2) Physical count on dateto (pcount)
$pcountMap = []; // [item_code] => pcount
$stmt = $db->prepare("SELECT item_code, SUM(p_count) AS qty FROM dbc_inventory_pcount WHERE trans_date = ? GROUP BY item_code");
if ($stmt) {
    $stmt->bind_param('s', $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $pcountMap[$r['item_code']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 3) Inventory IN (dbc_fgts_pcount) grouped by item_code, trans_date
$inMap = []; // [item_code][date] => qty
$stmt = $db->prepare("SELECT item_code, trans_date, SUM(pcount) AS qty FROM dbc_fgts_pcount WHERE trans_date BETWEEN ? AND ? GROUP BY item_code, trans_date");
if ($stmt) {
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $inMap[$r['item_code']][$r['trans_date']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 4) Other data tables: return, damage, complementary, badorder, charges, rnd
$otherTypes = ['return','damage','complementary','badorder','charges','rnd'];
$otherMaps = []; // $otherMaps['return'][item_code][date] = qty
foreach ($otherTypes as $type) {
    // table presumed to be `dbc_$type` with columns: item_code, report_date, quantity, status
    $table = "dbc_" . $type;
    $sql = "SELECT item_code, report_date, SUM(quantity) AS qty FROM $table WHERE report_date BETWEEN ? AND ? AND status = 0 GROUP BY item_code, report_date";
    $stmt = $db->prepare($sql);
    if (!$stmt) continue;
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $otherMaps[$type][$r['item_code']][$r['report_date']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 5) Transfer Out (dbc_branch_order) grouped by item_code, delivery_date
// include only control_no in order_request where logistics = '1'
$outMap = []; // [item_code][date] => qty
// We'll use a JOIN instead of subquery for performance
$sql = "SELECT bo.item_code, bo.delivery_date, SUM(bo.actual_quantity) AS qty
        FROM dbc_branch_order bo
        INNER JOIN dbc_order_request r ON bo.control_no = r.control_no AND r.logistics = '1'
        WHERE bo.delivery_date BETWEEN ? AND ?
        GROUP BY bo.item_code, bo.delivery_date";
$stmt = $db->prepare($sql);
if ($stmt) {
    $stmt->bind_param('ss', $datefrom, $dateto);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) {
        $outMap[$r['item_code']][$r['delivery_date']] = (float)$r['qty'];
    }
    $stmt->close();
}

// 6) Preload items list (with unit_price, desc, uom)
$items = []; // keyed by item_code
$sql = "SELECT item_code, item_description, uom, unit_price FROM wms_itemlist";
$res = $db->query($sql);
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $items[$row['item_code']] = [
            'desc' => $row['item_description'],
            'uom'  => $row['uom'],
            'price'=> isset($row['unit_price']) ? (float)$row['unit_price'] : 0
        ];
    }
}

// Done preloading - now build the HTML table output
ob_start();
?>

<style>
/* (keep your existing CSS) */
.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 2 !important;
    white-space: nowrap;
}
.sticky-column-1 { left: 0; z-index: 1; }
.sticky-column-1-data { left: 0; background:white !important; }
.sticky-column-2 { left: 35px; }
.sticky-column-2-data { left: 35px; background:white !important; }
.sticky-column-3 { left: 140px; }
.sticky-column-3-data { left: 140px; background:white !important; }
.sticky-column-4 { left: 478px; }
.sticky-column-4-data { left: 478px; background:white !important; }
.sticky-column-5{ left: 577px; }
.sticky-column-5-data { left: 577px; background:white !important; }
.sticky-header { position: -webkit-sticky; position: sticky; top: 0; background-color: #f5f5f5; z-index: 3 !important; }
</style>

<table style="width:100%" class="table table-bordered table-striped">
    <thead>
        <tr class="border-report-title">
            <th colspan="5" style="text-align:center" class="bg-primary sticky-column sticky-column-1">INVENTORY <?php echo strtoupper(htmlspecialchars($month_name));?></th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff" class="bg-secondary">INVENTORY IN</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff" class="bg-info">INVENTORY RETURN</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff" class="bg-success">INVENTORY DAMAGE</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff;background-color:#153515">INVENTORY COMPLEMENTARY</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff;background-color:#433409">INVENTORY BAD ORDER</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff;background-color:#344865">INVENTORY CHARGES</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff;background-color:#736333">INVENTORY R &amp; D</th>
            <th colspan="<?php echo count($dateRange)+1?>" style="text-align:center;color:#fff" class="bg-warning">INVENTORY OUT</th>
            <th colspan="8" style="text-align:center;color:#fff" class="bg-danger">TOTAL SUMMARY</th>
        </tr>
        <tr>
            <th class="bg-primary sticky-column sticky-column-1 sticky-header" style="width:50px">#</th>
            <th class="bg-primary sticky-column sticky-column-2 sticky-header" style="width:70px">ITEM CODE</th>
            <th class="bg-primary sticky-column sticky-column-3 sticky-header">ITEM NAME</th>
            <th class="bg-primary sticky-column sticky-column-4 sticky-header">UOM</th>
            <th class="bg-primary sticky-column sticky-column-5 sticky-header" style="width:70px">BEGINNING</th>
            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#707070">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#707070">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#0dcaf0">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#0dcaf0">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#198754">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#198754">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#153515">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#153515">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#433409">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#433409">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#344865">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#344865">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px; background-color:#736333">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th style="width:70px;text-align:center; background-color:#736333">TOTAL</th>

            <?php foreach ($dateRange as $d): ?>
                <th style="text-align:center;width:50px" class="bg-warning">Day <?php echo (int)date('j', strtotime($d)); ?></th>
            <?php endforeach; ?>
            <th  class="bg-warning">TOTAL OUT</th>
            <th class="bg-danger">EXPTD. STKS</th>
            <th class="bg-danger">PHY. COUNT</th>
            <th class="bg-danger">VARIANCES</th>
            <th class="bg-danger">UNIT PRICE</th>
            <th class="bg-danger">VAR. AMOUNT</th>
            <th class="bg-danger">SHORT</th>
            <th class="bg-danger">OVER</th>
        </tr>
    </thead>
    <tbody>
    <?php
    if (empty($items)) {
        echo '<tr><td colspan="50" style="text-align:center;color:#fff" class="bg-primary"><i class="fa fa-bell color-orange"></i> No Record(s) found.</td></tr>';
    } else {
        $i = 0;
        // iterate items
        foreach ($items as $itemcode => $meta) {
            $i++;
            $unitprice = $meta['price'];
            $itemdesc  = $meta['desc'];
            $uom       = $meta['uom'];
            // beginning from preloaded map
            $beginQty = $beginning[$itemcode] ?? 0;

            // initialize totals
            $totals = [
                'in' => 0, 'return' => 0, 'damage' => 0,
                'complementary' => 0, 'badorder' => 0,
                'charges' => 0, 'rnd' => 0, 'out' => 0
            ];

            echo '<tr>';
            echo '<td class="sticky-column sticky-column-1-data" style="text-align:center">'.$i.'</td>';
            echo '<td class="sticky-column sticky-column-2-data" style="text-align:center">'.htmlspecialchars($itemcode).'</td>';
            echo '<td class="sticky-column sticky-column-3-data">'.htmlspecialchars($itemdesc).'</td>';
            echo '<td class="sticky-column sticky-column-4-data" style="text-align:center">'.htmlspecialchars($uom).'</td>';
            echo '<td class="sticky-column sticky-column-5-data" style="text-align:right;">'.number_format($beginQty,2).'</td>';

            // IN cells
            foreach ($dateRange as $day) {
                $val = $inMap[$itemcode][$day] ?? 0;
                $totals['in'] += $val;
                echo '<td style="text-align:right" class="psahovin" ondblclick="timeInDetails(\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['in'],2).'</td>';

            // RETURN
            foreach ($dateRange as $day) {
                $val = $otherMaps['return'][$itemcode][$day] ?? 0;
                $totals['return'] += $val;
                echo '<td style="text-align:right" class="psahovreturn" ondblclick="otherDetails(\'return\',\'INVENTORY RETURN\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['return'],2).'</td>';

            // DAMAGE
            foreach ($dateRange as $day) {
                $val = $otherMaps['damage'][$itemcode][$day] ?? 0;
                $totals['damage'] += $val;
                echo '<td style="text-align:right" class="psahovdamage" ondblclick="otherDetails(\'damage\',\'INVENTORY DAMAGE\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['damage'],2).'</td>';

            // COMPLEMENTARY
            foreach ($dateRange as $day) {
                $val = $otherMaps['complementary'][$itemcode][$day] ?? 0;
                $totals['complementary'] += $val;
                echo '<td style="text-align:right" class="psahovcomplementary" ondblclick="otherDetails(\'complementary\',\'INVENTORY COMPLEMENTARY\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['complementary'],2).'</td>';

            // BADORDER
            foreach ($dateRange as $day) {
                $val = $otherMaps['badorder'][$itemcode][$day] ?? 0;
                $totals['badorder'] += $val;
                echo '<td style="text-align:right" class="psahovbadorder" ondblclick="otherDetails(\'badorder\',\'INVENTORY BAD ORDER\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['badorder'],2).'</td>';

            // CHARGES
            foreach ($dateRange as $day) {
                $val = $otherMaps['charges'][$itemcode][$day] ?? 0;
                $totals['charges'] += $val;
                echo '<td style="text-align:right" class="psahovcharges" ondblclick="otherDetails(\'charges\',\'INVENTORY CHARGES\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['charges'],2).'</td>';

            // RND
            foreach ($dateRange as $day) {
                $val = $otherMaps['rnd'][$itemcode][$day] ?? 0;
                $totals['rnd'] += $val;
                echo '<td style="text-align:right" class="psahovrnd" ondblclick="otherDetails(\'rnd\',\'INVENTORY R&D\',\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }
            echo '<td style="text-align:right">'.number_format($totals['rnd'],2).'</td>';

            // OUT
            foreach ($dateRange as $day) {
                $val = $outMap[$itemcode][$day] ?? 0;
                $totals['out'] += $val;
                echo '<td style="text-align:right" class="psahovout" ondblclick="timeOutDetails(\''.addslashes($itemcode).'\',\''.$day.'\')">'.number_format($val,2).'</td>';
            }

            // expected stocks calculations
            $pcount = $pcountMap[$itemcode] ?? 0;
            $expectedstocks = ($beginQty + $totals['in'] + $totals['return'])
                - $totals['damage'] - $totals['complementary']
                - $totals['badorder'] - $totals['charges']
                - $totals['rnd'] - $totals['out'];

            $variance = $pcount - $expectedstocks;
            $varianceAmount = $unitprice * $variance;
            $short = $variance < 0 ? $variance : 0;
            $over  = $variance > 0 ? $variance : 0;
            $variancestyle = $variance < 0 ? 'color:#dc3545' : '';

            echo '<td style="text-align:right">'.number_format($totals['out'],2).'</td>';
            echo '<td style="text-align:right">'.number_format($expectedstocks,2).'</td>';
            echo '<td style="text-align:right">'.number_format($pcount,2).'</td>';
            echo '<td style="text-align:right;'.$variancestyle.'">'.number_format($variance,2).'</td>';
            echo '<td style="text-align:right">'.number_format($unitprice,2).'</td>';
            echo '<td style="text-align:right">'.number_format($varianceAmount,2).'</td>';
            echo '<td style="text-align:right">'.number_format($short,2).'</td>';
            echo '<td style="text-align:right">'.number_format($over,2).'</td>';
            echo '</tr>';
        } // end foreach items
    }
    ?>
    </tbody>
</table>

<script>
function otherDetails(table,title,itemcode,transdate){
    const dateStr = transdate;
    const dateObj = new Date(dateStr);
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const year = dateObj.getFullYear();
    const month = months[dateObj.getMonth()];
    const dayOfMonth = dateObj.getDate();
    const dateInWords = `${month} ${dayOfMonth}, ${year}`;
    $('#modaltitle').html(title+" "+dateInWords);
    $.post("./Modules/DBC_Management/apps/inventory_others_form.php", { itemcode: itemcode, transdate: transdate, table: table },
    function(data) {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });
}
function timeOutDetails(itemcode,transdate){
    const dateStr = transdate;
    const dateObj = new Date(dateStr);
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const year = dateObj.getFullYear();
    const month = months[dateObj.getMonth()];
    const dayOfMonth = dateObj.getDate();
    const dateInWords = `${month} ${dayOfMonth}, ${year}`;
    $('#modaltitle').html("INVENTORY OUT "+dateInWords);
    $.post("./Modules/DBC_Management/apps/inventory_out_form.php", { itemcode: itemcode, transdate: transdate },
    function(data) {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });
}
function timeInDetails(itemcode,transdate){
    const dateStr = transdate;
    const dateObj = new Date(dateStr);
    const months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    const year = dateObj.getFullYear();
    const month = months[dateObj.getMonth()];
    const dayOfMonth = dateObj.getDate();
    const dateInWords = `${month} ${dayOfMonth}, ${year}`;
    $('#modaltitle').html("INVENTORY IN "+dateInWords);
    $.post("./Modules/DBC_Management/apps/inventory_in_form.php", { itemcode: itemcode, transdate: transdate },
    function(data) {
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });
}
</script>

<?php
// flush buffer
echo ob_get_clean();
