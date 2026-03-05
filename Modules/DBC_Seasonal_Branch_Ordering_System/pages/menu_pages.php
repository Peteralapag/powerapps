<?php

$datekaron = date('Y-m-d');
$bawalngaDates = [
    ['start' => '2024-12-22', 'end' => '2024-12-25'],
    ['start' => '2024-12-28', 'end' => '2025-01-01']
];
$checkerDates = false;
foreach ($bawalngaDates as $range) {
    if ($datekaron >= $range['start'] && $datekaron <= $range['end']) {
        $checkerDates = true;
        break;
    }
}

define("MODULE_NAME", "DBC_Seasonal_Branch_Ordering_System");
$app_path = $_SERVER['DOCUMENT_ROOT']."/Modules/".MODULE_NAME;
if($_POST['page'] == 'dashboard')
{
	include ($app_path.'/includes/dashboard.php');
} else {
	
	$page = $_POST['page'];
	include ($app_path.'/includes/'.$page.'.php');
	/*	
	if ($checkerDates) {
		if($page == 'create_request' || $page == 'create_order'){
			echo '<i class="fa fa-exclamation-triangle text-danger" aria-hidden="true"></i> Opss, Create Request and Create Order Disabled. DBC Seasonal Orders are not allowed from December 22 to 25 and from December 28 to January 1.';
		} else {
			include ($app_path.'/includes/'.$page.'.php');
		}
	    
	} else {
	    include ($app_path.'/includes/'.$page.'.php');
	}
	*/	
}
?>