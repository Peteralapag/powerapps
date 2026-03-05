<div class="smnav-header">
	DBC SEASONAL SETTINGS
</div>
<div id="smnavdata" class="settings"><i class="fa fa-spinner fa-spin"></i></div>
<script>
$(function()
{
	$.post("./Modules/DBC_Seasonal_Management/dbc_settings/dbc_settings.php", {  },
	function(data) {		
		$('#smnavdata').html(data);
	});

});
</script>