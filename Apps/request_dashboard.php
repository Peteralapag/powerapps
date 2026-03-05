<style>
.request-wrapper {position:relative;display: flex;height: calc(100vh - 250px);width: calc(100vw - 300px);margin-bottom:10px;align-items: flex-start;}
.request-nav {position:absolute;width:100%;flex-grow: 1;padding:5px 5px 5px 5px;border-radius:8px;}
.tableddata td {border: 1px solid ##aeaeae;width:33%;height: calc(100vh - 317px);}
#tabledata {display: flex;width:100%;margin-top:65px;height: calc(100vh - 315px);gap: 10px;overflow:hidden;}
.col {border:1px solid #aeaeae;padding:5px;overflow:auto;background:#fff;border-radius:5px;}
.col { overflow: auto; height:); width:100% }
.col thead th { position: sticky; top: 0; z-index: 1; background:#0cccae; color:#fff }
.col table  { border-collapse: collapse;}
.col th, .col td { font-size:12px; white-space:nowrap }
</style>
<div class="request-wrapper">
	<div class="request-nav">
		<button class="btn btn-primary color-white" onclick="new_request()">New Request</button>
		<button class="btn btn-info color-white" onclick="rejected_request()">Rejected Request</button>
		<button class="btn btn-warning color-white" onclick="approved_request()">All Request</button>
	</div>
	<div id="tabledata">
		<div class="col" id="newrequest"></div>
		<div class="col" id="rejected_request"></div>
		<div class="col" id="all_request"></div>
	</div>
	<div id="actionresults"></div>
</div>
<script>
function requestActions(rowid,column,value)
{
	if(value==2){
		$('#modalicon').html('<i class="fa-solid fa-file color-red"></i>');
		$('#modaltitle').html('REJECTION FORM');	
		$.post("../Apps/reject_reason_form.php", { rowid: rowid },
		function(data) {
			$('#formmodal_page').html(data);
			$('#formmodal').show();
		});
	}
	else{
		$.post("../Actions/request_actions.php", { rowid: rowid, column: column, value: value },
		function(data) {
			$('#actionresults').html(data);
		});
	}
}
$(function()
{
	new_request();
	rejected_request();
	approved_request();
});
function new_request()
{	
	$('#newrequest').html(' Loading... <i class="fa fa-spinner fa-spin"></i>');
	setTimeout(function()
	{
		$.post("../Includes/new_request_data.php", {  },
		function(data) {
			$('#newrequest').html(data);
		});
	},1000);		
}
function rejected_request()
{	
	$('#rejected_request').html(' Loading... <i class="fa fa-spinner fa-spin"></i>');
	setTimeout(function()
	{
		$.post("../Includes/rejected_request_data.php", {  },
		function(data) {
			$('#rejected_request').html(data);
		});
	},1000);
}
function approved_request()
{	
	$('#all_request').html(' Loading... <i class="fa fa-spinner fa-spin"></i>');
	setTimeout(function()
	{
		$.post("../Includes/approved_request_data.php", {  },
		function(data) {
			$('#all_request').html(data);
		});
	},1000);
}

</script>