<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);  
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['binalot_userlevel'];

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

// Sanitize and set default values for datefrom and dateto
$datefrom = isset($_POST['datefrom']) ? mysqli_real_escape_string($db, $_POST['datefrom']) : $date;
$dateto = isset($_POST['dateto']) ? mysqli_real_escape_string($db, $_POST['dateto']) : $date;

?>
<table style="width: 100%" class="table table-bordered table-striped table-hover">
    <thead>
        <tr>
            <th style="width:60px;text-align:center">#</th>
            <th style="width:300px">Item Code</th>
            <th>Description</th>
            <th>Units (UOM)</th>
            <th>Quantity</th>
        </tr>
    </thead>        
    <tbody>
<?php
  
	$sqlQuery = "SELECT * FROM binalot_itemlist WHERE active = 1 ORDER BY ordered ASC"; 
	
	$results = mysqli_query($db, $sqlQuery);    
    if ($results && mysqli_num_rows($results) > 0) {
        $n = 0;
        while ($row = mysqli_fetch_array($results)) {
        	$itemcode = $row['item_code'];
        	$itemdescription = $row['item_description'];
            $n++;
?>  
        <tr>
            <td style="text-align:center"><?php echo $n; ?></td>
            <td><?php echo $itemcode?></td>
            <td><?php echo $row['item_description']; ?></td>
            <td><?php echo $row['uom']; ?></td>
            <td ondblclick="infothis('<?php echo $itemcode?>','<?php echo $itemdescription?>')"><?php echo $function->getValueSummaryviadate($datefrom, $dateto, $itemcode, $db)?></td>
        </tr>
<?php
        }
    } else { 
?>  
        <tr>
            <td colspan="6" style="text-align:center"><i class="fa fa-bell"></i> No Orders yet.</td>
        </tr>           
<?php 
    } 
?>
    </tbody>
</table>

<script>
function infothis(itemcode,itemdescription){

	var datefrom = '<?php echo $datefrom?>';
	var dateto = '<?php echo $dateto?>';
	
	
	$('#modaltitle').html(itemdescription);
	$.post("./Modules/Binalot_Management/apps/branch_order_receiving_qty_info_form.php", { itemcode: itemcode, datefrom: datefrom, dateto: dateto },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});


}
</script>
