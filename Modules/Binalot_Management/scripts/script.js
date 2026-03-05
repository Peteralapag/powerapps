function Check_Access(params,permission,action)
{
	var module = sessionStorage.module_name;
	$.post("./Modules/Binalot_Management/actions/check_permissions.php", { permission: permission, module: module },
	function(data) {
		if(data == 1)
		{
			action(params);
		}
		else if(data == 0)
		{
			swal("Access Denied","You have insufficient access. Please contact System Administrator","warning");
		}
	});
}
function Check_Permissions(permission,action,page,module)
{
	sessionStorage.setItem("page_name", page);
	sessionStorage.setItem("module_name", module);
	$.post("./Modules/Binalot_Management/actions/check_permissions.php", { permission: permission, module: module },
	function(data) {
		if(data == 1)
		{
			action(page);
		}
		else if(data == 0)
		{
			swal("Access Denied","You have insufficient access. Please contact System Administrator","warning");
		}
	});
}
function dialogue_confirm(dialogtitle,dialogmsg,dialogicon,command,params,btncolor)
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
			
			if(command == 'resetDataYes')
			{
				resetDataYes();
			}
			if(command == 'closeAppsYes')
			{
				closeAppsYes();
			}
			if(command == 'closeReceivingYes')
			{
				closeReceivingYes(params);
			}
			if(command == 'deleteTransferYes')
			{
				deleteTransferYes(params);
			}
			if(command == 'requestReopenYes')
			{
				requestReopenYes(params);
			}
			if(command == 'PostToSummary')
			{
				postSummary(params);
			}
			if(command == 'voidthisitem')
			{
				voidThis(params,btncolor);
			}

		}
	});
}
