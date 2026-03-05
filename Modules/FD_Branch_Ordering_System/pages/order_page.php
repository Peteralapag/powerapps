<?php
session_start();
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/FD_Branch_Ordering_System/class/Class.functions.php";
$function = new FDSFunctions;
define("MODULE_NAME", "FD_Branch_Ordering_System");
$control_no = $_POST['controlno'];
$form_type = $_POST['form_type'];
$creator = $_SESSION['fds_branch_appnameuser'];
$creatego = $function->getOrderCreator($creator,$control_no,$db);
?>
<style>
.order-page-wrappper {display: flex;height: calc(100vh - 222px);gap: 20px;flex-direction: row;}
.order-sidebar-wrapper {
width:300px !important;
min-width: 300px;
padding:10px;
overflow:hidden;
overflow-y: auto;
font-size:14px;
}
.order-content-wrapper {
width: calc(100vw - 300px);
}
</style>
<div class="order-page-wrappper">
	<div class="order-sidebar-wrapper" id="orderform"></div>
	<div class="order-content-wrapper" id="orderformdata"></div>
</div>
<script>
$(function()
{
	var creatego = '<?php echo $creatego ?>';
	load_order_form('<?php echo $control_no; ?>');
	get_input_form('<?php echo $control_no; ?>');
});
function get_input_form(control_no)
{	
	var creatego = '<?php echo $creatego ?>';
	var module = '<?php echo MODULE_NAME; ?>';
	$.post("./Modules/" + module + "/apps/order_form_input.php", { control_no: control_no, creatego: creatego },
	function(data) {		
		$('#orderform').html(data);
	});
}
function load_order_form(control_no)
{
	var module = '<?php echo MODULE_NAME; ?>';
	$.post("./Modules/" + module + "/includes/mrs_contents_data.php", { control_no: control_no },
	function(data) {		
		$('#orderformdata').html(data);
	});
}
function reload_order()
{
	alert("Sheet");
}
</script>