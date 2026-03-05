<script src="../Modules/Frozen_Dough_Management/scripts/script.js"></script>
<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['fds_userlevel'];

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['FDS_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['FDS_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
     $_SESSION['FDS_SHOW_LIMIT'] = $limitClause;
}
@$ord = $_POST['ord'];
@$_SESSION['FDS_ORD'] = $_POST['ord'];
if(isset($_POST['recipient']))
{
	$recipient = $_POST['recipient'];
	if($ord == 'Process Order')
	{
		$qr = "AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
	}
	else if($ord == 'Closed Order')
	{
		$qr = "AND recipient='$recipient' AND status='Closed'";
	}
	$_SESSION['fds_user_recipient'] = $recipient;
} else {
	if(isset($_SESSION['fds_user_recipient']))
	{
		$recipient = $_SESSION['fds_user_recipient'];
		if($ord == 'Process Order')
		{
			$qr = "AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
		}
		else if($ord == 'Closed Order')
		{
			$qr = "AND recipient='$recipient' AND status='Closed'";
		}
	} else {
		if(isset( $_POST['recipient']))
		{
			$recipient = $_POST['recipient'];
			if($ord == 'Process Order')
			{
				$qr = "AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
			}
			else if($ord == 'Closed Order')
			{
				$qr = "AND recipient='$recipient' AND status='Closed'";
			}
		} else {
			$recipient = $_POST['recipient'];
			if($ord == 'Process Order')
			{
				$qr = "AND recipient='$recipient' AND (status='Submitted' OR status='In-Transit')";
			}
			else if($ord == 'Closed Order')
			{
				$qr = "AND recipient='$recipient' AND status='Closed'";
			}
		}
	}
}
if(isset($_POST['search']) && $_POST['search'])
{
	$recipient = $_POST['recipient'];
	$search = $_POST['search'];
	if($ord == 'Process Order')
	{
		$qr = "AND control_no LIKE '%$search%' OR branch LIKE '%$search%' AND recipient='$recipient' AND status!='Closed'";
	}
	else if($ord == 'Closed Order')
	{
		$qr = " AND control_no LIKE '%$search%' OR branch LIKE '%$search%' AND recipient='$recipient' AND status='Closed'";
	}	
}

if(isset($_POST['datefrom']) && $_POST['dateto'])
{
	$datefrom = $_POST['datefrom'];
	$dateto = $_POST['dateto'];
	$qr = " AND trans_date BETWEEN '$datefrom' AND '$dateto'";
}

?>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th style="width:60px;text-align:center">#</th>
			<th style="width:300px">BRANCH</th>
			<th style="width:70px">REQUEST TYPE</th>
			<th style="width:250px">RECIPIENT</th>
			<th style="width:150px">CONTROL No.</th>
			<th>ORDER DATE</th>
			<th>DELIVERY DATE</th>
			<th style="width:100px">PICK LIST</th>
			<th style="width:100px">STATUS</th>
			<th style="width:150px">ACTIONS</th>
		</tr>
	</thead>		
	<tbody>
<?php
	$sqlQuery = "SELECT * FROM fds_order_request WHERE checked='Approved' AND approved='Approved' $qr ORDER BY trans_date DESC $limitClause";
	$results = mysqli_query($db, $sqlQuery);    
    if ( $results->num_rows > 0 ) 
    {
    	$n=0;
    	while($ROWS = mysqli_fetch_array($results))  
		{
			$n++;
			$control_no = $ROWS['control_no'];			
			
			if($ROWS['status'] == 'Closed')
			{
				$btn_text = "View Details";
				$btn_color = "btn-success";
			} else {
				$btn_text = "Process Order";
				$btn_color = "btn-info color-white";
			}
			if($ROWS['delivery_date'] == NULL && $ROWS['delivery_date'] == "")
			{
				$delivery_date = '';
			} else {
				$delivery_date = date("M. d, Y",strtotime($ROWS['delivery_date']));
			}
?>	
		<tr>
			<td style="text-align:center"><?php echo $n; ?></td>
			<td><?php echo $ROWS['branch']; ?></td>
			<td style="text-align:center"><?php echo $ROWS['form_type']; ?></td>
			<td><?php echo $ROWS['recipient']; ?></td>
			<td style="text-align:center"><?php echo $ROWS['control_no']; ?></td>
			<td><?php echo date("M. d, Y",strtotime($ROWS['trans_date'])); ?></td>
			<td><?php echo $delivery_date; ?></td>
		<?php 
			if($function->GetPickList($control_no,$db) == 1)
				{
					$td_style = 'style="background:#dafad1;text-align:center"';
					$td_icon = '<i class="fa-solid fa-circle-check color-green"></i>';
				} else {
					$td_style = '';
					$td_icon = '';
				}
			?>
			<td <?php echo $td_style; ?>><?php echo $td_icon; ?></td>
			<td><?php echo $ROWS['status']; ?></td>
			<td style="padding:3px !important">
				<button class="btn <?php echo $btn_color; ?> btn-sm w-100" onclick="Check_Access('<?php echo $control_no; ?>','p_write',orderProcess)"><?php echo $btn_text; ?></button>				
			</td>
		</tr>
	<?php } } else { ?>	
		<tr>
			<td colspan="10" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td>
		</tr>			
<?php } ?>
	</tbody>
</table>
<script>
function orderProcess(controlno)
{
	$.post("./Modules/Frozen_Dough_Management/includes/branch_order_process.php", { control_no: controlno },
	function(data) {		
		$('#smnavdata').html(data);
	});
}
</script>


