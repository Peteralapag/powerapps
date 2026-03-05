<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
$functions = new PageFunctions;
$path = $_POST['fullpath'];
$folder = $_POST['folder'];
$fullpath = $path."/".$folder;
$subfolder = $_POST['subfolder'];
if($folder == 'Job Description' || $folder == 'Policies And Memos' || $folder == 'Process Flows' || $folder == 'Table of Organization')
{
	$class = 'select-form';
} else {
	$class = '';
}
?>
<style>
.upload-form {width:350px;	margin-bottom:10px;}
.upload-form div {	margin-bottom:10px;}
.upload-form select {	font-size:14px;}
.select-form {	display: none;}
</style>
<div class="upload-form">
	<div class="<?php echo $class; ?>" id="selectform">
		<label class="form-label" for="customFile">Select folder to upload</label>
		<select id="storagefolder" class="form-control">
			<?php echo $functions->GetFolder($db); ?>
		</select>
	</div>
	<div>
		<label class="form-label" for="customFile">Select PDF File</label>
		<input type="file" class="form-control" id="inputFile">
	</div>
	<div style="text-align:right;margin-top:20px">
		<button id="uploadbtn" class="btn btn-success">Upload</button> 
		<button class="btn btn-danger" onclick="closeModal('formmodal')">Close</button>
	</div>
</div>
<div id="uploadresults"></div>
<script>
$(function()
{
	$('#uploadbtn').click(function()
	{
		var subfolder = '<?php echo $subfolder; ?>';
		var storagefolder = $('#storagefolder').val();
		var file_data = $('#inputFile').prop('files')[0];	
		
		if('<?php echo $class; ?>' == '')
		{
			if(storagefolder == '')
			{
				swal("System Message","Please Select folder to store the file","warning");
				return false;
			}			
			var fullpath = '<?php echo $path; ?>/' + storagefolder;			
		} else {
			var fullpath = '<?php echo $path; ?>';
		}
		if(file_data == undefined)
		{
			swal("System Message","Please Choose PDF File","warning");
			return false;
		}
		rms_reloaderOn('Uploading...');
		setTimeout(function()
		{
			var form_data = new FormData();
			form_data.append('subfolder', subfolder);
			form_data.append('file', file_data);
			form_data.append('fullpath', fullpath);
			$.ajax({
			    url         : '../../../../Modules/Document_Management/actions/upload_file_process.php',
			    cache       : false,
			    contentType : false,
			    processData : false,
			    data        : form_data,                         
			    type        : 'post',
			     beforeSend: function(){
		            $('.uploadbtn').attr("disabled","disabled");
		        },
			    success : function(output){
			        $('#uploadresults').html(output);
					$('.uploadbtn').attr("disabled",false);
					swal('Success','File has been uploaded and saved', 'success');
					$('#' + sessionStorage.slidercount).trigger('click');
					closeModal('formmodal');
					rms_reloaderOff();
			    }
			});
		},1000);		
	});
});
$("#inputFile").change(function() {
    var file = this.files[0];
    var fileType = file.type;
    console.log(fileType);
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
    	//var a = fileName.replace("C:\fakepath\", "");
    	$('#filename').val(fileName);
    }
});
</script>