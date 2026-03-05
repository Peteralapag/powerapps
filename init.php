<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
ini_set('default_mimetype', 'text/html');
ini_set('default_charset', 'utf-8');
require 'db_config.php';
define('DB_HOST', $dbhost);
define('DB_USER', $dbuser);
define('DB_PASSWORD', $dbpass);
define('DB_NAME', $dbname);
require 'Class/Class.encrypted_password.php';
require 'Class/Class.themes.php';
require 'Class/Class.functions.php';
$Themes = new Themes;
$encpass = new Password;
$functions = new PageFunctions;
$configdb = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	

$maint = $functions->getConfig('maintenance',$configdb);
$wms_maint = $functions->getWMSConfig($configdb);


if(isset($_SESSION['application_company']) AND isset($_SESSION['application_username']))
{
	define("COMPANY_NAME", "Jathnier Corporation");
	define("WD_PATH", "../Data");
	$username = $_SESSION['application_username'];
	$company = $_SESSION['application_company'];

	$dbe = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$SQLconfig = "SELECT * FROM tbl_user_wallpaper WHERE username='$username'";
	$configResult = mysqli_query($dbe, $SQLconfig);  
	
	if ( $configResult->num_rows > 0 ) 
	{ 
		while($CONFIGROW = mysqli_fetch_array($configResult))  
		{
			if($CONFIGROW["desktop"] != NULL) { define("BG_WALLPAPER", $CONFIGROW["desktop"]); } else { define("BG_WALLPAPER", "_default.jpg"); }
			if($CONFIGROW["login"] != NULL) { define("LOGIN_WALLPAPERS", $CONFIGROW["login"]); } else { define("LOGIN_WALLPAPER", "_default.jpg"); }
		}
	} else {
		
		if( isset($_SESSION['fds_application']) || isset($_SESSION['fds_branch_application']) && empty($_SESSION['branch_application']) ){
			define("BG_WALLPAPER", "psa_frozenManagement.jpg");
			define("LOGIN_WALLPAPER", "psa_frozenManagement.jpg");

		} else {
			define("BG_WALLPAPER", "_default.jpg");
			define("LOGIN_WALLPAPER", "_default.jpg");
		}
	}
} else {
	
	define("BG_WALLPAPER", "_default.jpg");
	define("LOGIN_WALLPAPER", "_default.jpg");
}
define("MAINTENANCE", $maint);
define("WMS_MAINTENANCE", $wms_maint);