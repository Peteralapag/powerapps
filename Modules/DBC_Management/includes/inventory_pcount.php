<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
$module = 'Inventory Physical Count';
$username = $_SESSION['dbc_username'];
$userlevel = $_SESSION['dbc_userlevel'];
$permission = 'p_edit';
if(isset($_SESSION['DBC_SHOW_LIMIT']))
{
	$show_limit = $_SESSION['DBC_SHOW_LIMIT'];
} else {
	$show_limit = '50';
}
if(isset($_SESSION['DBC_CLUSTER']))
{
	$cluster = $_SESSION['DBC_CLUSTER'];
	if(isset($_SESSION['DBC_BRANCH']))
	{
		$thebranch = $_SESSION['DBC_BRANCH'];
	} else {
		$thebranch = '';
	}
} else {
	$cluster = '';
	$thebranch = '';
}
if(isset($_SESSION['DBC_ITEM_LOCATION']))
{
	$location = $_SESSION['DBC_ITEM_LOCATION'];
} else {
	$location = "";
}
if(isset($_SESSION['DBC_TRANSDATE']))
{
	$trans_date = $_SESSION['DBC_TRANSDATE'];
} else {
	$trans_date = date("Y-m-d");
}
?>
<style>
.smnav-header input[type=text]{width:270px;margin-left: 10px;}
.smnav-header select {width:70px;}
.reload-data {display: flex;gap: 20px;margin-left: auto;right:0;}
.tableFixHead {margin-top:15px;background:#fff;}
.tableFixHeadPcount { overflow: auto; height: calc(100vh - 208px); width:100% }
.tableFixHeadPcount thead th { position: sticky; top: 0; z-index: 1; background:#e6e6e6; color:#000; font-weight:normal }
.tableFixHeadPcount table  { border-collapse: collapse;}
.tableFixHeadPcount th, .tableFixHeadPcount td { font-size:14px; white-space:nowrap } 
.clear-icon {position:absolute;right:6px;top: 0;font-size: 22px;cursor: pointer;}
.clear-icon:hover {color: red;}
.search-icon {position:absolute;top: 0;left:6px;font-size: 22px;cursor: pointer;	}
#search {padding-right:30px;padding-left:30px;}
</style>
<div class="subnav-header">
	<span style="position:relative">
		<input id="search" type="text" class="form-control form-control-sm" style="width: 300px" placeholder="Search Itemcode, Item name">
		<i class="fa-solid fa-magnifying-glass search-icon color-dodger"></i>
		<i class="fa-solid fa-circle-xmark clear-icon" onclick="clearSearch()"></i>
	</span>
	<select id="location" class="form-control form-control-sm" style="width:200px;margin-right:10px" disabled>
		<?PHP echo $function->GetItemLocation('DAVAO BAKING CENTER',$db); ?>
	</select>
	<input id="transdate" type="date" class="form-control form-control-sm" style="width:150px" value="<?php echo $trans_date; ?>" onchange="load_pcount()">
	<!-- button class="btn btn-primary btn-sm color-white" onclick="loadDataNow()"><i class="fa-solid fa-file-arrow-down"></i>&nbsp;&nbsp;Reload Inventory</button -->
	<span class="reload-data">
	<?php if($function->GetModulePermission($username,$userlevel,$module,$permission,$db) == 0) { ?>
		<button class="btn btn-danger btn-sm" style="cursor:default" disabled><i class="fa-solid fa-eye"></i>&nbsp;&nbsp;Read Only</button>
	<?php } ?>
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px;text-align:center" class="form-control form-control-sm" onchange="load_pcount()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHeadPcount" id="phycountdata"><div style="margin-left:10px"></div></div>
<script>
function clearSearch()
{
	$('#search').val('');
	load_pcount();
}
function load_pcount()
{
	var limit = $('#limit').val();
	var cluster = $('#cluster').val();
	var branch = $('#branches').val();
	var location = $('#location').val();
	var trans_date = $("#transdate").val();	
	rms_reloaderOn('Loading...');
	$.post("./Modules/DBC_Management/includes/phycount_data.php", { location: location, trans_date: trans_date, limit: limit },
	function(data) {		
		$('#phycountdata').html(data);
		rms_reloaderOff();
	}); 
}
$(document).ready(function()
{
	$('#search').keyup(function()
	{
		var search = $('#search').val();
		var limit = $('#limit').val();
		var cluster = $('#cluster').val();
		var branch = $('#branches').val();
		var location = $('#location').val();
		var trans_date = $("#transdate").val();
		$.post("./Modules/DBC_Management/includes/phycount_data.php", { search: search, location: location, trans_date: trans_date, limit: limit },
		function(data) {		
			$('#phycountdata').html(data);
			rms_reloaderOff();
		});	
	});
	load_pcount();		
});
</script>