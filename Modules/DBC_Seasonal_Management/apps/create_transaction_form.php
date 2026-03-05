<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Seasonal_Management/class/Class.functions.php");

$function = new DBCFunctions;

$date = date("Y-m-d");
$rowid = $_POST['rowid'];
$transdate = $_POST['transdate'];


$control_no = $function->GetOrderStatusSecondTable($rowid, 'control_no', $db);
$request_id = $function->GetOrderStatusSecondTable($rowid, 'request_id', $db);
$branch = $function->GetOrderStatusSecondTable($rowid, 'branch', $db);

?>

<style>
.custom-table-container {
    max-height: 70vh;
    overflow-y: auto;
    overflow-x: hidden;
    border: 1px solid #ccc;
    margin-top: 10px;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
}

.custom-table th, .custom-table td {
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
    font-size: 14px;
}

.custom-table th {
    background-color: #6c757d;
    color: #ffffff;
    font-weight: bold;
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
    position: sticky;
    top: 0;
    z-index: 1;
}
</style>


<div style="padding:10px">
	<button class="btn btn-primary btn-sm" onclick="viewIncompleteTransaction('<?php echo $control_no?>','','<?php echo $request_id?>','<?php echo $branch?>')"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
</div>

<div class="custom-table-container">
	<!--div style="float:right; margin-bottom:3px">
		<button type="button" class="btn btn-success btn-sm" onclick="generateThis('<?php echo $control_no?>','<?php echo $branch?>','<?php echo $transactionid?>')">Generate Transaction</button>
	</div-->
    <table class="custom-table">
        <thead>
            <tr>
                <th colspan="8">
                    INCOMPLETE TRANSACTION <?php echo $transdate?>
                </th>
            </tr>
            <tr>
                <th style="width:40px;">#</th>
                <th style="width:100px;">ITEM CODE</th>
                <th>ITEM DESCRIPTION</th>
                <th style="width:100px;">STOCK</th>
                <th style="width:100px;">UNITS (UOM)</th>
                <th style="width:100px;">REQ. QTY</th>
                <th style="width:100px;">REMAINING REQUEST QTY.</th>
                <th style="width:100px;">PARTIAL QTY.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sqlQuery = "
                SELECT item_code, item_description, uom, quantity
                FROM dbc_seasonal_branch_order
                WHERE control_no = '$control_no'
            ";

            $result = $db->query($sqlQuery);

            if ($result && $result->num_rows > 0) {
                $count = 1;
                while ($row = $result->fetch_assoc()) {
                    $item_code = $row['item_code'];
                    
                    $quantity = $row['quantity'];
                    
                  	$whquantity = $function->getvalueofseasonal('wh_quantity',$rowid,$item_code,$db);
                    $remaining = $quantity - $function->getSumRemainingBalance($control_no, $item_code, $db);
                    
                    $editablestat =  $remaining <= 0? 'false': 'true';
                    $editablebackground = $remaining <= '0'? '': '#f3eedf';
                    
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td id="idcode<?php echo $count?>"><?php echo htmlspecialchars($item_code); ?></td>
                        <td id="item<?php echo $count?>"><?php echo htmlspecialchars($row['item_description']); ?></td>
                        <td id="stock<?php echo $count?>"><?php echo htmlspecialchars($function->GetItemStock($item_code, $db)); ?></td>
                        <td id="uom<?php echo $count?>"><?php echo htmlspecialchars($row['uom']); ?></td>
                        <td id="req_quantity<?php echo $count?>"><?php echo $quantity?></td>
                        <td id="remaining_quantity<?php echo $count?>"><?php echo number_format($remaining,2)?></td>
                        <td id="partial_quantity<?php echo $count?>" contenteditable="<?php echo $editablestat?>" style="background:<?php echo $editablebackground?>" onfocusout="partialquantitysaving('<?php echo $count?>','<?php echo $rowid?>','<?php echo $remaining?>','<?php echo $whquantity?>')">
                        	<?php echo $whquantity?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='8'>No records found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div style="float:right; margin-top:15px">
    <button class="btn btn-success btn-sm" onclick="transactionProceed('<?php echo $rowid?>','<?php echo $control_no?>')"><i class="fa fa-bookmark" aria-hidden="true"></i> Transaction Proceed</button>
</div>

<div id="transactionResult"></div>

<script>
function transactionProceed(rowid,controlno){

	var mode = 'forwardtologisticsver2';
	var module = sessionStorage.module_name;
				
	rms_reloaderOn("Generating Delivery Receipt...");
	
	
	if (checkAllRemainingZero()) {
		var allRemaining = 1;
	} else {
		var allRemaining = 0;
	}		
	
	
	$.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { mode: mode, rowid: rowid, controlno: controlno, module: module, allRemaining: allRemaining },
	function(data) {		
		$('#orderdetails').html(data);
		rms_reloaderOff();	
	});
}


function checkAllRemainingZero() {
    let allZero = true;

    $('tr').each(function () {
        let remainingQuantity = parseFloat($(this).find('td[id^="remaining_quantity"]').text().trim());

        if (!isNaN(remainingQuantity) && remainingQuantity > 0) {
            allZero = false;
            return false;
        }
    });

    return allZero;
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



function reloadPageContent() {
    var pageUrl = "./Modules/DBC_Management/page_to_reload.php"; // Replace with the actual URL of the page or endpoint
    var targetElement = '#content-container'; // Replace with the ID or class of the container to update

    rms_reloaderOn("Reloading content...");

    $.get(pageUrl, function(data) {
        $(targetElement).html(data); // Update the container with the new content
        rms_reloaderOff();
    }).fail(function(xhr, status, error) {
        console.error("Error loading page content: " + status + " - " + error);
        alert("Failed to reload page content. Please try again.");
        rms_reloaderOff();
    });
}








////////////////////////////

function partialquantitysaving(params, rowid, remainingquantity, oldvalue) {
    var mode = 'partialquantitysaving';
    
    var idcode = $('#idcode' + params).text();
    var item = $('#item' + params).text();
    var uom = $('#uom' + params).text();
    var reqquantity = $('#req_quantity' + params).text();
    
    var stock = $('#stock' + params).text();

    var partialquantity = $('#partial_quantity' + params).text().trim();
    
    if (!/^\d+(\.\d+)?$/.test(partialquantity)) {
        app_alert("System Message", "Please enter valid numbers only", "warning");
        $('#partial_quantity' + params).text(oldvalue);
        calculateData(params,remainingquantity);
        return;
    }
    
    if (parseFloat(partialquantity) > parseFloat(stock)) {
        app_alert("System Message", "Partial quantity cannot be greater than stock quantity", "warning");
        $('#partial_quantity' + params).text(oldvalue);
        calculateData(params,remainingquantity);
        return;
    }
	
	if (parseFloat(partialquantity) > parseFloat(remainingquantity)) {
        app_alert("System Message", "Partial quantity cannot be greater than remaining quantity", "warning");
        $('#partial_quantity' + params).text(oldvalue);
        calculateData(params,remainingquantity);
        return;
    }

	
    rms_reloaderOn('Loading...');
    
    $.post("./Modules/DBC_Seasonal_Management/actions/actions.php", { 
        mode: mode, 
        rowid: rowid, 
        idcode: idcode,  
        item: item,  
        uom: uom, 
        reqquantity: reqquantity, 
        partialquantity: partialquantity 
    }, function(data) {        
        $('#transactionResult').html(data);
        calculateData(params,remainingquantity);
        
        rms_reloaderOff();
    });
}

function calculateData(params,remainingquantity) {

    var partialquantity = parseFloat($('#partial_quantity' + params).text().trim());


	if (isNaN(partialquantity)) {
		partialquantity = 0;
    }

    var result = remainingquantity - partialquantity;

    $('#remaining_quantity' + params).text(result.toFixed(2)); // Format to 2 decimal places
}





function viewIncompleteTransaction(controlno,transactionid,requestid,branch){
	
	$('#modaltitle').html("INCOMPLETE TRANSACTIONS");
	$.post("./Modules/DBC_Seasonal_Management/includes/view_incomplete_history_data.php", { controlno: controlno, transactionid: transactionid, requestid: requestid },
	function(data) {	
		$('#orderdetails').html(data);	
//		$('#formmodal_page').html(data);
//		$('#formmodal').show();
	});
}

$(function() {

    $('tr').each(function(index, row) {
        var params = index + 1;
        var remainingquantity = parseFloat($('#remaining_quantity' + params).text().trim());

        if (!isNaN(remainingquantity)) {
            calculateData(params, remainingquantity);
        }
    });
});


</script>
