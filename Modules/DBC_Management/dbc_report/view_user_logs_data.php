<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.inventory.php";
$function = new DBCFunctions;
$inventory = new DBCInventory;

$_SESSION['DBC_SUMMARY_DATEFROM'] = $_POST['dateFrom'];
$_SESSION['DBC_SUMMARY_DATETO'] = $_POST['dateTo'];

$datefrom = $_POST['dateFrom'];
$dateto = $_POST['dateTo'];

$startDate = new DateTime($datefrom);
$endDate = new DateTime($dateto);

?>
<style>

.sticky-column {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    background-color: #a4cfc8;
    z-index: 2 !important;
    white-space: nowrap;
}

.sticky-column-1 {
    left: 0;
    z-index: 1;
}
.sticky-column-1-data {
    left: 0;
    background:white !important;
}

.sticky-column-2 {
    left: 35px;
}
.sticky-column-2-data {
    left: 35px;
    background:white !important;
}

.sticky-column-3 {
    left: 140px;
}
.sticky-column-3-data {
    left: 140px;
    background:white !important;
}

.sticky-column-4 {
    left: 478px;
}
.sticky-column-4-data {
    left: 478px;
    background:white !important;
}

.sticky-column-5{
    left: 577px;
}
.sticky-column-5-data {
    left: 577px;
    background:white !important;
}

.sticky-header {
    position: -webkit-sticky;
    position: sticky;
    top: 0;
    background-color: #f5f5f5;
    z-index: 3 !important;
}


</style>
<table style="width:100%" class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>#</th>
			<th>TRANSDATE</th>
			<th>ACTIVITY</th>
			<th>USER</th>
		</tr>
	</thead>
	<tbody>
<?php
	$sqlQuery = "SELECT * FROM dbc_audit_logs WHERE DATE(log_date) BETWEEN '$datefrom' AND '$dateto'";
	$results = mysqli_query($db, $sqlQuery);
	if ($results->num_rows > 0) {
	    $i = 0;
	    while ($INVROW = mysqli_fetch_array($results))
	    {
		    $i++;
		    
		    $transdate = $INVROW['log_date'];	    
		    $activity = $INVROW['activity'];
			$user = $INVROW['log_by'];
			
			echo '<tr>';
			
			echo '<td>'.$i.'</td>';
			echo '<td>'.$transdate.'</td>';
			echo '<td>'.$activity.'</td>';
			echo '<td>'.strtoupper($user).'</td>';
			
			echo '</tr>';
		}
	} else {	



} ?>
	</tbody>
</table>

