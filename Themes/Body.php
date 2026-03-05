<?php
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$dms = $functions->GetCount('tbl_app_request','approved','DOCUMENT MANAGEMENT',$username,$db);
if($dms > 0)
{
	$dms_indi = '<i class="fa-solid fa-circle sm-circle color-orange"></i>';
} else {
	$dms_indi = '';
}
?>


<style>
.main-bg { background: url('../Images/media/<?php echo BG_WALLPAPER; ?>') no-repeat;background-size: cover;	background-position: center;	}
.sm-circle {
	position:absolute;
	font-size:8px
}
.footer-text {
	display:flex;
	align-items:center;
	justify-content:center;
	gap:8px;
}
.footer-logo {
	height:16px;
	width:auto;
	opacity:0.9;
}
</style>
<div class="left-wrapper" id="leftwrapper"></div>
<div class="main-content">
	 <header id="header">
	 	<ul>
	 		<li class="profile-hover" style="width:60px;text-align:center;position:relative" onclick="DropDownMenu('showmodules','modules')">
				<i class="fa fa-bars"></i>
				<div class="header-main-downdownmenu-wrapper" id="showmodules"></div>
			</li>
			<li style="border:0;cursor:default"><strong><span class="modulenames">Application Management</span></strong></li>
			<?php if($_SESSION['application_userlevel'] >= 80) { ?>
	 		<li class="profile settings profile-hover" style="position:relative" onclick="DropDownMenu('settingsdd','app_settings')">
				<i class="fa-solid fa-gear"></i>
				<div class="header-downdownmenu-wrapper" style="width:320px !important" id="settingsdd"></div>
			</li>
			<?php } ?>
	 		<li class="profile profile-hover" style="position:relative" onclick="DropDownMenu('profiledd','profile')">
				<i class="fa-solid fa-user"></i> <?php echo ucwords($_SESSION['application_appnameuser']); ?>
				<div class="header-downdownmenu-wrapper" style="overflow:hidden" id="profiledd"></div>
			</li>
			<li class="profile settings profile-hover" style="position:relative" onclick="DropDownMenu('noticsdata','notics')">
				<i class="fa-solid fa-bell"></i><?php echo $dms_indi; ?>
				<div class="header-downdownmenu-wrapper" style="width:240px !important" id="noticsdata"></div>
			</li>
	 	</ul>
	 </header>
	<main class="main" id="main" style="position:relative"></main>
	<div class="footer-text"><img src="../Images/jathnier_logo.png" alt="Jathnier Corporation" class="footer-logo"> <span>© 2023 Jathnier Corporation. All Rights Reserved.</span></div>
	<div id="jbc"></div>
</div>
<script>
function DropDownMenu(container,applications)
{
	console.log(applications);
	$.post("./Apps/" + applications + "_menu.php", { },
	function(data) {
		$('#' + container).html(data);
		if($('#' + container).is(":visible"))
		{
			$('#' + container).slideUp();
			$('#' + container).empty();
		} else {
			$('#' + container).slideDown();
		}
	});
}
$(document).ready(function()
{
	$('#main').addClass('main-bg');
	if(sessionStorage.module != null)
	{	
		$('.modulenames').html(sessionStorage.module.split("_").join(" "));
		$('#main').load('Modules/' + sessionStorage.module);
	} else {
		loadModules('My_Desktop');
	}
    var idleInterval = setInterval( timerIncrement, 60000 );
    $(this).mousemove(function (e) { idleTime = 0; });
    $(this).keypress(function (e) { idleTime = 0; });
});
function timerIncrement()
{
    var idleTime = idleTime + 1;
//    console.log(idleTime);
    if (idleTime > 19)
    {
       app_alert("Session Time out","You have been idle for 20 minutes. You`re about to be signed out","warning","Ok","","signingout");	      
    }
}
</script>