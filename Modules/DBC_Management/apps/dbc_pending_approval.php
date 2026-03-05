<?php
ini_set('display_error', 1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

require_once($_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Management/class/Class.functions.php");

$function = new DBCFunctions;

$dateNow = $_SESSION['DBC_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];

$pendingApprovals = $function->getPendingApprovals($db);
?>

<style>
.form-wrapper { 
    width: 800px; 
    max-height: 500px; 
    overflow-y: auto; 
}
.table {
    width: 100%; 
    border-collapse: collapse;
}
.table th { 
    font-size: 14px !important; 
    position: sticky;
    top: 0;
    background-color: #f8f9fa;
    z-index: 10;
}
.table th, .table td {
    padding: 8px;
    border: 1px solid #dee2e6;
}
.psapointer {
	cursor:pointer;
}
</style>

<div class="form-wrapper">    
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="bg-success text-white">#</th>
                <th class="bg-success text-white">DATE</th>
                <th class="bg-success text-white">ITEM DESCRIPTION</th>
                <th class="bg-success text-white">CREATED BY</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($pendingApprovals)) {
            $counter = 1;
            foreach ($pendingApprovals as $approval) {
                $transdate = $approval['report_date'];
        ?>
                <tr ondblclick="searchMeNow('<?php echo $transdate?>')" class="psapointer">
                    <td><?php echo $counter++?></td>
                    <td><?php echo $transdate?></td>
                    <td><?php echo $approval['item_description']?></td>
                    <td><?php echo htmlspecialchars($approval['posted_by'])?></td>
                </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='4'>No pending approvals found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
<div id="results"></div>

<script>
function searchMeNow(params){
    var transdate = params;    
    $('#transdate').val(transdate);
    
    rms_reloaderOn('Loading...');
    $.post("./Modules/DBC_Management/includes/dbc_receiving_data.php", { transdate: transdate },
    function(data) {
        $('#smnavdata').html(data);
        $('#formmodal').fadeOut();
        rms_reloaderOff();
    });
}
</script>
