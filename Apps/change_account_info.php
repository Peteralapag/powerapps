<table style="width: 100%" class="table">
	<tr>
		<th>Current Password</th>
		<td><input id="currpass" type="password" class="form-control" autocomplete="off"></td>
	</tr>
	<tr>
		<th>New Password</th>
		<td><input id="newpass" type="password" class="form-control" autocomplete="off"></td>
	</tr>
	<tr>
		<th>Comfirm Password</th>
		<td><input id="conpass" type="password" class="form-control" autocomplete="off"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:right">
			<button id="pushbtn" class="btn btn-primary" onclick="changePass()">Change Password</button>
		</td>
	</tr>
</table>
<div class="changepassresults"></div>
<script>
$(function()
{
	$('#currpass').keydown(function(event) {
	    if(event.which == 13) { 
			document.getElementById('newpass').focus()
	        return false;
	    } 
	});
	$('#newpass').keydown(function(event) {
	    if(event.which == 13) { 
	        document.getElementById('conpass').focus()
	        return false;
	    } 
	});
	$('#conpass').keydown(function(event) {
	    if(event.which == 13) { 
	       $('#pushbtn').trigger('click');
	        return false;
	    } 
	});
});
function changePass()
{
	var currpass = $('#currpass').val();
	var newpass = $('#newpass').val();
	var conpass = $('#conpass').val();	
	if(currpass == '')
	{
		app_alert("Current Password","Invalid Current Password","warning","Ok","currpass","focus");
		return false;
	}
	else if(newpass == '')
	{
		app_alert("New Password","Invalid New Password","warning","Ok","newpass","focus");
		return false;
	}
	else if(newpass != conpass)
	{
		app_alert("Password not match","New Password and Confirm password does not match","warning","Ok","newpass","focus");
		return false;
	}
	rms_reloaderOn("Changing Password");	
	setTimeout(function()
	{
		$.post("../Actions/change_password.php", { currpass: currpass, newpass: newpass },
		function(data) {
			$('.changepassresults').html(data);
			rms_reloaderOff();
		});
	},1000);
}	
</script>