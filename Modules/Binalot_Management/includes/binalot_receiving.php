<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;
$currentMonthDays = date('t');

$user_level = $_SESSION['binalot_userlevel'];
if(isset($_SESSION['BINALOT_SHOW_LIMIT']))
{
	$show_limit = $_SESSION['BINALOT_SHOW_LIMIT'];
} else {
	$show_limit = '50';
}
if(isset($_SESSION['BINALOT_ORD']))
{
	$ordd = $_SESSION['BINALOT_ORD'];
} else {
	$ordd = "Process Order";
}
if(isset($_SESSION['binalot_user_recipient']))
{
	if($user_level >= 60)
	{
		$user_recipient = $_SESSION['binalot_user_recipient'];
		$admin = 1;			
	} else {
		$admin = 0;
		$user_recipient = $_SESSION['binalot_user_recipient'];
	}
	
} else {

	if($user_level >= 60)
	{
		if(isset($_SESSION['binalot_user_recipient']))
		{
			$user_recipient = $_SESSION['binalot_user_recipient'];
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
$imagelink = $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/images/company_logo.png";


$trans_date = $_SESSION['BINALOT_TRANSDATE'];
?>
<style>
.smnav-header input[type=text]{padding-left:25px;padding-right:27px}
.smnav-header select {margin-left: 10px;width:270px;}
.reload-data {display: flex;gap: 15px;margin-left: auto;right:0;}
.date-shell {display: flex;gap: 5px;}
.date-shell input[type=text] {width:150px !important;}
.tableFixHead {margin-top:15px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 222px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
.psapointer { cursor:pointer; }

</style>
<div class="smnav-header">
	
	<div class="date-shell">
		<input id="transdate" type="date" class="form-control form-control-sm psapointer" style="width:150px" value="<?php echo $trans_date; ?>" onchange="searchMe()">
	</div>

	<span class="date-shell">
	
	
	<?php if($function->getPendingApprovalCount($db)!='0'){ ?>

		<span style="margin-left:30px; margin-top:6px; font-size:12px">
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

<script>

function openpendingapproval(){


	$('#modaltitle').html("ADD NEW FGTS DATA");
	$.post("./Modules/Binalot_Management/apps/binalot_pending_approval.php", {  },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});


}


function viewSummary(){
	
	$('#modaltitle').html(transdate+" SUMMARY DATA");
	
	$.post("./Modules/Binalot_Management/includes/binalot_summary.php", { },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});	
	
	
	var transdate = $("#transdate").val();	
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/includes/binalot_summary_data.php", { transdate: transdate },
	function(data) {
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});

}


function searchMe(){
	
	var transdate = $("#transdate").val();	
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/includes/binalot_receiving_data.php", { transdate: transdate },
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
	$.post("./Modules/Binalot_Management/includes/binalot_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
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
		
		$.post("./Modules/Binalot_Management/includes/binalot_receiving_data.php", { limit: limit, search: search, recipient: recipient, ord: ord },
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
		$.post("./Modules/Binalot_Management/includes/binalot_receiving_data.php", { limit: limit, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	},500);
}
</script>