<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
define("MODULE_NAME", "DBC_Seasonal_Branch_Ordering_System");
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/".MODULE_NAME."/class/Class.functions.php");
$function = new FDSFunctions;

$control_no = $_POST['controlno'];
$rowidmain = $_POST['rowidmain'];
$mrs_no = $control_no;

$branch = $function->mainorderTable($rowidmain,'branch',$db);
$trans_date = $function->mainorderTable($rowidmain,'trans_date',$db);

?>
<style>
.mrs-wrappers {width: 8.5in;border:1px solid #aeaeae;padding:0.2in;}
.table-thth th{background: #838383;color: #fff;font-weight:normal !important;padding:5px;text-align:center;}
.table-tdth {}
.table td {padding:4px;font-size:12px;font-weight: normal;border:0 !important;}
.table-hover:hover {background: #f7f8eb;}
.tabletas th {border: 1px solid #232323;font-size:11px;font-weight: 600;padding:2px;text-align:center;}
.tabletas td {border: 1px solid #232323;font-size:11px;padding:2px;font-weight: normal;}
.form-footer td {text-align: center;font-size: 12px;}
.btn-thin {padding: 0 !important}
.approval-text {font-weight: 600;font-size:18px;}
.names {font-weight: 600;}
.dates {font-style:italic;color: #aeaeae;}
.button {padding: 2px 7px 2px 7px !important;}
.order-circle {display: flex;width:50px;height:50px;border:5px solid green;border-radius:50%;justify-content: center;align-items: center;font-size:24px}
.order-circle-gray {display: flex;width:50px;height:50px;border:5px solid #aeaeae;border-radius:50%;justify-content: center;align-items: center;font-size:24px}
.status-text {text-align:center;font-size: 11px}
.status-date {text-align:center;text-align:center;font-style:italic;color:#AEAEAE;}
.icontext-color { color: #aeaeae; }
.bar-color {border: 2px solid green}
.bar-color-gray {border: 2px solid #aeaeae}
.psamouse {
	cursor:pointer;
}
</style>


<div style="padding:10px">
    <div class="mrs-wrappers" style="margin:0 auto; margin-top:10px; display: flex; align-items: center; justify-content: space-between;">
        <!-- Back button -->
        <button type="button" class="btn btn-primary btn-sm" onclick="viewOrderRecord('<?php echo $control_no; ?>','<?php echo $rowidmain?>')">
            <i class="fa fa-undo" aria-hidden="true"></i> Main
        </button>

        <!-- Dropdown container aligned to the right -->
        <span style="display: inline-flex; align-items: center; gap: 10px;">
            <label for="dr-number-select" style="margin: 0;">SELECT DR #:</label>
            <select id="dr-number-select" name="dr_number" class="form-control form-control-sm" style="width: 200px;" onchange="loadThisDr(this.value,'<?php echo $rowidmain?>')">
                <option value="">-- Select DR Number --</option>
                <?php
                $drNumbers = $function->selectDrNo($control_no, $branch, $db);
                if (!empty($drNumbers)) {
                    foreach ($drNumbers as $drNumber) {
                        echo '<option value="' . htmlspecialchars($drNumber) . '">' . htmlspecialchars($drNumber) . '</option>';
                    }
                } else {
                    echo '<option value="">No DR numbers available</option>';
                }
                ?>
            </select>
        </span>
    </div>
</div>



<!-- ############################################################ -->
<div class="mrs-wrappers" id="mrswrappers" style="margin:0 auto">	
	<table style="width: 100%;font-family:sans-serif" class="table">
		<tr class="table-thth">
			<th colspan="7">SUMMARY OF MATERIAL REQUISITION FORM</th>
		</tr>
		<tr>
			<td style="width:120px">Requesting Section/Branch:</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style="border-bottom:1px solid #232323 !important;width:400px"><?php echo $branch;?></td>
			<td style="width:">&nbsp;</td>
			<td>Control No.:</td>
			<td style="width:3px !important;"></td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center;color:red;font-weight:bold"><?php echo $mrs_no;?></td>
		</tr>
		<tr>
			<td style="width:120px">&nbsp;</td>
			<td style="width:5px !important;">&nbsp;</td>
			<td style=";width:400px">&nbsp;</td>
			<td style="width:">&nbsp;</td>
			<td>Date:</td>
			<td style="width:3px !important;">&nbsp;</td>
			<td style="width:150px;border-bottom:1px solid #232323 !important;text-align:center"><?php echo $trans_date;?></td>
		</tr>
	</table>
	<table style="width: 100%" class="tabletas">
		<tr>
			<th style="width:40px;text-align:center">#</th>
			<th style="width:80px">Item Code</th>
			<th>Description</th>
			<th style="width:100px">Units (UOM)</th>
			<th style="width:75px">Quantity</th>
			<th style="width:75px;">WH Qty</th>
			<th style="width:75px;">Inv Ending</th>
				<th style="width:75px;">Brch Rcvd</th>
		</tr>
<?php
	$sqlQueryData = "SELECT * FROM dbc_seasonal_branch_order WHERE branch='$branch' AND control_no='$mrs_no'";
	$dataResults = mysqli_query($db, $sqlQueryData);    
	if ( $dataResults->num_rows > 0 ) 
	{
		$x=0;
		while($DATAROW = mysqli_fetch_array($dataResults))  
		{
			$x++;
			$editid = $DATAROW['id'];
			$remarks = $DATAROW['remarks'];
			$itemcode = $DATAROW['item_code'];
			
			$controlno = $DATAROW['control_no'];
			
			$branchreceived = $DATAROW['branch_received'];
			$branchreceivedstatus = $DATAROW['branch_received_status'];
			
			$remarks = $function->shortenText($remarks,20);
			
			$actionButton = '';
			$branchrcvdstyle = '';	
			$brnchrcvdeditable = '';
			if($branchreceivedstatus == '0'){
				$brnchrcvdeditable = 'contenteditable="true"';
				$branchrcvdstyle = 'background-color:#f7e9d5';
			}
			
?>		
		<tr>
			<td style="text-align:center"><?php echo $x; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['item_code']; ?></td>
			<td><?php echo $DATAROW['item_description']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['uom']; ?></td>
			<td style="text-align:center"><?php echo $DATAROW['quantity']; ?></td>
			<td style="text-align:center">
			
				<?php
					echo $function->getSumQuantityItem('branch_mrs_transaction','wh_quantity',$branch,$controlno,$itemcode,$db);
				?>
			
			</td>
			<td style="text-align:center"><?php echo $DATAROW['inv_ending']; ?></td>
			
			<td id="brachrcvd<?php echo $x?>" style="text-align:center">
				<?php echo $function->sumbranchrecieved($itemcode, $controlno, $db)?>
			</td>
			
		</tr>
<?php } ?>
		<tr>
			<td colspan="10" style="padding:5px;white-space:normal">
				<span style="font-weight:600">REMARKS:</span> <?php echo $function->getOrderRemarks($mrs_no,$db); ?>
			</td>
		</tr>
<?php } else { ?>		
		<tr>
			<td colspan="10" style="text-align:center">No Items</td>
		</tr>
<?php } ?>	
	</table>
</div>


<?php if($function->GetOrderStatus($mrs_no,"order_transit",$db) == 1) { 
	 if($function->GetOrderStatus($mrs_no,"status",$db) == 'Closed') { $rcvdel = 'disabled'; } else { $rcvdel = ''; }
?>


<?php } mysqli_close($db); ?>
<div class="results"></div>
<script>

function loadThisDr(drno,rowidmain){
	
	var controlno = '<?php echo $control_no?>';
	if(drno == ''){
		
		viewOrderRecord(controlno,rowidmain);
			
	} else {
	
		var module = '<?php echo MODULE_NAME; ?>';
		$.post("./Modules/" + module + "/apps/dr_page_form.php", { drno: drno, rowidmain: rowidmain },
		function(data) {		
			$('#mrswrappers').html(data);
		});
		
	}
}

</script>
<script src="../Modules/<?php echo MODULE_NAME; ?>/scripts/script.js"></script>
