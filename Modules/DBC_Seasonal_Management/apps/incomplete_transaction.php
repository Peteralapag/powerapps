<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

$control_no = $_POST['controlno'];

$request_id = $function->GetOrderStatus($control_no, 'request_id', $db);
$branch = $function->GetOrderStatus($control_no, 'branch', $db);
$trans_date = $function->GetOrderStatus($control_no, 'trans_date', $db);


$transactionid = $control_no.date('YmdHis');
?>




<table class="table table-bordered table-striped table-hover table-sm">
	<thead>
		<!--tr>
			<th colspan="7" style="text-align:center">
				<?php echo $branch?> | <?php echo $control_no?> | <?php echo $trans_date?>
			</th>
		</tr-->
		<tr>
			<th>#</th>
			<th>DATE</th>
			<th>DR #</th>
			<th>MRS No.</th>
			<th>REPORT DATE</th>
			<th>CREATED BY</th>
			<th>STATUS</th>
		</tr>
	</thead>
	<tbody>
        <?php
        $query = "
            SELECT * FROM dbc_seasonal_branch_mrs_transaction";

        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
        ?>
                <tr>
                <td><?php echo $count++?></td>
                <td><?php echo ''?></td>
                <td><?php echo ''?></td>
                <td><?php echo htmlspecialchars($row['trans_date'])?></td>
                <td><?php echo htmlspecialchars($row['created_by'])?></td>
                <td><?php echo $row['created_by']?></td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr><td colspan='5' class='text-center'>No records found.</td></tr>";
        <?php
        }
        ?>
    </tbody>
</table>

<div id="resultsThis"></div>
