<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$currentMonthDays = date('t');

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
$imagelink = $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/images/company_logo.png";


$trans_date = $_SESSION['DBC_TRANSDATE'];
?>
<style>
.receiving-shell {
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:10px;
	box-shadow:0 1px 3px rgba(0,0,0,0.06);
	padding:12px;
}
.receiving-title {
	font-size:15px;
	font-weight:700;
	color:#2f3b4a;
	margin-bottom:8px;
}
.smnav-header {
	display:flex;
	align-items:center;
	gap:10px;
	flex-wrap:wrap;
	padding-bottom:8px;
	border-bottom:1px solid #e9ecef;
}
.smnav-header input[type=text],
.smnav-header input[type=date],
.smnav-header select {
	height:34px;
	font-size:13px;
	border-radius:6px;
}
.smnav-header select {width:220px;}
.reload-data {
	display:flex;
	align-items:center;
	gap:8px;
	margin-left:auto;
}
.date-shell {display:flex;align-items:center;gap:6px;}
.date-shell input[type=date] {width:170px !important;}
.smnav-header .btn {
	height:34px;
	font-size:12px;
	font-weight:600;
	padding:6px 12px;
	border-radius:6px;
}
.pending-wrap {
	font-size:12px;
	font-weight:600;
	color:#495057;
	margin-left:4px;
}
.pending-wrap .badge {
	font-size:11px;
	padding:5px 8px;
	border-radius:10px;
}
.tableFixHead {
	margin-top:12px;
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:8px;
	overflow:auto;
	height:calc(100vh - 255px);
	width:100%;
}
.tableFixHead thead th {
	position:sticky;
	top:0;
	z-index:1;
	background:#16a8a2;
	color:#fff;
	font-size:12px;
	font-weight:600;
}
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:13px; white-space:nowrap } 
.psapointer { cursor:pointer; }

</style>
<div class="receiving-shell">

	<div class="smnav-header">
	
	<div class="date-shell">
		<input id="transdate" type="date" class="form-control form-control-sm psapointer" style="width:150px" value="<?php echo $trans_date; ?>" onchange="searchMe()">
	</div>

	<span class="date-shell">
	
	
	<?php if($function->getPendingApprovalCount($db)!='0'){ ?>

		<span class="pending-wrap">
			Pending approval <span class="badge bg-danger psapointer" ondblclick="openpendingapproval()"><?php echo $function->getPendingApprovalCount($db)?></span>
		</span>
	
	<?php
	}
	?>
	
	
	</span>
	<span class="reload-data">
		<span><button type="button" class="btn btn-sm btn-success" onclick="viewSummary()" ><i class="fa fa-table" aria-hidden="true"></i>&nbsp;View Summary</button></span>

		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHead" id="smnavdata">&nbsp;&nbsp;Loading... <i class="fa fa-spinner fa-spin"></i></div>
</div>

<script>

function openpendingapproval(){


	$('#modaltitle').html("ADD NEW FGTS DATA");
	$.post("./Modules/DBC_Management/apps/dbc_pending_approval.php", {  },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});


}


function viewSummary(){
	
	$('#modaltitle').html(transdate+" SUMMARY DATA");
	
	$.post("./Modules/DBC_Management/includes/dbc_summary.php", { },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});	
	
	
	var transdate = $("#transdate").val();	
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/includes/dbc_summary_data.php", { transdate: transdate },
	function(data) {
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});

}


function searchMe(){
	
	var transdate = $("#transdate").val();	
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/includes/dbc_receiving_data.php", { transdate: transdate },
	function(data) {
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
function getRecipientData()
{
	var limit = $('#limit').val();
	var recipient = $('#recipient').val();
	var ord = $('#ord').val();
	rms_reloaderOn('Loading...');	
	$.post("./Modules/DBC_Management/includes/dbc_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
	function(data) {		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
$(function()
{
	$("#dateFrom, #dateTo").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
    });
    
    
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
		
		$.post("./Modules/DBC_Management/includes/dbc_receiving_data.php", { limit: limit, search: search, recipient: recipient, ord: ord },
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
		$.post("./Modules/DBC_Management/includes/dbc_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	},500);
}
</script>