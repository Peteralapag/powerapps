<?php
session_start();
define("MODULE_NAME", "DBC Management");
if(!isset($_SESSION['dbc_username'])) { ?>
<script>
$(function()
{
	$('#modaltitle').html("DBC Login");
	$('#modalicon').html('<i class="fa-solid fa-user color-dodger"></i>');	
	$.post("../Modules/DBC_Management/apps/login.php", { },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();		
	});
});
</script>	
<?php exit(); } ?>
<link rel="stylesheet" href="../Modules/DBC_Management/styles/styles.css">
<script src="../Modules/DBC_Management/scripts/script.js"></script>
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
	$('#logotitle').load('../Modules/DBC_Management/apps/logo_title.php');
	$('#navigation').load("../Modules/DBC_Management/pages/sidebar_navigation.php");
});
</script>