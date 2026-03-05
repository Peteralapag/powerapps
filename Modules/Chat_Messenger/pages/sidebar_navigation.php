<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];
?>
<style>
.user-wrappers ul {
	list-style-type: none;
	padding:0;
	margin:0;
}
.user-wrappers ul li {
	display: flex;
	padding: 7px 0 7px 0;
	border-bottom:1px solid #f1f1f1;
	gap: 7px;	
}
.user-wrappers .avatar {
	border: 3px solid #aeaeae;
	background:#f1f1f1;
	height:45px;
	width:45px;
	border-radius: 50%;
}
.user-wrappers .user-name {
	flex-grow: 1;
}
.user-wrappers .user-name p {
	margin:0;
	padding:0;
	font-size:14px;
	justify-content: center;
}
.user-wrappers .short-msg {
	font-size:11px;
	color: #aeaeae;
}
.user-search-wrapper {
	position:relative;
	margin-bottom:5px;
}
.btn-wrapper div {
	width:70px;
	background:#f6f6f6;
}
.btn-wrapper div:hover {
	border: 1px solid dodgerblue;
}
.btn-wrapper {
	margin-bottom:5px;
}
.user-wrappers {
	cursor: pointer;
}
</style>
<div class="user-search-wrapper">
	<input id="search" type="text" class="search-input form-control form-control" placeholder="Search Messenger" autocomplete="none">
	<div class="search-box" id="searchbox">
		<div id="searchdata"></div>
	</div>
</div>
<div class="btn-wrapper">
	<div class="btn btn-lite btn-sm" onclick="loadInbox()">Inbox</div>
	<div class="btn btn-lite btn-sm" onclick="loadGroup()">Group</div>
</div>
<div class="user-wrappers">
	<ul id="chatbox"></ul>
</div>

<script>
var modulepath = '../Modules/Chat_Messenger';
$(function()
{
	$('#search').keyup(function()
	{
		$('.search-box').slideDown();
		$.post("../Modules/Chat_messenger/apps/users_dp_list.php", {  },
		function(data) {
			$('#searchdata').html(data);		
		});
		
		
	});
	loadInbox();
});
function loadInbox()
{
	$('#chatbox').load(modulepath + '/apps/users_inbox.php');
}
function loadGroup()
{
	$('#chatbox').load(modulepath + '/apps/group_inbox.php');
}

</script>

