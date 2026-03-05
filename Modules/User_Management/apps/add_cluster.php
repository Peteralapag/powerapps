<?php
require '../../../init.php';
$functions = new PageFunctions;
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$cluster = isset($_POST['cluster']) ? $_POST['cluster'] : '';
$rowid = isset($_POST['rowid']) ? $_POST['rowid'] : '';
$idcode = isset($_POST['idcode']) ? $_POST['idcode'] : '';
?>
<table class="table" style="width:100%">
    <tr>
        <th>Cluster</th>
        <td>
            <select id="addcluster" class="form-control form-control-sm">
                <?php echo $functions->GetCluster($cluster, $db); ?>
            </select>
        </td>
    </tr>
</table>
<div style="float:right">
    <button class="btn btn-success btn-sm" onclick="add('<?php echo $rowid ?>','<?php echo $idcode ?>')">Add</button>
</div>

<script>
function add(rowid, idcode) {
    var cluster = $('#addcluster').val();
    var mode = 'addingClusterAssignments';
    $.ajax({
        url: '../../../Modules/User_Management/actions/actions.php',
        type: 'POST',
        dataType: 'json',
        data: { mode: mode, rowid: rowid, idcode: idcode, cluster: cluster },
        success: function(resp) {
            if(!resp) {
                alert('Invalid response from server');
                return;
            }
            if(resp.status === 'success'){
                if(typeof showToast === 'function'){
                    showToast('success', resp.message, 2000);
                } else if(typeof showMessage === 'function'){
                    showMessage('success','Success',resp.message,2000);
                } else {
                    alert(resp.message);
                }
                // refresh employee data and call get_users
                $.ajax({
                    url: '../../../Modules/User_Management/apps/employee_data.php',
                    type: 'POST',
                    data: { rowid: rowid, idcode: idcode },
                    success: function(response) {
                        if (typeof get_users === 'function') {
                            try{ get_users(rowid,'','getuser'); }catch(e){}
                        } else if (typeof loadModules === 'function') {
                            loadModules('User_Management');
                        }
                    },
                    error: function() { if(typeof showToast==='function') showToast('error','Error posting to employee_data.php.',2000); else alert('Error posting to employee_data.php.'); }
                });
            } else {
                if(typeof showToast === 'function'){
                    showToast('error', resp.message || 'Error', 2000);
                } else if(typeof showMessage === 'function'){
                    showMessage('error','Error',resp.message || 'Error',2000);
                } else {
                    alert(resp.message || 'Error');
                }
            }
        },
        error: function() { if(typeof showToast==='function') showToast('error','There was an error processing your request.',2000); else alert('There was an error processing your request.'); }
    });
}
</script>
