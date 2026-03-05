<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$date = date("Y-m-d");

if(isset($_POST['limit']) && $_POST['limit'] !== "") {
    // Validate and sanitize user input for limit
    $limit = filter_var($_POST['limit'], FILTER_VALIDATE_INT);
    
    if ($limit !== false && $limit > 0) {
        // Set session and query limit
        $_SESSION['BINALOT_SHOW_LIMIT'] = $limit;
        $limitClause = "LIMIT $limit";
    } else {
        // Handle invalid input
        $limitClause = "";
        $_SESSION['BINALOT_SHOW_LIMIT'] = $limitClause;
        // You might want to set an error message here
    }
} else {
    $limitClause = "";
    $_SESSION['BINALOT_SHOW_LIMIT'] = $limitClause;
}

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['BINALOT_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['BINALOT_TRANSDATE'];
}

?>
<style>
.table td {
	padding:2px 5px 2px 5px !important;
}
</style>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
	<thead>
		<tr>
			<th style="width:50px;text-align:center">#</th>
			<th>REPORT DATE</th>
			<th>BRANCH</th>
			<th>CATEGORY</th>
			<th>ITEM DESCRIPTION</th>
			<th>ITEM CODE</th>
			<th>QUANTITY</th>
			<th>REMARKS</th>
			<th>CHECKED BY</th>
			<th>ACTIONS</th>
			
		</tr>
	</thead>
	<tbody>	
<?PHP
	$sqlQuery = "SELECT * FROM binalot_return WHERE report_date='$transdate' ORDER BY id DESC";
	$results = mysqli_query($db, $sqlQuery); 
	$n=0;   
    if ( $results->num_rows > 0 ) 
    {
    	while($RECVROW = mysqli_fetch_array($results))  
		{
			$n++;
			$id = $RECVROW['id'];
			
			$reportdate = $RECVROW['report_date'];
			$branch = $RECVROW['branch'];
			$category = $RECVROW['category'];
			$itemdescription = $RECVROW['item_description'];
			$itemcode = $RECVROW['item_code'];
			$qty = $RECVROW['quantity'];
			$remarks = $RECVROW['remarks'];
			$createddate = $RECVROW['created_date'];
			$createdby = $RECVROW['created_by'];
			$voidby = $RECVROW['void_by'];
			$status = $RECVROW['status'];
			$trstyle = $status == 0? '':'#f5d6d9';
			?>	
				<tr style="vertical-align:middle; background-color:<?php echo $trstyle?>">
					<td style="text-align:center; height: 25px;"><?php echo $n; ?></td>
					<td style="height: 25px"><?php echo $reportdate?></td>
					<td style="height: 25px"><?php echo $branch?></td>
					<td style="height: 25px"><?php echo $category?></td>
					<td style="height: 25px"><?php echo $itemdescription?></td>
					<td style="height: 25px"><?php echo $itemcode?></td>
					<td style="height: 25px"><?php echo $qty?></td>
					<td title="<?php echo $remarks?>" style="height: 25px">
						<?php echo (strlen($remarks) > 40) ? htmlspecialchars(substr($remarks, 0, 40)) . '...' : htmlspecialchars($remarks); ?>
					</td>
					<td style="height: 25px"><?php echo $createdby?></td>
					<td style="text-align:center">
						<?php
							if($function->GetPcountDataInventoryChecker($transdate,$db) == '1'){
						?>
							<span>Closed</span>
						<?php
							} else {
							
							
								if($status == '0'){
						?>
									<button class="btn btn-danger btn-sm" onclick="voidConfirm('<?php echo $id?>','<?php echo $itemcode?>')">
										Void this?
									</button>
						<?php
							} else {
						?>
									<span>Void by <?php echo $voidby?></span>
						<?php
								}
							}
						?>
					</td>
				</tr>
			<?php 
		} 
		
	} else { 
			?>
				<tr>
					<td colspan="9" style="text-align:center"><i class="fa fa-bell"></i>&nbsp;&nbsp;No Records</td>
				</tr>
			<?php 
			} 
			?>
	</tbody>
</table>
<div id="returnresult"></div>

<script>
function voidConfirm(rowid,itemcode){

	dialogue_confirm('System Message','Are you sure to void this?','warning','voidthisitem',rowid,itemcode);
}

function voidThis(rowid,itemcode)
{
	var mode = 'voidthisreturn';
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, rowid: rowid, itemcode: itemcode },
	function(data) {		
		$('#returnresult').html(data);
		rms_reloaderOff();
	});
}
