<div class="smnav-header">
	BINALOT SETTINGS
</div>
<div id="smnavdata" class="settings"><i class="fa fa-spinner fa-spin"></i></div>
<script>
$(function()
{
	$.post("./Modules/Binalot_Management/binalot_settings/binalot_settings.php", {  },
	function(data) {		
		$('#smnavdata').html(data);
	});

});
</script>