<?php
$rowid = $_POST['rowid'];
?>
<style>
.print-wrapper {
	margin-bottom:10px;
}
.iframe-frame {
	display: flex;
	width:90vw;
	height: calc(100vh - 300px);
	overflow-y:auto;
	background:#fff;
	padding-bottom:10px
}
.main-frame {
	width:90vw;
}
</style>
<div class="iframe-frame" id="iframeframe">
	<iframe class="main-frame" id="printFrame"></iframe>
</div>
<script>

function printMe(rowid)
{
	$('#iframeframe').slideDown();
	$('#printFrame').attr('src', './Modules/DBC_Seasonal_Management/includes/print_dr_pdf.php?rowid='+rowid);
}
$(function()
{
	var rowid = '<?php echo $rowid; ?>';
	printMe(rowid);
//	$('#iframeframe').hide();
});
</script>