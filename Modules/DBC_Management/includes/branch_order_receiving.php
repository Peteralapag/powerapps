<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$currentMonthDays = date('t');
$date = date("Y-m-d");
$user_level = $_SESSION['dbc_userlevel'];
if(isset($_SESSION['DBC_SHOW_LIMIT']))
{
	$show_limit = $_SESSION['DBC_SHOW_LIMIT'];
} else {
	$show_limit = '50';
}
if(isset($_SESSION['DBC_ORD']))
{
	$ordd = $_SESSION['DBC_ORD'];
} else {
	$ordd = "Process Order";
}
if(isset($_SESSION['dbc_user_recipient']))
{
	if($user_level >= 60)
	{
		$user_recipient = $_SESSION['dbc_user_recipient'];
		$admin = 1;			
	} else {
		$admin = 0;
		$user_recipient = $_SESSION['dbc_user_recipient'];
	}
	
} else {

	if($user_level >= 60)
	{
		if(isset($_SESSION['dbc_user_recipient']))
		{
			$user_recipient = $_SESSION['dbc_user_recipient'];
		} else {
			$user_recipient = '';
		}
		$admin = 1;
	} else {
		$admin = 0;
		$user_recipient = 'NOT SET';
	}
}
$ord = '';
?>
<style>
.smnav-header input[type=text]{padding-left:25px;padding-right:27px}
.smnav-header select {margin-left: 10px;width:270px;}
.reload-data {display: flex;gap: 15px;margin-left: auto;right:0;}
.date-shell {display: flex;gap: 5px;}
.date-shell input[type=text] {width:200px !important;}
.tableFixHead {margin-top:15px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 222px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
</style>
<div class="smnav-header">
	<div class="search-shell">
		<input id="search" type="text" class="form-control form-control-sm" placeholder="Search Order / Branch">	
		<i class="fa-sharp fa-solid fa-magnifying-glass search-magnifying"></i>
		<i class="fa-solid fa-circle-xmark search-xmark" onclick="clearSearch()"></i>
	</div>
	<span class="date-shell">
	
		<input type="hidden" id="recipient" style="width:490px;" class="form-control form-control-sm" value="DAVAO BAKING CENTER" disabled>
	
		<span>
			<select id="ord"  style="width:200px" class="form-control " onchange="getRecipientData()">
				<?php echo $function->GetOrderReceiving($ordd); ?>
			</select>
		</span>
				
		<span style="margin-left:30px; margin-top:6px; font-size:12px">
			To view all orders organized by 
				<span style="color:red; cursor:pointer" onclick="Check_Permissions('p_view',openMenuGranted,'dbc_branch_order_receiving','Branch Order Receiving')"> Item Name</span> 
				or 
				<span style="color:red; cursor:pointer" onclick="Check_Permissions('p_view',openMenuGranted,'dbc_branch_order_receiving2','Branch Order Receiving')"> Item Name with Branch</span> 
		</span>
		

	</span>
	<span class="reload-data">
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHead" id="smnavdata">&nbsp;&nbsp;Loading... <i class="fa fa-spinner fa-spin"></i></div>

<script>
function filterviadate()
{
	var limit = $('#limit').val();
	var recipient = $('#recipient').val();
	var ord = $('#ord').val();

	$('#modaltitle').html("DATE FILTERING BRANCH ORDER");
	$.post("./Modules/DBC_Management/apps/date_filtering_form.php", { limit: limit, recipient: recipient, ord: ord },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}

function getRecipientData()
{
	var limit = $('#limit').val();
	var recipient = $('#recipient').val();
	var ord = $('#ord').val();
	rms_reloaderOn('Loading...');	
	$.post("./Modules/DBC_Management/includes/branch_order_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
$(function()
{
	var userlevel = '<?php echo $user_level; ?>';
	var ord = $('#ord').val();
	$('#search').keyup(function()
	{
		if($('#recipient').val() != '' || $('#recipient').val() != '')
		{
			var limit = '';
			var search = $('#search').val();
			if(userlevel >= 60)
			{
				var recipient = $('#recipient').val()
			} else {
				var recipient = $('#recipient').val()
			}
		} else {
			swal("Recipient", "Invalid Recipient", "warning");
		}	
		$.post("./Modules/DBC_Management/includes/branch_order_receiving_data.php", { limit: limit, search: search, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
		});

	});
	load_data();
});
function clearSearch()
{
	$('#search').val('');
	reload_data();
}
function reload_data()
{
	$('#' + sessionStorage.navfds).trigger('click');
}
function load_data()
{
	rms_reloaderOn('Loading...');
	var limit = $('#limit').val();
	var recipient = $('#recipient').val();
	var ord = $('#ord').val();
	setTimeout(function()
	{
		$.post("./Modules/DBC_Management/includes/branch_order_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	},500);
}
</script>