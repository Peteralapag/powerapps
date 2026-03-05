<?php
ini_set('display_error',1);
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
require_once($_SERVER['DOCUMENT_ROOT']."/Modules/Binalot_Management/class/Class.functions.php") ;

$function = new BINALOTFunctions;
$dateNow = $_SESSION['BINALOT_TRANSDATE'];
$user = $_SESSION['application_appnameuser'];

$q = '';
if (isset($_POST['branch'])) {
    $branch = mysqli_real_escape_string($db, $_POST['branch']);
    $q = "WHERE branch LIKE '%$branch%'";
}

?>
<style>
.form-wrapper {width:500px;max-height:500px;overflow-y:auto;}
.table th {font-size:14px !important;}

.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 24px;
}
.switch input { display: none; }
.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px;
}
.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
input:checked + .slider {
    background-color: #2196F3;
}
input:checked + .slider:before {
    transform: translateX(16px);
}


</style>
<div class="form-wrapper">	
	
	<table style="width: 100%" class="table">
            
        <?php
        
            $sqlQuery = "SELECT * FROM tbl_branch $q";
			$results = mysqli_query($db, $sqlQuery);    
		    if ( $results->num_rows > 0 ) 
		    {
		    	$n=0;
		    	while($RECVROW = mysqli_fetch_array($results))  
				{
					$id = $RECVROW['id'];
					$branch = $RECVROW['branch'];
					$isActive = $function->getIntoDbcTblBranchTableStatus($id, $db);
				?>
					<tr>
						<td>
							<label class="switch">
                                <input type="checkbox" id="switch<?php echo $id?>" <?php echo ($isActive == 1 ? 'checked' : '') ?> onchange="addThisBrachActive(<?php echo $id?>)">
                                <span class="slider"></span>
                            </label>
						</td>
					
						<td id="branch<?php echo $id?>">
							<?php echo $branch?>
						</td>
					</tr>
				<?php
				
				}
			}
        ?>
	</table>
</div>
<div id="results"></div>

<script>
function searchBranchHere(branch){
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/apps/set_branch_active_form.php", { branch: branch },
	function(data) {
		$('#results').html(data);
		rms_reloaderOff();
	});

	
}
function addThisBrachActive(branchid){
	
	var mode = 'addThisbranchtoactive';
	var branch = $('#branch'+branchid).text();
	var checkbox = document.getElementById("switch"+branchid);
	
	var branchvalue = checkbox.checked ? 1 : 0;
	
	rms_reloaderOn('Loading...');
	$.post("./Modules/Binalot_Management/actions/actions.php", { mode: mode, branchid: branchid, branch: branch, branchvalue: branchvalue },
	function(data) {
		$('#results').html(data);
		rms_reloaderOff();
	});

}

</script>