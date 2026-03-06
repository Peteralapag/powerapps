<?php
class DBCFunctions
{

	public function GetBranchOrderRecevingServeAllPending($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_order_request WHERE trans_date = '$transdate' AND status='Submitted'";
		$RESULTS = mysqli_query($db, $QUERY);
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}


	public function getPendingApprovals($db)
	{
	    $query = "SELECT item_description, report_date, posted_by FROM dbc_dbc_production WHERE status = 'No' ORDER BY report_date DESC";
	    
	    $result = mysqli_query($db, $query);
	    $pendingApprovals = [];
	
	    if ($result) {
	        while ($row = mysqli_fetch_assoc($result)) {
	            $pendingApprovals[] = $row;
	        }
	        mysqli_free_result($result);
	    } else {
	        error_log("Query error: " . mysqli_error($db));
	    }
	
	    return $pendingApprovals;
	}


	public function GetTransferOutDataViaBranchReceived($branch,$itemcode,$transdate,$db)
	{
		
	    $val = 0;
	    $QUERY = "SELECT SUM(actual_quantity) AS QUANTITY 
	              FROM dbc_branch_order 
	              WHERE item_code = ? 
	              AND trans_date = ? 
	              AND branch = ? 
	              AND control_no IN (
	                  SELECT control_no 
	                  FROM dbc_order_request 
	                  WHERE status = 'Closed' 
	                  AND order_delivered = 1
	              )";
	    
	    if ($stmt = $db->prepare($QUERY)) {
	        $stmt->bind_param("sss", $itemcode, $transdate, $branch);
	        
	        $stmt->execute();
	        
	        $result = $stmt->get_result();
	        
	        if ($row = $result->fetch_assoc()) {
	            $val = $row['QUANTITY'] !== null ? $row['QUANTITY'] : 0;
	        }
	        
	        $stmt->close();
	    }
	
	    return $val;

	}

	function countBranchesInCluster($cluster,$db) {
	    $sql = "SELECT COUNT(*) AS branch_count FROM tbl_branch WHERE location = ?";
	    
	    if ($stmt = $db->prepare($sql)) {
	        $stmt->bind_param("s", $cluster);
	        
	        $stmt->execute();
	        
	        $result = $stmt->get_result();
	        
	        $row = $result->fetch_assoc();
	        $branch_count = $row['branch_count'];
	        
	        $stmt->free_result();
	        $stmt->close();
	        
	        return $branch_count;
	    } else {
	        return "Error: " . $db->error;
	    }
	}

	function countDetectEmployeeChargedBAKERONLY2($module, $chargeno, $db) {

	    $allowed_columns = ['quantity', 'total'];
	    if (!in_array($module, $allowed_columns)) {
	        return 0;
	    }
	
	    $query = "SELECT SUM($module) as total FROM dbc_charges_extension_record WHERE chargecodeno = ? AND employee_type = 'BAKER'";
	    
	    $stmt = $db->prepare($query);
	    $stmt->bind_param("s", $chargeno);
	
	    if ($stmt->execute()) {
	        $result = $stmt->get_result();
	        
	        if ($row = $result->fetch_assoc()) {
	            $count = $row['total'];
	            return $count ? (float)$count : 0;
	        } else {
	            return 0;
	        }
	    } else {
	        return 0;
	    }
	
	    $stmt->close();
	}
	
	
	function countDetectEmployeeChargedNONEBAKERONLY($module, $chargeno, $db) {

	    $allowed_columns = ['quantity', 'total'];
	    if (!in_array($module, $allowed_columns)) {
	        return 0;
	    }
	
	    $query = "SELECT SUM($module) as total FROM dbc_charges_extension_record WHERE chargecodeno = ? AND employee_type = 'NONE BAKER'";
	    
	    $stmt = $db->prepare($query);
	    $stmt->bind_param("s", $chargeno);
	
	    if ($stmt->execute()) {
	        $result = $stmt->get_result();
	        
	        if ($row = $result->fetch_assoc()) {
	            $count = $row['total'];
	            return $count ? (float)$count : 0;
	        } else {
	            return 0;
	        }
	    } else {
	        return 0;
	    }
	
	    $stmt->close();
	}

	
	
	function updateChargesByChargedBAKERONLY($chargeno, $new_quantity, $new_total, $db) {

	
	    $stmt = $db->prepare("UPDATE dbc_charges_extension_record SET quantity = ?, total = ? WHERE chargecodeno = ? AND employee_type = 'BAKER'");
	    $stmt->bind_param("dds", $new_quantity, $new_total, $chargeno);
	    
	    if ($stmt->execute()) {
	    
	    } else {

	    }
	
	    $stmt->close();
	}

	
	function countDetectEmployeeChargedBAKERONLY($chargeno, $db) {

	    $stmt = $db->prepare("SELECT COUNT(*) as record_count FROM dbc_charges_extension_record WHERE chargecodeno = ? AND employee_type = 'BAKER'");
    	$stmt->bind_param("s", $chargeno);	
    		
		if ($stmt->execute()) {
		    $result = $stmt->get_result();
		    
		    if ($row = $result->fetch_assoc()) {
		        $count = $row['record_count'];
		        return $count;
		    } else {
		        return 0;
		    }
		} else {
		    return false;
		}
		
		$stmt->close();

	}


	function updateChargesByChargeCode($chargeno, $new_quantity, $new_total, $db) {

	
	    $stmt = $db->prepare("UPDATE dbc_charges_extension_record SET quantity = ?, total = ? WHERE chargecodeno = ?");
	    $stmt->bind_param("dds", $new_quantity, $new_total, $chargeno);
	    
	    if ($stmt->execute()) {
//	        return "Successfully updated quantity and total for chargecodeno: $chargeno";
	    } else {
//	        return "Error updating record: " . $db->error;
	    }
	
	    $stmt->close();
	}

	function countDetectEmployeeCharged($chargeno, $db) {

	    $stmt = $db->prepare("SELECT COUNT(*) as record_count FROM dbc_charges_extension_record WHERE chargecodeno = ?");
		$stmt->bind_param("s", $chargeno); // Bind $chargeno as a string
		
		if ($stmt->execute()) {
		    $result = $stmt->get_result();
		    
		    if ($row = $result->fetch_assoc()) {
		        $count = $row['record_count'];
		        return $count;
		    } else {
		        return 0;
		    }
		} else {
		    return false;
		}
		
		$stmt->close();

	}


	function generateControlNumber() {

	    $year = date('y');
	    $month = date('m');
	    $day = date('d');
	    $hours = date('H');
	    $minutes = date('i');
	    $seconds = date('s');
	
	    $randomNum = rand(1000, 9999);
	    $controlNumber = $year . $month . $day . $hours . $minutes . $seconds . $randomNum;
	
	    return $controlNumber;
	}
	
	
	public function getValueSummaryviadate($datefrom, $dateto, $itemcode, $db)
	{
	    $QUERY = "
	        SELECT SUM(b.quantity) AS qty 
	        FROM dbc_branch_order b
	        JOIN dbc_order_request o ON b.control_no = o.control_no
	        AND b.item_code = '$itemcode' 
	        AND b.trans_date BETWEEN '$datefrom' AND '$dateto'
	        AND o.status != 'Open'
	        AND o.status != 'Void'
	    ";
	
	    $RESULTS = mysqli_query($db, $QUERY);
	    
	    $val = 0;
	    if ($RESULTS && $RESULTS->num_rows > 0) 
	    {
	        while ($ROW = mysqli_fetch_array($RESULTS))  
	        {
	            $val = $ROW["qty"];
	        }
	    }
	
	    return $val;
	}



	public function getChargesPeaple($module,$chargecodeno,$db)
	{
		$sqlQueryStk = "SELECT * FROM dbc_charges_extension_record WHERE chargecodeno = '$chargecodeno'";
	    $stkResults = mysqli_query($db, $sqlQueryStk);
		$count = 1;
	    if ($stkResults->num_rows > 0)
	    {
	       	while($ROWS = mysqli_fetch_array($stkResults))  
			{
				echo $count.'. '.$ROWS[$module].'<br>';
				$count++;
			}

		}
	}
	
	public function customemoduletablegetvalue($module,$table,$wheremodule,$wherevalue,$db)
	{
		$sqlQueryStk = "SELECT $module FROM $table WHERE $wheremodule='$wherevalue'";
	    $stkResults = mysqli_query($db, $sqlQueryStk);
	    $val = '';
	    if ($stkResults->num_rows > 0)
	    {
	       	while($ROWS = mysqli_fetch_array($stkResults))  
			{
				$val = $ROWS[$module];
			}
			return $val;
		}
		else {
			return $val;
		}
	}

	public function voidRetouchData($rowid,$itemcode,$dateupdated,$updatedby,$db)
	{
		$returnquantity = $this->Getfromthetable('quantity','dbc_retouch',$rowid,$itemcode,$db);
		
		$stockbeforepcountdate = $this->GetInventoryStockStocknHandReturn('stock_before_pcount_date',$itemcode,$db);
		$stockbeforepcount = $this->GetInventoryStockStocknHandReturn('stock_before_pcount',$itemcode,$db);
		
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$stockbeforepcountdate',stock_before_pcount='$stockbeforepcount', stock_in_hand=stock_in_hand-'$returnquantity', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }
	}

	
/*	public function getVallueBranchandItemDate($datefrom, $dateto, $branch, $itemcode, $db)
	{

		$QUERY = "SELECT SUM(quantity) AS qty FROM dbc_branch_order WHERE branch = '$branch' AND item_code = '$itemcode' AND trans_date BETWEEN '$datefrom' AND '$dateto'";
		$RESULTS = mysqli_query($db, $QUERY);
		
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["qty"];
			}
			return $val;
		} else {
			return $val;
		}

	}
*/	

	public function getVallueBranchandItemDate($datefrom, $dateto, $branch, $itemcode, $db)
	{
	    $QUERY = "
	        SELECT SUM(b.quantity) AS qty 
	        FROM dbc_branch_order b
	        JOIN dbc_order_request o ON b.control_no = o.control_no
	        WHERE b.branch = '$branch' 
	        AND b.item_code = '$itemcode' 
	        AND b.trans_date BETWEEN '$datefrom' AND '$dateto'
	        AND o.status != 'Open'
	        AND o.status != 'Void'
	    ";
	
	    $RESULTS = mysqli_query($db, $QUERY);
	    
	    $val = 0;
	    if ($RESULTS && $RESULTS->num_rows > 0) 
	    {
	        while ($ROW = mysqli_fetch_array($RESULTS))  
	        {
	            $val = $ROW["qty"];
	        }
	    }
	
	    return $val;
	}


	public function getIntoDbcTblitemslist($datefrom, $dateto, $db)
	{

	    $branches = [];
	    $branchQuery = "SELECT * FROM dbc_tbl_branch WHERE status = 1";  
	    $branchResult = mysqli_query($db, $branchQuery);  
	    while($branchRow = mysqli_fetch_array($branchResult))  
	    {
	        $branches[] = $branchRow['branch'];
	    }
	
	    $totalPerBranch = array_fill(0, count($branches), 0);
	
	    $query = "SELECT * FROM wms_itemlist WHERE active = 1 ORDER BY ordered ASC";  
	    $result = mysqli_query($db, $query);  
	    $i = 1;
	    while($ROWS = mysqli_fetch_array($result))  
	    {
	        $itemcode  = $ROWS["item_code"];
	        $itemname = $ROWS["item_description"];
	        $uom = $ROWS["uom"];
	        $totalitems = 0;
	        
	        echo '<tr>';
	        echo '<td class="sticky-column sticky-column-1-data" style="text-align:center">'.$i.'</td>';
	        echo '<td class="sticky-column sticky-column-2-data">'.$itemname.'</td>';
	        echo '<td class="sticky-column sticky-column-3-data" style="text-align:center">'.$uom.'</td>';
	        
	        foreach ($branches as $index => $branch) {
	            $itemss = $this->getVallueBranchandItemDate($datefrom, $dateto, $branch, $itemcode, $db);
	            $totalitems += $itemss;
	            $totalPerBranch[$index] += $itemss;
	            echo '<td style="text-align:center">'.$itemss.'</td>';
	        }
	        
	        echo '<td style="text-align:center">'.number_format($totalitems, 2).'</td>';
	        echo '</tr>';
	        $i++;
	    }
	
	    echo '<tr>';
	    echo '<td colspan="3" class="sticky-column sticky-column-1-data" style="text-align:center"><strong>TOTAL</strong></td>';
	    foreach ($totalPerBranch as $total) {
	        echo '<td style="text-align:center">'.number_format($total, 2).'</td>';
	    }
	    echo '<td style="text-align:center">'.number_format(array_sum($totalPerBranch), 2).'</td>';
	    echo '</tr>';
	}

	
	public function getIntoDbcTblBranchTableHeader($db)
	{
		$query ="SELECT * FROM dbc_tbl_branch WHERE status = 1";  
		$result = mysqli_query($db, $query);  
		while($ROWS = mysqli_fetch_array($result))  
		{
			echo '<td style="background-color:#0cccae;color:#fff">'.$ROWS["branch"].'</td>';
		}
	}

	
	public function getIntoDbcTblBranchTableStatus($branchid, $db)
	{
		$QUERY = "SELECT * FROM dbc_tbl_branch WHERE branch_id='$branchid'";
		$RESULTS = mysqli_query($db, $QUERY);
		
		$status = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$status = $ROW["status"];
			}
			return $status;
		} else {
			return $status;
		}
	}

	
	public function insertIntoDbcTblBranchTable($branch, $branchid, $active, $db)
	{
		$branch = trim($branch);
	    // Check if the branch already exists
	    $checkQuery = "SELECT * FROM dbc_tbl_branch WHERE branch_id = ?";
	    $stmt = $db->prepare($checkQuery);
	    $stmt->bind_param('i', $branchid);
	    $stmt->execute();
	    $result = $stmt->get_result();
	    
	    if ($result->num_rows > 0) {
	        
	        

			// Delete existing branch
	        $deleteQuery = "DELETE FROM dbc_tbl_branch WHERE branch_id = ?";
	        $stmt = $db->prepare($deleteQuery);
	        $stmt->bind_param('i', $branchid);
	        $stmt->execute();
	        if ($stmt->affected_rows > 0) {
	            return "Branch deleted successfully.";
	        } else {
	            return "Failed to delete the branch.";
	        }



	    } else {
	        // Insert new branch
	        $insertQuery = "INSERT INTO dbc_tbl_branch (branch_id, branch, status) VALUES (?, ?, ?)";
	        $stmt = $db->prepare($insertQuery);
	        $stmt->bind_param('ssi', $branchid, $branch, $active);
	        $stmt->execute();
	        if ($stmt->affected_rows > 0) {
	            return "Branch inserted successfully.";
	        } else {
	            return "Failed to insert branch.";
	        }
	    }
	}

	
	public function brachCheckingIfActive($branchid,$db)
	{
		$QUERY = "SELECT * FROM dbc_tbl_branch WHERE branch_id='$branchid'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}


	public function selectBranchList($db){
		$query ="SELECT * FROM tbl_branch";  
		$result = mysqli_query($db, $query);  
		while($ROWS = mysqli_fetch_array($result))  
		{
			echo '<option>'.$ROWS["branch"].'</option>';
		}
	}

	public function getIdcodeViaEmployeeName($acctname,$db)
	{
		$QUERY = "SELECT idcode FROM tbl_employees WHERE acctname='$acctname'";
		$RESULTS = mysqli_query($db, $QUERY);
		
		$idcode = '';
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$idcode = $ROW["idcode"];
			}
			return $idcode;
		} else {
			return $idcode;
		}
	}

	public function selectEmployeeList($db){
		$query ="SELECT * FROM tbl_employees";  
		$result = mysqli_query($db, $query);  
		
		if (!$result) {
	       exit();
	    }
		
		echo '<option value="">--- SELECT EMPLOYEE ---</option>';
		while($ROWS = mysqli_fetch_array($result))  
		{
			echo '<option value="'.$ROWS["acctname"].'"></option>';
		}

	}
	
	public function GetOtherDataFromTable($table1,$column,$itemcode,$reportdate,$db)
	{
		$table = 'dbc_'.$table1;
		$QUERY = "SELECT SUM($column) AS TOTAL FROM $table WHERE report_date='$reportdate' AND item_code='$itemcode' AND status=0";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["TOTAL"];
			}
			return $val;
		} else {
			return $val;
		}
	}

	
	public function Getfromthetable($module,$table,$rowid,$itemcode,$db)
	{
		$sqlQueryStk = "SELECT $module FROM $table WHERE id='$rowid'";
	    $stkResults = mysqli_query($db, $sqlQueryStk);
	    $val = 0;
	    if ($stkResults->num_rows > 0)
	    {
	       	while($ROWS = mysqli_fetch_array($stkResults))  
			{
				$val = $ROWS[$module];
			}
			return $val;
		}
		else {
			return $val;
		}
	}
	
	public function voidotherdata($table,$rowid,$itemcode,$dateupdated,$updatedby,$db)
	{
		$quantity = $this->Getfromthetable('quantity','dbc_'.$table,$rowid,$itemcode,$db);
		
		$stockbeforepcountdate = $this->GetInventoryStockStocknHandReturn('stock_before_pcount_date',$itemcode,$db);
		$stockbeforepcount = $this->GetInventoryStockStocknHandReturn('stock_before_pcount',$itemcode,$db);
		
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$stockbeforepcountdate',stock_before_pcount='$stockbeforepcount', stock_in_hand=stock_in_hand+'$quantity', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }
	}


	public function otherinventoryoutdata($itemcode,$itemval,$dateupdated,$updatedby,$db)
	{
		$stockbeforepcount = $this->GetInventoryStockStocknHandReturn('stock_before_pcount',$itemcode,$db);
		$stockbeforepcountdate = $this->GetInventoryStockStocknHandReturn('stock_before_pcount_date',$itemcode,$db);
		
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$stockbeforepcountdate',stock_before_pcount='$stockbeforepcount', stock_in_hand=stock_in_hand-'$itemval', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }

	}

	public function voidReturnData($rowid,$itemcode,$dateupdated,$updatedby,$db)
	{
		$returnquantity = $this->Getfromthetable('quantity','dbc_return',$rowid,$itemcode,$db);
		
		$stockbeforepcountdate = $this->GetInventoryStockStocknHandReturn('stock_before_pcount_date',$itemcode,$db);
		$stockbeforepcount = $this->GetInventoryStockStocknHandReturn('stock_before_pcount',$itemcode,$db);
		
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$stockbeforepcountdate',stock_before_pcount='$stockbeforepcount', stock_in_hand=stock_in_hand-'$returnquantity', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }
	}

	public function insertReturnData($itemcode,$returnval,$dateupdated,$updatedby,$db)
	{
		$stockbeforepcount = $this->GetInventoryStockStocknHandReturn('stock_before_pcount',$itemcode,$db);
		$stockbeforepcountdate = $this->GetInventoryStockStocknHandReturn('stock_before_pcount_date',$itemcode,$db);
		$stockinhand = $returnval + $this->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
		
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$stockbeforepcountdate',stock_before_pcount='$stockbeforepcount', stock_in_hand=stock_in_hand+'$returnval', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }

	}
	
	public function GetInventoryStockStocknHandReturn($module,$itemcode,$db)
	{
		$sqlQueryStk = "SELECT $module FROM dbc_inventory_stock WHERE item_code='$itemcode'";
	    $stkResults = mysqli_query($db, $sqlQueryStk);
	    $val = 0;
	    if ($stkResults->num_rows > 0)
	    {
	       	while($ROWS = mysqli_fetch_array($stkResults))  
			{
				$val = $ROWS[$module];
			}
			return $val;
		}
		else {
			return $val;
		}

	}

	public function dateLockCheckingExist($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_datelock_checker WHERE report_date='$transdate'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}
	
	
	public function dateLockChecking($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_datelock_checker WHERE report_date='$transdate' AND status='1'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}

	public function limitStringLength($string, $maxLength) {
	    if (mb_strlen($string) > $maxLength) {
	        return mb_substr($string, 0, $maxLength)."...";
	    }
	    return $string;
	}
	public function executeInventory($itemcode,$pcount,$transdate,$dateupdated,$updatedby,$db)
	{
		$valuebeforepcount = $this->GetInventoryStockStocknHand($itemcode,$db);
		$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_before_pcount_date='$transdate',stock_before_pcount='$valuebeforepcount', stock_in_hand='$pcount', date_updated='$dateupdated',updated_by='$updatedby' WHERE item_code='$itemcode'";
        if ($db->query($queryDataUpdate) === TRUE) {
        } else {
            echo $db->error;
        }
	}
	
	public function GetInventoryStockStocknHand($itemcode,$db)
	{
		$sqlQueryStk = "SELECT stock_in_hand FROM dbc_inventory_stock WHERE item_code='$itemcode'";
	    $stkResults = mysqli_query($db, $sqlQueryStk);
	    $val = 0;
	    if ($stkResults->num_rows > 0)
	    {
	       	while($ROWS = mysqli_fetch_array($stkResults))  
			{
				$val = $ROWS['stock_in_hand'];
			}
			return $val;
		}
		else {
			return $val;
		}

	}
	
	public function GetProductionCharges($column,$itemcode,$transdate,$db)
	{
		$QUERY = "SELECT SUM($column) AS TOTAL FROM dbc_dbc_production WHERE item_code = '$itemcode' AND MONTH(report_date) = MONTH('$transdate')";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["TOTAL"];
			}
			return $val;
		} else {
			return $val;
		}
	}

	public function GetPcountBeginning($column,$itemcode,$transdate,$db)
	{
		$QUERY = "SELECT SUM($column) AS TOTAL FROM dbc_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["TOTAL"];
			}
			return $val;
		} else {
			return $val;
		}
	}

	public function GetPcountDataInventoryChecker($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_inventory_pcount WHERE trans_date = '$transdate'";
		$RESULTS = mysqli_query($db, $QUERY);
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}
	
	public function GetItemListColumn($column,$itemcode,$db)
	{
		$query ="SELECT * FROM wms_itemlist WHERE item_code='$itemcode'";  
		$RESULTS = mysqli_query($db, $query);
		$val = '';
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW[$column];
			}
			return $val;
		} else {
			return $val;
		}		
	}

	public function getPcountExist($transdate, $db) {
	    $transdate = mysqli_real_escape_string($db, $transdate);
	    
	    $query = "SELECT trans_date FROM dbc_inventory_pcount WHERE trans_date='$transdate'";
	    
	    $results = mysqli_query($db, $query);
	    
	    if ($results && mysqli_num_rows($results) > 0) {
	        return 1;
	    } else {
	        return 0;
	    }
	}


	public function GetProductionIDExist($itemcode,$transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_dbc_production WHERE item_code='$itemcode' AND report_date='$transdate' AND status <> 'Void'";
		$RESULTS = mysqli_query($db, $QUERY);
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}

	public function GetDataByRecevingId($column,$table,$receivingdetailid,$db)
	{
		$QUERY = "SELECT * FROM $table WHERE receiving_detail_id='$receivingdetailid'";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = '';
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW[$column];
			}
			return $val;
		} else {
			return $val;
		}
	}

	public function GetProductionifExist($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_dbc_production WHERE report_date='$transdate' AND status='Yes'";
		$RESULTS = mysqli_query($db, $QUERY);
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}

	public function getLastDayOfMonth($year,$month)
	{
	    $nextMonth = new DateTime("$year-$month-28");
	    $nextMonth->modify('+4 days');
	    
	    return $nextMonth->modify("-{$nextMonth->format('j')} days")->format('j');
	}

	public function GetTransferOutData($itemcode,$transdatefrom,$transdateto,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE item_code='$itemcode' AND trans_date BETWEEN '$transdatefrom' AND '$transdateto' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE status = 'Closed' AND order_delivered = 1)";
		
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW['QUANTITY'];
			}
			return $val;
		} else {
			return $val;
		}
	}


	public function GetProductionData($column,$itemcode,$datefrom,$dateto,$db)
	{
		$QUERY = "SELECT SUM($column) AS TOTAL FROM dbc_dbc_production WHERE report_date BETWEEN '$datefrom' AND '$dateto' AND item_code='$itemcode' AND status='Yes'";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["TOTAL"];
			}
			return $val;
		} else {
			return $val;
		}
	}
	
	public function GetPcountData($column,$itemcode,$datefrom,$dateto,$db)
	{
		$QUERY = "SELECT SUM($column) AS TOTAL FROM dbc_fgts_pcount WHERE trans_date BETWEEN '$datefrom' AND '$dateto' AND item_code='$itemcode'";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$val = $ROW["TOTAL"];
			}
			return $val;
		} else {
			return $val;
		}
	}


	public function getPendingApprovalCount($db)
	{
	    $query ="SELECT COUNT(*) AS pendingTotal FROM dbc_dbc_production WHERE status='No'";  
	    
	    $result = mysqli_query($db, $query);  
	
	    if($result) {
	        $row = mysqli_fetch_assoc($result);
	
	        $pendingTotal = $row['pendingTotal'];
	
	        mysqli_free_result($result);
	
	        return $pendingTotal;
	    } else {
	        return 0;
	    }
	}

	

	public function GetItemDescription($db)
	{
		$query ="SELECT item_description FROM wms_itemlist WHERE recipient='DAVAO BAKING CENTER' AND active = 1";  
		$result = mysqli_query($db, $query);  
		echo '<option value="">--- SELECT ITEM ---</option>';
		while($ROWS = mysqli_fetch_array($result))  
		{
			echo '<option>'.$ROWS["item_description"].'</option>';
		}		
	}

	public function UpdateBranchOrderDeliveryDate($control_no,$delivery_date,$db)
	{
		$queryDataUpdate = "UPDATE dbc_branch_order SET delivery_date='$delivery_date' WHERE control_no='$control_no'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
		} else {
			return $db->error;
		}
	}

	public function GetItemClassData($column,$item_code,$db)
	{
		$QUERY = "SELECT * FROM dbc_classifications_records WHERE item_code='$item_code' AND flag=0";
		$RESULTS = mysqli_query($db, $QUERY);		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$retVal += $ROW[$column];
			}
			return $retVal;
		} else {
			return 0;
		}
	}
	public function GetClassificationData($item_code,$db)
	{
		$QUERY = "SELECT * FROM dbc_order_request WHERE item_code='$item_code'";
		$RESULTS = mysqli_query($db, $QUERY);		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$logistics = $ROW['logistics'];
				$transit = $ROW['order_transit'];
			}
			if($logistics == 1 AND $transit == 1)
			{
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	public function GetClassification($class)
	{
		$classfication = array("Bad Order"=>"Bad Order","Damaged"=>"Damaged","Seconds"=>"Seconds","Disposal"=>"Disposal","Re-Classification"=>"Re-Classification");
       	$return = '<option value="">--- Classifications ---</option>';
        foreach ( $classfication as $key => $value )
        {
        	$selected = '';
        	if($value == $class)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
	}
	public function checkInTransit($control_no,$db)
	{
		$QUERY = "SELECT * FROM dbc_order_request WHERE control_no='$control_no'";
		$RESULTS = mysqli_query($db, $QUERY);		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$logistics = $ROW['logistics'];
				$transit = $ROW['order_transit'];
			}
			if($logistics == 1 AND $transit == 1)
			{
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	public function getDeliveryDate($control_no,$db)
	{
		$QUERY = "SELECT * FROM dbc_order_request WHERE control_no='$control_no'";
		$RESULTS = mysqli_query($db, $QUERY);		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$delDate = $ROW['delivery_date'];
			}
			return $delDate;
		} else {
			return 0;
		}
	}
	public function getDeliveryMonth($control_no,$db)
	{
		$cur_date = date("m");
		$QUERY = "SELECT * FROM dbc_order_request WHERE control_no='$control_no'";
		$RESULTS = mysqli_query($db, $QUERY);		
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$delDate = date("m", strtotime($ROW['delivery_date']));
			}
			if($cur_date == $delDate)
			{
				return 1;
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	public function reOpenOrder($item_code,$control_no,$actual_qty,$app_user,$db)
	{
		$queryDataUpdate = "UPDATE dbc_order_request SET status='Submitted', logistics=0, order_transit=0 WHERE control_no='$control_no'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			//echo "SUCCESS";
		} else {
			echo $db->error;
		}
	}
	public function cancelOrderDeduction($item_code,$control_no,$actual_qty,$app_user,$db)
	{
		$QUERY = "SELECT * FROM dbc_inventory_stock WHERE item_code='$item_code'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$new_stock = ($ROW['stock_in_hand'] + $actual_qty);				
			/* ------------------------------------------------------------------------------------------- */
				$queryDataUpdate = "UPDATE dbc_inventory_stock SET stock_in_hand='$new_stock' WHERE item_code='$item_code'";
				if ($db->query($queryDataUpdate) === TRUE)
				{
				/* ##################################################################### */
					$queryDataUpdate = "UPDATE dbc_order_request SET status='Submitted', logistics=0, order_transit=0 WHERE control_no='$control_no'";
					if ($db->query($queryDataUpdate) === TRUE)
					{
					} else {
						echo $db->error;
					}
				/* ##################################################################### */
				} else {
					echo $db->error;
				}
			/* ------------------------------------------------------------------------------------------- */
			}
			return $new_stock;
		} else {
			return 'Invalid';
		}
	}
	public function GetPickList($control_no,$db)
	{
		$queryBO = "SELECT * FROM dbc_branch_order WHERE control_no='$control_no'";
		$resultBO = mysqli_query($db, $queryBO);		
		if ($resultBO)
		{
		    $boCount = mysqli_num_rows($resultBO);
		} else {

		    $boCount = 0;
		}
		$queryPL = "SELECT * FROM dbc_picklist WHERE control_no='$control_no'";
		$resultPL = mysqli_query($db, $queryPL);		
		if ($resultPL)
		{
		    $plCount = mysqli_num_rows($resultPL);
		} else {

		    $plCount = 0;
		}
		
		if($boCount == $plCount)
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function getPOSI($receiving_id,$column,$db)
	{
		$QUERY = "SELECT * FROM dbc_receiving WHERE receiving_id='$receiving_id'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$return = $ROW[$column];
			}
			return $return;
		} else {
			return 'Invalid';
		}
	}
	public function closeReceiving($receiving_id,$db)
	{
		$query = "SELECT * FROM dbc_receiving WHERE receiving_id='$receiving_id' AND status='Closed'";
		$result = mysqli_query($db, $query);		
	    if ( $result->num_rows > 0 ) 
	    {
	    	return 1;
		} else {
			return 0;
		}
	}
	public function GetPickCount($branch,$control_no,$db)
	{
		$queryBO = "SELECT * FROM dbc_branch_order WHERE control_no='$control_no' AND branch='$branch'";
		$resultBO = mysqli_query($db, $queryBO);		
		if ($resultBO)
		{
		    $boCount = mysqli_num_rows($resultBO);
		} else {

		    $boCount = 0;
		}
		$queryPL = "SELECT * FROM dbc_picklist WHERE control_no='$control_no'";
		$resultPL = mysqli_query($db, $queryPL);		
		if ($resultPL)
		{
		    $plCount = mysqli_num_rows($resultPL);
		} else {

		    $plCount = 0;
		}
		
		if($boCount == $plCount)
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function GetModulePermission($username,$userlevel,$module,$permission,$db)
	{
		if($userlevel >= 80)
		{
			return 1;
		} elseif($userlevel < 80) {
			$checkPolicy = "SELECT * FROM tbl_system_permission WHERE username='$username' AND modules='$module' AND $permission=1";
			$pRes = mysqli_query($db, $checkPolicy);    
		    if ( $pRes->num_rows > 0 ) 
		    {
		    	return 1;
			} else {
				return 0;
			}
		}
		mysqli_close($db);
	}
	public function GetOrderReceiving($ord)
	{
		$stats = array("Process Order"=>"Process Order","Open Order"=>"Open Order","Closed Order"=>"Closed Order","Void Order"=>"Void Order");
        $return = "";
        foreach ( $stats as $key => $value )
        {
        	$selected = "";
        	if($value == $ord)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
	}
	public function GetItemStock($itemcode,$db)
	{
		$QUERY = "SELECT * FROM dbc_inventory_stock WHERE item_code='$itemcode'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$return = $ROW['stock_in_hand'];
			}
			return $return;
		} else {
			return 0;
		}
	}
	public function GetDBCReports($report,$db)
	{
		$query = "SELECT * FROM dbc_reporting_option WHERE active=1 ORDER BY ordering ASC";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">-- SELECT REPORT --</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$reporting = $ROW['reporting_option'];
				$selected = '';
				if($report == $reporting)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$reporting.'">'.$reporting.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO DATA ---</option>';
		}
		mysqli_close($db);
	}
	public function DoAuditLogs($log_date,$activity,$log_by,$db)
	{
		$column = "`log_date`,`activity`,`log_by`";	
		$insert = "'$log_date','$activity','$log_by'";
		$queryInsert = "INSERT INTO dbc_audit_logs ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
		} else {
			echo $db->error;
		}
	}
	public function GetItemLocation($location,$db)
	{
		$query = "SELECT * FROM dbc_item_location WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">-- ITEM LOCATION --</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$item_location = $ROW['item_location'];
				$selected = '';
				if($location == $item_location)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$item_location.'">'.$item_location.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO LOCATION ---</option>';
		}
		mysqli_close($db);
	}
	public function GetItemName($itemcode,$db)
	{
		$query = "SELECT * FROM wms_itemlist WHERE item_code='$itemcode'";
		$results = $db->query($query);			
	    if($results->num_rows > 0)
	    {
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW['item_description'];
			}
	    } else {
	    	return 'No Item found on that Itemcode '. $itemcode;
	    }
	    mysqli_close($db);
	}
	public function GetTotalBranch($db)
	{
		$query = "SELECT * FROM tbl_branch";
		$results = $db->query($query);
		if($results->num_rows > 0)
	    {
		    return mysqli_num_rows($results);echo "A: ". $itemcode;
		} else {
		    return 0;
		}
		mysqli_close($db);
	}
	public function GetRemarksDateQuery($itemcode,$transdate,$column,$db)
	{
		$query = "SELECT * FROM dbc_inventory_pcount WHERE item_code='$itemcode' AND trans_date='$transdate'";
		$results = $db->query($query);			
	    if($results->num_rows > 0)
	    {
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW[$column];
			}
	    } else {
	    	return '-';
	    }
	    mysqli_close($db);
	}
	public function GetAnyQuery($table,$column,$column_value,$query_col,$db)
	{
		$query = "SELECT * FROM $table WHERE $column='$column_value'";
		$results = $db->query($query);			
	    if($results->num_rows > 0)
	    {
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW[$query_col];
			}
	    } else {
	    	return 0;
	    }
	    mysqli_close($db);
	}
	public function GetUnitPriceRecords($itemcode,$year,$month,$db)
	{
		$query = "SELECT * FROM dbc_inventory_records WHERE item_code='$itemcode' AND month='$month' AND year='$year'";
		$results = $db->query($query);			
	    if($results->num_rows > 0)
	    {
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW['unit_price'];
			}
	    } else {
	    	return 0;
	    }
	    mysqli_close($db);
	}
	public function GetUnitPrice($itemcode,$db)
	{
		$query = "SELECT * FROM wms_itemlist WHERE item_code='$itemcode'";
		$results = $db->query($query);			
	    if($results->num_rows > 0)
	    {
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW['unit_price'];
			}
	    } else {
	    	return 0;
	    }
	    mysqli_close($db);
	}
	public function CountBranch($cluster,$db)
	{
		$query = "SELECT * FROM tbl_branch WHERE location='$cluster'";
		$results = $db->query($query);			
	    $row_count = $results->num_rows;
	    if($results->num_rows > 0)
	    {
		    return $results->num_rows;
	    } else {
	    	return 0;
	    }
	    mysqli_close($db);
	}
	public function CountRows($table,$db)
	{
		$query = "SELECT id FROM $table WHERE active=1";
		$results = $db->query($query);			
	    $row_count = $results->num_rows;
	    if($results->num_rows > 0)
	    {
		    return $results->num_rows;
	    } else {
	    	return 0;
	    }
	    mysqli_close($db);
	}
	public function GetCluster($cluster,$db)
	{
		$query = "SELECT * FROM tbl_cluster ORDER BY cluster ASC";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">-- CLUSTER --</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$cluster_name = $ROW['cluster'];
				$selected = '';
				if($cluster == $cluster_name)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$cluster_name.'">'.$cluster_name.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO CLUSTER ---</option>';
		}
		mysqli_close($db);
	}
	public function GetBranch($branch,$db)
	{
		$query = "SELECT * FROM tbl_branch ORDER BY branch ASC";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">-- BRANCH --</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$branch_name = $ROW['branch'];
				$selected = '';
				if($branch == $branch_name)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$branch_name.'">'.$branch_name.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO BRANCH ---</option>';
		}
		mysqli_close($db);
	}
	public function GetYear($year)
	{
		for($i=2023; $i <= 2030; $i++)
		{
			$yearsArray[] = $i;
		}
		$return = '';
		foreach($yearsArray as $years)
		{
			$selected='';
			if($years == $year)
			{
				$selected = 'selected';
			}
			$return .= '<option '.$selected.' value="'.$years.'">'.$years.'</option>'; 
		}
		return $return;
		mysqli_close($db);
	}
	public function GetWeekOfMonth($week)
	{
		// $stats = array("Week 1 (1-7)"=>"1","Week 2 (8-14)"=>"2","Week 3 (15-21)"=>"3","Week 4 (22-28)"=>"4","Week 5 (29-31)"=>"5","Monthly (1-31)"=>"0");
		$stats = array("Monthly (1-31)"=>"0");
        $return = "";
        foreach ( $stats as $key => $value )
        {
        	$selected = "";
        	if($value == $week)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	public function GetMonths($month)
	{
		$stats = array("January"=>"01","February"=>"02","March"=>"03","April"=>"04","May"=>"05","June"=>"06","July"=>"07","August"=>"08","September"=>"09","October"=>"10","November"=>"11","December"=>"12");
        $return = "";
        foreach ( $stats as $key => $value )
        {
        	$selected = "";
        	if($value == $month)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	public function GetDays($day,$day_cnt)
	{
		$values = array();
		for ($i = 1; $i <= 31; $i++) {
		    $value = str_pad($i, 2, "0", STR_PAD_LEFT);
		    $values[] = $value;
		}
		foreach ( $values as $key => $value )
		{
			$selected = "";
        	if($value == $day)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$value.'</option>';  
		}
		return $return;
		mysqli_close($db);
	}
	public function getDBCSubReport($table,$submenu,$db)
	{
		$query = "SELECT * FROM dbc_reporting_option_sub WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$menu_name = $ROW['reporting_sub'];
				$value = $ROW['page_name'];
				$selected = '';
				if($value == $submenu)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$value.'">'.$menu_name.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NOTHING TO REPORT ---</option>';
		}
		mysqli_close($db);
	}
	public function getDBCReport($table,$db)
	{
		$query = "SELECT * FROM dbc_reporting_option WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT MODULE ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$menu_name = $ROW['reporting_option'];
				$value = $ROW['table_name'];
				$selected = '';
				if($value == $table)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$value.'">'.$menu_name.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NOTHING TO REPORT ---</option>';
		}
		mysqli_close($db);
	}
	public function GetOnHand($itemcode,$db)
	{
		$query = "SELECT * FROM dbc_inventory_stock WHERE item_code='$itemcode'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$stock = $ROW['stock_in_hand'];
			}
			return $stock;
		} else {
			return '0';
		}
		mysqli_close($db);
	}
	public function GetDrNumber($db)
	{
		$query = "SELECT * FROM dbc_form_numbering WHERE id=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$numbering = $ROW['dr_number'];
			}
			return $numbering;
		} else {
			return 'Numbering is not configured';
		}
		mysqli_close($db);
	}
	public function GetOrderStatus($control_no,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_order_request WHERE control_no='$control_no'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$retVal = $ROW[$column];
			}
			return $retVal;
		} else {
			return $retVal;
		}
		
		mysqli_close($db);
	}
	public function getOrderRemarks($mrs_no,$column,$db)
	{
		$QUERY = "SELECT * FROM dbc_branch_order_remarks WHERE control_no='$mrs_no'";
//		return $QUERY;
//		exit();
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				return $ROW[$column];
			}
		} else {
			return "";
		}
		mysqli_close($db);
	}
	public function shortenText($text, $maxLength)
	{
		if (strlen($text) <= $maxLength)
		{
			return $text;
		} else {
		    $shortenedText = substr($text, 0, $maxLength);
		    $shortenedText .= '...';
		    return $shortenedText;
		}
		mysqli_close($db);
	}
	public function GetRequestStatus($status)
	{
		$stats = array(
	        "Open" => "Open",
            "Closed" => "Closed",
            "Cancelled" => "Cancelled"
        );
        $return = "";
        foreach ( $stats as $key => $value )
        {
        	$selected = "";
        	if($value == $status)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	public function GetPriority($priority)
	{
        $priority = array(
	        "Normal" => "Normal",
            "Urgent" => "Urgent"                        
        );
        foreach ( $priority as $key => $value )
        {
        	$selected = "";
        	if($value == $transtype)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	
	public function GetLocation($item_location,$db)
	{
		$query = "SELECT * FROM dbc_item_location WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT ITEM LOCATION ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$location = $ROW['item_location'];
				$selected = '';
				if($location == $item_location)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$location.'">'.$location.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- Recipient Not Configured ---</option>';
		}
		mysqli_close($db);
	}
	public function GetRecipient($recipient,$db)
	{
		$query = "SELECT * FROM dbc_recipient WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT RECIPIENT ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$homopient = $ROW['recipient'];
				$selected = '';
				if($homopient == $recipient)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$homopient.'">'.$homopient.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- Recipient Not Configured ---</option>';
		}
		mysqli_close($db);
	}
	public function GetMRSNumber($db)
	{
		$query = "SELECT * FROM dbc_form_numbering WHERE id=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$numbering = $ROW['mrs_number'];
			}
			return $numbering;
		} else {
			return 'Numbering is not configured';
		}
		mysqli_close($db);
	}
	public function GetTransType($transtype)
	{
        $transaction = array(
	        "Receiving" => "Receiving",
            "Return" => "Return"                        
        );
        foreach ( $transaction as $key => $value )
        {
        	$selected = "";
        	if($value == $transtype)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}

	public function GetDeliverytatust($status)
	{
        $delstatus = array(
	        "Full" => "Full",
            "Partial" => "Partial"                        
        );
        foreach ( $delstatus as $key => $value )
        {
            $return .= '<option value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	public function GetOrderStatust($status)
	{
        $ordstatus = array(
	        "Full" => "Full",
            "Partial" => "Partial"                        
        );
        foreach ( $ordstatus as $key => $value )
        {
            $return .= '<option value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
        mysqli_close($db);
	}
	public function GetSupplierName($supplier_id,$db)
	{
		$query = "SELECT * FROM dbc_supplier WHERE id='$supplier_id'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$name = $ROW['name'];
			}
			return $name;
		} else {
			return 'Supplier not exists';
		}
		mysqli_close($db);
	}
	public function GetItemInfo($column,$itemcode,$db)
	{
		$query = "SELECT * FROM wms_itemlist WHERE item_code='$itemcode'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				return $ROW[$column];
			}
		} else {
			return NULL;
		}
		mysqli_close($db);
	}
	public function GetSupplier($supplier_id,$db)
	{
		$query = "SELECT * FROM dbc_supplier";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT SUPPLIER ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$name = $ROW['name'];
				$value = $ROW['id'];
				$selected = '';
				if($value == $supplier_id)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$value.'">'.$name.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO SUPPLIER ---</option>';
		}
		mysqli_close($db); 
	}
	public function GetUOM($uom,$db)
	{
		$query = "SELECT * FROM dbc_units_measures";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT UOM ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$units = $ROW['unit_name'];
				$selected = '';
				if($units == $uom)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$units.'">'.$units.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO UOM ---</option>';
		}
		mysqli_close($db);
	}
	public function GetItemCategory($cat,$db)
	{
		$query = "SELECT * FROM dbc_item_category WHERE active=1";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">--- SELECT CATEGORY ---</option>';
		    while($ROW = mysqli_fetch_array($results))  
			{
				$category = $ROW['category'];
				$selected = '';
				if($category == $cat)
				{
					$selected = "selected";
				}
				$return .= '<option '.$selected.' value="'.$category.'">'.$category.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- NO CATEGORY ---</option>';
		} 
		mysqli_close($db);
	}
	public function GetRowLimit($showlimit)
	{
        $limit = array(            
            "50" => "50",            
            "100" => "100",
            "200" => "200",
			"250" => "250",
			"500" => "500",
			"All" => ""
        );
        foreach ( $limit as $key => $value )
        {
        	$selected = "";
        	if($value == $showlimit)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
	}
	public function checkPolicy($username,$module,$permission,$user_level,$db)
	{
		if($user_level >= 80)
		{
			return 1;
		} 
		else
		{
			$checkPolicy = "SELECT * FROM tbl_system_permission WHERE username='$username' AND modules='$module' AND $permission=1";
			$pRes = mysqli_query($db, $checkPolicy);    
		    if ( $pRes->num_rows > 0 ) 
		    {
		    	return 1;
			} else {
				return 0;
			}
		}
	}
}