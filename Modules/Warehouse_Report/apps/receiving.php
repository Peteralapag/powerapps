<?php
$date_search = date("Y-m-d");
?>
<style>
.bar-wrapper {
	border-bottom:3px solid #aeaeae;
	margin-bottom: 10px;	
	padding-bottom:10px;
}
.bar-wrapper button {
	color:#fff;
}
.bar-wrapper input[type=text] {
	width:300px;	
}
.wr-data {
	border:1px solid red;
}
.tableFixHeadData { overflow: auto; height: calc(100vh - 220px); width:100% }
.tableFixHeadData thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff;font-size:14px }
.tableFixHeadData table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHeadData td { font-size:14px; white-space:nowrap }
</style>
<div class="bar-wrapper">
	<table style="width: 100%;border-collapse:collapse" cellpadding="0" cellspacing="0">
		<tr>
			<td style="width:180px">	
				<button class="btn btn-info w-100" onclick="ReceiveItem()">Receive Item</button>
			</td>
			<td style="width:10px;">&nbsp;</td>
			<td style="width:200px">
				<input type="date" class="form-control" placeholder="Date received" value="<?php echo $date_search; ?>">
			</td>
			<td style="width:10px;">&nbsp;</td>
			<td>
				<input id="searchitem" class="form-control" type="text" placeholder="Search Item">
			</td>
			<td style="width:10px;">&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>	
</div>	
<div class="tableFixHeadData" id="wrdata"></div>
<script>
function ReceiveItem()
{
	$('#formodalsmtitle').html('RECEIVING');
	$.post("modules/" + sessionStorage.module + "/apps/receive_items.php", { },
	function(data) {
		$('#formodalsm_page').html(data);
		$('#formodalsm').show();
	});
}
$(function()
{
	$('#searchitem').keyup(function()
	{
		var search = $('#searchitem').val();
		$.post('modules/' + sessionStorage.module + '/includes/receiving_data.php', { search: search },
		function(data) {
			$('#wrdata').html(data);
		});
	});
	loadReceivingData();
});
function loadReceivingData()
{
	$('#wrdata').load('modules/' + sessionStorage.module + '/includes/receiving_data.php');
}
</script>