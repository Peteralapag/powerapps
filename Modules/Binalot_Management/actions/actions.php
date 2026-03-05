<?php
ini_set('display_errors', 0);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require $_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php";
$function = new BINALOTFunctions;

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
if(isset($_SESSION['binalot_appnameuser']))
{
	$app_user = strtolower($_SESSION['binalot_appnameuser']);
	$app_user = ucwords($app_user);
}
$date = date("Y-m-d");
$date_time = date("Y-m-d H:i:s");



if ($mode == 'saveChargesEmployee') {

	
    $chargecodeno = $_POST['chargecodeno'];
    $dateselected = $_POST['dateselected'];
    $idcode = $_POST['idcode'];
    $employeename = $_POST['employeename'];
    $employeetype = 'NONE BAKER';
    $encodedby = $_POST['encodedby'];
    $category = $_POST['category'];
    $itemname = $_POST['itemname'];
    $itemcode = $_POST['itemcode'];
    $unitprice = (float)$_POST['unitprice'];
    $quantity = (float)$_POST['quantity'];
    $total = (float)$_POST['total'];
    
    $remainingqty = (float)$_POST['remainingqty'];
    $remainingtotal = (float)$_POST['remainingtotal'];
    
    $quantity_existing = (float)$function->countDetectEmployeeChargedBAKERONLY2('quantity', $chargecodeno, $db) + $quantity;
    $total_existing = (float)$function->countDetectEmployeeChargedBAKERONLY2('total', $chargecodeno, $db) + $total;
    
    $original_unitprice= (float)$_POST['original_unitprice'];
    $original_quantity = (float)$_POST['original_quantity'];
    $original_total = (float)$_POST['original_total'];
    
    $bakerEmployeeCount = (float)$function->countDetectEmployeeChargedBAKERONLY($chargecodeno, $db);
    
    $quantityAffectedBaker = (float)($remainingqty - $quantity) / $bakerEmployeeCount;
    $totalAffectedBaker = (float)($remainingtotal - $total) / $bakerEmployeeCount;
    
    
    
    echo '<script>console.log("qty ====Orig quantity: '.$remainingqty.' - unatity existing: '.$quantity.' devide '.$bakerEmployeeCount.'\n")</script>';
    
    

    // Check if the employee already exists with the same chargecodeno and report_date
    $check_stmt = $db->prepare("SELECT COUNT(*) FROM binalot_charges_extension_record WHERE idcode = ? AND chargecodeno = ? AND report_date = ?");
    $check_stmt->bind_param("sss", $idcode, $chargecodeno, $dateselected);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
       
        echo '<script>
        		app_alert("System Message", "This employee charge already exists for the selected charge code and date.", "warning");
        	</script>';
        exit();
    }


    $stmt = $db->prepare("INSERT INTO binalot_charges_extension_record 
        (chargecodeno, report_date, idcode, employee_name, employee_type, item_description, category, item_code, quantity, unit_price, total, created_date, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("ssssssssdddss", 
        $chargecodeno, $dateselected, $idcode, $employeename, $employeetype, $itemname, $category, $itemcode, 
        $quantity, $unitprice, $total, $date_time, $encodedby);

    if ($stmt->execute()) {
      
        $function->updateChargesByChargedBAKERONLY($chargecodeno, $quantityAffectedBaker, $totalAffectedBaker, $db);
        
        echo '
        	<script>
        		var chargecodeno = "'.$chargecodeno.'";
        		var item = "'.$itemname.'";
        		var qty = "'.$quantity.'";
        		var unitprice = "'.$unitprice.'";
        		var total = "'.$total.'";
        		var category = "'.$category.'";
        		var itemcode = "'.$itemcode.'";
        		var transdate = "'.$dateselected.'";
        		var status = 0;
        		
        		var employeename = "'.$employeename.'";
        		app_alert("System Message", employeename+" charge successfully saved", "success");
        		
        		viewemployeecharges(chargecodeno,item,qty,unitprice,total,category,itemcode,transdate);
        		
        		viewemployeecharges(chargecodeno,item,qty,unitprice,total,category,itemcode,transdate,status)
        		
        	</script>
        ';
        
        
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}



if($mode == 'getStockInHand'){
	
	$itemCode = $_POST['itemcode'];
	echo $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
}

if ($mode == 'deletethisemployeechargeextension') {
    $chargecodeno = $_POST['chargecode'];

    $sql = "DELETE FROM binalot_charges_extension_record WHERE chargecodeno = ?";

    $stmt = $db->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $chargecodeno);

        if ($stmt->execute()) {

            echo $employees = $function->getChargesPeaple('employee_name', $chargecodeno, $db);

        } else {
            echo "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $db->error;
    }
}


if ($mode == 'saveChargesInvolved') {

    $user = $app_user;
    $chargecode = $_POST['chargecode'];
    $reportDate = $_POST['reportDate'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];
    $quantity = $_POST['quantity'];
    $unitprice = $_POST['unitprice'];
    $remarks = $_POST['remarks'];
    $total = $quantity * $unitprice;

    $queryCheckExist = "SELECT COUNT(*) FROM binalot_charges WHERE chargecodeno = ?";
    $stmtCheck = $db->prepare($queryCheckExist);
    $stmtCheck->bind_param("s", $chargecode);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
	
	if($quantity > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}


    if ($count > 0) {
        echo '<script>
            app_alert("System Message", "Charge code already exists. Record not saved.", "warning");
        </script>';
    } else {
        $queryDataInsert = "INSERT INTO binalot_charges (chargecodeno, report_date, category, item_description, item_code, quantity, unit_price, total, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($queryDataInsert);
        $stmt->bind_param("sssssdddsss", $chargecode, $reportDate, $category, $itemDescription, $itemCode, $quantity, $unitprice, $total, $date_time, $user, $remarks);
        if ($stmt->execute()) {
            
            $function->otherinventoryoutdata($itemCode, $quantity, $date_time, $encodedBy, $db);   
            
            echo '<script>
                	Check_Permissions("p_view", openMenuGranted, "charges", "Charges");
                	app_alert("System Message", "Successfully Item Saved", "success");
                	location.reload();
            	</script>
            ';
            
        } else {
            echo "Error inserting record: " . $db->error;
        }
        $log_msg = 'Add Charges | '.$itemDescription.' | '.$quantity.' QUANTITY';
        $function->DoAuditLogs($date_time, $log_msg, $app_user, $db);
    }
}





if($mode == 'getItemCodeNewModulesCharges'){
	$itemname = $_POST['itemname'];
    
    $query ="SELECT * FROM binalot_itemlist WHERE item_description=?";  
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $itemname);
    $stmt->execute();
    $result = $stmt->get_result();  
    
    if($ROWS = $result->fetch_assoc())  
    {
        $item = $ROWS['item_description'];
        $category_name = $ROWS['category'];
        $item_id = $ROWS['item_code'];
        $unitprice = $ROWS['unit_price'];
        echo '
            <script>
                var c = "'.$category_name.'";
                var i = "'.$item_id.'";
                var u = "'.$unitprice.'";
                $("#category").val(c);
                $("#itemcode").val(i);
                $("#unitprice").val(u);
                
            </script>';
    }
    else {

    }
    $stmt->close();
}


if($mode == 'addthisemployeechargeextension'){

    $dateselected = $_POST['dateselected'];
    $chargecodeno = $_POST['chargecode'];
    $idcode = $_POST['idcode'];
    $employeename = $_POST['employeename'];
    $employeetype = 'BAKER';
    $encodedby = $_POST['encodedby'];
    $category = $_POST['category'];
    $itemdescription = $_POST['itemdescription'];
    $itemcode = $_POST['itemcode'];
    $quantity1 = (float)$_POST['quantity'];
    $unitprice = (float)$_POST['unitprice'];
    $remarks = $_POST['remarks'];
    
    $countbakers = (float)$function->countDetectEmployeeCharged($chargecodeno, $db) + 1;
    
    $quantity = $quantity1 / $countbakers;
    
    $total = $unitprice * $quantity;
    
    

    $check_sql = "SELECT COUNT(*) as count FROM binalot_charges_extension_record WHERE chargecodeno = ? AND idcode = ?";
    $check_stmt = $db->prepare($check_sql);
    
    if ($check_stmt) {
        $check_stmt->bind_param("ss", $chargecodeno, $idcode);
        $check_stmt->execute();
        $check_stmt->bind_result($count);
        $check_stmt->fetch();
        $check_stmt->close();

        if ($count == 0) {
            $sql = "INSERT INTO binalot_charges_extension_record (chargecodeno, report_date, idcode, employee_name, employee_type, created_by, category, item_description, item_code, unit_price, remarks) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $db->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sssssssssds", $chargecodeno, $dateselected, $idcode, $employeename, $employeetype, $encodedby, $category, $itemdescription, $itemcode, $unitprice, $remarks);

                if ($stmt->execute()) {
                    // echo "Record successfully added!";
                    $function->updateChargesByChargeCode($chargecodeno, $quantity, $total, $db);
                    echo '
                    	<script>
                    		$("#employeename").val("");
                    		$("#idcode").val("");
                    		app_alert("System Message", "'.$employeename.' successfully added", "success");
                    	</script>
                    ';
                    
                } else {
                    // echo "Error: " . $stmt->error;
                }

                $stmt->close();
            } else {
                // echo "Error: " . $db->error;
            }
        } else {
            // echo "Record already exists!";
            echo '<script>
        		app_alert("System Message", "Employee Name already exists", "warning");
        	</script>';

        }
        
    } else {
        // echo "Error: " . $db->error;
    }
    
    echo $employees = $function->getChargesPeaple('employee_name',$chargecodeno,$db);
}


if($mode == 'voidthisbranchorder')
{
	$app_user = ucwords($app_user);
	$control_no = $_POST['controlno'];
	$module = $_POST['module'];
	$reasons = $_POST['reasons'];
	
	$queryDataUpdate = "UPDATE binalot_order_request SET status='Void', order_remarks='$reasons', void_by='$app_user' WHERE control_no='$control_no' AND order_received=0";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		$log_msg = $module.' | Void Order | CN'.$control_no;
		print_r('
			<script>
				swal("Success", "Order has been successfully Void", "success");
				orderProcess("'.$control_no.'");
			</script>
		');		
		echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);	
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}	
}


if($mode == 'voidthisretouch'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	$retouchquantity = $function->Getfromthetable('quantity','binalot_retouch',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_retouch',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }
/*	
	if($retouchquantity > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process void item", "warning");
    	</script>';

		exit();
	}
*/	
	$QUERY = "UPDATE binalot_retouch SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
//    	$function->voidRetouchData($rowid,$itemcode,$date_time,$app_user,$db);
    	
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"retouch","Create Retouch");
    			app_alert("System Message", "Item retouch void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
	
	$log_msg = 'Void Retouch | '.$itemdescription.' | '.$retouchquantity.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);


}



if($mode == 'saveRetouch'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;    
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }

    
    $queryDataInsert = "INSERT INTO binalot_retouch (report_date, branch, category, item_description, item_code, quantity, created_date, created_by)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user);
    if ($stmt->execute()) {
    
//    	$function->insertReturnData($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"retouch","Create Retouch");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Retouch | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}



if($mode == 'addThisbranchtoactive'){
	
	$branch = $_POST['branch'];
	$branchid = $_POST['branchid'];
	$branchvalue = $_POST['branchvalue'];
	
	
	$function->insertIntoDbcTblBranchTable($branch, $branchid, $branchvalue, $db);
}

if($mode == 'acctnameCkecking'){

	$employeename = $_POST['employeename'];
	$idcode = $function->getIdcodeViaEmployeeName($employeename,$db);
	echo '<script>$("#idcode").val("'.$idcode.'");</script>';
}

if($mode == 'voidthisrnd'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	
	$quantity = $function->Getfromthetable('quantity','binalot_rnd',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_rnd',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }

	$QUERY = "UPDATE binalot_rnd SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidotherdata('rnd',$rowid,$itemcode,$date_time,$app_user,$db);
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"rnd","RND");
    			app_alert("System Message", "Item R & D void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
}

if($mode == 'saveRnd'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;
    
    $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/	
	if($actualYield > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}

    
    $queryDataInsert = "INSERT INTO binalot_rnd (report_date, branch, category, item_description, item_code, quantity, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdsss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    	
    	$function->otherinventoryoutdata($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"rnd","Create R & D");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Add R & D | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}

if($mode == 'voidthischarges'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	
	$quantity = $function->Getfromthetable('quantity','binalot_charges',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_charges',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }

	$QUERY = "UPDATE binalot_charges SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidotherdata('charges',$rowid,$itemcode,$date_time,$app_user,$db);
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"charges","Charges");
    			app_alert("System Message", "Item Charges void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
}

if($mode == 'saveCharges'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $idcode = $_POST['idcode'];
    $employeename = $_POST['employeename'];
    
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;
    
    $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
    

	$unitprice = $function->customemoduletablegetvalue('unit_price','binalot_itemlist','item_code',$itemCode,$db);
	
	$total = $actualYield * $unitprice;

	
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/	
	if($actualYield > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}

    
    $queryDataInsert = "INSERT INTO binalot_charges (report_date, idcode, employee_name, category, item_description, item_code, quantity, unit_price, total, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("ssssssdddsss", $reportDate, $idcode, $employeename, $category, $itemDescription, $itemCode, $actualYield, $unitprice, $total, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    	
    	$function->otherinventoryoutdata($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"charges","Charges");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Add Charges | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}

if($mode == 'voidthisbadorder'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	
	$quantity = $function->Getfromthetable('quantity','binalot_badorder',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_badorder',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);

	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }
	
	$QUERY = "UPDATE binalot_badorder SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidotherdata('badorder',$rowid,$itemcode,$date_time,$app_user,$db);
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"badorder","Badorder");
    			app_alert("System Message", "Item Bad Order void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
}

if($mode == 'saveBadorder'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;
    
    $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/    
    if($actualYield > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}

    
    $queryDataInsert = "INSERT INTO binalot_badorder (report_date, branch, category, item_description, item_code, quantity, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdsss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    	
    	$function->otherinventoryoutdata($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"badorder","Create Bad Order");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Add Bad order | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}

if($mode == 'voidthiscomplementary'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	
	$quantity = $function->Getfromthetable('quantity','binalot_complementary',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_complementary',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);

	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }
	
	$QUERY = "UPDATE binalot_complementary SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidotherdata('complementary',$rowid,$itemcode,$date_time,$app_user,$db);
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"complementary","Complementary");
    			app_alert("System Message", "Item Complementary void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
}


if($mode == 'saveComplementary'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;    
    
    $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/   
    if($actualYield > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}
    
    $queryDataInsert = "INSERT INTO binalot_complementary (report_date, branch, category, item_description, item_code, quantity, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdsss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    	
    	$function->otherinventoryoutdata($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"complementary","Create Complementary");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Add Complementary | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}


if($mode == 'voidthisdamage'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	
	$quantity = $function->Getfromthetable('quantity','binalot_damage',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_damage',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }
	
	$QUERY = "UPDATE binalot_damage SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidotherdata('damage',$rowid,$itemcode,$date_time,$app_user,$db);
    	
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"damage","Damage");
    			app_alert("System Message", "Item damage void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
}

if($mode == 'saveDamage'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;    
    
    $stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemCode,$db);
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/	
	if($actualYield > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process this item", "warning");
    	</script>';
		exit();
	}

    
    $queryDataInsert = "INSERT INTO binalot_damage (report_date, branch, category, item_description, item_code, quantity, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdsss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    	
    	$function->otherinventoryoutdata($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"damage","Create Damage");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Add Damage | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}


if($mode == 'voidthisreturn'){

	$rowid = $_POST['rowid'];
	$itemcode = $_POST['itemcode'];
	$returnquantity = $function->Getfromthetable('quantity','binalot_return',$rowid,$itemcode,$db);
	$itemdescription = $function->Getfromthetable('item_description','binalot_return',$rowid,$itemcode,$db);
	$stockinhand = $function->GetInventoryStockStocknHandReturn('stock_in_hand',$itemcode,$db);
	
	if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more void of items", "warning");
        	</script>';
        exit();
    }
	
	if($returnquantity > $stockinhand)
	{
		echo '<script>
    			app_alert("System Message", "'.$itemdescription.'"+" quantity is greater than the stock in hand value and cannot process void item", "warning");
    	</script>';

		exit();
	}
	
	$QUERY = "UPDATE binalot_return SET status='1', void_date='$date_time', void_by='$app_user' WHERE id='$rowid'";
    if (mysqli_query($db, $QUERY)) {
    	
    	$function->voidReturnData($rowid,$itemcode,$date_time,$app_user,$db);
    	
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"return","Create Return");
    			app_alert("System Message", "Item return void", "success");
    	</script>';
    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
	
	$log_msg = 'Void Return | '.$itemdescription.' | '.$returnquantity.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);


}


if($mode == 'getItemCodeNewModules'){
	$itemname = $_POST['itemname'];
    
    $query ="SELECT * FROM binalot_itemlist WHERE item_description=?";  
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $itemname);
    $stmt->execute();
    $result = $stmt->get_result();  
    
    if($ROWS = $result->fetch_assoc())  
    {
        $item = $ROWS['item_description'];
        $category_name = $ROWS['category'];
        $item_id = $ROWS['item_code'];
        echo '
            <script>
                var c = "'.$category_name.'";
                var i = "'.$item_id.'";
                $("#category").val(c);
                $("#itemcode").val(i);
                
            </script>';
    }
    else {

    }
    $stmt->close();
}


if($mode == 'saveReturns'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $branch = $_POST['branch'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];

    $actualYield = $_POST['actualYield'];
    $remarks = $_POST['remarks'];

    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;    
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/
    
    $queryDataInsert = "INSERT INTO binalot_return (report_date, branch, category, item_description, item_code, quantity, created_date, created_by, remarks)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ? ,?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("sssssdsss", $reportDate, $branch, $category, $itemDescription, $itemCode, $actualYield, $date_time, $user, $remarks);
    if ($stmt->execute()) {
    
    	$function->insertReturnData($itemCode,$actualYield,$date_time,$encodedBy,$db);
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"return","Create Return");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Return | '.$itemDescription.' | '.$actualYield.' QUANTITY';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}


if ($mode == 'dashboarddateselected') {
	$transdate = $_POST['transdate'];
	$_SESSION['BINALOT_TRANSDATE'] = $transdate;
}

if ($mode == 'datelockChecker') {

    $transdate = $_POST['transdate'];
    $checkboxValue = $_POST['checboxVals'];

    if ($function->dateLockCheckingExist($transdate, $db) == '1') {

        $QUERY = "UPDATE binalot_datelock_checker SET status='$checkboxValue', updated_by='$app_user', updated_date='$date_time' WHERE report_date='$transdate'";
    } else {

        $QUERY = "INSERT INTO binalot_datelock_checker (report_date, status, created_by, date_created) VALUES ('$transdate', '$checkboxValue', '$app_user', '$date_time')";
    }


    if (mysqli_query($db, $QUERY)) {
    	
    	if($checkboxValue == 0) {
    	
	    	echo '<script>
	    			app_alert("System Message", "Branch Ordering Open", "warning");
	    	</script>';
	    } else {
	    
	    	echo '<script>
	    			app_alert("System Message", "Branch Ordering Locked", "success");
	    	</script>';
	    }

    } else {
    
        echo "Error executing query: " . mysqli_error($db);
    }
    
    
    
    if($checkboxValue=='0'){
    	$log_msg = 'Date Open | '.$transdate;
    } else {
    	$log_msg = 'Date Locked | '.$transdate;
    }
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);

}


if ($mode == 'pcountposting') {

    $dataToSend = json_decode($_POST['data'], true);
    $uom = 'Piece(s)';
    $transdate = $_POST['transdate'];
    
    $prevdate = date('Y-m-d', strtotime($transdate. ' -1 day'));

    if($function->GetPcountExist($prevdate,$db) == '0'){
    
    	echo '<script>
    			app_alert("System Message", "Post previous summary before posting this", "warning");
    	</script>';
    	exit();
    }

    foreach ($dataToSend as $row) {
        $itemcode = $row['itemcode'];
        $category = $function->GetItemListColumn('category', $itemcode, $db);
        $itemdescription = $function->GetItemListColumn('item_description', $itemcode, $db);
       	$pcount = $row['pcount'];

		       
		$queryDataInsert = "INSERT INTO binalot_inventory_pcount (item_code, category, item_description, trans_date, p_count)
                            VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($queryDataInsert);
        $stmt->bind_param("ssssd", $itemcode, $category, $itemdescription, $transdate, $pcount);
        if ($stmt->execute()) {
        	$function->executeInventory($itemcode,$pcount,$transdate,$date_time,$app_user,$db);
            echo '<script>
                    app_alert("System Message", "Summary Successfully Saved", "success");
                  </script>';
        } else {
            echo "Error inserting record: " . $db->error;
        }
        
    }
    $log_msg = 'Daily Pcount Posted | '.$transdate;
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}

if($mode == 'saveFgts'){
	
	$user = $_SESSION['application_appnameuser'];
	$date_time = date("Y-m-d H:i:s");
	
	$reportDate = $_POST['reportDate'];
    $supplier = $_POST['supplier'];
    $encodedBy = $_POST['encodedBy'];
    $category = $_POST['category'];
    $itemDescription = $_POST['itemDescription'];
    $itemCode = $_POST['itemCode'];
    $batch = $_POST['batch'];
    $actualYield = $_POST['actualYield'];
    $timecreated = $_POST['timecreated'];
    
    
    $_SESSION['BINALOT_TRANSDATE'] = $reportDate;
    $_SESSION['BINALOT_DATEFROM'] = $reportDate;
    $_SESSION['BINALOT_DATETO'] = $reportDate;
    
    
    if($function->GetPcountDataInventoryChecker($reportDate,$db) == '1'){
    	echo '<script>
        		app_alert("System Message", "This date already finished pcount, no more additions of items", "warning");
        	</script>';
        exit();
    }
/*    
    if($function->GetProductionIDExist($itemCode,$reportDate,$db) == '1'){
		echo '<script>
        		app_alert("System Message", "This item exist in this date", "warning");
        	</script>';
        exit();
	}
*/   
    $queryDataInsert = "INSERT INTO binalot_binalot_production (created_time, category, item_description, item_code, batch_received, quantity_received, received_date, report_date, date_created, posted_by)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($queryDataInsert);
    $stmt->bind_param("ssssddssss", $timecreated, $category, $itemDescription, $itemCode, $batch, $actualYield, $date_time, $reportDate, $date_time, $user);
    if ($stmt->execute()) {
    	
        echo '<script>
        		Check_Permissions("p_view",openMenuGranted,"create_fgts","Create FGTS");
        		app_alert("System Message", "Sucessfully Item Saved", "success");
        	</script>';
    } else {
        echo "Error inserting record: " . $db->error;
    }
    $log_msg = 'Create FGTS | '.$itemDescription.' | '.$batch.' BATCH';
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}
if($mode == 'getActualYield'){
	$batch = $_POST['batch'];
	$itemcode = $_POST['itemcode'];
    
    $query ="SELECT * FROM binalot_itemlist WHERE item_code=?";  
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $itemcode);
    $stmt->execute();
    $result = $stmt->get_result();  
    
    if($ROWS = $result->fetch_assoc())  
    {
    	if (!is_numeric($ROWS['yield_perbatch']) || $ROWS['yield_perbatch'] == '') {
	        // If yield_perbatch is not numeric or is empty
	        echo '
	            <script>
	                $("#actualyield").val("0");
	            </script>';
	    } else {
	        $yield_perbatch = $ROWS['yield_perbatch'] * $batch;
	        echo '
	            <script>
	                var y = "'.$yield_perbatch.'";
	                $("#actualyield").val(y);
	            </script>';
	    }    
	}
    else {

    }
    $stmt->close();
}

if($mode == 'getItemCode'){
    $itemname = $_POST['itemname'];
    
    $query ="SELECT * FROM binalot_itemlist WHERE item_description=?";  
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $itemname);
    $stmt->execute();
    $result = $stmt->get_result();  
    
    if($ROWS = $result->fetch_assoc())  
    {
        $item = $ROWS['item_description'];
        $category_name = $ROWS['category'];
        $item_id = $ROWS['item_code'];
        $yield_perbatch = $ROWS['yield_perbatch'] ?? '';  // Handle NULL values
        
        $batch = empty($yield_perbatch) ? '0' : '1';

        // Use json_encode() for safe JavaScript variable assignment
        echo '
            <script>
                var y = '.json_encode($yield_perbatch).';
                var c = '.json_encode($category_name).';
                var i = '.json_encode($item_id).';
                var b = '.json_encode($batch).';

                $("#category").val(c);
                $("#itemcode").val(i);
                $("#batch").val(b);
                $("#actualyield").val(y);
            </script>';
    }
    $stmt->close();
}


if ($mode == 'voidbinalot') {
    $id = $_POST['id'];
    $reportdate = $_POST['reportdate'];
    $shift = $_POST['shift'];
    $itemcode = $_POST['itemcode'];
    $category = $_POST['category'];
    $itemdescription = $_POST['itemdescription'];
    $qty = $_POST['qty'];
    $user = $_SESSION['binalot_appnameuser'];

   
    $updateQueryProduction = "UPDATE binalot_binalot_production
                              SET confirmed_by = ?, status = 'Void'
                              WHERE receiving_detail_id = ?";
    $stmtProduction = $db->prepare($updateQueryProduction);
    if ($stmtProduction) {
        $stmtProduction->bind_param("ss", $user, $id);
        if ($stmtProduction->execute()) {
        	
            echo '<script>
            		Check_Permissions("p_view",openMenuGranted,"binalot_receiving","Binalot Receiving");
            		app_alert("System Message", "Void item '.$itemdescription.'", "success");
            	</script>';
            	
        } else {
            echo '<script>app_alert("System Message", "Error updating status in binalot_binalot_production", "error");</script>';
        }
        $stmtProduction->close();
    } else {
        echo '<script>app_alert("System Message", "Error preparing update query for binalot_binalot_production", "error");</script>';
    }
       
}


######################################


if ($mode == 'receivebinalot') {
    $id = $_POST['id'];
    $reportdate = $_POST['reportdate'];
    $shift = $_POST['shift'];
    $itemcode = $_POST['itemcode'];
    $category = $_POST['category'];
    $itemdescription = $_POST['itemdescription'];
    $qty = $_POST['qty'];
    $user = $_SESSION['binalot_appnameuser'];
    
    $pcount = $_POST['pcount'];
    $remarks = $_POST['remarks'];
    
    $date_time = date("Y-m-d H:i:s");
    
    $charge = 0;
    $overyield = 0;
    
    if($pcount < $qty){
    	$charge = $qty - $pcount;
    	$overyield = 0;
    }
    if($pcount > $qty) {
    	$charge = 0;
    	$overyield = $pcount - $qty;
    }
     
    
    
    $prevdate = date('Y-m-d', strtotime($reportdate. ' -1 day'));

    if($function->getPcountExist($prevdate,$db) == '0'){
    
    	echo '<script>
    			Check_Permissions("p_view",openMenuGranted,"binalot_receiving","Binalot Receiving");
    			app_alert("System Message", "Post previous pcount before receive item", "warning");
    	</script>';
    	exit();
    }
	

    $updateQueryStock = "UPDATE binalot_inventory_stock
                         SET stock_in_hand = stock_in_hand + ?, updated_by = ?
                         WHERE item_description = ?";
    $stmtStock = $db->prepare($updateQueryStock);
    if ($stmtStock) {
        $stmtStock->bind_param("dss", $pcount, $user, $itemdescription);
        if ($stmtStock->execute()) {
        
            $updateQueryProduction = "UPDATE binalot_binalot_production
                                      SET actual_received = ?, charge = ?, over_yield = ?, confirmed_by = ?, status = 'Yes'
                                      WHERE receiving_detail_id = ?";
            $stmtProduction = $db->prepare($updateQueryProduction);
            if ($stmtProduction) {
                $stmtProduction->bind_param("dddss", $pcount,$charge,$overyield,$user, $id);
                if ($stmtProduction->execute()) {
                    
                    
                    $queryDataUpdate = "INSERT INTO binalot_fgts_pcount (item_code,category,item_description,trans_date,pcount,receiving_detail_id,remarks,date_created,posted_by) VALUES ('$itemcode','$category','$itemdescription','$reportdate','$pcount','$id','$remarks','$date_time','$user')";
					if ($db->query($queryDataUpdate) === TRUE)
					{
						echo '<script>
								searchMe();
	                    		Check_Permissions("p_view",openMenuGranted,"binalot_receiving","Binalot Receiving");
	                    		app_alert("System Message", "Received item '.$itemdescription.'", "success");
	                    	</script>';

					} else {
						echo $db->error;
					}

              				
					
               
                } else {
                    echo '<script>app_alert("System Message", "Error updating status in binalot_binalot_production", "error");</script>';
                }
                $stmtProduction->close();
            } else {
                echo '<script>app_alert("System Message", "Error preparing update query for binalot_binalot_production", "error");</script>';
            }
        } else {
            echo '<script>app_alert("System Message", "Error updating item in binalot_inventory_stock", "error");</script>';
        }
        $stmtStock->close();
    } else {
        echo '<script>app_alert("System Message", "Error preparing update query for binalot_inventory_stock", "error");</script>';
    }
    $log_msg = 'FGTS Receiving | '.$itemdescription.' | '.$qty;
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}



######################################




if ($mode == 'receivebinalot______') {
    $id = $_POST['id'];
    $reportdate = $_POST['reportdate'];
    $shift = $_POST['shift'];
    $itemcode = $_POST['itemcode'];
    $category = $_POST['category'];
    $itemdescription = $_POST['itemdescription'];
    $qty = $_POST['qty'];
    $user = $_SESSION['binalot_appnameuser'];

    $updateQueryStock = "UPDATE binalot_inventory_stock
                         SET stock_in_hand = stock_in_hand + ?, updated_by = ?
                         WHERE item_description = ?";
    $stmtStock = $db->prepare($updateQueryStock);
    if ($stmtStock) {
        $stmtStock->bind_param("dss", $qty, $user, $itemdescription);
        if ($stmtStock->execute()) {
        
            $updateQueryProduction = "UPDATE binalot_binalot_production
                                      SET confirmed_by = ?, status = 'Yes'
                                      WHERE receiving_detail_id = ?";
            $stmtProduction = $db->prepare($updateQueryProduction);
            if ($stmtProduction) {
                $stmtProduction->bind_param("ss", $user, $id);
                if ($stmtProduction->execute()) {
                	
                    echo '<script>
                    		Check_Permissions("p_view",openMenuGranted,"binalot_receiving","Binalot Receiving");
                    		app_alert("System Message", "Received item '.$itemdescription.'", "success");
                    	</script>';
                    	
                } else {
                    echo '<script>app_alert("System Message", "Error updating status in binalot_binalot_production", "error");</script>';
                }
                $stmtProduction->close();
            } else {
                echo '<script>app_alert("System Message", "Error preparing update query for binalot_binalot_production", "error");</script>';
            }
        } else {
            echo '<script>app_alert("System Message", "Error updating item in binalot_inventory_stock", "error");</script>';
        }
        $stmtStock->close();
    } else {
        echo '<script>app_alert("System Message", "Error preparing update query for binalot_inventory_stock", "error");</script>';
    }
}




if($mode == 'reopenclosedorderrequest')
{
	$year = $_POST['year'];
	$month = $_POST['month'];
	$day = $_POST['day']; 
	$control_no = $_POST['control_no']; 
	$recipient = $_POST['recipient'];
	$daycol = "day_".$day;

	$query = "SELECT * FROM binalot_request WHERE control_no='$control_no' AND flag=0"; 
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
		$log_msg = $recipient." | ".$control_no." | trying to Re-Open the pending Request.";
		echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
		print_r('
			<script>
				swal("Pending Request","This transaction still have pending request for re-open","warning");
			</script>
		');
		exit();
	}

}
if($mode == 'grandfdsrequest')
{
	$rowid = $_POST['rowid'];
	$transit = $_POST['transit'];
	$control_no = $_POST['control_no'];
	$transit = $function->checkInTransit($control_no,$db);
	$queryDataUpdate = "UPDATE binalot_request SET granted_by='$app_user',granted_date='$date_time', flag=1 WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		reOpenOrder($control_no,$app_user,$function,$transit,$db);
	} else {
		echo $db->error;
	}
}
function reOpenOrder($control_no,$app_user,$function,$transit,$db)
{
	$query = "SELECT * FROM binalot_branch_order WHERE control_no='$control_no'";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
	    while($ROW = mysqli_fetch_array($results))  
		{
			$item_code = $ROW['item_code'];
			$actual_qty = $ROW['actual_quantity'];
			if($transit == 1)
			{
				echo $function->cancelOrderDeduction($item_code,$control_no,$actual_qty,$app_user,$db);
			}
			if($transit == 0)
			{
				echo $function->reOpenOrder($item_code,$control_no,$actual_qty,$app_user,$db);
			}
		}
	} else {
		echo "No Records";
	}
}
if($mode == 'reopenorderrequest')
{
	$recipient = $_POST['recipient'];
	$control_no = $_POST['control_no'];
	$query = "SELECT * FROM binalot_request WHERE control_no='$control_no' AND flag=0";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
		$log_msg = $recipient." | ".$control_no." | trying to Re-Open the pending Request.";
		echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
		print_r('
			<script>
				swal("Pending Request","This transaction still have pending request for re-open","warning");
			</script>
		');
	} else {
		$column = "`recipient`,`control_no`,`requested_by`,`request_date`";
		$insert = "'$recipient','$control_no','$app_user','$date_time'";
		$queryInsert = "INSERT INTO binalot_request ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
			$log_msg = $recipient." | ".$control_no." | Re-Open Request submitted";
			echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
			print_r('
				<script>
					swal("Success","Your request has been submit","success");
				</script>
			');
		} else {
			echo $db->error;
		}
	}
}
if($mode == 'reopenorder')
{
	$control_no = $_POST['control_no'];
	$query = "SELECT * FROM binalot_branch_order WHERE control_no='$control_no'";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
	    while($ROW = mysqli_fetch_array($results)) 
		{
			$item_code = $ROW['item_code'];
			$actual_qty = $ROW['actual_quantity'];
			echo $function->cancelOrderDeduction($item_code,$control_no,$actual_qty,$app_user,$db);
		}
	} else {
		echo "No Records";
	}
}
if($mode == 'updateleadtime')
{
	$minLT = $_POST['minValue'];
	$maxLT = $_POST['maxValue'];

	$queryDataUpdate = "UPDATE binalot_inventory_leadtime SET average_leadtime='$minLT', max_leadtime='$maxLT' WHERE leadtime_id=1";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		
	} else {
		echo $db->error;
	}
}
if($mode == 'setreportbranch')
{
	$_SESSION['BINALOT_REPORT_BRANCH'] = $_POST['branch'];
}
if($mode == 'loadbranch')
{
	$cluster = $_POST['cluster'];
	$branch = $_POST['branch'];
	$query = "SELECT * FROM tbl_branch WHERE location='$cluster'";
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
		echo $return;
	} else {
		echo '<option value="">-- BRANCH --</option>';
	}
}
if($mode == 'setbranch')
{
	$_SESSION['BINALOT_BRANCH'] = $_POST['branch'];
}
if($mode == 'setcluster')
{
	$_SESSION['BINALOT_CLUSTER'] = $_POST['cluster'];
}
if($mode == 'undopcount')
{
	$transdate = $_POST['trans_date'];
	$itemcode = $_POST['itemcode'];
	$elemid = $_POST['elemid'];
	$sqlQueryStk = "SELECT * FROM binalot_inventory_stock WHERE item_code='$itemcode' AND stock_before_pcount_date='$transdate'";
	echo $sqlQueryStk;
    $stkResults = mysqli_query($db, $sqlQueryStk);
    if ($stkResults->num_rows > 0)
    {
       	while($ROWS = mysqli_fetch_array($stkResults))  
		{
			$rowid = $ROWS['inventory_id'];
			$stock_before_pcount = $ROWS['stock_before_pcount'];
		}
		$queryDataUpdate = "UPDATE binalot_inventory_stock SET stock_in_hand='$stock_before_pcount' WHERE inventory_id='$rowid'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
	    	$qDelete = "DELETE FROM binalot_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
			if ($db->query($qDelete) === TRUE){
				print_r('
					<script>
						var elemid = "'.$elemid.'";
						$("#pcountvalue" + elemid).html("0.00");
					</script>
				');
			} 
			else {echo $db->error;}
		} else {
			echo $db->error;
		}
	} else {
		print_r('		
			<script>
				swal("Invalid Request","The Physical count data was not recorded yet.", "warning");
			</script>
		');
	}
}
if($mode == 'saveinvsetup')
{
	$transdate = $_POST['trans_date'];
	$category = $_POST['category'];
	$itemcode = $_POST['itemcode'];
	$itemname = $_POST['itemname'];
	$phycount = $_POST['phycount'];
	$uom = $_POST['uom'];

	$query = "SELECT * FROM binalot_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
	$results = $db->query($query);			
    if($results->num_rows > 0)
    {
    	$update = "`item_code`='$itemcode',`category`='$category',`item_description`='$itemname',`uom`='$uom',`trans_date`='$transdate',`p_count`='$phycount'";
    	$queryDataUpdate = "UPDATE binalot_inventory_pcount SET $update WHERE trans_date='$transdate' AND item_code='$itemcode'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			executeInventory($itemcode,$category,$itemname,$uom,$function,$phycount,$date_time,$date,$app_user,$db);
		} else {
			echo $db->error;
		}
    } else {
    	$column = "`item_code`,`category`,`item_description`,`uom`,`trans_date`,`p_count`";	
		$insert = "'$itemcode','$category','$itemname','$uom','$transdate','$phycount'";
		$queryInsert = "INSERT INTO binalot_inventory_pcount ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
			executeInventory($itemcode,$category,$itemname,$uom,$function,$phycount,$date_time,$date,$app_user,$db);
		} else {
			echo $db->error;
		}
    }
    $strvalpcount = trim($phycount);
    $log_msg = 'Monthly Pcount | '.$itemname.' | '.$strvalpcount.' | '.$transdate;
    $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
}
function executeInventory($itemcode,$category,$itemname,$uom,$function,$phycount,$date_time,$date,$app_user,$db)
{
	$supplier_id = $function->GetItemInfo('supplier_id',$itemcode,$db);
	
	$sqlQueryStk = "SELECT * FROM binalot_inventory_stock WHERE item_code='$itemcode'";
    $stkResults = mysqli_query($db, $sqlQueryStk);
    if ($stkResults->num_rows > 0)
    {
       	while($ROWS = mysqli_fetch_array($stkResults))  
		{
			$value_before_pcount = $ROWS['stock_in_hand'];
			$before_pcount_date = $ROWS['stock_before_pcount_date'];			
		}
		if($before_pcount_date == $date)
		{	
			$queryDataUpdate = "UPDATE binalot_inventory_stock SET stock_before_pcount_date='$date', stock_in_hand='$phycount', date_updated='$date_time',updated_by='$app_user' WHERE item_code='$itemcode'";
	        if ($db->query($queryDataUpdate) === TRUE) {
	        } else {
	            echo $db->error;
	        }
		}
		else 
		{
			$queryDataUpdate = "UPDATE binalot_inventory_stock SET stock_before_pcount_date='$date',stock_before_pcount='$value_before_pcount', stock_in_hand='$phycount', date_updated='$date_time',updated_by='$app_user' WHERE item_code='$itemcode'";
	        if ($db->query($queryDataUpdate) === TRUE) {
	        } else {
	            echo $db->error;
	        }
	    }
    } 
    else
    {
		$column = "supplier_id,item_code,category,item_description,stock_before_pcount_date,stock_in_hand,uom,date_updated,updated_by";
        $insert = "'$supplier_id','$itemcode','$category','$itemname','$date','$phycount','$uom','$date_time','$app_user'";
        $queryInsert = "INSERT INTO binalot_inventory_stock ($column) VALUES ($insert)";
        if ($db->query($queryInsert) === TRUE) {
        } else {
            echo $db->error;
        } 
	}
}
if($mode == 'saveexpdate')
{
	$transdate = $_POST['transdate'];
	$itemcode = $_POST['itemcode'];
	$itemname = $_POST['itemname'];
	$category = $_POST['category'];
	$expdate = $_POST['expdate'];
	
	$query = "SELECT * FROM binalot_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
	$results = $db->query($query);			
    if($results->num_rows > 0)
    {
    	$queryDataUpdate = "UPDATE binalot_inventory_pcount SET expiration_date='$expdate' WHERE trans_date='$transdate' AND item_code='$itemcode'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
		} else {
			echo $db->error;
		}
    } else {
    	$column = "`item_code`,`category`,`item_description`,`trans_date`,`expiration_date`";	
		$insert = "'$itemcode','$category','$itemname','$transdate','$expdate'";
		$queryInsert = "INSERT INTO binalot_inventory_pcount ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
		} else {
			echo $db->error;
		}
    }
}
if($mode == 'saveremarks')
{
	$transdate = $_POST['transdate'];
	$itemcode = $_POST['itemcode'];
	$itemname = $_POST['itemname'];
	$category = $_POST['category'];
	$remarks = $_POST['remarks'];
	
	$query = "SELECT * FROM binalot_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
	$results = $db->query($query);			
    if($results->num_rows > 0)
    {
    	$queryDataUpdate = "UPDATE binalot_inventory_pcount SET remarks='$remarks' WHERE trans_date='$transdate' AND item_code='$itemcode'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
		} else {
			echo $db->error;
		}
    } else {
    	$column = "`item_code`,`category`,`item_description`,`trans_date`,`remarks`";	
		$insert = "'$itemcode','$category','$itemname','$transdate','$remarks'";
		$queryInsert = "INSERT INTO binalot_inventory_pcount ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
		} else {
			echo $db->error;
		}
    }
}
if($mode == 'savepcount')
{
	$transdate = $_POST['transdate'];
	$itemcode = $_POST['itemcode'];
	$itemname = $_POST['itemname'];
	$category = $_POST['category'];
	$phycount = $_POST['phycount'];
	
	$query = "SELECT * FROM binalot_inventory_pcount WHERE trans_date='$transdate' AND item_code='$itemcode'";
	$results = $db->query($query);			
    if($results->num_rows > 0)
    {
    	$queryDataUpdate = "UPDATE binalot_inventory_pcount SET p_count='$phycount' WHERE trans_date='$transdate' AND item_code='$itemcode'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
		} else {
			echo $db->error;
		}
    } else {
    	$column = "`item_code`,`category`,`item_description`,`trans_date`,`p_count`";	
		$insert = "'$itemcode','$category','$itemname','$transdate','$phycount'";
		$queryInsert = "INSERT INTO binalot_inventory_pcount ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
		} else {
			echo $db->error;
		}
    }
}
if($mode == 'setintransit')
{
	$control_no = $_POST['control_no'];		
	$query = "SELECT * FROM binalot_order_request WHERE control_no='$control_no'";
	$results = mysqli_query($db, $query);    
	if ( $results->num_rows > 0 ) 
	{
	    while($ROW = mysqli_fetch_array($results))  
		{
			$year = date("Y", strtotime($ROW['delivery_date']));
			$month = date("m", strtotime($ROW['delivery_date']));
			$day = date("d", strtotime($ROW['delivery_date']));
		}
	} else {
		print_r('
			<script>
				swal("Warning","Control Number is invalid", "warning");
			</script>
		');
		exit();
	}
	
	function updateLogistics($control_no, $db)
	{
	    $date = date("Y-m-d");
	    $queryDataUpdate = "UPDATE binalot_order_request SET order_transit=1, status='In-Transit',order_transit_date=? WHERE control_no=? ORDER BY request_id DESC";
	    $stmt = $db->prepare($queryDataUpdate);
	    $stmt->bind_param("ss", $date, $control_no);	   
	 	if ($stmt->execute()) {
	        print_r('
	            <script>				
	                dr_details("' . $control_no . '");
	            </script>
	        ');
	    } else {
	        echo $stmt->error;
	    }
	    $stmt->close();
	}
	function updateToInventory($year, $month, $day, $item_code, $unit_price, $item_description, $new_quantity, $control_no, $db)
	{
	    $col = "day_" . $day;	    
	    $queryDataUpdate = "UPDATE binalot_inventory_records SET $col=? WHERE item_code=? AND year=? AND month=?";
	    $stmt = $db->prepare($queryDataUpdate);
	    $stmt->bind_param("issi", $new_quantity, $item_code, $year, $month);
	    if ($stmt->execute()) {
	        updateLogistics($control_no, $db);
	        print_r('
	            <script>				
	                swal("Success", "The order has been passed to Logistics for Transit", "success");
	            </script>
	        ');
	    } else {
	        print_r('
	            <script>
	                swal("Update Error:", "' . $stmt->error . '", "warning");
	            </script>
	        ');
	    }
	    $stmt->close();
	}

	function insertToInventory($year, $month, $day, $item_code, $unit_price, $item_description, $actual_quantity, $control_no, $db)
	{
		$column = "`unit_price`,`item_code`,`item_description`,`year`,`month`,`day_" . $day . "`";
		$insert = "'$unit_price','$item_code','$item_description','$year','$month','$actual_quantity'";

		$queryInsert = "INSERT INTO binalot_inventory_records ($column) VALUES ($insert)";
		if ($db->query($queryInsert) === TRUE)
		{
	        updateLogistics($control_no, $db);
	        print_r('
	            <script>
	                swal("Success", "The order has been passed to Logistics for Transit", "success");
	            </script>
	        ');
		} else {
			 print_r('
	            <script>
	                swal("Insert Error:", "' . $db->error . '", "warning");
	            </script>
	        ');
		}
	}
	function queryInventory($year, $month, $day, $unit_price, $item_code, $item_description, $actual_quantity, $control_no, $db)
	{
	    $col = "day_" . $day;    
	    $QUERYRECORDS = "SELECT * FROM binalot_inventory_records WHERE item_code=? AND year=? AND month=?";
	    $stmt = $db->prepare($QUERYRECORDS);
	    $stmt->bind_param("ssi", $item_code, $year, $month);
	    $stmt->execute();
	    
	    $RECORDSRESULTS = $stmt->get_result();
	    
	    if ($RECORDSRESULTS->num_rows > 0) {
	        while ($ROWS = $RECORDSRESULTS->fetch_assoc()) {
	            $quantity = intval($ROWS[$col]);
	            $new_quantity = $quantity + $actual_quantity;
	            updateToInventory($year, $month, $day, $item_code, $unit_price, $item_description, $new_quantity, $control_no, $db);
	        }
	    } else {
	        insertToInventory($year, $month, $day, $item_code, $unit_price, $item_description, $actual_quantity, $control_no, $db);
	    }
	    $stmt->close();
	}
	function updateStock($item_code,$new_stock,$db)
	{
	    $queryDataUpdate = "UPDATE binalot_inventory_stock SET stock_in_hand=? WHERE item_code=?";
	    $stmt = $db->prepare($queryDataUpdate);
	    $stmt->bind_param("is", $new_stock, $item_code);
	    if ($stmt->execute()) {
	    } else {
	        print_r('
	            <script>
	                swal("Update Error:", "' . $stmt->error . '", "warning");
	            </script>
	        ');
	    }
	    $stmt->close();
	}
	
	$QUERY = "SELECT * FROM binalot_branch_order WHERE control_no=?";
	$stmt = $db->prepare($QUERY);
	$stmt->bind_param("s", $control_no);
	$stmt->execute();
	$RESULTS = $stmt->get_result();
	while ($ROW = $RESULTS->fetch_assoc())
	{
	    $item_code = $ROW['item_code'];
	    $actual_quantity = $ROW['actual_quantity'];
	    $item_description = $ROW['item_description'];
	    $unit_price = $function->GetUnitPrice($item_code,$db);
	    queryInventory($year, $month, $day, $unit_price, $item_code, $item_description, $actual_quantity, $control_no, $db);
	    $stock = $function->GetOnHand($item_code,$db);
	    $new_stock = ($stock - $actual_quantity);
	    updateStock($item_code,$new_stock,$db);
	}
	$stmt->close();	
	
	$log_msg = 'Branch Order Receiving | Set In-Transit | '.$control_no;
	$function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
	
	
	
	
	
	
/*	
	
	
	$branch = $function->GetOrderStatus($control_no,'branch',$db);
	$transdate = $function->GetOrderStatus($control_no, 'trans_date', $db);
	$delivery_driver = $function->GetOrderStatus($control_no, 'delivery_driver', $db);
	$plate_number = $function->GetOrderStatus($control_no, 'plate_number', $db);
	$icon = "\u{1F69A}\u{1F4A8}";

	print_r('
		<script>
			var sender = "'.$app_user.'";
			var message = "Set In-Transit '.$icon.' --> '.$branch.' -> MRS No.:'.$control_no.' -> Transaction Date:'.$transdate.' -> Delivery Drive:'.$delivery_driver.' -> Plate No.:'.$plate_number.'";
			var tablename = "binalot_chat_data";
			sendMessage(JSON.stringify({
                sender: sender, 
                message: message,
                tablename: tablename
            }));       
		</script>
	');

*/	
	
	
	
	
}
if($mode == 'savelogisticinfo')
{
	$control_no = $_POST['control_no'];
	$delivery_date = $_POST['delivery_date'];
	$delivery_driver = $_POST['delivery_driver'];
	$plate_number = $_POST['plate_number'];
	
	$_SESSION['LOGISTIC_DRIVER'] = $delivery_driver;
	$_SESSION['LOGISTIC_PLATE'] = $plate_number;

	$queryDataUpdate = "UPDATE binalot_order_request SET delivery_date=?, delivery_driver=?, plate_number=? WHERE control_no=?";
	$stmt = $db->prepare($queryDataUpdate);
	$stmt->bind_param("ssss", $delivery_date, $delivery_driver, $plate_number, $control_no);
	if ($stmt->execute())
	{
		echo $function->UpdateBranchOrderDeliveryDate($control_no,$delivery_date,$db);
	    print_r('
	        <script>
	            swal("Success", "Logistic Information has been added to the Delivery Receipt", "success");
	            dr_details("' . $control_no . '");
	        </script>
	    ');
	} else {
	    print_r('
	        <script>
	            swal("Saving Error:", "' . $stmt->error . '", "warning");
	        </script>
	    ');
	}
	$stmt->close();
}
if($mode == 'forwardtologistics')
{
	$drnumber = $function->GetDrNumber($db);
	$number  = intval($drnumber);
	$number += 1;
	$new_dr_no = str_pad($number, strlen($drnumber), '0', STR_PAD_LEFT);
	$dr_number = "DR-".$drnumber;

	$control_no = $_POST['control_no'];
	$module = $_POST['module'];
	
	$approvedDate = $function->GetOrderStatus($control_no, 'approved_date', $db);
	
	$date = date("Y-m-d");
	$prevdate = date('Y-m-d', strtotime($approvedDate. ' -1 day'));
    if($function->getPcountExist($prevdate,$db) == '0'){
    	echo '<script>
    			app_alert("System Message", "Post previous pcount before Finalize this", "warning");
    	</script>';
    	exit();
    }	
	
	$queryDataUpdate = "UPDATE binalot_order_request SET logistics=1, dr_number='$dr_number' WHERE control_no='$control_no'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		$queryDataUpdate = "UPDATE binalot_form_numbering SET dr_number='$new_dr_no' WHERE id=1";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			$log_msg = $module.' | Finalized Order | CN'.$control_no;
			print_r('
				<script>
					swal("Success", "Delivery Receipt has been Created successfuly", "success");
					orderProcess("'.$control_no.'");
				</script>
			');
			echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);
		} else {
			print_r('
				<script>
					swal("Numbering Error:", "'.$db->error.'", "warning");
				</script>
			');
		}		
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}	
}
if($mode == 'preparatorremarks')
{
	$remarks = $_POST['remarks'];
	$control_no = $_POST['control_no'];

	$sqlQuery = "SELECT * FROM binalot_branch_order_remarks WHERE control_no='$control_no'";
	$results = mysqli_query($db, $sqlQuery);
	if ( $results->num_rows > 0 ) 
    {
		$queryDataUpdate = "UPDATE binalot_branch_order_remarks SET preparator_remarks='$remarks' WHERE control_no='$control_no'";
		if ($db->query($queryDataUpdate) === TRUE)
		{
			echo $remarks." -- ".$control_no;
		} else {
			print_r('
				<script>
					swal("Warning", "'.$db->error.'", "warning");
				</script>
			');
		}
	}
	else
	{	
		$queryInsert = "INSERT INTO binalot_branch_order_remarks (`preparator_remarks`,`control_no`) VALUES ('$remarks','$control_no')";
		if ($db->query($queryInsert) === TRUE)
		{
		} else {
			echo $db->error;
		}

	}
}
if($mode == 'changeactualcount')
{
	$rowid = $_POST['rowid'];
	$control_no = $_POST['control_no'];
	$item_code = $_POST['item_code'];	
	$actual_quantity = $_POST['actual_quantity'];
	$transdate = $_POST['transdate'];
	
	
	$prevdate = date('Y-m-d', strtotime($transdate. ' -1 day'));

	
	$queryDataUpdate = "UPDATE binalot_branch_order SET wh_quantity='$actual_quantity', actual_quantity='$actual_quantity' WHERE id='$rowid'";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		print_r('
			<script>
				orderProcess("'.$control_no.'");
			</script>
		');		
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}
}
if($mode == 'preparebranchorder')
{
	$app_user = ucwords($app_user);
	$module = $_POST['module'];
	$control_no = $_POST['control_no'];
	$queryDataUpdate = "UPDATE binalot_order_request SET order_preparing=1, order_preparing_by='$app_user', order_preparing_date='$date' WHERE control_no='$control_no' AND order_preparing=0";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		$log_msg = $module.' | Preparing Order | CN'.$control_no;
		print_r('
			<script>
				swal("Success", "Order has been successfully unlock", "success");
				orderProcess("'.$control_no.'");
			</script>
		');		
		echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);	
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}	
}
if($mode == 'receivebranchorder')
{
	$app_user = ucwords($app_user);
	$control_no = $_POST['control_no'];
	$module = $_POST['module'];
	$queryDataUpdate = "UPDATE binalot_order_request SET order_received=1, order_received_by='$app_user', order_received_date='$date' WHERE control_no='$control_no' AND order_received=0";
	if ($db->query($queryDataUpdate) === TRUE)
	{
		$log_msg = $module.' | Received Order | CN'.$control_no;
		print_r('
			<script>
				swal("Success", "Order has been successfully received", "success");
				orderProcess("'.$control_no.'");
			</script>
		');		
		echo $function->DoAuditLogs($date_time,$log_msg,$app_user,$db);	
	} else {
		print_r('
			<script>
				swal("Warning", "'.$db->error.'", "warning");
			</script>
		');
	}	
}
if($mode == 'displaybranch')
{
	
	if(isset($_POST['search']))
	{
		$search = $_POST['search'];
		$q = "WHERE branch LIKE '%$search%'";
	} else {
		$q = "";
	}
	$sqlQuery = "SELECT * FROM tbl_branch $q LIMIT 100";
	$results = mysqli_query($db, $sqlQuery);    
	echo '<ul class="searchlist">';
    if ( $results->num_rows > 0 ) 
    {
    	while($ITEMSROW = mysqli_fetch_array($results))  
		{
			$branch = $ITEMSROW['branch'];
			
?>
			<li onclick="setSearch('<?php echo $branch; ?>')"><?php echo $branch; ?></li>
<?php
		}
	} else {
		echo "<li>No Record.</li>";
	}
}
if($mode == 'recevingstocks')
{
	
	if(isset($_POST['search']))
	{
		$search = $_POST['search'];
		$q = "WHERE active=1 AND item_description LIKE '%$search%' OR item_code LIKE '%$search%' OR qr_code LIKE '%$search%'";
	} else {
		$q = "WHERE active=1";
	}
	$sqlQuery = "SELECT * FROM binalot_itemlist $q";
	$results = mysqli_query($db, $sqlQuery);    
	echo '<ul class="searchlist">';
    if ( $results->num_rows > 0 ) 
    {
    	while($ITEMSROW = mysqli_fetch_array($results))  
		{
			$item_code = $ITEMSROW['item_code'];
			$item = $ITEMSROW['item_description'];
			$uom = $ITEMSROW['uom'];	
			$unitprice = $ITEMSROW['unit_price'];
			$supplier_id = $ITEMSROW['supplier_id'];	
			$category = $ITEMSROW['category'];
			
?>
			<li onclick="setSearch('<?php echo $item; ?>','<?php echo $item_code; ?>','<?php echo $unitprice; ?>','<?php echo $uom; ?>','<?php echo $category; ?>')"><?php echo $item; ?></li>
<?php
		}
	} else {
		echo "<li>No Record.</li>";
	}
}
if($mode == 'transfersearch')
{
	
	if(isset($_POST['search']))
	{
		$search = $_POST['search'];
		$q = "WHERE active=1 AND item_description LIKE '%$search%' OR item_code LIKE '%$search%' OR qr_code LIKE '%$search%'";
	} else {
		$q = "WHERE active=1";
	}
	$sqlQuery = "SELECT * FROM binalot_itemlist $q LIMIT 100";
	$results = mysqli_query($db, $sqlQuery);    
	echo '<ul class="searchlist">';
    if ( $results->num_rows > 0 ) 
    {
    	while($ITEMSROW = mysqli_fetch_array($results))  
		{
			$item_code = $ITEMSROW['item_code'];
			$item = $ITEMSROW['item_description'];
			$uom = $ITEMSROW['uom'];	
			$unitprice = $ITEMSROW['unit_price'];
			$supplier_id = $ITEMSROW['supplier_id'];	
			$category = $ITEMSROW['category'];
			
?>
			<li onclick="setSearchItem('<?php echo $item; ?>','<?php echo $item_code; ?>')"><?php echo $item; ?></li>
<?php
		}
	} else {
		echo "<li>No Record.</li>";
	}
}
mysqli_close($db);
?>