<div class="tabcontainer">
	<span class="search" style="width:300px">
		<input id="searchitem" type="text" class="form-control" placeholder="Search">
		<span class="search-icon"><i class="fa-sharp fa-solid fa-magnifying-glass"></i></span>
		<span class="search-clear" onclick="Clear_Search()"><i class="fa-solid fa-xmark"></i></span>
	</span>
	<span style="margin-left: auto">
		<button class="btn btn-primary" onclick="openAccessList()">Access</button>
		<button class="btn btn-warning" onclick="load_content()">Reload</button>
	</span>
</div>
<div class="users-list-data" id="userslistdata"></div>
<script>
function openAccessList()
{
	$.post("./Modules/User_Management/includes/access_list.php", { },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
$(function()
{
	$('#searchitem').keyup(function()
	{
		var search = $('#searchitem').val();
		$.post("./Modules/User_Management/includes/users_list_data.php", { search: search },
		function(data) {
			$('#userslistdata').html(data);
		});
	});
	load_content();
});
function Clear_Search()
{
	$('#searchitem').val('');
	load_content();
}
function load_content()
{
	rms_reloaderOn();
	setTimeout(function()
	{
		$.post("./Modules/User_Management/includes/users_list_data.php", { },
		function(data) {
			$('#userslistdata').html(data);	
			rms_reloaderOff();	
		});
	},500);
}
</script>