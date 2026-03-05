<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;

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


$transdate = $_SESSION['BINALOT_TRANSDATE'];

if(isset($_POST['transdate'])) {
	$transdate = $_POST['transdate'];
	$_SESSION['BINALOT_TRANSDATE'] = $transdate;
} else {
	$transdate = $_SESSION['BINALOT_TRANSDATE'];
}

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
</style>
<div class="smnav-header">
	<div class="date-shell">
		<input id="transdate" type="date" class="form-control form-control-sm" style="width:150px" value="<?php echo $transdate; ?>" onchange="searchMe()">
	</div>
	<span class="date-shell">
	<?php 
		if($function->GetPcountDataInventoryChecker($transdate,$db) == '0'){
	?>
	<span>
		<button type="button" class="btn btn-sm btn-success" onclick="addMe()" style="width:120px"><i class="fa fa-plus" aria-hidden="true"></i>
		&nbsp;
		Add Item</button>
	</span>	
	<?php
		}
	?>
	
	</span>
	<span class="reload-data">
		<!--span><button type="button" class="btn btn-sm btn-warning" onclick="printMe()" style="width:100px"><i class="fa fa-print" aria-hidden="true"></i>&nbsp;Print</button></span-->
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHead" id="smnavdata">&nbsp;&nbsp;Loading... <i class="fa fa-spinner fa-spin"></i></div>

<script>



function addMe(params)
{
	$('#modaltitle').html("ADD NEW FGTS DATA");
	$.post("./Modules/Binalot_Management/apps/binalot_add_form.php", { params: params },
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
    printWindow.document.write('<html><head><title>Binalot Actual Branch Receiving</title>');

    printWindow.document.write('<style>@page { size: letter; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } td:nth-child(2) { width: 200px; } td:nth-child(3) { width: 150px; } td:nth-child(4) { width: 250px; } .company-header { text-align: center; } .document-title { text-align: center; margin-bottom: 20px; } #print-content { margin-top: 20px; } #prepared-by { text-align: left; }</style>');
    printWindow.document.write('</head><body>');

    printWindow.document.write('<div class="company-header"> Binalot Actual Receiving</div>');

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

	var transdate = $("#transdate").val();	
	rms_reloaderOn('Loading...');
	var mode = 'dashboarddateselected';
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, transdate: transdate },
	function(data) {		
		$('#returnresult').html(data);
		$('#' + sessionStorage.navfds).trigger('click');
		rms_reloaderOff();
	});
}
function getRecipientData()
{
	var limit = $('#limit').val();
	var recipient = $('#recipient').val();
	var ord = $('#ord').val();
	rms_reloaderOn('Loading...');	
	$.post("./Modules/Binalot_Management/includes/create_fgts_data.php", { limit: limit, recipient: recipient, ord: ord },
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
		
		$.post("./Modules/Binalot_Management/includes/create_fgts_data.php", { limit: limit, search: search, recipient: recipient, ord: ord },
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
		$.post("./Modules/Binalot_Management/includes/create_fgts_data.php", { limit: limit, recipient: recipient, ord: ord },
		function(data) {
			$('#smnavdata').html(data);
			rms_reloaderOff();
		});
	},500);
}
</script>