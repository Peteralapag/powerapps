<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

$controlno = $_POST['controlno'];

$requestid = $function->GetOrderStatus($controlno, 'request_id', $db);



$branch = $function->GetOrderStatus($controlno, 'branch', $db);
$trans_date = $function->GetOrderStatus($controlno, 'trans_date', $db);

?>


<style>

.cursorpointer {
	cursor:pointer;
}
</style>
<div style="padding:10px">
	<button class="btn btn-primary btn-sm" onclick="viewMyRecord('<?php echo $controlno?>')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
</div>

<div style="max-height: 80vh; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">

	<table class="table table-bordered table-striped table-hover table-sm">
		<thead>
			<tr>
				<th colspan="7" style="text-align:center">
					INCOMPLETE TRANSACTIONS LIST
				</th>
			</tr>
			<tr>
				<th>#</th>
				<th>TRANSDATE</th>
				<th>MRS NO.</th>
				<th>CREATED BY</th>
				<th>DATE CREATED</th>
				<th>ACTION</th>
			</tr>
		</thead>
		<tbody>
	        <?php
	        $query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE control_no = '$controlno' AND status = 'Open' ORDER BY id DESC";
	
	        $result = $db->query($query);
	        if ($result && $result->num_rows > 0) {
	            $count = 1;
	            while ($row = $result->fetch_assoc()) {
	            	
	            	$rowid = $row['id'];
	            	$transdate = $row['trans_date'];
	            	$controlno = $row['control_no'];
	            	$createdby = $row['created_by'];
	            	$status = $row['status'];
	            	$datecreated = $row['date_created'];
	            	
	            	$transdate = $row['trans_date'];
	            	
	        ?>
	                <tr>
		                <td><?php echo $count++?></td>
		                <td><?php echo $transdate?></td>
		                <td><?php echo $controlno?></td>
		                <td><?php echo $createdby?></td>
		                <td><?php echo $datecreated?></td>
		                <td>
		                	<button class="btn btn-primary btn-sm color-white" onclick="viewincompletedata('<?php echo $rowid?>','<?php echo $transdate?>')">Details</button>
							<!--button class="btn btn-danger btn-sm color-white" onclick="voidTransaction('<?php echo $rowid?>','<?php echo $controlno?>')">Void</button-->

		                </td>
	                </tr>
	        <?php
	            }
	        } else {
	        ?>
	            <tr><td colspan='7' class='text-center' style="color:red">No records found.</td></tr>
	        <?php
	        }
	        ?>
	    </tbody>
	</table>




</div>


<div id="resultsThis"></div>
<script>

function voidTransaction(rowid,controlno){
	mode = 'voidthisseasonaltransaction';
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, rowid: rowid, controlno: controlno }, function(data) {        
        $('#results').html(data);
        rms_reloaderOff();
    });
}

function viewincompletedata(rowid,transdate){


	$('#modaltitle').html("CREATE TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/apps/create_transaction_form.php", { rowid: rowid, transdate: transdate },
	function(data) {		
		$('#orderdetails').html(data);
	});

}

function viewIncompleteTransaction(controlno,transactionid,requestid,branch){
	
	$('#modaltitle').html("INCOMPLETE TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/apps/incomplete_transaction.php", { controlno: controlno, transactionid: transactionid, requestid: requestid },
	function(data) {	
		$('#orderdetails').html(data);	
	});
}

function confirming(controlno,transactionid,requestid){

	app_confirm("System Message","Are you sure to add transaction?","warning");
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

function viewIncompleteTransaction(controlno,transactionid,requestid,branch){
	
	$('#modaltitle').html("INCOMPLETE TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/includes/view_incomplete_history_data.php", { controlno: controlno, transactionid: transactionid, requestid: requestid },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}


function viewMyRecord(controlno){

	$('#modaltitle').html("VIEW HISTORY TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/includes/view_history_data.php", { controlno: controlno },
	function(data) {
		$('#orderdetails').html(data);		
	});
}

</script>