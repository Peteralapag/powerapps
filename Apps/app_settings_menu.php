<div class="dropdown-wrapper set_tings">
	<ul>
		<li><span><i class="fa-brands fa-app-store color-dodger"></i></span>Application Settings</li>
		<li onclick="loadModules('User_Management')"><span><i class="fa-solid fa-user-gear text-primary"></i></span>Manage User</li>
		<li onclick="showRequest()"><span><i class="fa-solid fa-seal-question color-dodger"></i></span>Request Dashboard</li>
		<li style="background:#f1f1f1;text-align:center;font-size:12px"><u>C</u>lose</li>
	</ul>
</div>
<script>
function showRequest()
{
	$('#modalicon').html('<i class="fa-solid fa-seal-question color-dodger"></i>');
	$('#modaltitle').html("REQUEST DASHBOARD");
	$.post("../Apps/request_dashboard.php", {  },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').show();
	});
}
function signOut()
{
	app_confirm("Signing Out","Are you sure to Sign Out?","warning","signingout","","red");
}
</script>
