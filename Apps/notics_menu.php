<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
if($functions->GetCount('tbl_app_request','approved','DOCUMENT MANAGEMENT',$username,$db) > 0)
{
	$dmscnt = $functions->GetCount('tbl_app_request','approved','DOCUMENT MANAGEMENT',$username,$db);
	$class_cnt = "cnt";
} else {
	$dmscnt = '';
	$class_cnt = "cnt-none";
}
?>
<style>
.cnt-none {
	display:none;
}
.cnt {
	border:1px solid red;
	font-size:9px;
	width:17px;
	height:18px;
	padding:2px ;
	border-radius:50%;
	background:red;
	color:#fff;
	top: 2px;
	margin-left: auto;
	align-content: center;
	text-align:center;
}
</style>
<div class="dropdown-wrapper set_tings">
	<ul>
		<li style="display: flex" onclick="openNotifs()"><span><i class="fa-brands fa-app-store color-dodger"></i></span> DMS Notifs <span class="<?php echo $class_cnt; ?>"><?php echo $dmscnt; ?></span></li>
		<li onclick="loadModules('Chat_Messenger')"><span><i class="fa-solid fa-user-gear text-primary"></i></span>Messenger</li>
		<li style="background:#f1f1f1;text-align:center;font-size:12px"><u>C</u>lose</li>
	</ul>
</div>
<script>
function signOut()
{
	app_confirm("Signing Out","Are you sure to Sign Out?","warning","signingout","","red");
}
</script>
