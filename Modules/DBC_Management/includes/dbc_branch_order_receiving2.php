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
$imagelink = $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/images/company_logo.png";
?>
<style>
.reload-data {display: flex;gap: 15px;margin-left: auto;right:0;}
.date-shell {display: flex;gap: 5px;}
.date-shell input[type=text] {width:130px !important;}
.tableFixHead {margin-top:15px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 222px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
#branchactive{
	width:190px !important;
}
</style>
<div class="smnav-header">

	<div class="date-shell">
		<input type="text" id="dateFrom" class="form-control form-control-sm" style="width:50px" value="<?php echo $date?>">
		&nbsp;<i class="fa fa-long-arrow-right" style="color:#198754" aria-hidden="true"></i>&nbsp;
		<input type="text" id="dateTo" class="form-control form-control-sm" value="<?php echo $date?>">
		
	</div>
	<span class="date-shell">
	
		<span>
			&nbsp;&nbsp;
			<button type="button" class="btn btn-sm btn-primary" onclick="branchSetActive()" style="width:180px"><i class="fa fa-bars" aria-hidden="true"></i>&nbsp;Set Active Branch</button>
		</span>
		<span>
			&nbsp;&nbsp;
			<button type="button" class="btn btn-sm btn-success" onclick="searchMe()" style="width:100px"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Go</button>
		</span>

	
	</span>
	<span class="reload-data">
		<span><button type="button" class="btn btn-sm btn-warning" onclick="printMe()" style="width:100px"><i class="fa fa-print" aria-hidden="true"></i>&nbsp;Print</button></span>
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHead" id="smnavdata">&nbsp;&nbsp;Loading... <i class="fa fa-spinner fa-spin"></i></div>

<script>

function branchSetActive(params){

	$('#modaltitle').html("SET BRANCH ACTIVE");
	$.post("./Modules/DBC_Management/apps/set_branch_active_form.php", { params: params },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});



}


function printMe() {
    var printContent = $('#smnavdata').html();

    if (printContent.trim() === '') {
        swal('Error', 'No data to print', 'error');
        return;
    }
    
    var datefrom = $('#dateFrom').val();
    var dateto = $('#dateTo').val();
    
    var imagelink = '<?php echo $imagelink;?>';

    var preparedBy = '________'; 

    var printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write('<html><head><title>DBC Actual Branch Receiving</title>');

    printWindow.document.write('<style>@page { size: letter; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } td:nth-child(2) { width: 200px; } td:nth-child(3) { width: 150px; } td:nth-child(4) { width: 250px; } .company-header { text-align: center; } .document-title { text-align: center; margin-bottom: 20px; } #print-content { margin-top: 20px; } #prepared-by { text-align: left; }</style>');
    printWindow.document.write('</head><body>');

    printWindow.document.write('<div class="company-header"> DBC Actual Receiving</div>');

    printWindow.document.write('<div class="document-title">Date From: <span style="color:red">'+datefrom+'</span> Date To: <span style="color:red">'+dateto+'</span></div>');
    printWindow.document.write('<div id="print-content">' + printContent + '</div>');

    printWindow.document.write('<div id="prepared-by" style="margin-top: 20px;">Prepared by: ' + preparedBy + '</div>');

    printWindow.document.write('</body></html>');
    printWindow.document.close();

    printWindow.onload = function() {
        printWindow.focus();
        printWindow.print();
    };
}


function searchMe(){
	var datefrom = $('#dateFrom').val();
	var dateto = $('#dateTo').val();
	
	var fromDate = new Date(datefrom);
    var toDate = new Date(dateto);
    
	if (toDate < fromDate) {
        swal('System Message','Dateto should be greater than Datefrom','warning');
        return;
    }
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/includes/dbc_branch_order_receiving_data2.php", { datefrom: datefrom, dateto: dateto },
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
	$.post("./Modules/DBC_Management/includes/dbc_branch_order_receiving_data2.php", { limit: limit, recipient: recipient, ord: ord },
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
		
		$.post("./Modules/DBC_Management/includes/dbc_branch_order_receiving_data2.php", { limit: limit, search: search, recipient: recipient, ord: ord },
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
		$.post("./Modules/DBC_Management/includes/dbc_branch_order_receiving_data2.php", { limit: limit, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	},500);
}
</script>