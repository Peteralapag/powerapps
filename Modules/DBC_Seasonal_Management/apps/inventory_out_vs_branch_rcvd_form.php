<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Seasonal_Management/class/Class.functions.php";

$inventory = new DBCInventory;
$functions = new DBCFunctions;

$transdate = $_POST['transdate'];
$itemcode = $_POST['itemcode'];
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr style="vertical-align:middle">
			<th style="text-align:center">#</th>
			<th style="text-align:center">BRANCH OUT</th>
			<th style="text-align:center">UNITS (UOM)</th>
			<th style="text-align:center">QTY OUT</th>	
		</tr>
		<?php
		
			$QUERY = "SELECT * FROM dbc_seasonal_branch_order WHERE item_code='$itemcode' AND delivery_date='$transdate' AND branch_received_status='1' AND control_no IN (SELECT control_no FROM dbc_seasonal_order_request WHERE logistics = '1')";
			$RESULTS = mysqli_query($db, $QUERY);

			if ( $RESULTS->num_rows > 0 ) 
			{
				$i=$total=0;
				while($ROW = mysqli_fetch_array($RESULTS))  
				{
					$i++;
					$branch = $ROW['branch'];
					$actual_quantity = $ROW['actual_quantity'];
					$total += $actual_quantity;
					?>
					
						<tr>
							<td style="text-align:center"><?php echo $i?></td>
							<td style="text-align:center"><?php echo $branch?></td>
							<td style="text-align:center"><?php echo $functions->GetItemListColumn('uom',$itemcode,$db)?></td>
							<td style="text-align:center"><?php echo $actual_quantity?></td>
						</tr>
					
					<?php
					
				}
				
				?>
				
					<tr>
						<td colspan="3">TOTAL:</td>
						<td style="text-align:center"><?php echo number_format($total,2)?></td>
					</tr>
				
				<?php
				
			} else {

			}
		?>
		
		
	</table>
</div>
<div id="results"></div>
<script>

