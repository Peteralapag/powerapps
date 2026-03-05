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

$orderTransit = $function->GetOrderStatus($control_no, 'order_transit', $db);


if ($orderTransit == 0) :
?>


	<div style="display: flex; justify-content: space-between; margin-bottom: 5px; padding: 10px">
	    <button class="btn btn-primary btn-sm color-white" onclick="viewIncompleteTransaction('<?php echo $control_no?>','<?php echo $transactionid?>','<?php echo $request_id?>','<?php echo $branch?>')">
	        <i class="fa fa-list" aria-hidden="true"></i>
	        View Open transaction
	    </button>
	    <button class="btn btn-success btn-sm color-white" onclick="addTransaction('<?php echo $control_no?>','<?php echo $transactionid?>','<?php echo $request_id?>','<?php echo $branch?>')">
	        Create
	        Open Transaction
	    </button>
	</div>

<?php endif; 

if ($orderTransit == 1) :
	
	echo '
		<script>
			loadOrderDetailsver2("'.$control_no.'");
		</script>
	';
	
endif;	
?>

<table class="table table-bordered table-striped table-hover table-sm">
	<thead>
		<!--tr>
			<th colspan="7" style="text-align:center">
				<?php echo $branch?> | <?php echo $control_no?> | <?php echo $trans_date?>
			</th>
		</tr-->
		<tr>
			<th style="height: 22px">#</th>
			<th style="height: 22px">TRANSDATE</th>
			<th style="height: 22px">DR #</th>
			<th style="height: 22px">MRS NO.</th>
			<th style="height: 22px">REPORT DATE</th>
			<th style="height: 22px">CREATED BY</th>
			<th style="height: 22px">STATUS</th>
		</tr>
	</thead>
	<tbody>
        <?php
        $query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE control_no ='$control_no' AND status <> 'Open' ORDER BY trans_date DESC";

        $result = $db->query($query);
        if ($result && $result->num_rows > 0) {
            $count = 1;
            while ($row = $result->fetch_assoc()) {
            	
            	$rowid = $row['id'];
            	$status = $row['status'];
            	$endicatorStat = $status == 'Void'? 'table table-danger': '';
            	$controlno = $row['control_no'];
            	$onclickAttribute = $status == 'Void' ? '' : "onclick=\"viewdetails('$rowid','$controlno')\"";
            	
        ?>
                <tr class="<?php echo $endicatorStat ?>" <?php echo $onclickAttribute; ?>>
                <td><?php echo $count++?></td>
                <td><?php echo htmlspecialchars($row['trans_date'])?></td>
                <td><?php echo htmlspecialchars($row['dr_number'])?></td>
                <td><?php echo htmlspecialchars($controlno)?></td>
                <td><?php echo htmlspecialchars($row['created_by'])?></td>
                <td><?php echo $row['created_by']?></td>
                <td><?php echo $status?></td>
                </tr>
        <?php
            }
        } else {
        ?>
            <tr><td colspan='8' class='text-center' style="color:red">No records found.</td></tr>
        <?php
        }
        ?>
    </tbody>
</table>

<div id="resultsThis"></div>
<script>
function viewdetails(rowid,controlno){
	
	$.post("./Modules/DBC_Seasonal_Management/includes/delivery_details_ver2.php", { rowid: rowid, controlno: controlno }, function(data) {        
        $('#drdetails').html(data);
    });
	
}


function viewIncompleteTransaction(controlno,transactionid,requestid,branch){
	
	$('#modaltitle').html("INCOMPLETE TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/includes/view_incomplete_history_data.php", { controlno: controlno, transactionid: transactionid, requestid: requestid },
	function(data) {	
		$('#orderdetails').html(data);	
		$('#drdetails').empty();
	});
}

function addTransaction(controlno,transactionid,requestid,branch) {
	
	$('#formmodal').fadeOut();
	rms_reloaderOn('Generating...');	
	
	var mode = 'generateTransactionId';
	
	rms_reloaderOn('Loading...');	
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, controlno: controlno, branch: branch, transactionid: transactionid },
	function(data) {		
		$('#results').html(data);
		rms_reloaderOff();
	});	
}
</script>