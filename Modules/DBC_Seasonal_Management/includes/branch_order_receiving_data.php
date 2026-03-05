<script src="../Modules/DBC_Seasonal_Management/scripts/script.js"></script>
<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);    
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";

$function = new DBCFunctions();
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['dbc_seasonal_userlevel'];

$limitClause = $qr = $recipient = "";

if (!empty($_POST['limit'])) {
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    if ($limit && $limit > 0) {
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        $_SESSION['DBC_SEASONAL_SHOW_LIMIT'] = "";
    }
}

$ord = $_SESSION['DBC_SEASONAL_ORD'] = $_POST['ord'] ?? $_SESSION['DBC_SEASONAL_ORD'] ?? '';
$recipient = $_SESSION['dbc_seasonal_user_recipient'] = $_POST['recipient'] ?? $_SESSION['dbc_seasonal_user_recipient'] ?? '';

$orderConditions = [
    'Process Order' => "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')",
    'Closed Order' => "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND status='Closed'",
    'Open Order' => "recipient='$recipient' AND status='Open'",
    'Void Order' => "recipient='$recipient' AND status='Void'",
];
$qr = $orderConditions[$ord] ?? '';

if (!empty($_POST['search'])) {
    $search = $db->real_escape_string($_POST['search']);
    $qr .= " AND (control_no LIKE '%$search%' OR branch LIKE '%$search%')";
}

if (!empty($_POST['datefrom']) && !empty($_POST['dateto'])) {
    $datefrom = $db->real_escape_string($_POST['datefrom']);
    $dateto = $db->real_escape_string($_POST['dateto']);
    $qr .= " AND trans_date BETWEEN '$datefrom' AND '$dateto'";
}

$sqlQuery = "SELECT * FROM dbc_seasonal_order_request WHERE $qr ORDER BY trans_date DESC $limitClause";
$results = $db->query($sqlQuery);
?>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th style="width:60px;text-align:center">#</th>
            <th style="width:300px">BRANCH</th>
            <th style="width:70px">REQUEST TYPE</th>
            <th style="width:250px">RECIPIENT</th>
            <th style="width:150px">CONTROL No.</th>
            <th>DATE CREATED</th>
            <th>ORDER DATE</th>
            <th>DELIVERY DATE</th>
            <th style="width:100px">PICK LIST</th>
            <th style="width:100px">STATUS</th>
            <th style="width:150px">ACTIONS</th>
        </tr>
    </thead>        
    <tbody>
<?php
if ($results && $results->num_rows > 0) {
    $n = 0;
    while ($row = $results->fetch_assoc()) {
        $n++;
        $control_no = htmlspecialchars($row['control_no'], ENT_QUOTES, 'UTF-8');
        $branch = htmlspecialchars($row['branch'], ENT_QUOTES, 'UTF-8');
        $form_type = htmlspecialchars($row['form_type'], ENT_QUOTES, 'UTF-8');
        $recipient = htmlspecialchars($row['recipient'], ENT_QUOTES, 'UTF-8');
        $date_created = htmlspecialchars($row['date_created'], ENT_QUOTES, 'UTF-8');
        $trans_date = date("M. d, Y", strtotime($row['trans_date']));
        $delivery_date = !empty($row['delivery_date']) ? date("M. d, Y", strtotime($row['delivery_date'])) : '';
        $status = htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8');
        $btn_text = ($status == 'Closed') ? "View Details" : "Process Order";
        $btn_color = ($status == 'Closed') ? "btn-success" : "btn-info color-white";

        // Check if the order has a pick list
        $hasPickList = $function->GetPickList($control_no, $db);
        $td_style = $hasPickList ? 'style="background:#dafad1;text-align:center"' : '';
        $td_icon = $hasPickList ? '<i class="fa-solid fa-circle-check color-green"></i>' : '';
        
        
        $doneservingstatus = '';
        if ($function->GetOrderStatus($control_no, 'finalize', $db) == 1 && $status == 'In-Transit') {
        	$doneservingstatus = 'color: red';
        }
        
        $orderType = '';
        if($function->detectItemIdIfExiist($control_no,'01683',$db) == 1){
        $orderType = 'CHIFFON';
        }
        

?>
        <tr style="<?php echo $doneservingstatus?>" ondblclick="viewbranchorderreceiving('<?php echo $control_no?>','<?php echo $branch?>')">
            <td style="text-align:center"><?= $n; ?></td>
            <td><?= $branch; ?></td>
            <td style="text-align:center"><?= $orderType?></td>
            <td><?= $recipient; ?></td>
            <td style="text-align:center"><?= $control_no; ?></td>
            <td><?= $date_created; ?></td>
            <td><?= $trans_date; ?></td>
            <td><?= $delivery_date; ?></td>
            <td <?= $td_style; ?>><?= $td_icon; ?></td>
            <td><?= $status; ?></td>
            <td style="padding:3px !important">
                <?php if ($status != 'Void'): ?>
                    <button class="btn <?= $btn_color; ?> btn-sm w-100" onclick="Check_Access('<?= $control_no; ?>', 'p_write', orderProcess)">
                        <?= $btn_text; ?>
                    </button>                
                <?php else: ?>
                    <?= htmlspecialchars($row['void_by'], ENT_QUOTES, 'UTF-8'); ?>
                <?php endif; ?>
            </td>
        </tr>
<?php 
    } 
} else { 
?>
        <tr>
            <td colspan="11" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td>
        </tr>            
<?php 
} 
?>
    </tbody>
</table>

<script>

function viewbranchorderreceiving(controlno,branch) {

	$('#modaltitle').html(branch+" | CONTROL NO. "+controlno);
	$.post("./Modules/DBC_Seasonal_Management/apps/view_this_controlno.php", { controlno: controlno, branch: branch },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});

}
function orderProcess(controlNo) {
    $.post("./Modules/DBC_Seasonal_Management/includes/branch_order_process.php", { control_no: controlNo }, function(data) {
        $('#smnavdata').html(data);
    });
}
</script>
