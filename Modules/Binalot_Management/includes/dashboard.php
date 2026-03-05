<?php
include '../../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . "/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions();
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$transdate = $_SESSION['BINALOT_TRANSDATE'];

$vals = $function->dateLockChecking($transdate,$db);
?>

<style>
    .container-fluid {
        margin: 20px;
    }
    .psaclass {
        display: flex;
        align-items: center;
    }
    .toggle-switch {
        display: none;
    }
    .toggle-label {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
        background-color: #ccc;
        border-radius: 34px;
        cursor: pointer;
        transition: background-color 0.4s;
        margin-left: 10px;
    }
    .toggle-label::before {
        content: "";
        position: absolute;
        width: 26px;
        height: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.4s;
    }
    .toggle-switch:checked + .toggle-label {
        background-color: #2196F3;
    }
    .toggle-switch:checked + .toggle-label::before {
        transform: translateX(26px);
    }
    .date-shell {
        margin-right: 10px;
    }
</style>
    
<div class="">
    <div class="alert alert-success">
        This switch is to control those at the branch to prevent them from ordering at the wrong time and to avoid errors in the reports.
    </div>
    <div class="psaclass">
        <div class="date-shell">
            <input id="transdate" type="date" class="form-control form-control-sm" style="width:150px" value="<?php echo $transdate; ?>" onchange="dateselected()">
        </div>
        <input id="lockThisDate" type="checkbox" class="toggle-switch" onchange="submitOffThisDate('<?php echo $transdate?>')" <?php echo $vals == 1 ? 'checked' : ''; ?> value="Lock">
        <label for="lockThisDate" class="toggle-label"></label>
        <label id="switchdata" style="margin-left:5px"><?php echo $vals ? 'Branch Ordering Locked' : 'Branch Ordering Open'; ?></label>
    </div>
</div>

<div id="dashboardresult"></div>

<script>
function dateselected(){

	var mode = 'dashboarddateselected';
	var transdate = $('#transdate').val();
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, transdate: transdate },
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

    $.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, transdate: transdate, checboxVals: checboxVals },
	function(data) {
		$('#dashboardresult').html(data);
		rms_reloaderOff();
	});

}
</script>
