<?php
include '../init.php';
if(!(isset($_SESSION['application_username '])) || !(isset($_SESSION['application_company'])) || !(isset($_SESSION['application_userlevel'])) || !(isset($_SESSION['application_userrole'])) || !(isset($_SESSION['application_application'])))
{
	$nn =  1;
} else {
	$nn = 0;
	exit();
}
?>
<script>
$(function()
{
	if('<?php echo $nn; ?>' == 1)
	{
		window.location.reload();
	}
});
</script>