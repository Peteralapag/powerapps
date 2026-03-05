<?php
ini_set('display_errors', 1);
error_reporting(E_ALL); // Report all errors for debugging

include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

require $_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Seasonal_Management/class/Class.inventory.php";
require $_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Seasonal_Management/class/Class.functions.php";

$functions = new DBCFunctions;
$chargecodeno = $_POST['chargecodeno'];
$item = $_POST['item'];
$itemcode = $_POST['itemcode'];
$category = $_POST['category'];
$original_qty = $_POST['qty'];
$original_unitprice = $_POST['unitprice'];
$original_total = $_POST['total'];

$transdate = $_POST['transdate'];
$voidstatus = $_POST['status'];





if($functions->GetPcountDataInventoryChecker($transdate,$db) == '1' || $voidstatus == '1'){ 
	$addbtnstatus = 'none';
} else {
	$addbtnstatus = 'block';
}


?>
<style>
.form-wrapper { width: 900px; max-height: 500px; overflow-y: auto; }
.table th { font-size: 14px !important; }
</style>

<div class="form-wrapper">    
	<div style="float:right; display:<?php echo $addbtnstatus?>">
		<button type="button" class="btn btn-success btn-sm" onclick="addChargesNoneBaker()"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add</button>
	</div>
    <table style="width: 100%" class="table">
    	<tr>
    		<th colspan="6" style="font-size:large">
    			<?php
 //   				echo 'ITEM NAME: '.$item.'	QUANTITY: '.$qty.'	UNIT PRICE: '.$unitprice.'	TOTAL: '.$total;
    			?>
    		</th>
    	</tr>
        <tr style="vertical-align: middle;">
            <th style="text-align: center;">#</th>
            <th>BAKER / EMPLOYEE</th>
            <th style="text-align: center;">ITEM DESCRIPTION</th>
            <th style="text-align: center;">QTY</th>
            <th style="text-align: center;">U.PRICE</th>
            <th style="text-align: center;">TOTAL</th>
        </tr>

        <?php

        $stmt = $db->prepare("SELECT * FROM dbc_seasonal_charges_extension_record WHERE chargecodeno = ?");
        $stmt->bind_param("s", $chargecodeno);


        $stmt->execute();
        $result = $stmt->get_result();

		$ttotal = (float) 0;
		$tqty = (float) 0;
        if ($result->num_rows > 0) {
            $count = 1;

            while ($row = $result->fetch_assoc()) {
                $chargecodeno = $row['chargecodeno'];
                $employee = $row['employee_name'];
                $itemDescription = $row['item_description'];
                $qty = (float)$row['quantity'];
                $unitPrice = (float)$row['unit_price'];
                $total = (float)$row['total'];
                $ttotal += $total;
                $tqty += $qty;
                
                $employeetype = $row['employee_type'];
                $employeetype == 'NONE BAKER'? $emptype = '#e5f3eb': $emptype = '';
		?>
				<tr style="background:<?php echo $emptype?>">
					<td style="text-align:center"><?php echo $count?></td>
					<td><?php echo $employee?></td>
					<td><?php echo $itemDescription?></td>
					<td style="text-align:right"><?php echo $qty?></td>
					<td style="text-align:right"><?php echo $unitPrice?></td>
					<td style="text-align:right"><?php echo $total?></td>

				</tr>
                      
                      
		<?php
                $count++;
            }
        ?>
        
        		<tr>
        			<td colspan="3">TOTAL</td>
        			<td style="text-align:right"><?php echo $tqty?></td>
        			<td></td>
        			<td style="text-align:right"><?php echo $ttotal?></td>
        		</tr>
        
        <?php
        } else {

            echo "<tr><td colspan='6' style='text-align: center;'>No records found</td></tr>";
        }


        $stmt->close();
        ?>
    </table>
</div>
<div id="results"></div>

<script>
function addChargesNoneBaker(){
	
	var chargecodeno = '<?php echo $chargecodeno?>';
	var itemname = '<?php echo $item?>';
	var itemcode = '<?php echo $itemcode?>';
	var category = '<?php echo $category?>';
	var original_qty = <?php echo $original_qty?>;
	var original_unitprice = <?php echo $original_unitprice?>;
	var original_total = <?php echo $original_total?>
	
	$('#modaltitle').html(itemname);
	$.post("./Modules/DBC_Seasonal_Management/apps/dbc_add_charges_none_baker.php", { chargecodeno:chargecodeno, itemname:itemname, itemcode:itemcode, category:category, original_qty:original_qty, original_unitprice:original_unitprice, original_total:original_total },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});

}
</script>
