<?php
session_start();
if($_POST['mode'] == 'changecluster')
{
	$_SESSION['fds_branch_cluster'] = $_POST['cluster'];
}
if($_POST['mode'] == 'changebranch')
{
	$_SESSION['fds_branch_branch'] = $_POST['branch'];
}
print_r('
	<script>
		window.location.reload();
	</script>
');
?>