<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/DBC_Management/class/Class.functions.php";
$function = new DBCFunctions;
if(isset($_SESSION['DBC_SHOW_LIMIT']))
{
	$show_limit = $_SESSION['DBC_SHOW_LIMIT'];
} else {
	$show_limit = '50';
}
?>
<style>
.itemlist-shell {
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:10px;
	box-shadow:0 1px 3px rgba(0,0,0,0.06);
	padding:12px;
}
.itemlist-title {
	font-size:15px;
	font-weight:700;
	color:#2f3b4a;
	margin-bottom:8px;
}
.smnav-header {
	display:flex;
	align-items:center;
	gap:8px;
	flex-wrap:wrap;
	padding-bottom:8px;
	border-bottom:1px solid #e9ecef;
}
.smnav-header input[type=text] {
	width:100%;
	padding-left:26px;
	padding-right:27px;
	height:34px;
	font-size:13px;
	border-radius:6px;
}
.smnav-header select {
	height:34px;
	font-size:13px;
	border-radius:6px;
	width:220px;
}
.smnav-header .btn {
	height:34px;
	font-size:12px;
	font-weight:600;
	padding:6px 12px;
}
.reload-data {
	display:flex;
	align-items:center;
	gap:8px;
	margin-left:auto;
}
.search-shell {
	position:relative;
	width:280px;
}
.search-magnifying {
	position:absolute;
	top:7px;
	left:8px;
	color:#6c757d;
}
.search-xmark {
	position:absolute;
	top:5px;
	right:7px;
	font-size:18px;
	cursor:pointer;
	color:#6c757d;
}
.search-xmark:hover {color:#dc3545;}
.tableFixHead {
	margin-top:12px;
	background:#fff;
	border:1px solid #dfe3e7;
	border-radius:8px;
	overflow:auto;
	height:calc(100vh - 255px);
	width:100%;
}
.tableFixHead thead th {
	position:sticky;
	top:0;
	z-index:1;
	background:#16a8a2;
	color:#fff;
	font-size:12px;
	font-weight:600;
}
.tableFixHead table { border-collapse: collapse; }
.tableFixHead th, .tableFixHead td { font-size:13px; white-space:nowrap; }
</style>
<div class="itemlist-shell">
	
	<div class="smnav-header">
	<button class="btn btn-primary btn-sm" onclick="itemsForm('add')">Add Item</button>
	<button class="btn btn-success btn-sm" onclick="reload_data()">Reload Item Lists</button>
	<div class="search-shell">
		<input id="search" type="text" class="form-control form-control-sm" placeholder="Search Items/Products">	
		<i class="fa-sharp fa-solid fa-magnifying-glass search-magnifying"></i>
		<i class="fa-solid fa-circle-xmark search-xmark" onclick="clearSearch()"></i>
	</div>
	<select id="category" class="form-control form-control-sm" onchange="selectCategory(this.value)" disabled>
		<?php echo $function->GetItemCategory('BREADS',$db)?>
	</select>
	<span class="reload-data">
		<span style="margin-left:20px;margin-top:4px;">Show</span>
		<select id="limit" style="width:70px" class="form-control form-control-sm" onchange="load_data()">
			<?php echo $function->GetRowLimit($show_limit); ?>
		</select>
	</span>
</div>
<div class="tableFixHead" id="smnavdata">Loading... <i class="fa fa-spinner fa-spin"></i></div>
</div>

<script>
function selectCategory(category)
{
	var limit = '';
	if(category != '')
	{
		$.post("./Modules/DBC_Management/includes/itemlist_data.php", { limit: limit, category: category},
		function(data) {		
			$('#smnavdata').html(data);
		});
	} else {
		load_data();
	}
}
function itemsForm(params)
{
	$('#modaltitle').html("ADD ITEMS");
	$.post("./Modules/DBC_Management/apps/itemlist_form.php", { params: params },
	function(data) {		
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
$(function()
{
	$('#search').keyup(function()
	{
		if($('#category').val() != '')
		{
			var limit = '';
			var search = $('#search').val();
			var category = $('#category').val()
		} else {
			var limit = '';
			var search = $('#search').val();
			var category = '';
		}		
		$.post("./Modules/DBC_Management/includes/itemlist_data.php", { limit: limit, search: search, category: category },
		function(data) {
			$('#smnavdata').html(data);
		});

	});
	load_data();
});
function reload_data()
{
	$('#' + sessionStorage.navfds).trigger('click');
}
function clearSearch()
{
	$('#search').val('');
	reload_data();
}
function load_data()
{
	var limit = $('#limit').val();
	rms_reloaderOn("Loading data...");
	$.post("./Modules/DBC_Management/includes/itemlist_data.php", { limit: limit },
	function(data) {
		$('#smnavdata').html(data);
		rms_reloaderOff();
	});
}
</script>