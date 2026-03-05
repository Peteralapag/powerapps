<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php");
$function = new BINALOTFunctions;
$datenow = date('Y-m-d');
?>

<table style="width: 100%" class="table">
    <tr>
        <td>
            <input type="text" id="datefrom" class="form-control form-control-sm" value="<?php echo $datenow?>">
        </td>
        <td>
            <input type="text" id="dateto" class="form-control form-control-sm" value="<?php echo $datenow?>">
        </td>
    </tr>
</table>
<div style="float:right">
    <button type="button" class="btn btn-success" onclick="selectDateRange()">Go</button>
</div>

<script>

$(function() {
    $("#datefrom").datepicker();
    $("#dateto").datepicker();
});

function selectDateRange() {
    var datefrom = document.getElementById('datefrom').value;
    var dateto = document.getElementById('dateto').value;

    rms_reloaderOn('Loading...');	
	$.post("./Modules/Binalot_Management/includes/branch_order_receiving_data.php", { datefrom: datefrom, dateto: dateto },
	function(data) {
		$('#formmodal').fadeOut();		
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});

}

</script>
