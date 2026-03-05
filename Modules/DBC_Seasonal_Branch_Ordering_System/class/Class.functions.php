<?php
class FDSFunctions
{
	
	public function getPendingToReceive($branch,$db)
	{
		$val = 0;
		$query = "SELECT COUNT(*) AS total FROM dbc_seasonal_order_request_generate_dr WHERE branch='$branch' AND status='In-Transit' AND YEAR(delivery_date) >= 2025";
		$result = mysqli_query($db, $query);
		if($result->num_rows > 0){
			while ($rows = $result->fetch_assoc()){
				$val = $rows['total'];
			}
			return $val;
		}
		return $val;
	}
	
	
	public function getDRFilter($drno,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE dr_number='$drno'";
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

	public function mainorderTable($requestid,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_seasonal_order_request WHERE request_id='$requestid'";
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
	}

	
	
	public function selectDrNo($controlno, $branch, $db)
	{
	    $query = "SELECT dr_number FROM dbc_seasonal_order_request_generate_dr WHERE control_no = ? AND branch = ?";
	    
	    if ($stmt = $db->prepare($query)) {
	        $stmt->bind_param('ss', $controlno, $branch);
	
	        $stmt->execute();
	
	        $result = $stmt->get_result();
	
	        $drNumbers = [];
	        while ($row = $result->fetch_assoc()) {
	            $drNumbers[] = $row['dr_number'];
	        }
	
	        $stmt->close();
	
	        return $drNumbers;
	    } else {
	        return [];
	    }
	}

	
	public function updateMainOrderToClosed($requestid, $db)
	{
	    $query = "UPDATE dbc_seasonal_order_request SET status = 'Closed' WHERE request_id = ?";
	
	    if ($stmt = $db->prepare($query)) {
	        $stmt->bind_param("i", $requestid);
	
	        if ($stmt->execute()) {
	            $stmt->close();
	            return true;
	        } else {
	            error_log("Database Execution Error: " . $stmt->error);
	            $stmt->close(); 
	            return false;
	        }
	    } else {
	        error_log("Database Preparation Error: " . $db->error);
	        return false;
	    }
	}




	public function orderRequestMotherTable($rowid,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_seasonal_order_request WHERE request_id='$rowid'";
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
	}


	public function orderRequestSecondaryTable($requestid,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE id='$requestid'";
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
	}


    public function selectViaControlnoAngBranch($controlno, $branch, $db)
	{
	    $query = "SELECT DISTINCT id FROM dbc_seasonal_order_request_generate_dr WHERE control_no = ? AND branch = ? AND status = 'In-Transit'";
	
	    $stmt = $db->prepare($query);
	    $stmt->bind_param('ss', $controlno, $branch);
	    $stmt->execute();
	    $result = $stmt->get_result();
	
	    $val = 0;
	
	    while ($row = $result->fetch_assoc()) {
	        $requestno = $row['id'];
	
	        if ($this->drnumberDetectComplete($requestno, $db) == 1) {
	            $val++;
	        }
	    }
	
	    return $val;
	}

	public function GetOrderForeign($controlno,$column,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE control_no='$controlno'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$retVal = $ROW[$column];
			}
			return $retVal;
		} 
	}


	public function countPendingTransaction($requestno, $db)
	{
	    $query = "SELECT COUNT(*) AS total_count 
	              FROM dbc_seasonal_branch_mrs_transaction 
	              WHERE request_id = ? AND branch_received_status = 0";
	
	    $stmt = $db->prepare($query);
	    $stmt->bind_param('s', $requestno);
	    $stmt->execute();
	    $result = $stmt->get_result();
	
	    if ($row = $result->fetch_assoc()) {
	        return (int) $row['total_count'];
	    } else {
	        return 0;
	    }
	}


	public function GetOrderStatusSecondTable($rowid,$column,$db)
	{
		$retVal = '';
		$query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE id='$rowid'";
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

	
	public function updateStatusOfOrdersToComplete($appuser, $dateuser, $requestid, $db)
	{
	    $query = "UPDATE dbc_seasonal_order_request_generate_dr 
	              SET order_accepted_by = ?, 
	                  order_delivered = 1, 
	                  order_delivered_date = ?, 
	                  status = 'Closed' 
	              WHERE id = ?";
	    
	    if ($stmt = $db->prepare($query)) {
	        $stmt->bind_param("ssi", $appuser, $dateuser, $requestid);
	        
	        if ($stmt->execute()) {
	            return true;
	        } else {
	            return false;
	        }
	    }
	    return false;
	}	
	
	
	public function sumbranchrecieved($itemcode, $controlno, $db)
	{
	    $query = "SELECT SUM(branch_received) AS total_received 
	              FROM dbc_seasonal_branch_mrs_transaction 
	              WHERE control_no = ? AND item_code = ? AND branch_received_status = 1";
	    
	    if ($stmt = $db->prepare($query)) {
	        $stmt->bind_param("ss", $controlno, $itemcode);
	        $stmt->execute();
	        $result = $stmt->get_result();
	        
	        if ($row = $result->fetch_assoc()) {
	            return $row['total_received'] ?? 0;
	        }
	    }
	    return 0;
	}
	
	public function drnumberDetectComplete($requestno, $db)
	{
	    $query = "SELECT * FROM dbc_seasonal_branch_mrs_transaction WHERE request_id = '$requestno' AND branch_received_status = 0 LIMIT 1";
	    $results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}

	public function GetOrderStatusVer2($requestid,$column,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_order_request_generate_dr WHERE id='$requestid'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$retVal = $ROW[$column];
			}
			return $retVal;
		} 
	}

	
	
	public function getSumQuantityItem($table1, $column, $branch, $controlno, $itemcode, $db)
	{
	    $table = 'dbc_seasonal_' . $table1;
	
	    $queryStatus = "SELECT id FROM dbc_seasonal_order_request_generate_dr WHERE (status = 'In-Transit' OR status='Closed') AND branch = ? AND control_no = ?";
	    $stmtStatus = $db->prepare($queryStatus);
	    if ($stmtStatus === false) {
	        echo '
	            <script>
	                swal("Query Preparation Error (Status Check):", "' . $db->error . '", "error");
	            </script>
	        ';
	        return 0;
	    }
	
	    $stmtStatus->bind_param("ss", $branch, $controlno);
	    $stmtStatus->execute();
	    $resultStatus = $stmtStatus->get_result();
	
	    if ($resultStatus->num_rows === 0) {
	        $stmtStatus->close();
	        return 0;
	    }
	
	    $totalQuantity = 0;
	    while ($rowStatus = $resultStatus->fetch_assoc()) {
	        $request_id = $rowStatus['id'];
	
	        $query = "SELECT SUM($column) AS TOTAL FROM $table WHERE branch = ? AND control_no = ? AND item_code = ? AND request_id = ?";
	        $stmt = $db->prepare($query);
	        if ($stmt === false) {
	            echo '
	                <script>
	                    swal("Query Preparation Error (Sum Check):", "' . $db->error . '", "error");
	                </script>
	            ';
	            $stmtStatus->close();
	            return 0;
	        }
	
	        $stmt->bind_param("sssi", $branch, $controlno, $itemcode, $request_id);
	        $stmt->execute();
	        $result = $stmt->get_result();
	
	        if ($result->num_rows > 0) {
	            $row = $result->fetch_assoc();
	            $totalQuantity += $row['TOTAL'] ?? 0;
	        }
	
	        $stmt->close();
	    }
	
	    $stmtStatus->close();
	
	    return $totalQuantity;
	}

	
	
	

	public function countPartialDelivery($controlno,$branch,$db)
	{
		$QUERY = "SELECT COUNT(*) AS status_count FROM dbc_seasonal_order_request_generate_dr WHERE control_no='$controlno' AND branch = '$branch' AND status = 'In-Transit'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ($RESULTS) {
	   		$row = mysqli_fetch_assoc($RESULTS);
			return $row['status_count'];
		} else {
			return 0;
		}
	}

	public function branchorderopengetmodulevalue($module,$branch,$controlno,$itemcode,$db)
	{
		
	    
	    $allowedColumns = ['quantity', 'inv_ending'];
	    if (!in_array($module, $allowedColumns)) {
	        return false;
	    }
	    
	    $query = "SELECT `$module` FROM dbc_seasonal_branch_order WHERE branch = ? AND control_no = ? AND item_code = ?";

	    if ($stmt = $db->prepare($query)) {
	        
	        $stmt->bind_param("sss", $branch, $controlno, $itemcode);
	        
	        $stmt->execute();
	        
	        $result = $stmt->get_result();
	        
	        $data = $result->fetch_assoc();
	        
	        $stmt->close();
	        
	        return $data[$module] ?? null;
	    } else {

	        return false;
	    }	    
	    
	    
	}



	public function dateLockChecking($transdate,$db)
	{
		$QUERY = "SELECT * FROM dbc_seasonal_datelock_checker WHERE report_date='$transdate' AND status='1'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}
	
	public function branchOrderNULLCheking($controlno,$db)
	{
		$QUERY = "SELECT * FROM dbc_seasonal_branch_mrs_transaction WHERE control_no='$controlno' AND (branch_received IS NULL OR branch_received = '')";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}

	public function getSubmittedOrder($branch,$db)
	{
		$QUERY = "SELECT COUNT(*) AS status_count FROM dbc_seasonal_order_request WHERE branch = '$branch' AND status = 'Submitted'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ($RESULTS) {
	   		$row = mysqli_fetch_assoc($RESULTS);
			return $row['status_count'];
		} else {
			return 0;
		}
	}
	public function getForApproval($branch,$db)
	{
		$QUERY = "SELECT COUNT(*) AS status_count FROM dbc_seasonal_order_request WHERE branch = '$branch' AND status = 'Approval'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ($RESULTS) {
	   		$row = mysqli_fetch_assoc($RESULTS);
			return $row['status_count'];
		} else {
			return 0;
		}
	}
	public function getOpenOrder($branch,$db)
	{
		$QUERY = "SELECT COUNT(*) AS status_count FROM dbc_seasonal_order_request WHERE branch = '$branch' AND status = 'In-Transit' AND delivery_date < CURDATE()";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ($RESULTS) {
	   		$row = mysqli_fetch_assoc($RESULTS);
			return $row['status_count'];
		} else {
			return 0;
		}
	}
	public function getOrderCreator($creator,$control_no,$db)
	{
		$QUERY = "SELECT * FROM dbc_seasonal_order_request WHERE control_no='$control_no' AND created_by='$creator'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
	}
	public function GetItemStock($itemcode,$db)
	{
		$QUERY = "SELECT * FROM dbc_seasonal_inventory_stock WHERE item_code='$itemcode'";
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
	public function LoadBranch($cluster,$db)
	{
		$QUERY = "SELECT * FROM tbl_branch WHERE location='$cluster'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			$return = '<option value=""> --- SELECT BRANCH --- </option>';
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				$branch = $ROW['branch'];
				$return .= '<option value="'.$branch.'">'.$branch.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- No Branch ---</option>';
		}
	}
	public function GetOrderStatus($control_no,$column,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_order_request WHERE control_no='$control_no'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$retVal = $ROW[$column];
			}
			return $retVal;
		} 
	}

	public function GetChecked($control_no,$column,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_order_request WHERE control_no='$control_no'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$retVal = $ROW[$column];
			}
			return $retVal;
		}
	}
	public function GetControlNo($branch,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_order_request WHERE branch='$branch'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = "";
		    while($ROW = mysqli_fetch_array($results))  
			{
				$controlno = $ROW['control_no'];
				$form_type = $ROW['form_type'];
				$return .= '<option value="'.$controlno.'">'.$controlno.' - '.$form_type.'</option>';
			}
			return $return;
		} else {
			return '<option value="">--- Recipient Not Configured ---</option>';
		}
	}
	public function GetRequestType($form_type)
	{
		$rt = array(
	        "MRS" => "MRS",
            "POF" => "POF"
        );
        $return = "";
        foreach ( $rt as $key => $value )
        {
        	$selected = "";
        	if($value == $form_type)
        	{
        		$selected = "selected";
        	}
            $return .= '<option '.$selected.' value="'.$value.'">'.$key.'</option>';
        }
        return $return;
	}
	public function getOrderRemarks($mrs_no,$db)
	{
		$QUERY = "SELECT * FROM dbc_seasonal_branch_order_remarks WHERE control_no='$mrs_no'";
		$RESULTS = mysqli_query($db, $QUERY);    
		if ( $RESULTS->num_rows > 0 ) 
		{
			while($ROW = mysqli_fetch_array($RESULTS))  
			{
				return $ROW['remarks'];
			}
		} else {
			return "-|-";
		}
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
	}
	public function GetRequestStatus($status)
	{
		$stats = array(
	        "Open" => "Open",
            "Closed" => "Closed",
            "Cancel" => "Cancel"
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
	}
	public function LoadRecipient($form_type,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_recipient WHERE form_type='$form_type'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
//			$return = '<option value="">--- SELECT RECIPIENT ---</option>';
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
	}	
	public function GetRecipient($recipient,$form_type,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_recipient WHERE form_type='$form_type'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
//			$return = '<option value="">--- SELECT RECIPIENT ---</option>';
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
	}
	public function GetMRSNumber($db)
	{
		$query = "SELECT * FROM dbc_seasonal_form_numbering WHERE id=1";
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
	}
	public function GetOrderStatust($status)
	{
        $ordstatus = array(
	        "Open" => "Open",
            "Closed" => "Closed"                        
        );
        foreach ( $ordstatus as $key => $value )
        {
            $return .= '<option value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
	}
	public function GetSupplierName($supplier_id,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_supplier WHERE id='$supplier_id'";
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
	}
	public function GetSupplier($supplier_id,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_supplier";
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
	}
	public function GetUOM($uom,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_units_measures";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$return = '<option value="">- UOM -</option>';
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
	}
	public function GetItemCategory($cat,$db)
	{
		$query = "SELECT * FROM dbc_seasonal_item_category WHERE active=1";
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
	}
	public function GetRowLimit()
	{
        $limit = array(            
            "50" => "50",            
            "100" => "100",
			"250" => "250",
			"500" => "500",
			"All" => ""
        );
        foreach ( $limit as $key => $value )
        {
            $return .= '<option value="'.$value.'">'.$key.'</option>';                        
        }
        return $return;
	}
}