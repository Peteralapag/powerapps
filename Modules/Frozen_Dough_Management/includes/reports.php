<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
$currentMonthDays = date('t');
$date_from = date("Y-m-01");
$date_to = date("Y-m-".$currentMonthDays);
if(isset($_SESSION['FDS_TABLE']))
{
	$fds_table = $_SESSION['FDS_TABLE'];
} else {
	$fds_table = 'fds_supplier';
}
?>
<style>
.smnav-header select {margin-left: 10px;width:270px;}
.reload-data {display: flex;gap: 15px;margin-left: auto;right:0;}
.date-shell {display: flex;gap: 10px; border:1px solid red}
.inpwd {
	width:140px !important;
}
</style>
<div class="smnav-header">
	<div class="search-shel">
		<select id="fdsmodule" class="form-control form-control-sm" style="width:200PX">
			<?php echo $function->getFDSReport($fds_table,$db); ?>
		</select>
	</div>
	<span class="date-shell" id="dateinputs">
		<select class="form-control form-control-sm" style="margin-left:5px;width:150px"><?php echo $function->getFDSSubReport($fds_table,$db); ?></select>
		<input id="date_from" type="date" class="form-control form-control-sm inpwd" value="<?php echo $date_from; ?>">
		<span style="margin-top:4px;">To</span>
		<input id="date_to" type="date" class="form-control form-control-sm inpwd" value="<?php echo $date_to; ?>">
	</span>
	<div class="search-shell">
		<div class="btn-group" role="group" aria-label="Ronan Sarbon">
			<button class="btn btn-secondary btn-sm" style="width:100px" onclick="">GENERATE</button>
			<button class="btn btn-danger btn-sm" style="width:100px" onclick="">PDF&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-file-pdf pull-right"></i></button>
			<button class="btn btn-success btn-sm" style="width:100px" onclick="GenerateExcel('<?php echo $fds_table; ?>')">EXCEL&nbsp;&nbsp;&nbsp;<i class="fa-solid fa-file-excel"></i></button>	
		</div>
	</div>
	<span class="reload-data">
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit(); ?>
		</select>
	</span>
</div>
<div id="smnavdatas">Loading... <i class="fa fa-spinner fa-spin"></i></div>
<script>
function GenerateExcel(table)
{
	if(sessionStorage.stringKo === undefined || sessionStorage.stringKo === null)
	{
		swal("Select Column", "Please select at least one column to generate", "warning");
	} else {
		rms_reloaderOn('Generating Excel...');
		$("#genxcel_page").attr("src", "./Modules/Frozen_Dough_Management/reporting/generate_excel.php?table=" + table + "&columns=" + sessionStorage.stringKo);
		$("#genxcel").show();
		var iframe_document = document.querySelector('#genxcel_page').contentDocument;
	    if (iframe_document.readyState !== 'loading') onLoadingCompleted();
	    else iframe_document.addEventListener('DOMContentLoaded', onLoadingCompleted);
	    function onLoadingCompleted()
	    {
	       	setInterval(function()
			{
				$("#genxcel").hide();
				sessionStorage.setItem("excelreport", 0);
				rms_reloaderOff();
			},2000);
	    }
	}
}
$(function()
{
	if('<?php echo $fds_table; ?>' == 'fds_inventory_records')
	{
		$('#dateinputs').show();
	} else {
		$('#dateinputs').hide();
	}
	loadModule('<?php echo $fds_table; ?>');
	$('#fdsmodule').change(function()
	{	
		console.log("sheet");
		let table = $('#fdsmodule').val();
		loadModule(table);
		window.location.reload();
	});
	
	$('#date_from,#date_to').on('change', function(event)
	{
		var datefrom = $('#date_from').val();
		var dateto = $('#date_to').val();
		loadWithDate('<?php echo $fds_table; ?>',datefrom,dateto);
	});
});
function loadWithDate(table,datefrom,dateto)
{
	var limit = $('#limit').val();
	$.post("./Modules/Frozen_Dough_Management/reporting/inventory_report.php", { table: table, datefrom: datefrom, dateto: dateto },
	function(data) {		
		$('#smnavdata').html(data);
	});
}
function loadModule(table)
{
	var limit = $('#limit').val();
	 if ($('#dateinputs').is(":hidden"))
	 {
		$.post("./Modules/Frozen_Dough_Management/reporting/show_report.php", { table: table },
		function(data) {		
			$('#smnavdata').html(data);
		});	 	
	 } else {
 	 	var datefrom = $('#date_from').val();
	 	var dateto = $('#date_to').val();
		$.post("./Modules/Frozen_Dough_Management/reporting/inventory_report.php", { limit: limit },
		function(data) {		
			$('#smnavdata').html(data);
		});
	}
}
</script>