<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
define("MODULE_NAME", "DBC_Seasonal_Branch_Ordering_System");
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/".MODULE_NAME."/class/Class.functions.php");
$function = new FDSFunctions;
$year = date("Y");
$app_user = $_SESSION['dbc_seasonal_branch_appnameuser'];

$controlno = $_POST['controlno'];
$branch = $_POST['branch'];


?>


<table style="width: 100%" class="table table-hover table-bordered">
    	<thead>
    		<tr>
    			<th>#</th>
		        <th>TRANSDATE</th>
		        <th>DR NO.</th>
		        <th>DRIVER NAME</th>
		        <th>PLATE NO.</th>
		        <th>STATUS</th>
    		</tr>
    	</thead>
    	<tbody>
    	
    		<?php
		    $stmt = $db->prepare("SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE branch = ? AND control_no = ? AND (status='In-Transit' OR status='Closed')");
		    $stmt->bind_param("ss", $branch, $controlno);
		    $stmt->execute();
		    $result = $stmt->get_result();
		
		    if ($result->num_rows > 0) {
		        $count = 0;
		        while ($row = $result->fetch_assoc()) {
		        	
		        	$count++;
		        	$rowid = $row['id'];
		        	$transdate = $row['trans_date'];
		        	$drno = $row['dr_number'];
		        	$status = $row['status'];
		        	$driver = $row['delivery_driver'];
		        	$plateno = $row['plate_number'];
		        	
		        	$receivestat = $function->drnumberDetectComplete($rowid, $db) == 1? 'red': '';
		        	

		        ?>
		        	
		        	<tr style="color:<?php echo $receivestat?>" ondblclick="viewThisTransaction('<?php echo $rowid?>','<?php echo $controlno?>')">
		        		<td><?php echo $count?></td>
		        		<td><?php echo $transdate?></td>
		        		<td><?php echo $drno?></td>
		        		<td><?php echo $driver?></td>
		        		<td><?php echo $plateno?></td>
						<td><?php echo $status?></td>
		        	</tr>            
		            
				<?php
		        }
		    } else {
		    
		    	?>
		    	
		        	<tr>
		        		<td colspan='5' style='text-align: center;'>No records found</td>
		        	</tr>
		        
		        <?php
		    }
		
		    $stmt->close();
		    $db->close();
		    ?>
    	
    	</tbody>
</table>

<script>

function viewThisTransaction(rowid,controlno){
	
	var module = 'DBC_Seasonal_Branch_Ordering_System';
	$.post("./Modules/" + module + "/includes/mrs_order_tracking_data_ver2.php", { requestid: rowid, controlno: controlno },
	function(data) {		
		$('#smnavdata').html(data);
	});	
}

</script>