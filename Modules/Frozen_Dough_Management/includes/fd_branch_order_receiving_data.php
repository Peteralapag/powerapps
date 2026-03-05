<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);  
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['fds_userlevel'];

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input for limit
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['FDS_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['FDS_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
    $_SESSION['FDS_SHOW_LIMIT'] = $limitClause;
}

// Sanitize and set default values for datefrom and dateto
$datefrom = isset($_POST['datefrom']) ? mysqli_real_escape_string($db, $_POST['datefrom']) : $date;
$dateto = isset($_POST['dateto']) ? mysqli_real_escape_string($db, $_POST['dateto']) : $date;

?>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th style="width:60px;text-align:center">#</th>
            <th>Report Date</th>
            <th style="width:300px">Item Code</th>
            <th>Description</th>
            <th>Units (UOM)</th>
            <th>Quantity</th>
        </tr>
    </thead>        
    <tbody>
<?php
    $sqlQuery = "SELECT SUM(quantity) AS qty, item_code, trans_date, item_description, uom FROM fds_branch_order WHERE trans_date BETWEEN '$datefrom' AND '$dateto' GROUP BY item_code, trans_date";
    $results = mysqli_query($db, $sqlQuery);    
    if ($results && mysqli_num_rows($results) > 0) {
        $n = 0;
        while ($row = mysqli_fetch_array($results)) {
            $n++;
?>  
        <tr>
            <td style="text-align:center"><?php echo $n; ?></td>
            <td><?php echo $row['trans_date']; ?></td>
            <td><?php echo $row['item_code']; ?></td>
            <td><?php echo $row['item_description']; ?></td>
            <td><?php echo $row['uom']; ?></td>
            <td><?php echo $row['qty']; ?></td>
        </tr>
<?php
        }
    } else { 
?>  
        <tr>
            <td colspan="6" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td>
        </tr>           
<?php 
    } 
?>
    </tbody>
</table>
