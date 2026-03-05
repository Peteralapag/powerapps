<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";

$functions = new DBCFunctions;

$datefrom = $_POST['datefrom'];
$dateto = $_POST['dateto'];
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
			<th>BRANCH</th>
			<th style="text-align:center">UNITS (UOM)</th>
			<th style="text-align:center">QTY</th>	
		</tr>
		<?php
			
			$QUERY = "
		        SELECT * 
		        FROM dbc_branch_order b
		        JOIN dbc_order_request o ON b.control_no = o.control_no 
		        AND b.item_code = '$itemcode' 
		        AND b.trans_date BETWEEN '$datefrom' AND '$dateto'
		        AND o.status != 'Open'
		        AND o.status != 'Void'
		    ";

			
			
			$RESULTS = mysqli_query($db, $QUERY);

			if ( $RESULTS->num_rows > 0 ) 
			{
				$i=$total=0;
				while($ROW = mysqli_fetch_array($RESULTS))  
				{
					$i++;
					$branch = $ROW['branch'];
					$quantity = $ROW['quantity'];
					$uom = $ROW['uom'];
					$total += $quantity;
					?>
					
						<tr>
							<td style="text-align:center"><?php echo $i?></td>
							<td><?php echo $branch?></td>
							<td style="text-align:center"><?php echo $uom?></td>
							<td style="text-align:center"><?php echo $quantity?></td>
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

