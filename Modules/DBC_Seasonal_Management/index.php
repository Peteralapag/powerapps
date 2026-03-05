<?php
session_start();
define("MODULE_NAME", "DBC Seasonal Management");
if(!isset($_SESSION['dbc_seasonal_username'])) { ?>
<script>
$(function()
{
	$('#modaltitle').html("DBC Seasonal Login");
	$('#modalicon').html('<i class="fa-solid fa-user color-dodger"></i>');	
	$.post("../Modules/DBC_Seasonal_Management/apps/login.php", { },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();		
	});
});
</script>	
<?php exit(); } ?>
<link rel="stylesheet" href="../Modules/DBC_Seasonal_Management/styles/styles.css">
<script src="../DBC_Seasonal_Management/scripts/script.js"></script>
<!-- @@@@@@@@@@ ################### @@@@@@@@@@@ -->
<div class="sidebar">
	<div class="logo-title" id="logotitle"></div>
	<div class="navigation" id="navigation"></div>
</div>
<div class="content-wrapper">
	<div class="contents" id="contents"></div>
</div>
<script>
$(function()
{
	var module = '<?php echo MODULE_NAME; ?>';
	$('#logotitle').load('../Modules/DBC_Seasonal_Management/apps/logo_title.php');
	$('#navigation').load("../Modules/DBC_Seasonal_Management/pages/sidebar_navigation.php");
});
</script>