<?php
include '../../../init.php';
include '../../../Class/PSA.functions.php';
$psaFuntion = new PSAFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$username = $_SESSION['application_username'];

$myIdCode = $psaFuntion->getMyIDCode($username,$db);
?>
<style>
.tsa-a-na-may-tae {display: flex;gap: 10px}
.table td, .table th {white-space:nowrap;padding: 3px !important;border: 0 !important;line-height:20px}
#captchashow, #captcha {
	letter-spacing:4px;
	font-family:"Times New Roman", Times, serif;
}
</style>
<table class="table" style="width:430px">
	<tr>
		<th>IDCODE:</th>
		<td><input type="text" id="idcode" class="form-control" disabled></td>
	</tr>
	<tr>
		<th>FIRST NAME:</th>
		<td><input type="text" id="firstname" class="form-control" disabled></td>
	</tr>
	<tr>
		<th>LASTNAME:</th>
		<td><input type="text" id="lastname" class="form-control" disabled ></td>
	</tr>

	<tr>
		<th>USERNAME:</th>
		<td><input type="text" id="username" class="form-control" disabled></td>
	</tr>
	<!--tr>
		<th>PASSWORD:</th>
		<td><input type="password" id="password" class="form-control" autocomplete="off"></td>
	</tr>
	<tr>
		<th>CONFIRM PASSWORD:</th>
		<td><input type="password" id="confirmpassword" class="form-control" autocomplete="off"></td>
	</tr-->
	<tr>
		<th>COMPANY:</th>
		<td>
			<input id="company" class="form-control" disabled>
		</td>
	</tr>
	<tr>
		<th>CLUSTER:</th>
		<td>
			<select id="cluster" class="form-control" disabled >
				<?php echo GetCluster($db); ?>
			</select>
		</td>
	</tr>
	<tr>
		<th>BRANCH:</th>
		<td>
			<select id="branch" class="form-control" disabled >
				<?php echo GetBranch($db); ?>
			</select>
			<input type="hidden" id="department" class="form-control" disabled>
		</td>
	</tr>	
	<tr>
		<th>
			VERIFICATION CODE:
		</th>
		<td class="tsa-a-na-may-tae">
			<input id="captcha" style="text-align:center" type="text" class="form-control" autocomplete="off">
			<input id="captchashow" style="text-align:center" type="text" data-captcha="" class="form-control" autocomplete="off" disabled>
			<input id="captchaconfirm" type="hidden">			
		</td>
	</tr>
	<tr>
		<td style="height:10px"></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<button id="ronanamoytae" type="button" class="btn btn-primary btn-lg w-50"><i class="fa fa-cube" aria-hidden="true"></i>&nbsp;&nbsp;DBC Register</button>
			<button class="btn btn-danger btn-lg w-50"onclick="closeModal('formmodal')"><i class="fa-solid fa-ban"></i>&nbsp;&nbsp;Cancel</button>
		</td>
	</tr>
</table>
<div id="resultss"></div>
<script>
$(function()
{	
	$('#ronanamoytae').click(function()
	{
		register();
	});
	var mode = 'captchaGenerate';
	$.post("../Modules/User_Management/register/DBC_register_process.php", { mode:mode },
	function(data) {
		$('#captchashow').attr("placeholder", data);		
		$('#captchaconfirm').val(data);
	});
	$("#captcha").on("input", function()
	{
		var inputValue = $(this).val().toUpperCase();
		$(this).val(inputValue);
	});
	$("#captcha").keydown(function(event)
	{
		if (event.keyCode === 13)
		{
			event.preventDefault();
			register();
		}
	});
	$("#idcode").keydown(function(event)
	{
		if (event.keyCode === 13)
		{
			event.preventDefault();
			idcodeSearch();
		}
	});
	
	var myidcode = '<?php echo $myIdCode?>';
	idcodeSearch(myidcode);
});

function idcodeSearch(idcode)
{
//	var idcode = $("#idcode").val();
	var mode = 'idcodeSearch';	
	$.post("../Modules/User_Management/register/DBC_register_process.php", { mode: mode,idcode: idcode },
	function(data) {
		$('#resultss').html(data);
		document.getElementById("password").focus();
	});
}
function register()
{
	var mode = 'register';
	var idcode = $('#idcode').val();
	var firstname = $('#firstname').val();
	var lastname = $('#lastname').val();
	var username = $('#username').val();
	var password = $('#password').val();
	var confirmpassword = $('#confirmpassword').val();
	var company = $('#company').val();
	var department = $('#department ').val();
	var branch = $('#branch').val();
	var cluster = $('#cluster').val();
	var captcha = $('#captcha').val();
	var captchaconfirm = $('#captchaconfirm').val();

	if(idcode==''||firstname==''||lastname==''||username==''||branch==''||cluster=='')
	{
		app_alert("Warning"," Fill all inputs","warning","Ok","","no");
		return false;
	}
	else if(captcha!=captchaconfirm){
		app_alert("Warning","Verification Code is incorrect","warning","Ok","","no");
		return false;
	}
	rms_reloaderOn("Registering user...");

	setTimeout(function()
	{
		$.post("../Modules/User_Management/register/DBC_register_process.php",
		{
			mode: mode,
			idcode: idcode,
			firstname: firstname,
			lastname: lastname,
			username: username,
			branch: branch,
			cluster: cluster,
			company: company,
			department: department
		},
		function(data) {
			console.log(data);
			$('#resultss').html(data);
			$('#pedtroalagay').attr('disabled', false);
			rms_reloaderOff();
		});
	},1000);
}
</script>

<?php
function GetCluster($db)
{	
	$query = "SELECT * FROM tbl_cluster";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
		$return = '<option value=""></option>';
	    while($ROW = mysqli_fetch_array($results))  
		{
			$clester = $ROW['cluster'];
			$selected = '';
			$return .= '<option '.$selected.' value="'.$clester.'">'.$clester.'</option>';
		}
		return $return;
	} else {
		return '<option value="">--NO CLUSTER--</option>';;
	}
}
function GetBranch($db)
{	
	$query = "SELECT * FROM tbl_branch";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
		$return = '<option value=""></option>';
	    while($ROW = mysqli_fetch_array($results))  
		{
			$brench = $ROW['branch'];
			$selected = '';
			$return .= '<option '.$selected.' value="'.$brench.'">'.$brench.'</option>';
		}
		return $return;
	} else {
		return '<option value="">--NO BRANCH--</option>';;
	}
}

?>