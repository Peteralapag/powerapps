<style>
.profile-wrapper ul {display:flex;flex-direction:column; flex:1;position:relative;height:200px;}
.profile-wrapper li {gap: 10px;display: inline-block;width:100%;border-bottom:1px solid #e7e7e7;font-size:16px;}
.profile-wrapper li span {margin-right:20px;}
.profile-wrapper li:last-child {margin-top:auto;border-bottom:0;border-top:1px solid #e7e7e7;align-self: flex-end;}
.profile-wrapper li:hover {background:#dbe3ec}
</style>
<div class="profile-wrapper">
	<ul>
		<li onclick="changePassword()"><span><i class="fa-solid fa-user-gear text-primary"></i></span>Change Password</li>
		<li onclick="myThemes()"><span><i class="fa-solid fa-image color-orange"></i></span>My Themes</li>
		<li onclick="signOut()"><span><i class="fa-solid fa-right-from-bracket color-red"></i></span>Sign Out</li>
	</ul>
</div>
<script>
function changePassword()
{
	$('#modaltitle').html("Change Password");
	$('#modalicon').html('<i class="fa-solid fa-key color-yellow"></i>');	
	$.post("../Apps/change_account_info.php", { },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();		
	});
}
function myThemes()
{
	$('#modaltitle').html("System Wallpaper");
	$('#modalicon').html('<i class="fa-solid fa-image color-dodger"></i>');	
	$.post("../Apps/my_themes.php", { },
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
