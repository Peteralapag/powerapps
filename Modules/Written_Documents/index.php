<?php
session_start();
if(!isset($_SESSION['wd_username'])) { ?>
<script>
$(function()
{
	$('#modaltitle').html("Written Documents Login");
	$('#modalicon').html('<i class="fa-solid fa-user color-dodger"></i>');	
	$.post("../Modules/Written_Documents/apps/login.php", { },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();		
	});
});
</script>	
<?php exit(); } ?>
<link rel="stylesheet" href="../Modules/Written_Documents/styles/styles.css">
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
	$('#logotitle').load('../Modules/Written_Documents/apps/logo_titple.php');
	$('#navigation').load("../Modules/Written_Documents/pages/sidebar_navigation.php");
});
/* ############ FILE INFORMATION FORM ############ */
function openThisFile(file_id,file_name,organization)
{
	closeModal('formmodal');
	var module = sessionStorage.module;
	if(organization == '')
	{
		var organization = sessionStorage.organization;
	}
	var subfolder = sessionStorage.subfolder;
	$('#modaltitlePDF').html("File: " + file_name);
	$('#modalicon').html('<i class="fa-solid fa-circle-info color-dodger"></i>');
	$.post("../pages/pdf_viewer.php", { module: module, file_id: file_id, file_name: file_name, organization: organization, subfolder: subfolder },
	function(data) {
		$('#pdfviewer_page').html(data);		
		$('#pdfviewer').show();
	});
}
</script>