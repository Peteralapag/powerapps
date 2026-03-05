<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
if(isset($_SESSION['FDS_TABLE']))
{
	$fds_table = $_SESSION['FDS_TABLE'];
} else {
	$fds_table = 'fds_supplier';
}
?>
<style>
.smnav-header input[type=text]{padding-left:25px;padding-right:27px}
.smnav-header select {width:250px;}
.reload-data {display: flex;gap: 15px;margin-left: auto;right:0;}
.sub-menu-nav {display: flex;flex-grow: 1;padding:0;margin:0;}
</style>
<div class="smnav-header">	
	<div class="sub-menu" style="margin-right:5px">
		<select class="form-control form-control-sm" onchange="loadModule(this.value)"><?php echo $function->getFDSReport($fds_table,$db); ?></select>
	</div>
	<div class="sub-menu-nav" id="submenunav"></div>
</div>
<div class="" id="reportbody"></div>
<script>
function loadModule(table)
{
	$.post("./Modules/Frozen_Dough_Management/topnavs/" + table + "_top.php", { table: table },
	function(data) {		
		$('#submenunav').html(data);
		getReportBody(table)
	});
}
function getReportBody(table)
{
	$.post("./Modules/Frozen_Dough_Management/reporting/" + table + "_report.php", { table: table },
	function(data) {		
		$('#reportbody').html(data);
	});
}
$(function()
{
	loadModule('<?php echo $fds_table; ?>');
});
</script>

