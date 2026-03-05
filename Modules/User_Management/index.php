<link rel="stylesheet" href="../Modules/User_Management/styles/styles.css">
<!-- @@@@@@@@@@ USER MANAGEMENT @@@@@@@@@@@ -->
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
	$('#logotitle').load('../Modules/User_Management/apps/logo_titple.php');
	$('#navigation').load("../Modules/User_Management/pages/sidebar_navigation.php");
	$('#contents').load('../Modules/User_Management/apps/users_list.php');
});
</script>
<script src="../Modules/User_Management/js/toast.js"></script>