<?php
include 'init.php';
if(!isset($_SESSION['application_username']))
{
	require 'Pages/user_login.php';
	exit();
} else {	
	if(MAINTENANCE  == 1)
	{
		if($_SESSION['application_userlevel'] >= 80)
		{
			require 'main.php';
		} else {
			require 'Pages/maintenance.php';
		}		
	} else {
		require 'main.php';
	}
}
