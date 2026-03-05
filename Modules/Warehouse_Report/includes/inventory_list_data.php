<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
if(isset($_POST['search']))
{
	$search = $_POST['search'];
	$q = "WHERE item_code LIKE '%$search%' OR class LIKE '%$search%' OR item_description LIKE '%$search%'  OR item_description LIKE '%$search%'";
} 
else
{
	$q = '';
}
?>
<style>
.table th {
	white-space:nowrap;
}
.table td {
	padding:5px !important;
	font-size:12px;
	white-space:nowrap !important;
	padding:2 !important;
}
.btnpadding {
	padding:2 !important;
	font-size:12px;
}
</style>
<table style="width:100%" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th style="width:50px; text-align:center">#</th>
			<th>ITEM CODE</th>
			<th>ITEM CLASS</th>
			<th>ITEM DESCRIPTION</th>
			<th>ON HAND</th>
			<th>UOM</th>
			<th>CONVERSION</th>
			<th style="width:80px">ACTIONS</th>
		</tr>
	</thead>
	<tbody>
<?php
$query = "SELECT * FROM rpt_warehouse $q";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{	
		$i=0;
		while($ROW = mysqli_fetch_array($results))  
		{
			$i++;
			$rowid = $ROW['id'];
			$item_code = $ROW['item_code'];
			$class = $ROW['class'];
			$item_description = $ROW['item_description'];
			$on_hand = $ROW['on_hand'];
			$uom = $ROW['uom'];
			$conversion = $ROW['conversion'];

?>	
		<tr ondblClick="editItem('edit','<?php echo $rowid; ?>','<?php echo $item_description; ?>')">
			<td style="width:50px; text-align:center;font-weight:600"><?php echo $i; ?></td>
			<td><?php echo $item_code; ?></td>
			<td><?php echo $class; ?></td>
			<td><?php echo $item_description; ?></td>
			<td style="text-align:right; padding-right:20px;"><?php echo $on_hand; ?></td>
			<td><?php echo $uom; ?></td>
			<td><?php echo $conversion; ?></td>
			<td>
				<!-- button class="btn btn-warning btnpadding btn-sm color-white" onClick="editItem('edit','<?php echo $rowid; ?>','<?php echo $item_description; ?>')">Edit</button -->
				<button class="btn btn-success btnpadding btn-sm" onclick="showProperties('<?php echo $item_code; ?>','<?php echo $item_description; ?>','<?php echo $class; ?>','<?php echo $uom; ?>')">Properties</button>
			</td>
		</tr>
<?php } } else { ?>
		<tr>
			<td colspan="8" style="text-align:center"><i class="fa fa-bell"></i> No Records</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<script>
function showProperties(itemcode,itemname,classes,uom)
{
	$('#modaltitle').html("<strong>" +itemname + " Properties</strong>");
	$.post("modules/" + sessionStorage.module + "/apps/item_properties.php", { itemcode: itemcode, itemname: itemname, classes: classes, uom: uom },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});	
}
function editItem(mode,rowid,itemname)
{
	$('#modaltitle').html('EDIT ' + itemname);
	$.post("modules/" + sessionStorage.module + "/apps/add_item.php", { mode: mode, rowid: rowid },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
</script>

