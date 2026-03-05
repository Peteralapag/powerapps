<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$username = $_SESSION['application_username'];
$company = $_SESSION['application_company'];
$user_level = $_SESSION['application_userlevel'];
if($user_level >= 50)
{
	$q = 'tbl_system_modules WHERE application_id=1002';
} else {
	$q = "tbl_system_permission WHERE username='$username' AND applications='DOCUMENT MANAGEMENT'";
}
?>
<style>
.radio-active { background:#d7d8d9; border-radius: 10px;border: 1px solid #3c77ae;font-weight: bold;}
.slidebtn {font-size:16px;font-weight:normal;}
.showwd {font-size:16px;font-weight:normal;}
.showwd-active {background: dodgerblue; border-radius: 10px;color:#fff;}
</style>
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
			$department = $SPROWS['modules'];
?>
		<div class="button btn-default btn-block slidebtn" id="wd<?php echo $i; ?>" data-nav="wd<?php echo $i; ?>" onclick="slider('<?php echo $i; ?>')">
			<i id="mainnav<?php echo $i; ?>" class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;<?php echo $SPROWS['modules']; ?>
		</div>
        <ul class="nav navpadleft nav-list collapse" id="menudown<?php echo $i; ?>">
<?php
	$QUERY = "SELECT * FROM tbl_mdmain_subfolder";
	$RESULTS = mysqli_query($db, $QUERY);    
	$x=0;
    while($ROWS = mysqli_fetch_array($RESULTS))  
	{
		$x++;
		$subfolder = $ROWS['subfolder'];
?>        
            <li class="showwd" id="show<?php echo $i.$x; ?>" data-show="show<?php echo $i.$x; ?>" onclick="showWD('<?php echo $department; ?>','<?php echo $subfolder; ?>')">
            	<a title="Show list of tickets"><i class="fa-solid fa-caret-right"></i>&nbsp;&nbsp;&nbsp;<?php echo $ROWS['subfolder']; ?></a>
            </li>
<?php } ?>
        </ul>
  <?php } } else {} ?>
<script>
$(function()
{
	$('.slidebtn').click(function(){
		var div_id = $(this).attr('data-nav');
//		slider(div_id);
		$('.slidebtn').removeClass('radio-active');
		$(this).addClass('radio-active');
		$("#"+div_id).addClass('radio-active');
	});
	$('.showwd').click(function(){
		var show_id = $(this).attr('data-show');
		sessionStorage.setItem("slidercount", show_id);
		$('.showwd').removeClass('showwd-active');
		$(this).addClass('showwd-active');
		$("#"+show_id).addClass('showwd-active');
	});
});
function showWD(department,subfolder)
{
	$.post("../Modules/Document_Management/apps/directory_browser.php", { department: department, subfolder: subfolder },
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
	
	/* if($("#menudown" + params).is(":visible")){} 
	else { $('#mainnav' + params).toggleClass("fa-caret-right fa-caret-down"); } */
}
</script>