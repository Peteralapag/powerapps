<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);  
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['dbc_seasonal_userlevel'];

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        $limitClause = "";
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limitClause;
    }
} else {
    $limitClause = "";
    $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limitClause;
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

.sticky-column-1 {
    left: 0;
    z-index: 1;
}
.sticky-column-1-data {
    left: 0;
    background:white !important;
}

.sticky-column-2 {
    left: 30px;
}
.sticky-column-2-data {
    left: 30px;
    background:white !important;
}

.sticky-column-3 {
    left: 308px;
}
.sticky-column-3-data {
    left: 308px;
    background:white !important;
}

</style>

<div class="sticky-column sticky-column-1" style="text-align:center">
    <span>Date From: <span style="text-decoration:underline"><?php echo $datefrom?></span> Date To: <span style="text-decoration:underline"><?php echo $dateto?></span></span>
</div>
<table class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th class="sticky-column sticky-column-1">#</th>
            <th class="sticky-column sticky-column-2">ITEM DESCRIPTION</th>
            <th class="sticky-column sticky-column-3">UOM</th>
            <?php $function->getIntoDbcTblBranchTableHeader($db)?>
            <td>TOTAL</td>
        </tr>
    </thead>
    <tbody>
        <?php $function->getIntoDbcTblitemslist($datefrom, $dateto, $db)?>
    </tbody>
</table>
