function openNotifs()
{
	if($('#closenotifs').is(":visible"))
	{	
		$('#closenotifs').slideUp();	
	} else {
		$('#closenotifs').slideDown();
	}
}
function loadDashboard()
{
	sessionStorage.removeItem('module');
	sessionStorage.setItem('module', 'dashboard');
	$('.modulenames').html(module.split("_").join(" "));
	$('#main').load('Apps/dashboard.php');
}
function loadModules(module)
{
	sessionStorage.setItem('module', module);
	$('.modulenames').html(module.split("_").join(" "));
	$('#main').load('Modules/' + module);		

}
function AddNewEmployee(mode)
{
	rms_reloaderOn();
	$.post("../apps/employee.php", { mode: mode },
	function(data) {
		$('#contentdata').html(data);
		rms_reloaderOff();
	});	
}
function app_confirm(dialogtitle,dialogmsg,dialogicon,command,params,btncolor)
{
	if(btncolor == null || btncolor == '') 
	{
		var btncolor = '';
	} else {
		var btncolor = btncolor;
	}
	swal({
		title: dialogtitle,
		text: dialogmsg,
		icon: dialogicon,
		buttons: [
		'No',
		'Yes'
		],
		dangerMode: btncolor,
	}).then(function(isConfirm) {
		if (isConfirm)
		{
			// If caller passed a function reference, call it directly
			if (typeof command === 'function') {
				try { command(params, btncolor); } catch(e) { console.error('app_confirm callback error', e); }
				return;
			}
			// If caller passed a string that matches a global function name, call it
			if (typeof command === 'string' && typeof window[command] === 'function') {
				try { window[command](params, btncolor); } catch(e) { console.error('app_confirm global callback error', e); }
				return;
			}
			if(command == 'signingout')
			{
				window.location.href = 'log_awt.php';
			}
			else if(command == 'deleteFileYes')
			{					
				deleteFileYes(params);
			}
			else if(command == 'deleteUserYes')
			{
				deleteUserYes(params,btncolor);
			}
			else if(command == 'deleteFileYes')
			{
				deleteFileYes(params);
			}
			else if(command == 'cancelRequestYes')
			{					
				cancelRequestYes(params);
			}
			else if(command == 'removePermissions')
			{					
				removePermissions(params);
			}
			else if(command == 'deleteShareFile')
			{					
				unshareFileYes(params,btncolor);
			}
			else if(command == 'deletesupplier')
			{					
				deleteSupplierYes(params);
			}
			else if(command == 'deletereceiving')
			{					
				deleteReceivingYes(params);
			}
			else if(command == 'deletesupplier')
			{					
				deleteSupplierYes(params);
			}
			else if(command == 'deleteitem')
			{					
				deleteItemYes(params);
			}
			else if(command == 'submitOrderYes')
			{					
				submitOrderYes(params);
			}
			else if(command == 'deleteAccessYes')
			{					
				deleteAccessYes(params);
			}
		}
	});
}
function app_alert(p_title,p_text,p_icon,p_button_text,aydi,command)
{
	swal({
		title: p_title,
		text: p_text + "!",
		icon: p_icon,
		button: p_button_text,
	}).then(function()	{
		if(command == 'signingout')
		{
			window.location.href = 'log_awt.php';
		}
		if(command == 'focus')
		{
			if(aydi != '')
			{				
				document.getElementById(aydi).focus();
			}
		}
		else if(command == 'yes')
		{
			$('.page-loader-bd').show();
			closeMsg();
		}
		else if(command == 'changeyes')
		{
			closeModal('formmodal');
		}
		else if(command == 'appyes')
		{
			rms_reloaderOn('Opening Application');
			closeMsg();
		}
	});		
}
function closeMsg()
{
	setTimeout(function(){
		window.location.reload(400);
	}, 2000);
}
function signOut()
{
	app_confirm("Signing Out","Are you sure to sign-out?","warning","signingout","","true")
	return false;
}
function logout() {
    app_alert("Session Time out","You have been idle for 20 minutes. You`re about to be signed out","warning","Ok","","signingout");
}	
function resetTimer() {
    clearTimeout(time);
    time = setTimeout(logout, 1200000); // 10 minutes
}

$(function()
{
	$(window).resize(function()
	{
		 $("#downdownmenuwrapper,#settingsdd,#profiledd").slideUp();
	});
});
$(document).mouseup(function (e) {
     var popup = $("#showmodules,#profiledd,#settingsdd,#noticsdata,#searchuser,#itemdropdown,#formdropper");
     if (!$('#showmodules,#profiledd,#settingsdd,#noticsdata,#searchuser,#itemdropdown,#formdropper').is(e.target) && !popup.is(e.target) && popup.has(e.target).length == 0) {
         $("#showmodules,#profiledd,#settingsdd,#noticsdata,#searchbox,#itemdropdown,#formdropper").slideUp();
     }
   //  document.addEventListener('contextmenu', event => event.preventDefault()); // RIGHT CLICK DISABLED
});
function ronanReload()
{
	$('#' + sessionStorage.navcount).trigger('click');
}
