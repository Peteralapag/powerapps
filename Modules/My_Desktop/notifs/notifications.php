	<div class="notifs" id="closenotifs" style="position:relative">
		<i class="fa-solid fa-circle-xmark xmark" onclick="openNotifs()"></i>
		<div class="notifs-title">NOTIFICATIONS</div>
		<div class="title-header" id="titleheader"></div>
		<div class="notif-data" id="notifdata">AAA</div>
	</div>
<script>
$(function()
{
	setInterval(function()
	{
		$('#notifdata').load('../Includes/notif_data.php');
	},30000);
});
</script>