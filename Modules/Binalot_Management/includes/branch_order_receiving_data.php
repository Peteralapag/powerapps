<script src="../Modules/Binalot_Management/scripts/script.js"></script>
<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";

$function = new BINALOTFunctions();
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['binalot_userlevel'];

// Initialize variables
$limitClause = "";
$qr = "";
$recipient = "";

// Validate and set the limit if provided
if (isset($_POST['limit']) && $_POST['limit'] !== "") {
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    if ($limit !== false && $limit > 0) {
        $_SESSION['BINALOT_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        $_SESSION['BINALOT_SHOW_LIMIT'] = "";
    }
}

// Set order type and recipient filters
if (isset($_POST['ord'])) {
    $_SESSION['BINALOT_ORD'] = $_POST['ord'];
    $ord = $_POST['ord'];
}

if (isset($_POST['recipient'])) {
    $recipient = $_POST['recipient'];
    $_SESSION['binalot_user_recipient'] = $recipient;
} else {
    $recipient = $_SESSION['binalot_user_recipient'] ?? '';
}

// Determine the query condition based on the order type
if ($ord == 'Process Order') {
    $qr = "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
} elseif ($ord == 'Closed Order') {
    $qr = "checked='Approved' AND approved='Approved' AND recipient='$recipient' AND status='Closed'";
} elseif ($ord == 'Open Order') {
    $qr = "recipient='$recipient' AND status='Open'";
} elseif ($ord == 'Void Order') {
    $qr = "recipient='$recipient' AND status='Void'";
}


// Search functionality
if (isset($_POST['search']) && !empty($_POST['search'])) {
    $search = $db->real_escape_string($_POST['search']);
    $qr .= " AND (control_no LIKE '%$search%' OR branch LIKE '%$search%')";
}

// Date range filtering
if (isset($_POST['datefrom']) && isset($_POST['dateto'])) {
    $datefrom = $db->real_escape_string($_POST['datefrom']);
    $dateto = $db->real_escape_string($_POST['dateto']);
    $qr .= " AND trans_date BETWEEN '$datefrom' AND '$dateto'";
}

// Fetch data from the database
$sqlQuery = "SELECT * FROM binalot_order_request WHERE $qr ORDER BY trans_date DESC $limitClause";
$results = $db->query($sqlQuery);

?>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th style="width:60px;text-align:center">#</th>
            <th style="width:300px">BRANCH</th>
              
            <?php
            
				if($ord == 'Void Order'){
				            	
            	} else {
			?>
					<th style="width:70px">REQUEST TYPE</th>
            		<th style="width:250px">RECIPIENT</th>
            <?php            
            	
            	}
            ?>
            
            <th style="width:150px">CONTROL No.</th>
            <th>DATE CREATED</th>
            <th>ORDER DATE</th>
            
            <?php
            
				if($ord == 'Void Order'){
				
			?>
			
					<th>Remarks</th>
			
			<?php
            	
            	} else {
			?>
					<th>DELIVERY DATE</th>
            		<th style="width:100px">PICK LIST</th>
			<?php            
            	
            	}
            ?>
            
            <th style="width:100px">STATUS</th>
            <th style="width:150px">ACTIONS</th>
        </tr>
    </thead>        
    <tbody>
<?php
if ($results && $results->num_rows > 0) {
    $n = 0;
    while ($ROWS = $results->fetch_assoc()) {
        $n++;
        $control_no = $ROWS['control_no'];
        $branch = htmlspecialchars($ROWS['branch'], ENT_QUOTES, 'UTF-8');
        $status = $ROWS['status'];
        $orderremarks = $ROWS['order_remarks'];
        

        // Determine button text and color based on the order status
        $btn_text = ($status == 'Closed') ? "View Details" : "Process Order";
        $btn_color = ($status == 'Closed') ? "btn-success" : "btn-info color-white";

        // Format delivery date
        $delivery_date = (!empty($ROWS['delivery_date'])) ? date("M. d, Y", strtotime($ROWS['delivery_date'])) : '';

        // Check if the order has a pick list
        $hasPickList = $function->GetPickList($control_no, $db);
        $td_style = ($hasPickList == 1) ? 'style="background:#dafad1;text-align:center"' : '';
        $td_icon = ($hasPickList == 1) ? '<i class="fa-solid fa-circle-check color-green"></i>' : '';
?>
        <tr ondblclick="viewbranchorderreceiving('<?php echo $control_no?>','<?php echo $branch?>')">
            <td style="text-align:center"><?php echo $n; ?></td>
            <td><?php echo $branch?></td>
            
            
            <?php
            
            	if($ord == 'Void Order'){
            	            	
    			} else {
    			
    		?>
    		
    				<td style="text-align:center"><?php echo htmlspecialchars($ROWS['form_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            		<td><?php echo htmlspecialchars($ROWS['recipient'], ENT_QUOTES, 'UTF-8'); ?></td>    		
    		<?php
    			
    			}
            
            ?>  
            
            
            <td style="text-align:center"><?php echo htmlspecialchars($ROWS['control_no'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($ROWS['date_created'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo date("M. d, Y", strtotime($ROWS['trans_date'])); ?></td>
            
            <?php
            
            	if($ord == 'Void Order'){
            	
            ?>
            
            	<td title="<?php echo $orderremarks?>" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo $orderremarks?></td>
            
            <?php
            	
    			} else {
    			
    		?>
    		
    				<td><?php echo $delivery_date?></td>
            		<td <?php echo $td_style?>><?php echo $td_icon?></td>
    		
    		<?php
    			
    			}
            
            ?>
            
            
            
            
            
            
            <td><?php echo htmlspecialchars($ROWS['status'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td style="padding:3px !important">
            	<?php 
            		if($status !='Void'){
            	?>
                		<button class="btn <?php echo $btn_color; ?> btn-sm w-100" onclick="Check_Access('<?php echo htmlspecialchars($control_no, ENT_QUOTES, 'UTF-8'); ?>','p_write', orderProcess)"><?php echo $btn_text; ?></button>                
            	<?php
            		} else {
            			echo $ROWS['void_by'];
            		}
            	?>
            </td>
        </tr>
<?php 
    } 
} else { 
?>
        <tr>
            <td colspan="10" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td>
        </tr>            
<?php 
} 
?>
    </tbody>
</table>

<script>

function viewbranchorderreceiving(controlno,branch) {
	
	$('#modaltitle').html(branch+" | CONTROL NO. "+controlno);
	$.post("./Modules/Binalot_Management/apps/view_this_controlno.php", { controlno: controlno, branch: branch },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}

function orderProcess(controlno) {
    $.post("./Modules/Binalot_Management/includes/branch_order_process.php", { control_no: controlno },
    function(data) {        
        $('#smnavdata').html(data);
    });
}
</script>
