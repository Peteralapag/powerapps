<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";

$function = new DBCFunctions;
$inventory = new DBCInventory;


$_SESSION['DBC_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SUMMARY_DATETO'] = $_POST['dateTo'];


$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$start = new DateTime($datefrom);
$end = new DateTime($dateto);



$branches = [];
$sql = "SELECT branch FROM tbl_branch";
if ($stmt = $db->prepare($sql)) {
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row['branch'];
    }
    
    $stmt->close();
} else {
    echo "Error: " . $db->error;
}


?>

<style>


.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 5 !important;
    white-space: nowrap;
}


.sticky-column-1 { left: 0; z-index: 6; }
.sticky-column-1-data { left: 0; background: white !important; }
.sticky-column-2 { left: 35px; z-index: 6; }
.sticky-column-2-data { left: 35px; background: white !important; }
.sticky-column-3 { left: 140px; z-index: 6; }
.sticky-column-3-data { left: 140px; background: white !important; }


.sticky-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #f5f5f5;
    z-index: 4 !important;
}


.sticky-column.sticky-header {
    top: 0;
    z-index: 7 !important;
}



</style>

<table style="width:100%" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="bg-success sticky-column sticky-column-1 sticky-header" style="width:50px; vertical-align:middle; text-align:center">#</th>
            <th class="bg-success sticky-column sticky-column-2 sticky-header" style="width:70px; vertical-align:middle; text-align:center">ITEM CODE</th>
            <th class="bg-success sticky-column sticky-column-3 sticky-header" style="vertical-align:middle; text-align:center">ITEM NAME</th>
            
            <?php
            foreach ($branches as $branch) {
            	echo '<th class="bg-primary sticky-header" text-align:center">' . htmlspecialchars($branch) . '</th>';
            }
            ?>
            <th class="bg-success sticky-header" style="width:80px; text-align:center">TOTAL</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sqlQuery = "SELECT * FROM wms_itemlist WHERE active=1";
    $results = mysqli_query($db, $sqlQuery);
    if ($results && $results->num_rows > 0) {
        $i = 0;
        while ($INVROW = mysqli_fetch_array($results)) {
            $i++;
            $itemcode = $INVROW['item_code'];	
            $itemname = $INVROW['item_description'];    
            ?>	
            
            <tr>
                <td style="text-align:center" class="sticky-column sticky-column-1-data"><?php echo $i; ?></td> 
                <td style="text-align:center" class="sticky-column sticky-column-2-data"><?php echo htmlspecialchars($itemcode); ?></td>
                <td class="sticky-column sticky-column-3-data"><?php echo htmlspecialchars($itemname); ?></td>

                <?php
				
					$totalVal = 0;
					foreach ($branches as $branch) {
						$thisValue = $function->getVallueBranchandItemDate($datefrom, $dateto, $branch, $itemcode, $db);
						$totalVal += $thisValue;

						echo '<td style="text-align:center">'.$thisValue.'</td>';
					}
                
                
                ?>
                <td style="text-align:center"><?php echo number_format($totalVal,2)?></td>
            </tr>
            
            <?php			
        }
    } else { 
        ?>		
        <tr>
            <td colspan="<?php echo 3 + $daysCount; ?>" style="text-align:center;color:#fff" class="bg-primary">
                <i class="fa fa-bell color-orange"></i> No Record(s) found.
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
