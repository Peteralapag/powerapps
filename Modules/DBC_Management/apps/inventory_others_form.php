<?php

include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";

$inventory = new DBCInventory;
$functions = new DBCFunctions;

$transdate = $_POST['transdate'];
$itemcode = $_POST['itemcode'];
$table1 = $_POST['table'];
$table = 'dbc_'.$table1;
?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}
</style>
<div class="form-wrapper">	
	<table style="width: 100%" class="table">
		<tr style="vertical-align:middle">
			<th style="text-align:center">#</th>
			<th style="text-align:center"><?php echo $table1=='charges'? 'EMPLOYEE NAME': 'BRANCH'; ?></th>			
			<th style="text-align:center">QTY OUT</th>	
		</tr>
		<?php
			$QUERY = "SELECT * FROM $table WHERE report_date='$transdate' AND item_code='$itemcode' AND status=0";
			$RESULTS = mysqli_query($db, $QUERY);
			if ( $RESULTS->num_rows > 0 ) 
			{
				$i=$total=0;
				while($ROW = mysqli_fetch_array($RESULTS))  
				{
					$i++;
					$branch = $table1=='charges'? $ROW['employee_name']: $ROW['branch'];
					$quantity = $ROW['quantity'];
					$total += $quantity;
					
					?>
					
						<tr>
							<td style="text-align:center"><?php echo $i?></td>
							<td style="text-align:center"><?php echo $branch?></td>
							<td style="text-align:center"><?php echo $quantity?></td>
						</tr>
					
					<?php
				}
				
				?>
				
					<tr>
						<td colspan="2">TOTAL:</td>
						<td style="text-align:center"><?php echo number_format($total,2)?></td>
					</tr>
				
				<?php
				
			} else {

			}
		?>
		
		
	</table>
</div>
<div id="results"></div>


