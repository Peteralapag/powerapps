<?php
class PSAFunctions
{
	public function getMyIDCode($username,$db){
		$sql = "SELECT * FROM tbl_system_user WHERE username='$username'";
		$result = $db->query($sql);
		$val = '';
		if ($result->num_rows > 0)
		{
			while($row = $result->fetch_assoc())
		  	{
		  		$val = $row['idcode'];
		  	}
		  	return $val;
		}
		else {
			return $val;
		}

	}
	public function userLevelChecking($username,$applications,$db)
	{
		$config = "SELECT * FROM tbl_system_permission WHERE username='$username' AND applications='$applications'";
		$confResult = mysqli_query($db, $config);    
	    if ( $confResult->num_rows > 0 ) 
	    {
			return 1;
		} else {
			return 0;
		}
	}
}

