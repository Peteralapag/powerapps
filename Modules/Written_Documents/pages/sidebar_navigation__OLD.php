<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];
if($user_level >= 80)
{
	$q = 'tbl_wd_organization';
} else {
	$q = "tbl_system_permission WHERE username='$username' AND applications='Written Documents (Archiving)'";
}
?>
<?php
	$sqlDesktop = "SELECT * FROM  $q";
	$DesktopResult = mysqli_query($db, $sqlDesktop);
	$cnt = $DesktopResult->num_rows; 
	if ( $DesktopResult->num_rows > 0 ) 
	{ 
		$i=0;
	    while($SPROWS = mysqli_fetch_array($DesktopResult))  
		{
			$i++;
			$organization = $SPROWS['modules'];
?>
		<div class="button btn-default btn-block" id="wd<?php echo $i; ?>" data-nav="wd<?php echo $i; ?>" onclick="slider('<?php echo $i; ?>')"><i id="mainnav<?php echo $i; ?>" class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;<?php echo $SPROWS['modules']; ?></div>
        <ul class="nav navpadleft nav-list collapse" id="menudown<?php echo $i; ?>">
<?php
	$QUERY = "SELECT * FROM tbl_wd_subfolder";
	$RESULTS = mysqli_query($db, $QUERY);    
	$x=0;
    while($ROWS = mysqli_fetch_array($RESULTS))  
	{
		$x++;
		$subfolder = $ROWS['subfolder'];
?>        
            <li onclick="showWD('<?php echo $organization; ?>','<?php echo $subfolder; ?>')"><a title="Show list of tickets"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;<?php echo $ROWS['subfolder']; ?></a></li>
<?php } ?>
        </ul>
  <?php } } else {} ?>
<script>
$(function()
{
});
function showWD(organization,subfolder)
{
	$.post("../Modules/Written_Documents/apps/archive_apps.php", { organization: organization, subfolder: subfolder },
	function(data) {
		$('#contents').html(data);
	});
}

function slider(params)
{
	if($("#menudown" + params).is(":visible"))
	{
		$('#mainnav' + params).addClass("fa-caret-right");
		$('#mainnav' + params).removeClass("fa-caret-down");
		$('#menudown' + params).slideUp();
	} else {
		$('#menudown' + params).slideDown();
		$('#mainnav' + params).removeClass("fa-caret-right");
		$('#mainnav' + params).addClass("fa-caret-down");
	}
	if($("#menudown" + params).is(":visible")){} 
	else { $('#mainnav' + params).toggleClass("fa-caret-right fa-caret-down"); }
}
</script>