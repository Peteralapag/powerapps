<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];
?>
<style>
.wh-reporter ul {
	list-style-type: none;
	padding:0;
	margin:0;
}
.wh-reporter li {
	padding:5px;
	margin-bottom:5px;
	border-bottom:1px solid #aeaeae;
	cursor:pointer;
}
.wh-reporter li:hover {
	background:#f1f1f1;
}
</style>
<div class="wh-reporter">
	<ul>
		<li onclick="inventoryList()"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;INVENTORY DATA</li>
		<li onclick="receivingItem()"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;RECEIVING</li>
		<li onclick="ordering()"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;ORDERING</li>
	</ul>	
</div>
<script>
$(function()
{
	inventoryList();
});
function ordering()
{
	$('#contents').load('modules/' + sessionStorage.module + '/apps/ordering.php');
}
function receivingItem()
{
	$('#contents').load('modules/' + sessionStorage.module + '/apps/receiving.php');
}
function inventoryList()
{
	$('#contents').load('modules/' + sessionStorage.module + '/apps/inventory_list.php');
}
</script>