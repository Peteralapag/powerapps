<?php
session_start();
?>
<link rel="stylesheet" href="../Modules/Chat_Messenger/styles/styles.css">
<!-- @@@@@@@@@@ WRITTEN DOCUMENTS V2 @@@@@@@@@@@ -->
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
	$('#logotitle').load('../Modules/Chat_Messenger/apps/logo_titple.php');
	$('#navigation').load("../Modules/Chat_Messenger/pages/sidebar_navigation.php");
});
</script>