<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$username = isset($_SESSION['application_username']) ? $_SESSION['application_username'] : '';
$safeUsername = $db->real_escape_string($username);
?>
<style>
.modules-wrapper ul {display:flex;flex-direction:column; flex:1;position:relative;}
.modules-wrapper li {gap: 10px;display: inline-block;width:100%;border-bottom:1px solid #e7e7e7;font-size:14px;}
.modules-wrapper li span {margin-right:20px;}
.modules-wrapper li:last-child {margin-top:auto;border-bottom:0;border-top:1px solid #e7e7e7;align-self: flex-end;}
.modules-wrapper li:hover {background:#dbe3ec}
</style>
<div class="modules-wrapper">
	<ul>
		<li onclick="loadModules('My_Desktop')"><span><i class="fa-solid fa-gauge color-red"></i></span>My Desktop / Dashboard</li>
<?php
	$sqlModules = "SELECT am.*
				   FROM tbl_app_modules am
				   WHERE am.active=1
				   AND (
						EXISTS (
							SELECT 1
							FROM tbl_system_permission sp
							WHERE sp.username='$safeUsername'
							AND sp.applications = am.module_name
						)
						OR (
							am.module_name IN (
								'Branch Ordering System',
								'FD Branch Ordering System',
								'DBC Branch Ordering System',
								'DBC Seasonal Branch Ordering System'
							)
							AND EXISTS (
								SELECT 1
								FROM tbl_system_permission sp2
								WHERE sp2.username='$safeUsername'
								AND sp2.applications='Branch Ordering System'
							)
						)
				   )
				   ORDER BY am.ordering ASC";
	$modukeResults = mysqli_query($db, $sqlModules);    
    if ( $modukeResults->num_rows > 0 ) 
    { 
	    while($MODULEROWS = mysqli_fetch_array($modukeResults))  
		{
?>	
		<li onclick="loadModules('<?php echo $MODULEROWS['module_page']; ?>')">
			<span><i class="<?php echo $MODULEROWS['module_icon']." ".$MODULEROWS['icon_color']; ?>"></i></span><?php echo $MODULEROWS['module_name']; ?>
		</li>
<?php 	}

	} else { ?>		
		<li>There is no Application(s) assign under you</li>
	</ul>

<?php 
	} 
?>	
</div>
<script>
function not_working()
{
	swal("System Message", "This page is under development","warning");
}
function signOut()
{
	app_confirm("Signing Out","Are you sure to Sign Out?","warning","signingout","","red");
}
</script>
