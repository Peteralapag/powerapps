<?php
include '../../../init.php';

$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;

// Update session variables
$_SESSION['DBC_REPORT_PAGE']     = $_POST['reports']    ?? '';
$_SESSION['DBC_RECIPIENT_REPORT'] = $_POST['recipient'] ?? '';
$_SESSION['DBC_BRANCH_REPORT']   = $_POST['branch']     ?? '';

$datefrom = $_SESSION['DBC_SUMMARY_DATEFROM'] ?? '';
$dateto   = $_SESSION['DBC_SUMMARY_DATETO']   ?? '';
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
.subpage-wrapper {
    margin-top: 5px;
    border: 1px solid #aeaeae;
    border-bottom: 3px solid #aeaeae;
    background: #fff;
    display: flex;
    gap: 5px;
    padding: 10px;
    min-width: 600px;
    overflow-x: auto;
    white-space: nowrap;
}
.tableFixHead {
    margin-top: 5px;
    overflow: auto;
    height: calc(100vh - 272px);
    width: 100%;
    background: #fff;
}
.tableFixHead thead th {
    position: sticky;
    top: 0;
    z-index: 1;
    background: green;
    color: #fff;
}
.tableFixHead table {
    border-collapse: collapse;
}
.tableFixHead th,
.tableFixHead td {
    font-size: 14px;
    white-space: nowrap;
}
</style>

<div class="subpage-wrapper">
    <input type="date" id="datefrom" class="form-control form-control-sm" style="width:150px"
           value="<?= htmlspecialchars($datefrom) ?>" placeholder="Date from">
    <input type="date" id="dateto" class="form-control form-control-sm" style="width:150px"
           value="<?= htmlspecialchars($dateto) ?>" placeholder="Date to">
    <button id="searchbtn" class="btn btn-info btn-sm">Search</button>
</div>

<div class="tableFixHead" id="iwd"></div>

<script>


$(function() {

    const $dateFrom = $('#datefrom');
    const $dateTo = $('#dateto');
    const $result = $('#iwd');
    const MAX_DAYS = 31;

    $('#searchbtn').on('click', searchItem);

    function markError(input, hasError) {
        input.css('border', hasError ? '2px solid red' : '');
    }

    function searchItem() {
        const dateFrom = $dateFrom.val();
        const dateTo = $dateTo.val();

        // Reset borders
        markError($dateFrom, false);
        markError($dateTo, false);

        // Validate empty fields
        if (!dateFrom || !dateTo) {
            markError($dateFrom, !dateFrom);
            markError($dateTo, !dateTo);
            return app_alert("System Message", "Please fill in both Date From and Date To fields.", "warning");
        }

        const fromDate = new Date(dateFrom);
        const toDate = new Date(dateTo);

        // Validate date format
        if (isNaN(fromDate) || isNaN(toDate)) {
            markError($dateFrom, isNaN(fromDate));
            markError($dateTo, isNaN(toDate));
            return app_alert("System Message", "Please enter valid dates.", "warning");
        }

        // Validate range
        if (fromDate > toDate) {
            markError($dateFrom, true);
            markError($dateTo, true);
            return app_alert("System Message", "Date From must be earlier than Date To.", "warning");
        }

        // Check if within limit
        const daysDiff = (toDate - fromDate) / (1000 * 3600 * 24);
        if (daysDiff >= MAX_DAYS) {
            markError($dateFrom, true);
            markError($dateTo, true);
            return app_alert("System Message", "Must be within 31 days maximum.", "warning");
        }

        
        
//        console.log("Sending AJAX:", dateFrom, dateTo); // 👈 debug log

	    rms_reloaderOn('Searching...');
	    $.post("./Modules/DBC_Management/dbc_report/monthly_inventory_report_data.php", 
	        { dateFrom: dateFrom, dateTo: dateTo },
	        function(data) {
	            console.log("Response received:");
	            console.log(data); // 👈 makita kung unsay gi return
	            $('#iwd').html(data);
	            rms_reloaderOff();
	        }
	    ).fail(function(xhr, status, error) {
	        console.error("AJAX failed:", status, error);
	        console.log(xhr.responseText); // 👈 ipakita error sa console
	        rms_reloaderOff();
	    });
        
        
    }
});
</script>
