<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;

$module = 'Inventory Physical Count';
$username = $_SESSION['dbc_username'];
$userlevel = $_SESSION['dbc_userlevel'];
$trans_date = $_POST['trans_date'];
$permission = 'p_edit';
if($function->GetModulePermission($username,$userlevel,$module,$permission,$db) == 1)
{
	$canEdit = 1;
	$contenteditable = 'true';
} else {
	$canEdit = 0;
	$contenteditable = 'false';
}
$_SESSION['DBC_SHOW_LIMIT'] = $_POST['limit'];
$_SESSION['DBC_TRANSDATE'] = $trans_date;
if(isset($_POST['limit']) && $_POST['limit'] != "")
{
	$limit = $_POST['limit'];
	$limit = "LIMIT $limit";
} else {
	$limit = "";
}
if(isset($_POST['location']) && $_POST['location'] != "")
{
	
	$location = $_POST['location'];
	$_SESSION['DBC_ITEM_LOCATION'] = $location;
	$q = "WHERE recipient='$location'";
} else {
	$q = "";
}
if(isset($_POST['search']) && $_POST['search'])
{
	$location = $_POST['location'];
	$search = $_POST['search'];
	$_SESSION['DBC_ITEM_LOCATION'] = $location;
	$q = "WHERE recipient='$location' AND item_description LIKE '%$search%'";
}
?>
<style>
.phy-shell {
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:8px;
	box-shadow:0 1px 2px rgba(0,0,0,0.04);
	padding:10px;
}
.phy-title {
	font-size:14px;
	font-weight:600;
	color:#2f3b4a;
	margin:0 0 8px 2px;
}
.phy-table {
	width:100%;
	border-collapse:collapse;
	background:#fff !important;
	margin-bottom:0;
}
.phy-table thead th {
	background:#16a8a2;
	color:#fff;
	border:1px solid #11918c;
	padding:5px 8px !important;
	font-size:12px;
	font-weight:600;
	white-space:nowrap;
	vertical-align:middle;
}
.phy-table td {
	border:1px solid #d7dde3;
	padding:5px 8px !important;
	font-size:12px;
	vertical-align:middle;
}
.excel-cell {
	text-align:right;
	line-height:1.3;
}
.row-index {
	text-align:center !important;
	background:#f4f6f8;
	font-weight:600;
	color:#4a5568;
}
.item-code-cell,
.uom-cell {
	text-align:center !important;
}
.item-name-cell {
	text-align:left !important;
}
.pcount-cell {
	background:#f8fafc;
	min-width:140px;
	font-weight:600;
}
.pcount-cell[contenteditable="true"] {
	background:#fff8e8;
	outline:none;
}
.action-cell {
	width:185px;
	padding:4px !important;
}
.action-stack {
	display:flex;
	flex-direction:row;
	gap:4px;
	align-items:center;
	white-space:nowrap;
}
.action-stack .btn {
	font-size:11px;
	font-weight:600;
	padding:4px 8px;
	min-width:86px;
}
.spacer-col {
	width:100%;
	background:#fff;
	border-left:none !important;
	border-right:none !important;
}
</style>
<div class="phy-shell">
<div class="phy-title">Inventory Physical Count Details</div>
<table class="phy-table resizable-table table table-bordered table-striped">
	<thead>
		<tr>
			<th style="text-align:center;width:40px !important">#</th>
			<th><i class="fa-solid fa-code color-green"></i>&nbsp;&nbsp;ITEMCODE</th>
			<th><i class="fa-solid fa-basket-shopping color-green"></i>&nbsp;&nbsp;ITEM DESCRIPTION</th>
			<th><i class="fa-solid fa-weight-scale color-green"></i>&nbsp;&nbsp;UNITS OF MEASURES</th>		
			<th><i class="fa-solid fa-tally color-green"></i>&nbsp;&nbsp;PHYSICAL COUNT</th>
	   	<?php if($canEdit == 1) { ?>			
			<th style="text-align:center"><i class="fa-solid fa-bolt color-red"></i>&nbsp;&nbsp;ACTIONS</th>	
		<?php } ?>
			<th></th>
		</tr>
	</thead>
	<tbody>
<?php
	$queryItems = "SELECT * FROM wms_itemlist $q $limit";
	$resultsItems = $db->query($queryItems);			
	if ( $resultsItems->num_rows > 0 ) 
    {	
		$j=0;
		while($COUNTROWS = mysqli_fetch_array($resultsItems))  
		{
			$j++;
			$rowid = $COUNTROWS['id'];
			$itemcode = $COUNTROWS['item_code'];
?>
	    <tr>
	        <td class="excel-cell resizable-cell row-index"><?php echo $j; ?></td>
	        <td class="excel-cell resizable-cell item-code-cell" style="width:150px"><?php echo $COUNTROWS['item_code']; ?></td>
	        <td class="excel-cell resizable-cell item-name-cell"><?php echo $COUNTROWS['item_description']; ?></td>	        
	       	<td class="excel-cell resizable-cell uom-cell" style="width:150px"><?php echo $COUNTROWS['uom']; ?></td>
	        <td id="pcountvalue<?php echo $j; ?>" contenteditable="<?php echo $contenteditable; ?>" class="excel-cell resizable-cell pcount-cell">
	        <?php echo number_format($inventory->GetPcountData($COUNTROWS['item_code'],$trans_date,'p_count',$db),2); ?>
	        </td>
				<input id="itemcode<?php echo $j; ?>" type="hidden" value="<?php echo $COUNTROWS['item_code']; ?>">
		        <input id="category<?php echo $j; ?>" type="hidden" value="<?php echo $COUNTROWS['category']; ?>">
		        <input id="itemname<?php echo $j; ?>" type="hidden" value="<?php echo $COUNTROWS['item_description']; ?>">
		        <input id="uom<?php echo $j; ?>" type="hidden" value="<?php echo $COUNTROWS['uom']; ?>">
	   		<?php if($canEdit == 1) { ?>
	        <td class="action-cell">
				<div class="action-stack">
	        	<button class="btn btn-info btn-sm color-white" onclick="savePcount('<?php echo $j; ?>')">
	        		<i class="fa-solid fa-pen-to-square"></i>&nbsp;&nbsp;Update
	        	</button>
	        	<?php
	        		if($inventory->GetUndoStatus($trans_date,$itemcode,$db) == 1)
	        		{
	        			 $dis_able = '';
	        		} else {
	        			$dis_able = 'disabled';
	        		}
	        	?>	        	
	        	<button class="btn btn-warning btn-sm color-white" onclick="undoPcount('<?php echo $j; ?>')" <?php echo $dis_able; ?>>
	        		<i class="fa-solid fa-rotate-left color-red"></i>&nbsp;&nbsp;Undo
	        	</button>
				</div>
	        </td>
        <?php } ?>
	        <td class="spacer-col">&nbsp;</td>
	    </tr>
<?php } } else { ?>
		<tr>
			<td colspan="<?php echo $canEdit == 1 ? 7 : 6; ?>" style="text-align:center; font-size:16px"><i class="fa fa-bell color-orange"></i>&nbsp;&nbsp;&nbsp;No Item found.</td>
		</tr>
<?php } ?>		    
	</tbody>
</table>
</div>
<div id="results"></div>
<script>
function undoPcount(elemid)
{
    var mode = 'undopcount';
	var trans_date = '<?php echo $trans_date; ?>';
	var itemcode = $('#itemcode' + elemid).val();
	var uom = $('#uom' + elemid).val();
	rms_reloaderOn("Undo changes...");
	$.post("./Modules/DBC_Management/actions/actions.php",
	{ 
		mode: mode,
		trans_date: trans_date,
		itemcode: itemcode,
		elemid: elemid
	},
	function(data) {		
		$('#results').html(data);		
		rms_reloaderOff();
	});
	
}
function savePcount(elemid)
{
	const cellA = document.getElementById('pcountvalue' + elemid);
    const valueA = cellA.textContent;
    var mode = 'saveinvsetup';
	var trans_date = '<?php echo $trans_date; ?>';
	var itemcode = $('#itemcode' + elemid).val();
	var category = $('#category' + elemid).val();
	var itemname = $('#itemname' + elemid).val();
	var phycount = valueA;
	var uom = $('#uom' + elemid).val();
	rms_reloaderOn("Updating...");
	$.post("./Modules/DBC_Management/actions/actions.php",
	{ 
		mode: mode,
		category: category,
		trans_date: trans_date,
		itemcode: itemcode,
		itemname: itemname,
		phycount: phycount,
		uom: uom
	},
	function(data) {		
		$('#results').html(data);
		rms_reloaderOff();
	});
}
function setValue(rowid,inputvalue)
{
	const cellA = document.getElementById('pcountvalue' + inputvalue);
    const valueA = cellA.textContent;   
    
    var mode = 'saveinvsetup';
	var trans_date = '<?php echo $trans_date; ?>';
	var itemcode = $('#itemcode' + inputvalue).val();
	var category = $('#category' + inputvalue).val();
	var itemname = $('#itemname' + inputvalue).val();
	var phycount = valueA;
	var uom = $('#uom' + inputvalue).val();

	$.post("./Modules/DBC_Management/actions/actions.php",
	{ 
		mode: mode,
		category: category,
		trans_date: trans_date,
		itemcode: itemcode,
		itemname: itemname,
		phycount: phycount,
		uom: uom
	},
	function(data) {		
		$('#results').html(data);
	});
}

$(function()
{
	 $('.excel-cell').on('keydown', function (e) {
        if (e.which === 13) {
            e.preventDefault(); // Prevent creating a new line
            // Find the next cell below in the same column
            var $this = $(this);
            var columnIndex = $this.index();
            var $nextRow = $this.closest('tr').next('tr');
            
            if ($nextRow.length > 0) {
                var $nextCell = $nextRow.find('.excel-cell').eq(columnIndex);

                if ($nextCell.length > 0) {
                    $nextCell.focus();
                }
            }
        }
        var inputValue = $(this).text();

        // Allow numeric values (0-9), backspace, and arrow keys
/*        if (
           (e.which >= 48 && e.which <= 57) || // 0-9 (main keyboard)
           (e.which >= 96 && e.which <= 105) || // 0-9 (numeric keypad)
           e.which === 8 || // Backspace
           (e.which >= 37 && e.which <= 40) || // Arrow keys
           e.which === 46 || // Delete
           e.which === 9 // Tab
           e.which === 110 || // Decimal point (.)       
           ) {
            // Allow the keypress

//           e.which === 110 || // Decimal point (.)
		 //  e.which === 190 // Decimal point (.) on the numpad 
            
            
        } else {
            e.preventDefault();
        } */
    });	
});
</script>
<?php
mysqli_close($db);
?>
