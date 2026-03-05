<?php
class DBCInventory
{

	public function GetBranchOrderReceiving($branch,$datefrom,$dateto,$itemcode,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND trans_date BETWEEN '$datefrom' AND '$dateto' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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



	public function GetInventoryOtherDataRange($table1, $module, $datefrom, $dateto, $itemcode, $db)
    {
    	$table = 'dbc_'.$table1;
        $query = "SELECT SUM($module) AS TOTAL FROM $table WHERE report_date BETWEEN '$datefrom' AND '$dateto' AND item_code='$itemcode' AND status=0";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = 0;
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW['TOTAL'];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }


	public function GetBranchReceivedDataRange($branch,$datefrom,$dateto,$itemcode,$db)
	{
		
		$QUERY = "SELECT SUM(branch_received) AS QUANTITY FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND delivery_date BETWEEN '$datefrom' AND '$dateto' AND branch_received_status='1' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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

	public function GetTransferOutDataVsbrchreceivingRange($branch,$datefrom,$dateto,$itemcode,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND delivery_date BETWEEN '$datefrom' AND '$dateto' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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

	public function GetInventoryProductionRange($datefrom, $dateto, $itemcode, $db)
    {
    	
        $query = "SELECT SUM(pcount) AS TOTAL FROM dbc_fgts_pcount WHERE trans_date BETWEEN '$datefrom' AND '$dateto' AND item_code='$itemcode'";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = 0;
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW['TOTAL'];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }


	public function GetInventoryInVS($transdate, $itemcode, $db)
    {
    	
        $query = "SELECT SUM(pcount) AS TOTAL FROM dbc_fgts_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = 0;
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW['TOTAL'];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }


	public function GetBranchReceivedData($branch,$itemcode,$transdate,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND delivery_date='$transdate'  AND branch_received_status='1' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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

	public function GetTransferOutDataVsbrchreceiving($branch,$itemcode,$transdate,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND delivery_date='$transdate' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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


	public function GetInventoryOtherData($table1,$module,$transdate, $itemcode, $db)
    {
    	$table = 'dbc_'.$table1;
        $query = "SELECT SUM($module) AS TOTAL FROM $table WHERE report_date='$transdate' AND item_code='$itemcode' AND status=0";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = 0;
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW['TOTAL'];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }

	
	public function GetdatafromtableTransferOutData($column, $transdate, $itemcode, $db)
	{
		
		$QUERY = "SELECT * FROM dbc_branch_order WHERE item_code='$itemcode' AND delivery_date='$transdate' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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
	
	public function Getdatafromtableproduction($column, $transdate, $itemcode, $db)
    {
    	
        $query = "SELECT * FROM dbc_dbc_production WHERE report_date='$transdate' AND item_code='$itemcode' AND status='Yes'";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = '';
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW[$column];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }

	public function GetPcountDataInventory($column,$itemcode,$datepcount,$db)
	{
		$QUERY = "SELECT * FROM dbc_inventory_pcount WHERE trans_date = '$datepcount' AND item_code='$itemcode'";
		$RESULTS = mysqli_query($db, $QUERY);
		$val = 0;
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
	
	public function getInventoryBeginning_new($itemcode, $month, $year, $db)
	{				

	    $previousMonth = $month - 1;
	    $previousYear = $year;
	    if ($previousMonth == 0) {
	        $previousMonth = 12;
	        $previousYear = $year - 1;
	    }
	    $previousMonthEndDate = date("Y-m-t", strtotime($previousYear . "-" . $previousMonth . "-01"));
	    
	    if ($month == 1) {
	        $previousYear = $year - 1;
	        $previousMonthEndDate = date("Y-m-t", strtotime($previousYear . "-12-01"));
	    }
	    
	    $query = "SELECT * FROM dbc_inventory_pcount WHERE trans_date = '$previousMonthEndDate' AND item_code='$itemcode'";
	    $results = mysqli_query($db, $query);  
	    
	    if ($results->num_rows > 0) {
	        $total = 0;
	        while ($ROW = mysqli_fetch_array($results)) {
	            $total += $ROW['p_count'];	
	        }
	        return $total;
	    } else {
	        return 0;
	    }
	    mysqli_close($db);
	}
	public function GetTransferOutData($itemcode,$transdate,$db)
	{
		
		$QUERY = "SELECT SUM(actual_quantity) AS QUANTITY FROM dbc_branch_order WHERE item_code='$itemcode' AND delivery_date='$transdate' AND control_no IN (SELECT control_no FROM dbc_order_request WHERE logistics = '1')";
				
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

	public function GetInventoryIn($transdate, $itemcode, $db)
    {
    	
        $query = "SELECT SUM(pcount) AS TOTAL FROM dbc_fgts_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
        $results = mysqli_query($db, $query);
        if ($results === false) {
            return $query;
        }

        $val = 0;
        if ($results->num_rows > 0) {
            while ($ROW = mysqli_fetch_array($results)) {
                $val = $ROW['TOTAL'];
            }
            return $val;
        } else {
            return $val;
        }

        mysqli_close($db);
    }



	public function GetUndoStatus($trans_date,$itemcode,$db)
	{
		$query = "SELECT * FROM dbc_inventory_stock WHERE item_code='$itemcode' AND stock_before_pcount_date='$trans_date'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			return 1;
		} else {
			return 0;
		}
		mysqli_close($db);
	}

	public function GetPcountData($itemcode,$trans_date,$column,$db)
	{
		$query = "SELECT * FROM dbc_inventory_pcount WHERE item_code='$itemcode' AND trans_date='$trans_date'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
                $col = $ROW[$column];
			}
			return $col;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function GetExpirationDate($itemcode,$db)
	{
		$query = "SELECT * FROM dbc_inventory_pcount WHERE item_code='$itemcode' AND trans_date='$transdate'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
                $qty = $ROW['p_count'];
			}
			return $qty;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function GetPcount($itemcode,$transdate,$db)
	{
		$query = "SELECT * FROM dbc_inventory_pcount WHERE item_code='$itemcode' AND trans_date='$transdate'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
                $qty = $ROW['p_count'];
			}
			return $qty;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function GetMonthlyPcount($cnt_start,$cnt_end,$days_cnt,$itemcode,$month,$year,$db)
	{
		$startDate = $year."-".$month."-".$cnt_start;
		$endDate = $year."-".$month."-".$cnt_end;
		$query = "SELECT * FROM dbc_inventory_pcount WHERE trans_date BETWEEN '$startDate' AND '$endDate' AND item_code='$itemcode'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$total=0;
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total = $ROW['p_count'];	
			}
			return $total;
		} else {
			return 0;
		}

	}
	public function GetWeeklyPcount($cnt_start,$cnt_end,$days_cnt,$itemcode,$month,$year,$db)
	{
		$startDate = $year."-".$month."-".$cnt_start;
		$endDate = $year."-".$month."-".$cnt_end;
						
		$query = "SELECT * FROM dbc_inventory_pcount WHERE trans_date BETWEEN '$startDate' AND '$endDate' AND item_code='$itemcode'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$total=0;
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total += $ROW['p_count'];	
			}
			return $total;
		} else {
			return 0;
		}

	}
	public function getMonthlyIn($week,$itemcode,$month,$year,$db)
	{
		if($week == 1)
		{
			$cnt_start = 1;
			$cnt_end = 7;
		}
		if($week == 2)
		{
			$cnt_start = 8;
			$cnt_end = 14;
		}
		if($week == 3)
		{
			$cnt_start = 15;
			$cnt_end = 21;
		}
		if($week == 4)
		{
			$cnt_start = 22;
			$cnt_end = 28;
		}
		if($week == 5)
		{
			$cnt_start = 29;
			$cnt_end = 31;
		}		
		$startDate = $year."-".$month."-".$cnt_start;
		$endDate = $year."-".$month."-".$cnt_end;		
		 $query = "SELECT * FROM dbc_receiving receiving
			INNER JOIN dbc_receiving_details details
			ON receiving.receiving_id = details.receiving_id
			WHERE details.received_date BETWEEN '$startDate' AND '$endDate' 
			AND details.item_code='$itemcode' 
			AND receiving.status='Closed';
		";
		
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$total=0;
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total += $ROW['quantity_received'];	
			}
			return $total;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function getWeeklyIn($cnt_start,$cnt_end,$days_cnt,$itemcode,$month,$year,$db)
	{
		
		$startDate = $year."-".$month."-".$cnt_start;
		$endDate = $year."-".$month."-".$cnt_end;
		
		$query = "SELECT * FROM dbc_receiving_details WHERE received_date BETWEEN '$startDate' AND '$endDate' AND item_code='$itemcode'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
			$total=0;
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total += $ROW['quantity_received'];	
			}
			return $total;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function GetInventoryOut($branch,$itemcode,$year,$month,$day,$db)
	{
		$trans_date = $year."-".$month."-".$day;
		$query = "SELECT actual_quantity FROM dbc_branch_order WHERE branch='$branch' AND item_code='$itemcode' AND trans_date='$trans_date'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
                $qty = $ROW['actual_quantity'];
			}
			return $qty;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function getUpdateEnding($itemcode,$ending,$month,$year,$db)
	{
	    $queryDataUpdate = "UPDATE dbc_inventory_records SET ending=? WHERE item_code=? AND year=? AND month=?";
	    $stmt = $db->prepare($queryDataUpdate);
	    $stmt->bind_param("ssss", $ending, $itemcode, $year, $month);	   
	 	
	    if ($stmt->execute()) {
	    } else {
	        return $stmt->error;
	    }
	    
	    $stmt->close();
	}
	public function getUpdateBeginning($itemcode,$beginning,$month,$year,$db)
	{
	    $queryDataUpdate = "UPDATE dbc_inventory_records SET beginning=? WHERE item_code=? AND year=? AND month=?";
	    $stmt = $db->prepare($queryDataUpdate);
	    $stmt->bind_param("ssss", $beginning, $itemcode, $year, $month);	   
	 	
	    if ($stmt->execute()) {
	    } else {
	        echo $stmt->error;
	    }
	    
	    $stmt->close();
	}
	public function getDailyBeginning($itemcode,$month,$year,$day,$db)
	{
		$Walang_Ka_Date = $year."-".$month."-".$day;
		$date = new DateTime($Walang_Ka_Date);
		$date->modify('-1 days');
		$Year = $date->format('Y');
		$Month = $date->format('m');
		$Day = $date->format('d');
		$trans_date = $Year."-".$Month."-".$Day;
		$str_day = "day_".$Day;
		
		$query = "SELECT * FROM dbc_inventory_pcount WHERE item_code='$itemcode' AND trans_date='$trans_date'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
                $pcount = $ROW['p_count'];
			}
			return $pcount;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function getInventoryBeginning($cnt_start,$cnt_end,$days_cnt,$col,$itemcode,$month,$year,$db)
	{
		$Walang_Ka_Date = $year."-".$month;
		$date = new DateTime($Walang_Ka_Date);
		$date->modify('-1 month');
		$Year = $date->format('Y');
		$Month = $date->format('m');
		$startDate = $year."-".$month."-".$cnt_start;
		$endDate = $year."-".$month."-".$cnt_end;
		
		$startDateTime = new DateTime($startDate);
		$endDateTime = new DateTime($endDate);

		// Calculate the number of days in the last week
		$lastWeekDays = $endDateTime->format('N');		
		// Subtract one week from the start date
		$startDateTime->sub(new DateInterval('P1W'));		
		// Subtract one week from the end date, accounting for the number of days in the last week
		$endDateTime->sub(new DateInterval('P1W' . $lastWeekDays . 'D'));		
		// Format the updated dates as strings
		$newStartDate = $startDateTime->format('Y-m-d');
		$newEndDate = $endDateTime->format('Y-m-d');;
				
		$query = "SELECT * FROM dbc_inventory_pcount WHERE trans_date BETWEEN '$newStartDate' AND '$newEndDate' AND item_code='$itemcode'";
		$results = mysqli_query($db, $query);  
		  
		if ( $results->num_rows > 0 ) 
		{
			$total=0;
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total = $ROW['p_count'];	
			}
			return $total;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function getMonthlyBeginning($itemcode,$month,$year,$db)
	{
		$Walang_Ka_Date = $year."-".$month;
		$date = new DateTime($Walang_Ka_Date);
		$date->modify('-1 month');
		$Year = $date->format('Y');
		$Month = $date->format('m');

		$query = "SELECT * FROM dbc_inventory_records WHERE item_code='$itemcode' AND month='$Month' AND year='$Year'";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$total=0;
	            for ($x = 1; $x <= 31; $x++)
	            {
	                $day = $ROW['day_' . $x];
	                $total += $day;
	            }
			}
			return $total;
		} else {
			return 0;
		}
		mysqli_close($db);
	}
	public function removeStringFromArray($arr, $stringToRemove)
	{
	    return array_filter($arr, function ($item) use ($stringToRemove) {
	        return $item !== $stringToRemove;
	    });
	}
	public function getColumns($tableName,$db)
	{
		$sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
		$stmt = mysqli_prepare($db, $sql);
		if ($stmt)
		{
		    $databaseName = 'documents_data';
		    mysqli_stmt_bind_param($stmt, 'ss', $databaseName, $tableName);
		
		    mysqli_stmt_execute($stmt);
		    mysqli_stmt_bind_result($stmt, $columnName);
		    $columns = array();
		    while (mysqli_stmt_fetch($stmt)) {
		    	if($tableName == 'dbc_inventory_records')
		    	{
			    	if($columnName != 'wid_id' && $columnName != 'supplier_id')
			    	{
			        	$columns[] = $columnName;
			        }
			    } else {
				    $columns[] = $columnName;
			    }
		    }
		    return $columns;
		} else {
		    echo "Error preparing the statement: " . mysqli_error($db);
		}
		mysqli_close($db);
	}
	public function getInventorySelection($inventory,$db)
	{
		$stats = array(
	        "Inventory Status" => "Inventory Status",
            "Inventory" => "Closed",
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
	public function GetLeadTime($lead_time,$db)
	{
		$query = "SELECT * FROM dbc_inventory_leadtime";
		$results = mysqli_query($db, $query);    
		if ( $results->num_rows > 0 ) 
		{
		    while($ROW = mysqli_fetch_array($results))  
			{
				$name = $ROW[$lead_time];
			}
			return $name;
		} else {
			return 0;
		} 
		mysqli_close($db);
	}
	public function GetDailyInventory($kwiri,$eVal,$days_count,$min_leadtime,$max_leadtime,$db)
	{
		$sqlQueryRecords = "SELECT * FROM dbc_inventory_records";
		$invResults = mysqli_query($db, $sqlQueryRecords);    
	    if ( $invResults->num_rows > 0 ) 
	    {	    	
	    	while($INVENTORYROW = mysqli_fetch_array($invResults))  
			{
		    	$max_average = array();$average=0;$rol=0;
				for($x = 1; $x <= $days_count; $x++)
				{
					if($INVENTORYROW['day_'.$x] > 0)
					{
						$average += $INVENTORYROW['day_'.$x];
						$max = $INVENTORYROW['day_'.$x];					
					}
					$max_average[] = $max;
									$average_dr = round($average / $days_count);			
				$max_dr = max($max_average);			
				$safety_stocks = $max_dr * $max_leadtime - $average_dr * $min_leadtime;			
				$rol = $average_dr * $min_leadtime + $safety_stocks;

				}				
			}				
	    } else {
	    	$rol = 0;
	    }
	    if($eVal == 'rol')
	    {
	    	return $rol;
	    }
	    mysqli_close($db);
	}
}
