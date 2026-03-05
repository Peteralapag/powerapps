<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$_SESSION['DBC_REPORT_PAGE'] = $_POST['reports'];
$_SESSION['DBC_RECIPIENT_REPORT'] = $_POST['recipient'];
$_SESSION['DBC_BRANCH_REPORT'] = $_POST['branch'];

$selectedbranch = @$_SESSION['DBC_SUMMARY_SELECTEDBRANCH'];

//$selecteddate = @$_SESSION['DBC_SUMMARY_SELECTEDDATED'];

$datefrom = @$_SESSION['DBC_SUMMARY_DATEFROM'];
$dateto = @$_SESSION['DBC_SUMMARY_DATETO'];

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
	

	<input id="selectedbranch" type="text" list="selectedbranchList" style="width:260px" class="form-control form-control-sm"  placeholder="---Select Branch---" value="<?php echo @$selectedbranch?>" autocomplete="off">
	<datalist id="selectedbranchList">
		<?php echo $function->GetBranch($selectedbranch,$db)?>
	</datalist>
	

		
	<input type="date" id="datefrom" class="form-control form-control-sm" style="width:150px" placeholder="Date from" value="<?php echo @$datefrom?>">
	<input type="date" id="dateto" class="form-control form-control-sm" style="width:150px" placeholder="Date to" value="<?php echo @$dateto?>">
	
	
	<button id="searchbtn" class="btn btn-info btn-sm" onclick="searchItem()"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
	
	

	<div  class="float-right">
		<button class="btn btn-success btn-sm" onclick="printDiv()"><i class="fa fa-file-excel" aria-hidden="true"></i>&nbsp;export to excel</button>
	</div>


	
</div>
<div class="tableFixHead" id="iwd"></div>
<script>

function printDiv() {



	const table = document.getElementById("iwd").innerHTML;
    const blob = new Blob([table], { type: "application/vnd.ms-excel" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "DOBr_Report.xls";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);


}

function searchItem() {

	var selectedbranch = $('#selectedbranch').val();
	var dateFrom = $('#datefrom').val();
    var dateTo = $('#dateto').val();
	
    var selectedbranchInput = document.getElementById("selectedbranch");
    var dateFromInput = document.getElementById("datefrom");
    var dateToInput = document.getElementById("dateto");
   
	selectedbranchInput.style.border = "";
   	dateFromInput.style.border = "";
    dateToInput.style.border = "";
     

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
  
    var page = 'dobr_via_branch_data.php';
	rms_reloaderOn('Searching...');
	$.post("./Modules/DBC_Management/dbc_report/" + page, { selectedbranch: selectedbranch, dateFrom: dateFrom, dateTo: dateTo},
	function(data) {		
		$('#iwd').html(data);
		rms_reloaderOff();
	});

}




</script>

