<?php
session_start();
define("MODULE_NAME", "Binalot Management");
$user = isset($_SESSION['binalot_acctname']) ? $_SESSION['binalot_acctname'] : '';
if(!isset($_SESSION['binalot_username'])) { ?>
<script>
$(function()
{
	$('#modaltitle').html("BINALOT Login");
	$('#modalicon').html('<i class="fa-solid fa-user color-dodger"></i>');	
	$.post("../Modules/Binalot_Management/apps/login.php", { },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();		
	});
});
</script>	
<?php exit(); } ?>
<link rel="stylesheet" href="../Modules/Binalot_Management/styles/styles.css">
<script src="../Binalot_Management/scripts/script.js"></script>
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
	$('#logotitle').load('../Modules/Binalot_Management/apps/logo_title.php');
	$('#navigation').load("../Modules/Binalot_Management/pages/sidebar_navigation.php");
});

// Function to initialize WebSocket connection
function connectWBPSA() {
	var username = '<?php echo $user?>';
    connbinalot = new WebSocket("ws://192.168.1.9:8080?username="+username+"&columnonline=binalot_online");
/*
    connbinalot.onopen = function () {
        console.log("WebSocket is OPEN");
    };

    connbinalot.onerror = function (error) {
        console.error("WebSocket Error:", error);
    };
*/
    connbinalot.onclose = function () {
//      console.warn("WB PSA CLOSED. Reconnecting...");
        setTimeout(connectWBPSA, 3000);
    };
}

// Initialize ang WBPSA
connectWBPSA();

</script>