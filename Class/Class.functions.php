<?php
class PageFunctions
{
	public function getWMSConfig($db)
	{
		$config = "SELECT * FROM tbl_wms_config WHERE id=1";
		$confResult = mysqli_query($db, $config);    
	    if ( $confResult->num_rows > 0 ) 
	    {
	    	while($ROWS = mysqli_fetch_array($confResult))  
			{
				return $ROWS['maintenance'];
			}
		}
	}
	public function getConfig($column,$db)
	{
		$config = "SELECT * FROM tbl_app_config WHERE id=1";
		$confResult = mysqli_query($db, $config);    
	    if ( $confResult->num_rows > 0 ) 
	    {
	    	while($ROWS = mysqli_fetch_array($confResult))  
			{
				return $ROWS[$column];
			}
		}
	}
	public function GeDownloadCount($filename,$column,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_document_properties WHERE file_name='$filename'";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				return $ROWS[$column];
			}
		} 
	}
	public function GetFileProperties($column,$filename,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_document_properties WHERE file_name='$filename'";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				return $ROWS[$column];
			}
		} 
	}
	public function GetCount($table,$column,$application,$username,$db)
	{
		$QUERYCNT = "SELECT * FROM $table WHERE requested_by='$username' AND applications='$application' AND $column=1 AND executed=0";
		$RESULTS = mysqli_query($db, $QUERYCNT); 
		if ( $RESULTS->num_rows > 0 ) 
	    {
			return $RESULTS->num_rows; 
		} else {
		//	return 0;
		}
	}	
	public function ExecuteAccess($application,$module,$file_name,$date,$db)
	{
		$queryDataUpdate = "UPDATE tbl_app_request SET executed=1,executed_date='$date' WHERE applications='$application' AND modules='$module' AND file_name='$file_name'  ";
		if ($db->query($queryDataUpdate) === TRUE) { } else { return $db->error; }	
	}
	public function AccessGranted($username,$application,$module,$file_name,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_app_request WHERE requested_by='$username' AND applications='$application' AND modules='$module' AND file_name='$file_name' AND approved=1 AND executed=0";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
			while($ROWS = mysqli_fetch_array($pRes))  
			{
				if($ROWS['approved'] == 0 AND $ROWS['approved'] == 0)
				{
					return 0;
				} 
				else if($ROWS['approved'] == 1 AND $ROWS['executed'] == 0)
				{
					return 1;
				}
			}
		} else {
			return '';
		}
	}
	public function CheckFullAccess($username,$application,$module,$file_name,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_app_request WHERE requested_by='$username' AND applications='$application' AND modules='$module'";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	while($ROWS = mysqli_fetch_array($pRes))  
			{
				if($ROWS['approved'] == 1)	
				{
					return 1;
				}
				else if($ROWS['approved'] == 2)	
				{
					return 2;
				}
				else if($ROWS['approved'] == 1 AND $ROWS['executed'] == 1)	
				{
					return 3;
				}
			}
		} else {
			return 0;
		}
	}
	public function CheckAccess($username,$application,$module,$access,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_system_permission WHERE username='$username' AND applications='$application' AND modules='$module' AND $access=1";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	return 1;
		} else {
			return 0;
		}
	}
	public function checkPermission($username,$user_app,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_system_permission WHERE username='$username' AND applications='$user_app'";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	return 1;
		} else {
			return 0;
		}
	}
	public function GetFileSize($filename)
	{
		$size = filesize($filename);
		return convert_filesize($size);
	}
	public function GetFileDate($filename)
	{
		if (file_exists($filename)) {
		    return "File was last uploaded: " . date ("F d Y H:i: A.", filemtime($filename));
		}
	}
	
	public function GetFolder($connection)
	{	
		$query = "SELECT * FROM tbl_document_folder";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT FOLDER ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$folder = $ROW['document_folder'];
				$return .= '<option '.$selected.' value="'.$folder.'">'.$folder.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO FOLDER FOUND ---</option>';
		}
	}
	public function GetModules($params,$connection)
	{	
		$query = "SELECT * FROM tbl_system_modules WHERE application_id='$params'";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT MODULE--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$module = $ROW['modules'];

				$return .= '<option '.$selected.' value="'.$module.'">'.$module.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO MODULES--</option>';
		}
	}
	public function GetApplication($params,$connection)
	{	
		$query = "SELECT * FROM tbl_system_applications";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT APPS--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$apps_id = $ROW['application_id'];
				$apps = $ROW['application_name'];
				$selected = '';
				if($apps_id == $params)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$apps_id.'">'.$apps.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO APPS--</option>';
		}
	}
	public function GeneratePassword($length)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $randomString = '';
	
	    for ($i = 0; $i < $length; $i++) {
	        $index = rand(0, strlen($characters) - 1);
	        $randomString .= $characters[$index];
	    }
	
	    return "RBS".$randomString;
    }
	public function GetUserLevel($params,$connection)
	{	
		$query = "SELECT * FROM tbl_system_userlevel";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT LEVEL--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$userlevel = $ROW['user_level'];
				$selected = '';
				if($userlevel== $params)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$userlevel.'">'.$userlevel.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO LEVEL--</option>';
		}
	}
	public function GetUserRole($role,$connection)
	{	
		$query = "SELECT * FROM tbl_system_userrole";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT ROLE--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$rowl = $ROW['user_role'];
				$selected = '';
				if($rowl == $role)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$rowl.'">'.$rowl.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO ROLES--</option>';;
		}
	}
	public function GetCompany($company,$connection)
	{	
		$query = "SELECT * FROM tbl_company";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT COMPANY--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$clester = $ROW['company'];
				$selected = '';
				if($clester == $company)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$clester.'">'.$clester.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO COMPANY--</option>';;
		}
	}
	public function GetCluster($cluster,$connection)
	{	
		$query = "SELECT * FROM tbl_cluster";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT CLUSTER--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$clester = $ROW['cluster'];
				$selected = '';
				if($clester == $cluster)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$clester.'">'.$clester.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO CLUSTER--</option>';;
		}
	}
	public function GetDepartment($department,$connection)
	{	
		$query = "SELECT * FROM tbl_department";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT DEPARTMENT--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$dept = $ROW['department'];
				$selected = '';
				if($dept == $department)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$dept.'">'.$dept.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO DEPARTMENT--</option>';;
		}
	}
	public function GetBranch($branch,$connection)
	{	
		$query = "SELECT * FROM tbl_branch";
		$results = mysqli_query($connection, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--SELECT BRANCH--</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$brench = $ROW['branch'];
				$selected = '';
				if($brench == $branch)
				{
					$selected = 'selected';
				}
				$return .= '<option '.$selected.' value="'.$brench.'">'.$brench.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--NO BRANCH--</option>';;
		}
	}
	public function deleteArchiveMemo($rowid,$db)
	{
		$sqldelItem = "DELETE FROM tbl_archived_memo WHERE id='$rowid'";
		if ($db->query($sqldelItem) === TRUE)
		{
			return 1;
		}
		else
		{
			return $db->error;			
		}
	}
	public function checkSharing($rowid,$db)
	{
		$QUERY="SELECT * FROM tbl_shared_memo WHERE shared_id='$rowid' ";  
		$RESULTS = mysqli_query($db, $QUERY);
		if($RESULTS->num_rows > 0)
		{ 
			$retval = 1;
		} else {
			$retval= 0;
		}
		return $retval;
	}
	public function checkPolicy($username,$user_app,$db)
	{
		$checkPolicy = "SELECT * FROM tbl_system_permission WHERE username='$username' AND applications='$user_app'";
		$pRes = mysqli_query($db, $checkPolicy);    
	    if ( $pRes->num_rows > 0 ) 
	    {
	    	return 1;
		} else {
			return 0;
		}
	}
}
function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
