<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$controlno = $_POST['controlno'];
$branch = $_POST['branch'];

$voidstat = 0;
?>

<style>

.flex-container {
  display: flex;
  gap: 10px;
}

.box {
  padding: 20px;
  text-align: center;
  border: 1px solid #ddd;
}

.box:first-child {
  flex: 3;
  max-height: 60vh;
  overflow: auto;
}

.box:last-child {
  flex: 2;
}

textarea {
  width: 100%;
  height:25%;
  min-height:80px;
  resize: vertical;
}
</style>

<div class="flex-container">
  <div class="box">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th>#</th>
                <th>ITEMCODE</th>
                <th>ITEM DESCRIPTION</th>
                <th>REQUESTED QTY.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $db->prepare("SELECT * FROM dbc_branch_order WHERE control_no = ? AND branch = ?");
            $stmt->bind_param("ss", $controlno, $branch);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $counter = 1;
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$counter}</td>
                        <td>{$row['item_code']}</td>
                        <td>{$row['item_description']}</td>
                        <td style='text-align: right'>{$row['quantity']}</td>
                    </tr>";
                    $counter++;
                }
            } else {
                echo "<tr>
                    <td colspan='4' class='text-center'>No records found</td>
                </tr>";
            }
            $stmt->close();
            $db->close();
            ?>       
        </tbody>
    </table>
  </div>
  
  <div id="voidbox" class="box" style="border:0px">
    <textarea id="voidremarks" placeholder="Reasons required" style="display:none"></textarea>
    <button id="voidbtnproceed" type="button" class="btn btn-danger btn-sm" style="display:none" onclick="voidthis('<?php echo $controlno?>')"><i class="fa fa-thumbs-down" aria-hidden="true"></i>&nbsp;&nbsp;Void This</button>
  </div>
</div>


<script>

function voidthis(controlno){
	
	var mode = 'voidthisbranchorder';
	var module = sessionStorage.module_name;
	var reasons = $('#voidremarks').val();
	
	if (!reasons || reasons.trim() === "")
	{
		app_alert("System Message","Remarks cannot be blank. Please provide a value","warning","Ok","","no");
		return false;	
	}
		
	rms_reloaderOn('Void this order...');
	setTimeout(function()
	{
		$.post("./Modules/DBC_Management/actions/actions.php", { mode: mode, controlno: controlno, module: module, reasons: reasons },
		function(data) {		
			$('#results').html(data);
			rms_reloaderOff();
		});
	},1000);
}


</script>
