<?php
require '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$userlevel = $_SESSION['application_userlevel'];
?>
<table style="width:100%" class="table table-hover table-striped table-bordered">
	<thead>
		<tr>
			<th style="width:50px; text-align:center">#</th>
			<th>ITEM DESCRIPTION</th>
			<th>ITEM CLASS</th>
			<th>UOM</th>
			<th>CONVERSION</th>
			<th>UNIT PRICE</th>
			<th>SUPPLIER</th>
			<th style="width:60px;">ACTIONS</th>
		</tr>
	</thead>
	<tbody>
<?php
	$query = "SELECT * FROM rpt_item_records";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{	
		$n=0;
		while($ROW = mysqli_fetch_array($results))  
		{
			$n++;
			$rowid = $ROW['id'];
			$item_code = $ROW['item_code'];
			$class = $ROW['class'];
			$item_name = $ROW['item_description'];
			$uom = $ROW['uom'];
?>	
		<tr>
			<td style="text-align:center;font-weight:600"><?php echo $n; ?></td>
			<td><?php echo $ROW['item_description']; ?></td>
			<td><?php echo $ROW['class']; ?></td>
			<td><?php echo $ROW['uom']; ?></td>
			<td><?php echo $ROW['conversion']; ?></td>
			<td><?php echo $ROW['unit_price']; ?></td>
			<td><?php echo $ROW['supplier']; ?></td>
			<td>
				<button class="btn btn-success btn-sm w-100"onclick="EditSupplier('edit','<?php echo $rowid; ?>')">Edit</button>
			</td>
		</tr>
<?php }} else { ?>
		<tr>
			<td colspan="8" style="text-align:center"><i class="fa fa-bell"></i> No Records.</td>
		</tr>
<?php } ?>
	</tbody>		
</table>
<script>
function EditSupplier(mode,rowid)
{
	$('#formodalsmtitle').html('ADD NEW SUPPLIER');
	$.post("modules/" + sessionStorage.module + "/apps/add_supplier.php", { mode: mode, rowid: rowid },
	function(data) {
		$('#formodalsm_page').html(data);
		$('#formodalsm').show();
	});
}
</script>
