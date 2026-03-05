<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);  
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['dbc_userlevel'];

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        $_SESSION['DBC_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        $limitClause = "";
        $_SESSION['DBC_SHOW_LIMIT'] = $limitClause;
    }
} else {
    $limitClause = "";
    $_SESSION['DBC_SHOW_LIMIT'] = $limitClause;
}

$datefrom = isset($_POST['datefrom']) ? mysqli_real_escape_string($db, $_POST['datefrom']) : $date;
$dateto = isset($_POST['dateto']) ? mysqli_real_escape_string($db, $_POST['dateto']) : $date;
?>

<style>
.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 1 !important;
}
.sticky-column-1 { left: 0; z-index: 1; }
.sticky-column-1-data { left: 0; background:white !important; }
.sticky-column-2 { left: 35px; }
.sticky-column-2-data { left: 35px; background:white !important; }
.sticky-column-3 { left: 371px; }
.sticky-column-3-data { left: 371px; background:white !important; }
</style>



<?php

$dupQuery = "
    SELECT control_no, COUNT(*) as total
    FROM dbc_order_request
    WHERE trans_date BETWEEN '$datefrom' AND '$dateto'
    GROUP BY control_no
    HAVING COUNT(*) > 1
";
$dupResult = mysqli_query($db, $dupQuery);

$duplicates = [];
while ($row = mysqli_fetch_assoc($dupResult)) {
    $duplicates[] = $row['control_no'];
}

if (!empty($duplicates)) {
    echo "<div style='color:red; font-weight:bold; margin:10px 0;'>
            ⚠ Naay duplicate control_no: " . implode(', ', $duplicates) . "
          </div>";
}
?>





<div class="sticky-column sticky-column-1" style="text-align:center">
    <span>Date From: <span style="text-decoration:underline"><?php echo $datefrom?></span> Date To: <span style="text-decoration:underline"><?php echo $dateto?></span></span>
</div>

<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th class="sticky-column sticky-column-1">#</th>
            <th class="sticky-column sticky-column-2">ITEM DESCRIPTION</th>
            <th class="sticky-column sticky-column-3">UOM</th>
            <?php $function->getIntoDbcTblBranchTableHeader($db) ?>
            <td>TOTAL</td>
        </tr>
    </thead>
    <tbody>
        <?php
        // Optimized item list display
        $branches = [];
        $branchQuery = "SELECT branch FROM dbc_tbl_branch WHERE status = 1";
        $branchResult = mysqli_query($db, $branchQuery);
        while ($row = mysqli_fetch_assoc($branchResult)) {
            $branches[] = $row['branch'];
        }

        $dataMap = [];
        $query = "
            SELECT 
                b.item_code, 
                b.branch, 
                SUM(b.quantity) AS qty
            FROM dbc_branch_order b
            JOIN dbc_order_request o ON b.control_no = o.control_no
            WHERE b.trans_date BETWEEN '$datefrom' AND '$dateto'
              AND o.status != 'Open' AND o.status != 'Void'
            GROUP BY b.item_code, b.branch
        ";
        $result = mysqli_query($db, $query);
        while ($row = mysqli_fetch_assoc($result)) {
            $item = $row['item_code'];
            $branch = $row['branch'];
            $qty = floatval($row['qty']);
            $dataMap[$item][$branch] = $qty;
        }

        $items = mysqli_query($db, "SELECT * FROM dbc_itemlist WHERE active = 1 ORDER BY ordered ASC");
        $i = 1;
        $totalPerBranch = array_fill_keys($branches, 0);

        while ($item = mysqli_fetch_assoc($items)) {
            $itemcode = $item['item_code'];
            $itemname = $item['item_description'];
            $uom = $item['uom'];
            $totalItemQty = 0;

            echo '<tr>';
            echo '<td class="sticky-column sticky-column-1-data" style="text-align:center">'.$i.'</td>';
            echo '<td class="sticky-column sticky-column-2-data">'.htmlspecialchars($itemname).'</td>';
            echo '<td class="sticky-column sticky-column-3-data" style="text-align:center">'.htmlspecialchars($uom).'</td>';

            foreach ($branches as $branch) {
                $qty = isset($dataMap[$itemcode][$branch]) ? $dataMap[$itemcode][$branch] : 0;
                $totalItemQty += $qty;
                $totalPerBranch[$branch] += $qty;
                echo '<td style="text-align:center">'.number_format($qty, 2).'</td>';
            }

            echo '<td style="text-align:center">'.number_format($totalItemQty, 2).'</td>';
            echo '</tr>';
            $i++;
        }

        echo '<tr>';
        echo '<td colspan="3" class="sticky-column sticky-column-1-data" style="text-align:center"><strong>TOTAL</strong></td>';
        foreach ($branches as $branch) {
            echo '<td style="text-align:center"><strong>'.number_format($totalPerBranch[$branch], 2).'</strong></td>';
        }
        echo '<td style="text-align:center"><strong>'.number_format(array_sum($totalPerBranch), 2).'</strong></td>';
        echo '</tr>'; ?>
    </tbody>
</table>
