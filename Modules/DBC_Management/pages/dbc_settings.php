<div class="smnav-header">
	DBC SETTINGS
</div>
<div id="smnavdata" class="settings"><i class="fa fa-spinner fa-spin"></i></div>
<script>
$(function()
{
	$.post("./Modules/DBC_Management/dbc_settings/dbc_settings.php", {  },
	function(data) {		
		$('#smnavdata').html(data);
	});

});
</script>