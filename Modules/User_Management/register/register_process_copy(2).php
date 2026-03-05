<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
if(isset($_POST['mode']))
{
	$mode = $_POST['mode'];
} else {
	print_r('
		<script>
			app_alert("Warning"," The Mode you are trying to pass does not exist","warning","Ok","","no");
		</script>
	');
	exit();
}
if($mode=='register')
{
	$idcode = $_POST['idcode'];
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$username = $_POST['username'];
	$passwordd = $_POST['password'];
	$password = encryptedPassword($passwordd,$db);
	$branch = $_POST['branch'];
	$cluster = $_POST['cluster'];
	$acctname = $firstname.' '.$lastname;
	echo "WALA";
}
if($mode=='idcodeSearch')
{
	$idcode = $_POST['idcode'];	
	if(CheckIdcodeExist($idcode,$db)==1){
		echo '<script>app_alert("Warning","IDCODE already registered","warning","Ok","","no");</script>';
		exit();
	}
	$sql = "SELECT firstname,lastname,branch,cluster,company,department,idcode FROM tbl_employees WHERE idcode='$idcode'";
	$result = $db->query($sql);
	
	if ($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
	  	{
	   		$firstname = $row['firstname'];
	   		$lastname = $row['lastname'];
	   		$branch = $row['branch'];
	   		$cluster = $row['cluster'];
	   		$company = $row['company'];
	   		$department = $row['department'];
	   		$user = strtolower($firstname).' '.strtolower($lastname);
	   		print_r('
	   			<script>
	   				$("#firstname").val("'.$firstname.'");
	   				$("#lastname").val("'.$lastname.'");
	   				$("#branch").val("'.$branch.'");
	   				$("#cluster").val("'.$cluster.'");
	   				$("#company").val("'.$company.'");
	   				$("#department").val("'.$department.'");
					$("#username").val("'.ucwords($user).'");

	   			</script>
	   		');
		}
	} 
	else
	{
		print_r('
   			<script>
   				$("#firstname").val("");
	   				$("#lastname").val("");
	   				$("#branch").val("");
	   				$("#cluster").val("");
	   				$("#company").val("");
	   				$("#department").val("");
					app_alert("Warning","IDCODE does not exist","warning","Ok","","no");
   			</script>
   		');
	}
	
}
function CheckIdcodeExist($idcode,$db)
{
	$query = "SELECT * FROM tbl_system_user WHERE idcode='$idcode'";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
		return 1;
	}
	else{
		return 0;
	}	
}
function encryptedPassword($password,$db)
{		
	$asin_ang_ulam = "DevelopedAndCodedByRonanSarbon";
	$password_enc = $encrypted_string=openssl_encrypt($password,"AES-256-ECB",$asin_ang_ulam);
	$strHashedPass = mysqli_real_escape_string($db, $password_enc);	
	$strHash = hash( 'sha256', $strHashedPass);
	return $strHash;
}
if($mode=='captchaGenerate'){
	$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$result = substr(str_shuffle($str_result),0 , 6);
	echo $result;
}

