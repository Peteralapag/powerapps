
<head>
<meta content="en-us" http-equiv="Content-Language">
</head>

<?php
include '../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$username = $_SESSION['application_username'];
$sqlLogin = "SELECT * FROM tbl_user_wallpaper WHERE username='$username'";
$result = mysqli_query($db, $sqlLogin);    
if ( $result->num_rows > 0 ) 
{ 
    while($WPROW = mysqli_fetch_array($result))  
	{
		$desktop = $WPROW['desktop'];
	}
} else {	
	$desktop = 'blank.png';
}
$dir = opendir('../Images/media');
?>
<style>
.wp-wrapper {margin-bottom: 10px;}
.wp-wrapper th {text-align:center;font-size:14px;color:#fff;background:#aeaeae;}
.wp-wrapper table td, .wp-wrapper table th {border:1px solid #aeaeae;padding:10px}
.main-wp {display: flex;width:450px;overflow:auto;align-content: flex-start;flex-wrap: wrap;gap: 10px; height: 300px}
.img-wrapper {width:100px;height:70px;border:3px solid #aeaeae;border-radius:8px;cursor: pointer;}
.img-wrapper:hover {border:3px solid dodgerblue;}
.img-profile {top:10px;float:left;width:100px;height:70px;border:3px solid #aeaeae;border-radius:8px;cursor: pointer;}
</style>
<div class="wp-wrapper">
	<table style="width: 100%">
		<tr>
			<th colspan="2">MAIN WALLPAPER</th>
		</tr>
		<tr>
			<td colspan="2">
			<div class="main-wp">
		<?php			
			while ($file = readdir($dir))
			{
			    if ($file == '.' || $file == '..' || $file == 'index.php' || $file == 'Thumbs.db' || $file == 'blank.png')
			    {
			        continue;
			    }
?>
		<div onclick="setWP('<?php echo $file; ?>')" class="img-wrapper" style="background: url('../Images/media/<?php echo $file; ?>') no-repeat; background-size: cover;background-position: center;"></div>
<?php } closedir($dir); ?>	
			</div>
		</tr>
		<tr>
			<td style="width: 50%;">
				<div style="width: 90%;display: flex; gap:10px;">
					<input id="valueko" type="hidden" value="">
					<div id="imageprofile" class="img-profile" style="background: url('../Images/media/<?php echo $desktop; ?>') no-repeat; background-size: cover;background-position: center;"></div>
					<button id="dsktop" class="btn btn-success">Apply Wallpaper</button>
					<!--button id="loginn" class="btn btn-primary">Apply to Login Page</button -->					
				</div>
			</td>
		</tr>
	</table>
	<div class="wpresults"></div>
</div>
<script>
$(function()
{
	$('#dsktop').click(function()
	{
		var wp = $('#valueko').val();
		var column = 'desktop';
		if(wp === '') { return false; }
		rms_reloaderOn('Applying Wallpaper');
		setTimeout(function()
		{
			$.post("../Actions/set_wallpaper.php", { wp: wp, column: column },
			function(data) {
				$('.wpresults').html(data);	
				rms_reloaderOff();
				window.location.reload();
			});
		},1000);
	});
	$('#loginn').click(function()
	{
		var wp = $('#valueko').val();
		var column = 'login';
		if(wp === '') { return false; }
		$.post("../Actions/set_wallpaper.php", { wp: wp, column: column },
		function(data) {
			$('.wpresults').html(data);	
		});
	});
});
function setWP(params)
{
	$('#imageprofile').css('background-image', 'url("../Images/media/' + params +'")');
	$('#valueko').val(params);
}

</script>
