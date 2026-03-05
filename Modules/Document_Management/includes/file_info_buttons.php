<?php
	if($md_userlevel <= 50) {
		if($subfolder == 'Archived') { ?>	
		<button class="btn btn-success btn-sm" onclick="viewThisFile('<?php echo $fname; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-folder-open color-yellow"></i>&nbsp;&nbsp;View File</button>
		<?php if($req_type == 2 AND $approved == 1) { ?>
		<button class="btn btn-primary btn-sm" onclick="renameThisFile('<?php echo $path; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-file-pen"></i>&nbsp;&nbsp;Rename File</button>
		<?php } if($req_type == 3 AND $approved == 1) { ?>
		<button class="btn btn-danger btn-sm" onclick="deleteFile('<?php echo $filename; ?>')"><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Delete File</button>
		<?php } if($request_btn == 1) { ?>
		<button class="btn btn-info btn-sm color-white" onclick="createRequest()"><i class="fa-solid fa-registered"></i>&nbsp;&nbsp;Create Request</button>
		<?php } if($request_cancel == 1) { ?>
		<button class="btn btn-secondary btn-sm" onclick="cancelRequest('<?php echo $rowid; ?>')"><i class="fa-solid fa-xmark"></i>&nbsp;&nbsp;Cancel Request</button>
		<?php } }
		if($subfolder == 'Raw') { if($req_type == 1 AND $approved == 1 AND $executed == 0) { ?>	
		<button class="btn btn-success btn-sm" onclick="DownloadThisFile('<?php echo $fname; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-folder-open color-yellow"></i>&nbsp;&nbsp;Download File</button>
		<?php } if($req_type == 2 AND $approved == 1) { ?>
		<button class="btn btn-primary btn-sm" onclick="renameThisFile('<?php echo $path; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-file-pen"></i>&nbsp;&nbsp;Rename File</button>
		<?php } if($req_type == 3 AND $approved == 1) { ?>
		<button class="btn btn-danger btn-sm" onclick="deleteFile('<?php echo $filename; ?>')"><i class="fa-solid fa-trash"></i>&nbsp;&nbsp;Delete File</button>
		<?php } if($request_btn == 1) { ?>
		<button class="btn btn-info btn-sm color-white" onclick="createRequest()"><i class="fa-solid fa-registered"></i>&nbsp;&nbsp;Create Request</button>
		<?php } if($request_cancel == 1) { ?>
		<button class="btn btn-secondary btn-sm" onclick="cancelRequest('<?php echo $rowid; ?>')"><i class="fa-solid fa-xmark"></i>&nbsp;&nbsp;Cancel Request</button>
		<?php } ?>			
	<?php } } ?>
	<?php if($md_userlevel >= 50) { 
		if($subfolder == 'Archived') { ?>
			<button class="btn btn-success btn-sm" onclick="viewThisFile('<?php echo $fname; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-folder-open color-yellow"></i>&nbsp;&nbsp;View File</button>
		<?php } 
		if($subfolder == 'Raw') { ?> 
			<button class="btn btn-success btn-sm" onclick="DownloadThisFile('<?php echo $fname; ?>','<?php echo $filename; ?>')"><i class="fa-solid fa-folder-open color-yellow"></i>&nbsp;&nbsp;Download File</button>		
<?php } } ?>
<button class="btn btn-danger btn-sm"  onclick="closeModal('formmodal')">Close</button>
