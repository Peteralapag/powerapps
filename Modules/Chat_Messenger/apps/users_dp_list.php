<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
?>
<style>
.ul-wrapper ul {list-style-type: none;margin:0;padding:0;}
.ul-wrapper li {display: flex;	padding:5px;align-items: center;justify-content: center;border-bottom: 1px solid #f1f1f1;}
.ul-wrapper .user-stream {border:1px solid #aeaeae;	width:30px;height:30px;border-radius:50%;margin-right:10px;background:#f1f1f1;}
.ul-wrapper .user-info {flex-grow: 1;font-size:12px;justify-contents: center;align-items: center;}
.ul-wrapper .user-stream, .user-info {
	border:0;
}
</style>
<div class="ul-wrapper">
	<ul>
<?php
	$checkPolicy = "SELECT * FROM tbl_system_user LIMIT 100";
	$pRes = mysqli_query($db, $checkPolicy);    
	if ( $pRes->num_rows > 0 ) 
	{
		while($ROWS = mysqli_fetch_array($pRes))  
		{
			$user = strtolower($ROWS['firstname']." ".$ROWS['lastname']);
?>	
		<li>
			<div class="user-stream"></div>
			<div class="user-info"><?php echo ucwords($user); ?></div>
		</li>
<?php } } else {} ?>
	</ul>
</div>