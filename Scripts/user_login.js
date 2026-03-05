function pushLogin()
{
	var mode = 'c10cc6b684e1417e8ffa924de1e58373';
	var usr = $('#username').val();
	var psw = $('#password').val();
	if(usr == '')
	{
		app_alert("Login Error","Invalid Username","warning","Ok","username","no");
		return false;
	}
	else if(psw == '')
	{
		app_alert("Login Error","Invalid Password","warning","Ok","password","no");
		return false;
	}
	$('#dloginbtn').html('Loging In <i class="fa fa-spinner fa-spin"></i>');
	$('#dloginbtn').attr('disabled', true);
 	setTimeout(function()
 	{
	 	$.post("./actions/login_process.php", { mode: mode, username: usr, password: psw },
		function(data) {
			$('.login').html(data);
			$('#dloginbtn').html('Login');
			$('#dloginbtn').attr('disabled', false);
		});
	},2000);
}
$(function()
{
	$('body').keydown(function(event) {
	    if(event.which == 113) { //F2
	       	UpdateUsers();
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
