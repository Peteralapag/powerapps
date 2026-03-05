<script src="../Modules/DBC_Management/scripts/script.js"></script>
<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);    
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";

$function = new DBCFunctions();
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['dbc_userlevel'];

$limitClause = "";
$qr = "";
$recipient = "";

if (isset($_POST['limit']) && $_POST['limit'] !== "") {
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    if ($limit !== false && $limit > 0) {
        $_SESSION['DBC_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    }
}

$ord = $_POST['ord'] ?? $_SESSION['DBC_ORD'] ?? '';
$_SESSION['DBC_ORD'] = $ord;

$recipient = $_POST['recipient'] ?? $_SESSION['dbc_user_recipient'] ?? '';
$_SESSION['dbc_user_recipient'] = $recipient;

if ($ord == 'Process Order') {
    $qr = "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
} elseif ($ord == 'Closed Order') {
    $qr = "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND status='Closed'";
} elseif ($ord == 'Open Order') {
    $qr = "recipient='$recipient' AND status='Open'";
} elseif ($ord == 'Void Order') {
    $qr = "recipient='$recipient' AND status='Void'";
}

if (!empty($_POST['search'])) {
    $search = $db->real_escape_string($_POST['search']);
    $qr .= " AND (control_no LIKE '%$search%' OR branch LIKE '%$search%')";
}

if (!empty($_POST['datefrom']) && !empty($_POST['dateto'])) {
    $datefrom = $db->real_escape_string($_POST['datefrom']);
    $dateto = $db->real_escape_string($_POST['dateto']);
    $qr .= " AND trans_date BETWEEN '$datefrom' AND '$dateto'";
}

$sqlQuery = "
    SELECT 
        control_no, branch, form_type, recipient, date_created, trans_date,
        delivery_date, status, order_remarks, void_by 
    FROM dbc_order_request 
    WHERE $qr 
    ORDER BY trans_date DESC 
    $limitClause";

$results = $db->query($sqlQuery);
$rows = [];
$control_nos = [];

if ($results && $results->num_rows > 0) {
    while ($row = $results->fetch_assoc()) {
        $rows[] = $row;
        $control_nos[] = $db->real_escape_string($row['control_no']);
    }
}

$boCounts = [];
$plCounts = [];
$picklistStatus = [];

if (!empty($control_nos)) {
    $inList = "'" . implode("','", $control_nos) . "'";

    $boSql = "SELECT control_no, COUNT(*) as count FROM dbc_branch_order WHERE control_no IN ($inList) GROUP BY control_no";
    $boRes = $db->query($boSql);
    while ($row = $boRes->fetch_assoc()) {
        $boCounts[$row['control_no']] = $row['count'];
    }

    $plSql = "SELECT control_no, COUNT(*) as count FROM dbc_picklist WHERE control_no IN ($inList) GROUP BY control_no";
    $plRes = $db->query($plSql);
    while ($row = $plRes->fetch_assoc()) {
        $plCounts[$row['control_no']] = $row['count'];
    }

    foreach ($control_nos as $ctrl) {
        $bo = $boCounts[$ctrl] ?? 0;
        $pl = $plCounts[$ctrl] ?? 0;
        $picklistStatus[$ctrl] = ($bo == $pl && $bo > 0) ? 1 : 0;
    }
}
?>

<table style="width: 100%" class="table table-bordered table-striped table-hover">
<thead>
<tr>
    <th style="width:60px;text-align:center">#</th>
    <th style="width:300px">BRANCH</th>
    <?php if ($ord != 'Void Order'): ?>
        <th style="width:70px">REQUEST TYPE</th>
        <th style="width:250px">RECIPIENT</th>
    <?php endif; ?>
    <th style="width:150px">CONTROL No.</th>
    <th>DATE CREATED</th>
    <th>ORDER DATE</th>
    <?php if ($ord == 'Void Order'): ?>
        <th>Remarks</th>
    <?php else: ?>
        <th>DELIVERY DATE</th>
        <th style="width:100px">PICK LIST</th>
    <?php endif; ?>
    <th style="width:100px">STATUS</th>
    <th style="width:150px">ACTIONS</th>
</tr>
</thead>
<tbody>
<?php
if (!empty($rows)) {
    $n = 0;
    foreach ($rows as $row) {
        $n++;
        $control_no = htmlspecialchars($row['control_no'], ENT_QUOTES, 'UTF-8');
        $branch = htmlspecialchars($row['branch'], ENT_QUOTES, 'UTF-8');
        $status = $row['status'];
        $orderremarks = htmlspecialchars($row['order_remarks'], ENT_QUOTES, 'UTF-8');
        $btn_text = ($status == 'Closed') ? "View Details" : "Process Order";
        $btn_color = ($status == 'Closed') ? "btn-success" : "btn-info color-white";
        $delivery_date = !empty($row['delivery_date']) ? date("M. d, Y", strtotime($row['delivery_date'])) : '';
        $hasPickList = isset($picklistStatus[$control_no]) && $picklistStatus[$control_no] == 1;
        $td_style = $hasPickList ? 'style="background:#dafad1;text-align:center"' : '';
        $td_icon = $hasPickList ? '<i class="fa-solid fa-circle-check color-green"></i>' : '';
?>
<tr ondblclick="viewbranchorderreceiving('<?php echo $control_no ?>','<?php echo $branch ?>')">
    <td style="text-align:center"><?php echo $n; ?></td>
    <td><?php echo $branch ?></td>
    <?php if ($ord != 'Void Order'): ?>
        <td style="text-align:center"><?php echo htmlspecialchars($row['form_type'], ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo htmlspecialchars($row['recipient'], ENT_QUOTES, 'UTF-8'); ?></td>
    <?php endif; ?>
    <td style="text-align:center"><?php echo $control_no; ?></td>
    <td><?php echo htmlspecialchars($row['date_created'], ENT_QUOTES, 'UTF-8'); ?></td>
    <td><?php echo date("M. d, Y", strtotime($row['trans_date'])); ?></td>
    <?php if ($ord == 'Void Order'): ?>
        <td title="<?php echo $orderremarks ?>" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            <?php echo $orderremarks ?>
        </td>
    <?php else: ?>
        <td><?php echo $delivery_date ?></td>
        <td <?php echo $td_style ?>><?php echo $td_icon ?></td>
    <?php endif; ?>
    <td><?php echo htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></td>
    <td style="padding:3px !important">
        <?php if ($status != 'Void'): ?>
            <button class="btn <?php echo $btn_color; ?> btn-sm w-100" onclick="Check_Access('<?php echo $control_no; ?>','p_write', orderProcess)">
                <?php echo $btn_text; ?>
            </button>
        <?php else: ?>
            <?php echo htmlspecialchars($row['void_by'], ENT_QUOTES, 'UTF-8'); ?>
        <?php endif; ?>
    </td>
</tr>
<?php }
} else { ?>
<tr><td colspan="10" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td></tr>
<?php } ?>
</tbody>
</table>

<script>
function viewbranchorderreceiving(controlno, branch) {
    $('#modaltitle').html(branch + " | CONTROL NO. " + controlno);
    $.post("./Modules/DBC_Management/apps/view_this_controlno.php", { controlno: controlno, branch: branch },
        function(data) {
            $('#formmodal_page').html(data);
            $('#formmodal').show();
        });
}

function orderProcess(controlno) {
    $.post("./Modules/DBC_Management/includes/branch_order_process.php", { control_no: controlno },
        function(data) {
            $('#smnavdata').html(data);
        });
}
</script>
