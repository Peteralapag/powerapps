<link rel="stylesheet" href="../Modules/Warehouse_Report/styles/styles.css">
<!-- @@@@@@@@@@ WAREHOUSE REPORTER @@@@@@@@@@@ -->
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
	$('#logotitle').load('../Modules/Warehouse_Report/apps/logo_titple.php');
	$('#navigation').load("../Modules/Warehouse_Report/pages/sidebar_navigation.php");
});
</script>