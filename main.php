<?php
$Themes->GetThemes("header");
$Themes->GetThemes("body");
$Themes->GetThemes("footer");
?>
<div class="user-check"></div>
<script>
$(function()
{
	$.post('./Actions/check_user.php', { },
	function(data) {
		$('.user-check').html(data);
		if(data == 1)
		{
			$('#modaltitle').html("Change Password");
			$('#modalicon').html('<i class="fa-solid fa-key color-yellow"></i>');	
			$.post("../Apps/change_account_info.php", { },
			function(data) {
				$('#formmodal_page').html(data);		
				$('#formmodal').show();		
			});
		}
	});
});
var deleteTime = (3600 * 5);
setTimeout(function()
{
	$.post('./Actions/delete_temp_files.php', { },
	function(data) {});
},deleteTime);
</script>