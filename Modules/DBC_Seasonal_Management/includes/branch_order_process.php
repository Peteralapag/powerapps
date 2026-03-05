<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);    
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";
$function = new DBCFunctions;

$currentMonthDays = date('t');
$date = date("Y-m-d");
$control_no = $_POST['control_no'];

$branch = $function->GetOrderStatus($control_no, 'branch', $db);
$trans_date = $function->GetOrderStatus($control_no, 'trans_date', $db);
$status = $function->GetOrderStatus($control_no, 'status', $db);
$order_received = $function->GetOrderStatus($control_no, 'order_received', $db);
$order_preparing = $function->GetOrderStatus($control_no, 'order_preparing', $db);
$logistics = $function->GetOrderStatus($control_no, 'logistics', $db);

$finalize = $function->GetOrderStatus($control_no, 'finalize', $db);
$request_id = $function->GetOrderStatus($control_no, 'request_id', $db);

$orpreparingval = $function->GetOrderStatus($control_no, 'order_preparing', $db);



$statusss = ($status === 'Open') ? 'none' : 'block';
$showdtls = ($logistics == 0) ? 1 : 0;

$orderTransit = $function->GetOrderStatus($control_no, 'order_transit', $db);
$finalize = $function->GetOrderStatus($control_no, 'finalize', $db);





?>

<style>
    .nav-button {display: flex; gap: 10px;}
    .process-header {display: flex; padding: 10px; background: #f1f1f1; border-bottom: 2px solid #aeaeae; color: #636363; align-items: center;}
</style>

<div class="process-header">
    <span>
        <strong>Branch:</strong> <?= htmlspecialchars($branch); ?> | 
        <strong>MRS No.:</strong> <?= htmlspecialchars($control_no); ?> | 
        <strong>Date:</strong> 
        <?= $trans_date ? date("F d, Y", strtotime($trans_date)) : "<p style='color:red'>Your submission is still pending at the branch.</p>"; ?>
    </span>
    <span style="margin-left: auto">
        <button class="btn btn-warning btn-sm" onclick="checkDBCRequest()"><i class="fa-solid fa-bells color-white"></i>&nbsp;&nbsp;Pending Request</button>
        <button class="btn btn-success btn-sm" onclick="loadPickList('<?= htmlspecialchars($control_no); ?>', '<?= htmlspecialchars($trans_date); ?>')"><i class="fa-solid fa-print"></i>&nbsp;&nbsp;Print All Picklist</button>
        <button class="btn btn-secondary btn-sm" onclick="load_data()"><i class="fa-solid fa-arrow-left"></i>&nbsp;&nbsp;Back</button>
    </span>
</div>

<?php // if ($showdtls) : ?>
    <div style="padding:10px; display: <?= $statusss; ?>">
        <div class="nav-button">
        
        		
        		<div id="orderdetailspsa"></div>
        		
        	
        	<?php if ($finalize == 0) : ?>
        		
        	
        	
        	
	            <?php if ($status === 'Void') : ?>
	                <img src="Modules/DBC_Seasonal_Management/images/void.png" alt="Void Status" style="width: 80px; height: 80px; margin-left: 20px;">
	            <?php else : ?>
	            
	            
	                
	                <?php if (!$order_received) : ?>
	                    <button class="btn btn-danger btn-sm" onclick="voidthis('<?= htmlspecialchars($control_no); ?>')"><i class="fa fa-thumbs-down"></i>&nbsp;&nbsp;Void This</button>
	                <?php endif; ?>
	                
	                <button <?= $order_received ? 'disabled' : ''; ?> class="btn btn-success btn-sm" onclick="receivedOrder('<?= htmlspecialchars($control_no); ?>')"><i class="fa-sharp fa-light fa-hands-holding-diamond"></i> Received Order</button>
	                
	                <?php if ($order_received) : ?>
	                    <button class="btn btn-danger btn-sm" onclick="savePickList('<?= htmlspecialchars($control_no); ?>')"><i class="fa-solid fa-plus color-black"></i> Add to Pick List</button>
	                    <?php if ($function->GetPickCount($branch, $control_no, $db) == 1) : ?>
	                        <button class="btn btn-info btn-sm color-white" onclick="loadPickListSingle('<?= htmlspecialchars($control_no); ?>')"><i class="fa-solid fa-print"></i> Print Picklist</button>
	                        <button class="btn btn-warning btn-sm color-white" onclick="prepareOrder('<?= htmlspecialchars($control_no); ?>')"><i class="fa-solid fa-basket-shopping"></i> Prepare Order</button>
							
							
							
							<?php if ($orpreparingval == 1) : ?>
								<button class="btn btn-secondary btn-sm color-white" onclick="viewMyRecord('<?= htmlspecialchars($control_no); ?>')"><i class="fa fa-eye" aria-hidden="true"></i> View History</button>
							<?php endif; ?>
	                        							
	                    <?php endif; ?>
	                <?php endif; ?>
	                
	            
	            
	            
	            <?php endif; ?>
        
			<?php endif; ?>					            
								            
								            
								            
        </div>
    </div>
<?php // else : ?>
    <!--div style="padding:10px; border-bottom:1px solid #aeaeae">
        <button id="showdtls" class="btn btn-info btn-sm" onclick="showDTLS()">Show Order List</button>
    </div-->
<?php // endif; ?>

<div class="order-details" id="orderdetails"></div>
<div class="order-details" id="drdetails"></div>
<div style="padding:10px" id="results"></div>

<script>

function loadOrderDetailsver2(control_no) {
    $.post("./Modules/DBC_Seasonal_Management/includes/order_details.php", { control_no }, function(data) {        
        $('#orderdetailspsa').html(data);
    });
}


function viewMyRecord(controlno){
	
	$('#modaltitle').html("VIEW HISTORY TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/includes/view_history_data.php", { controlno: controlno },
	function(data) {
		$('#orderdetails').html(data);		
	});
}

function voidthis(controlno) {
    executeAction("voidthisbranchorder", controlno, "Void this order...");
}

function checkDBCRequest() {    
    var recipient = $('#recipient').val();
    $('#modaltitle').html("RE-OPEN ORDER REQUEST");
    $.post("./Modules/DBC_Seasonal_Management/includes/pending_request_data.php", { recipient }, function(data) {        
        $('#formmodal_page').html(data);
        $('#formmodal').show();
    });
}

function loadPickList(controlno, transdate) {
    $('#modaltitlePDF').html("Print Pick Lists");
    $.post("./Modules/DBC_Seasonal_Management/includes/pick_list_loader.php", { transdate, control_no: controlno }, function(data) {        
        $('#pdfviewer_page').html(data);
        $('#pdfviewer').show();
    });
}

function createDR(controlno) {
    executeAction("forwardtologistics", controlno, "Generating Delivery Receipt...");
}

$(function() {
    if (<?= json_encode($showdtls); ?>) {
        if (<?= json_encode($order_preparing == 1); ?>) {
            loadOrderDetails('<?= htmlspecialchars($control_no); ?>');
        }
    }
    if (<?= json_encode($orderTransit == 1); ?>) {
        viewMyRecord('<?= htmlspecialchars($control_no); ?>');
    }    
    
});

function showDTLS() {
    if (<?= json_encode($order_preparing == 1); ?>) {
        loadOrderDetails('<?= htmlspecialchars($control_no); ?>');
    }
}

function loadOrderDetails(control_no) {
    $.post("./Modules/DBC_Seasonal_Management/includes/order_details.php", { control_no }, function(data) {        
        $('#orderdetails').html(data);
    });
}

function dr_details(control_no) {
    $.post("./Modules/DBC_Seasonal_Management/includes/delivery_details.php", { control_no }, function(data) {        
        $('#drdetails').html(data);
    });
}
function dr_details_ver2(rowid,controlno) {
    $.post("./Modules/DBC_Seasonal_Management/includes/delivery_details_ver2.php", { rowid: rowid, controlno: controlno }, function(data) {        
        $('#drdetails').html(data);
    });
}


function executeAction(mode, controlno, loadingText) {
    rms_reloaderOn(loadingText);
    setTimeout(function() {
        $.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode, control_no: controlno, module: sessionStorage.module_name }, function(data) {        
            $('#results').html(data);
            rms_reloaderOff();
        });
    }, 1000);
}
function savePickList(controlno)
{
	rms_reloaderOn("Saving to Pick List");
	setTimeout(function()
	{
		$.post("./Modules/DBC_Seasonal_Management/actions/picklist_process.php", { control_no: controlno },
		function(data) {		
			$('#results').html(data);
			rms_reloaderOff();
			orderProcess(controlno);
		});
	},500);
}

function loadPickListSingle()
{
	var mode = 'singleprint';
	var transdate = '<?php echo $trans_date; ?>';
	var branch = '<?php echo $branch ?>';
	var control_no = '<?php echo $control_no ?>';
	$('#modaltitlePDF').html("Print Pick Lists");
	$.post("./Modules/DBC_Seasonal_Management/includes/pick_list_loader.php", { mode: mode, transdate: transdate, branch: branch, control_no: control_no  },
	function(data) {		
		$('#pdfviewer_page').html(data);
		$('#pdfviewer').show();
	});
}

function receivedOrder(controlno)
{
	var mode = 'receivebranchorder';
	var module = sessionStorage.module_name;

	rms_reloaderOn('Receiving Order...');
	setTimeout(function()
	{
		$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, control_no: controlno, module: module },
		function(data) {		
			$('#results').html(data);
			rms_reloaderOff();
		});
	},1000);
}
function prepareOrder(controlno)
{
	var mode = 'preparebranchorder';
	var module = sessionStorage.module_name;
	rms_reloaderOn('Loading Order...');
	setTimeout(function()
	{
		$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, control_no: controlno, module: module  },
		function(data) {		
			$('#results').html(data);
			rms_reloaderOff();
		});
	},1000);
}

</script>
