<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;
$_SESSION['BINALOT_REPORT_PAGE'] = $_POST['reports'];
$_SESSION['BINALOT_RECIPIENT_REPORT'] = $_POST['recipient'];
$_SESSION['BINALOT_BRANCH_REPORT'] = $_POST['branch'];

$datefrom = @$_SESSION['BINALOT_SUMMARY_DATEFROM'];
$dateto = @$_SESSION['BINALOT_SUMMARY_DATETO'];

?>
<style>
.subpage-wrapper {margin-top:5px;border:1px solid #aeaeae;background:#fff;}
.tableFixHead {margin-top:5px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 272px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:green; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
.subpage-wrapper {display: flex;gap: 5px;white-space:nowrap;border:1px solid #aeaeae;border-bottom: 3px solid #aeaeae;padding:10px;
background: #fff;min-width:600px;overflow-x:auto;}
</style>
<div class="subpage-wrapper">
	
	<input type="date" id="datefrom" class="form-control form-control-sm" style="width:150px" placeholder="Date from" value="<?php echo @$datefrom?>">
	<input type="date" id="dateto" class="form-control form-control-sm" style="width:150px" placeholder="Date to" value="<?php echo @$dateto?>">
	
	<button id="searchbtn" class="btn btn-info btn-sm" onclick="searchItem()">Search</button>
	
</div>
<div class="tableFixHead" id="iwd"></div>
<script>



function searchItem() {

    var dateFrom = $('#datefrom').val();
    var dateTo = $('#dateto').val();

    var dateFromInput = document.getElementById("datefrom");
    var dateToInput = document.getElementById("dateto");

    dateFromInput.style.border = "";
    dateToInput.style.border = "";

    if (dateFrom === "" || dateTo === "") {
        if (dateFrom === "") {
            dateFromInput.style.border = "2px solid red";
        }
        if (dateTo === "") {
            dateToInput.style.border = "2px solid red";
        }

        app_alert("System Message", "Please fill in both Date From and Date To fields.", "warning");
        return;
    }

    var fromDate = new Date(dateFrom);
    var toDate = new Date(dateTo);

    if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime())) {
        if (isNaN(fromDate.getTime())) {
            dateFromInput.style.border = "2px solid red";
        }
        if (isNaN(toDate.getTime())) {
            dateToInput.style.border = "2px solid red";
        }

        app_alert("System Message", "Please enter valid dates in both fields.", "warning");
        return;
    }

    if (fromDate > toDate) {
        dateFromInput.style.border = "2px solid red";
        dateToInput.style.border = "2px solid red";

        app_alert("System Message", "Date From must be earlier than Date To.", "warning");
        return;
    }

    var timeDiff = toDate - fromDate;
    var daysDiff = timeDiff / (1000 * 3600 * 24);

    if (daysDiff > 30) {
        dateFromInput.style.border = "2px solid red";
        dateToInput.style.border = "2px solid red";

        app_alert("System Message", "Must be within 31 days maximum.", "warning");
        return;
    }
    
    var page = 'monthly_inventory_report_data.php';
	rms_reloaderOn('Searching...');
	$.post("./Modules/Binalot_Management/binalot_report/" + page, { dateFrom: dateFrom, dateTo: dateTo },
	function(data) {		
		$('#iwd').html(data);
		rms_reloaderOff();
	});

}

</script>

