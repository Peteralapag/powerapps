<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . "/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions();
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$transdate = $_SESSION['DBC_TRANSDATE'];

$vals = $function->dateLockChecking($transdate,$db);
?>

<style>
    .dbc-dashboard-shell {
        background: #ffffff;
        border: 1px solid #dfe3e7;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        padding: 14px;
        margin-bottom: 12px;
    }
    .dbc-dashboard-title {
        font-size: 16px;
        font-weight: 700;
        color: #2f3b4a;
        margin-bottom: 10px;
    }
    .dashboard-note {
        border: 1px solid #bde5c8;
        background: #f0fbf3;
        color: #1f6a36;
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 13px;
        margin-bottom: 12px;
    }
    .psaclass {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .date-shell {
        min-width: 160px;
    }
    .date-shell .form-control {
        height: 34px;
        border-radius: 6px;
        font-size: 13px;
    }
    .toggle-switch {
        display: none;
    }
    .toggle-label {
        position: relative;
        display: inline-block;
        width: 58px;
        height: 30px;
        background-color: #c9d1d8;
        border-radius: 999px;
        cursor: pointer;
        transition: background-color 0.25s;
        box-shadow: inset 0 0 0 1px rgba(0,0,0,0.08);
    }
    .toggle-label::before {
        content: "";
        position: absolute;
        width: 24px;
        height: 24px;
        left: 3px;
        top: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: transform 0.25s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .toggle-switch:checked + .toggle-label {
        background-color: #0d9f88;
    }
    .toggle-switch:checked + .toggle-label::before {
        transform: translateX(28px);
    }
    #switchdata {
        font-size: 13px;
        font-weight: 600;
        color: #2f3b4a;
    }
</style>
    
<div class="dbc-dashboard-shell">
    <div class="dbc-dashboard-title">Branch Ordering Control</div>
    <div class="dashboard-note">
        Use this switch to control branch ordering access and prevent off-schedule ordering that may cause report discrepancies.
    </div>
    <div class="psaclass">
        <div class="date-shell">
            <input id="transdate" type="date" class="form-control form-control-sm" value="<?php echo $transdate; ?>" onchange="dateselected()">
        </div>
        <input id="lockThisDate" type="checkbox" class="toggle-switch" onchange="submitOffThisDate('<?php echo $transdate?>')" <?php echo $vals == 1 ? 'checked' : ''; ?> value="Lock">
        <label for="lockThisDate" class="toggle-label"></label>
        <label id="switchdata"><?php echo $vals ? 'Branch Ordering Locked' : 'Branch Ordering Open'; ?></label>
    </div>
</div>

<div id="dashboardresult"></div>

<script>
function dateselected(){

	var mode = 'dashboarddateselected';
	var transdate = $('#transdate').val();
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, transdate: transdate },
	function(data) {
		$('#dashboardresult').html(data);
		rms_reloaderOff();
		$('#' + sessionStorage.navfds).trigger('click');
	});

}

function submitOffThisDate(transdate)
{
	
  	rms_reloaderOn('Loading...');	
	
	var mode = 'datelockChecker';
    var checkbox = document.getElementById("lockThisDate");
    var switchData = document.getElementById("switchdata");
    
    if (checkbox.checked) {
        switchData.textContent = 'Branch Ordering Locked';
        checboxValue = 1;
    } else {
        switchData.textContent = 'Branch Ordering Open';
        checboxValue = 0;
    }
    if (checkbox.checked) {  var checboxVals = 1; } else { var checboxVals = 0; }

    $.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, transdate: transdate, checboxVals: checboxVals },
	function(data) {
		$('#dashboardresult').html(data);
		rms_reloaderOff();
	});

}
</script>
