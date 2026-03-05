<?php
$organization = $_POST['organization'];
$subfolder = $_POST['subfolder'];
?>
<div class="upload-wrapper">
	<div>
		<label style="margin-bottom:5px;font-size:14px">Description</label>
		<input type="text" class="form-control form-control-sm" id="filedescription"  autocomplete="off">
	</div>
	<div>
		<label style="margin-bottom:5px;margin-top:10px;font-size:14px">File</label>
		<input class="form-control form-control-sm" id="inputFile" type="file">
	</div>
	<div class="input-box" style="margin-top:20px;text-align:right">
		<button class="btn btn-primary submitBtn" onclick="file_upload('')">Upload</button>
		<button class="btn btn-danger" onclick="closeModal('formmodal')">Cancel</button>
	</div>
</div>
<div id="uploadresults"></div>
<script>
function file_upload()
{
	var subfolder = sessionStorage.subfolder;	
	var organization = sessionStorage.organization;;
	var description = $('#filedescription').val();
	var file_data = $('#inputFile').prop('files')[0];
	if(description == '')
	{
		swal("System Message","Please Enter File Description","warning");
		return false;
	}
	if(file_data == undefined)
	{
		swal("System Message","Please Choose PDF File","warning");
		return false;
	}
	
	var form_data = new FormData();
	form_data.append('file', file_data);		
	form_data.append('description', description);
	form_data.append('organization', organization);
	form_data.append('subfolder', subfolder);
	rms_reloaderOn("Uploading...");
	setTimeout(function()
	{
		$.ajax({
		    url         : './Modules/Written_Documents/actions/upload_file_process.php',
		    cache       : false,
		    contentType : false,
		    processData : false,
		    data        : form_data,                         
		    type        : 'post',
		     beforeSend: function(){
	            $('.submitBtn').attr("disabled","disabled");
	        },
		    success : function(output){
		        $('#uploadresults').html(output);
				$('.submitBtn').attr("disabled",false);
				rms_reloaderOff();
		    }
		});
	},1000);
}
$("#inputFile").change(function() {
    var file = this.files[0];
    var fileType = file.type;
    var match = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if(!((fileType == match[0]) || (fileType == match[1]))){
		swal("System Message","only PDF files is allowed to submit.","warning");
		$('#inputFile').val('');
        return false;
    }
   	var file_name = $('#filename').val();
    var fileName = $(this).val();
    $(this).next('.custom-file-label').html(fileName);
	
    if(file_name == '')
    {
    	$('#filename').val(fileName);
    }
});
</script>