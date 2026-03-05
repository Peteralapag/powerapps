<?php
include '../init.php'; 

if($_POST['module'] == 'Written_Documents')
{
	$module = $_POST['module'];
	$file_id = $_POST['file_id']; 
	$file_name = $_POST['file_name'];
	$organization = $_POST['organization'];
	$subfolder = $_POST['subfolder'];
	$file_PATH = WD_PATH."/".$module."/".$organization."/".$subfolder."/".$file_name;
} else {
	exit();
}
?>
<body oncontextmenu="return false;">
<style>
.takips {
	position:absolute;
	height:px;
	background:#fff;
	background:transparent;
	width:calc(100vw - 401px);
	height: calc(100vh - 200px);
	border:1px solid red;
}
.iframe-wrap {
	width:100%;
	height:100%;
}
.not-exist {
	position:absolute;
	top: 52%;
	left: 50%;
	-webkit-transform: translate(-50%, -50%);
	font-weight:bold;
}
</style>
<div style="width:80vw; height:78vh">
<div class="takip"></div>
<?php
if(file_exists($file_PATH)) { ?>
	<embed id="embeded" class="iframe-wrap"  src="<?php echo $file_PATH; ?>#toolbar=1" style="width:100%; height: 100%"></embed>
<?php } else { ?>
	<span class="not-exist"><h1>FILE NOT EXIST</h1></span>
<?php } ?>
</div>