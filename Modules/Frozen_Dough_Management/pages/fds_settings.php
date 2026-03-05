<div class="smnav-header">
	FDS SETTINGS
</div>
<div id="smnavdata" class="settings"><i class="fa fa-spinner fa-spin"></i></div>
<script>
$(function()
{
	$.post("./Modules/Frozen_Dough_Management/fds_settings/fds_settings.php", {  },
	function(data) {		
		$('#smnavdata').html(data);
	});

});
</script>