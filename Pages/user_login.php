<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOGIN - AMS</title>
<link rel="stylesheet" href="Styles/fa/css/all.css">
<link rel="stylesheet" href="Styles/bootstrap-5.0.2/bootstrap.min.css">
<link rel="stylesheet" href="Styles/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="Styles/login_style.css">
<link rel="stylesheet" href="Plugins/loader/loader.css">
<script src="Scripts/jquery.min.js"></script>
<script src="Scripts/bootstrap-5.0.2/bootstrap.min.js"></script>
<link rel="stylesheet" href="Styles/jquery-ui.css">
<script src="Scripts/sweetalert.min.js"></script>
</head>
<body id="body">
	<div class="logo"></div>
	<div class="app-name"><h1>APPLICATION MANAGEMENT SYSTEM</h1></div>
	<div class="wrapper">
		<div class="form-box login">
			<h2>AMS Sign In</h2>
			<div class="input-box">
				<span class="icon"><i class="fa-solid fa-user"></i></span>
				<input id="uname" type="text" required>
				<label>Username</label>
			</div>
			<div class="input-box">
				<span class="icon"><i class="fa-solid fa-key"></i></span>
				<input id="upass" type="password" required>
				<label>Password</label>
			</div>
			<div class="login-button">
				<button id="submitlogin" class="btn btn-info btn-lg w-100" onclick="pushLogin()">Sign In</button>
			</div>
			<div style="margin-top:10px;text-align:center;margin-bottom:40px"><span class="register" onclick="registerUser()">WMS Branch Registration</span></div>
		</div>
	</div>
	<div class="page-loader-bd">
		<div class="page-loader"><i class="fa fa-spinner fa-spin"></i></div>
	</div>
	<div class="loginresults"></div>
<div class="login-footer">© 2023 Jathnier Corporation. All Rights Reserved.</div>
</body>
<?php

include 'Plugins/loader/loader.php';
include 'Plugins/modals/modals.php';
?>
<script>
function registerUser()
{
	$('#modaltitle').html('USER REGISTRATION');	
	$('#modalicon').html('<i class="fa-solid fa-user-plus"></i>');
	$.post("../Modules/User_Management/register/register_form.php", {  },
	function(data) {
		$('#formmodal_page').html(data);
		$('#formmodal').fadeIn();
	});
}
function pushLogin()
{
	var mode = 'c10cc6b684e1417e8ffa924de1e58373';
	var usr = $('#uname').val();
	var psw = $('#upass').val();
	if(usr == '')
	{
		app_alert("Login Error","Invalid Username","warning","Ok","uname","focus");
		return false;
	}
	else if(psw == '')
	{
		app_alert("Login Error","Invalid Password","warning","Ok","upass","focus");
		return false;
	}
	$('#submitlogin').html('Signing In... <i class="fa fa-spinner fa-spin"></i>');
	$('#submitlogin').attr('disabled', true);
 	setTimeout(function()
 	{
	 	$.post("./Actions/login_process.php", { mode: mode, username: usr, password: psw },
		function(data) {
			$('.loginresults').html(data);
			$('#submitlogin').html('Sign In');
			$('#submitlogin').attr('disabled', false);
		});
	},2000);
}
$(function()
{
	$('#body').css('background-image', 'url("../images/media/<?php echo LOGIN_WALLPAPER; ?>")');
	$('#uname').keydown(function(event) {
	    if(event.which == 13) { 
	        document.getElementById('upass').focus();
	        return false;
	    } 
	});
	$('#upass').keydown(function(event) {
	    if(event.which == 13) { 
	        pushLogin();
	        return false;
	    } 
	});
	$('#showpassword').click(function()
	{
		if($('#showpassword').hasClass('fa-solid fa-eye-slash'))
		{
			$('#showpassword').removeClass('fa-solid fa-eye-slash');
			$('#showpassword').addClass('fa-solid fa-eye');
			$('#password').get(0).type = 'text';
		} else {
			$('#showpassword').removeClass('fa-solid fa-eye');
			$('#showpassword').addClass('fa-solid fa-eye-slash');			
			$('#password').get(0).type = 'password';
		}
	});
	$("#username").keydown(function (e) {
	  if (e.keyCode == 13) {
	    document.getElementById('password').focus();
	  }
	});

	$("#password").keydown(function (e) {
	  if (e.keyCode == 13) {
	    $('#dloginbtn').trigger('click');
	  }
	});
	
});

</script>
<script src="Scripts/default_scripts.js"></script>
<script src="Plugins/loader/loader.js"></script>
<script src="Scripts/jquery-ui.js?v=<?php echo @filemtime(__DIR__.'/../Scripts/jquery-ui.js'); ?>"></script>
<script src="Scripts/jquery.dataTables.min.js"></script>
<script src="Scripts/dataTables.bootstrap.min.js"></script>
</html>
