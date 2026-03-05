<?php
$organization = strtolower($_POST['organization']); 
$subfolder = strtolower($_POST['subfolder']);
?>
<div class="org-title"><?php echo ucwords($organization." - ".$subfolder); ?></div>
<div class="tabcontainer">
	<div class="btn-group" role="group" aria-label="Basic example">
		<button id="ap" type="button" data-id="ap" class="btn btn-secondary" onclick="load_content('<?php echo $organization; ?>','<?php echo $subfolder; ?>','memo')"><i class="fa-solid fa-file-pdf color-red"></i>&nbsp;&nbsp;&nbsp;Archived PDF <?php echo ucwords($subfolder); ?></button>
		<button id="sd" type="button" data-id="sd" class="btn btn-secondary" onclick="load_content('<?php echo $organization; ?>','<?php echo $subfolder; ?>','shared_documents')"><i class="fa-solid fa-share-nodes color-blue"></i>&nbsp;&nbsp;&nbsp;Shared Documents</button>
		<button id="mf" type="button" data-id="mf" class="btn btn-secondary" onclick="load_content('<?php echo $organization; ?>','<?php echo $subfolder; ?>','my_files')"><i class="fa-solid fa-file-lines color-orange"></i>&nbsp;&nbsp;&nbsp;My <?php echo ucwords($subfolder); ?> Files</button>
	</div>
	<span class="search">
		<input id="searchitem" type="text" class="form-control" placeholder="Search">
		<span class="search-icon"><i class="fa-sharp fa-solid fa-magnifying-glass"></i></span>
		<span class="search-clear" onclick="clearSearch()"><i class="fa-solid fa-xmark"></i></span>
	</span>
	<span class="add-button"><button class="btn btn-danger" onclick="upload('<?php echo $organization; ?>','<?php echo $subfolder; ?>')">Upload <?php echo ucwords($subfolder); ?></button></span>
</div>
<div class="org-content" id="org_content"></div>
<script>
$(function()
{
	var orgwidth = $('#wrapper').width();
	$('#org_content').width(orgwidth - 295);
	$(window).resize(function()
	{
		var orgwidth = $('#wrapper').width();
		$('#org_content').width(orgwidth - 295);
	})
});
function upload(organization,subfolder)
{
	$('#modaltitle').html("Upload " + subfolder);
	$('#modalicon').html('<i class="fa-solid fa-upload color-dodger"></i>');
	$.post("../Modules/Written_documents/apps/upload.php", { organization: organization, subfolder: subfolder },
	function(data) {
		$('#formmodal_page').html(data);		
		$('#formmodal').show();
	}); 
}
$(function()	
{
	$('#searchitem').keyup(function()
	{
		var organization = sessionStorage.organization;
		var subfolder = sessionStorage.subfolder;
		var page = sessionStorage.page;
		var search = $('#searchitem').val();
		$.post("./Modules/Written_Documents/includes/" + page + "_data.php", { organization: organization, subfolder: subfolder, search: search },
		function(data) {
			$('#org_content').html(data);
			rms_reloaderOff();
		});
	});
	$('#org_content').height($('#wdheader').height() - 190);
	$('#wdheader').resize(function()
	{
		$('#org_content').height($('#wdheader').height() - 190);
	});
	load_content('<?php echo $organization; ?>','<?php echo $subfolder; ?>','memo');
	$("#ap").addClass('btn-info');	
});
function clearSearch()
{
	$('#searchitem').val('');
	var page = sessionStorage.page;
	load_content('<?php echo $organization; ?>','<?php echo $subfolder; ?>',page);
}
function load_content(organization,subfolder,page)
{
	$('#searchitem').val('');
	sessionStorage.setItem("organization", organization);
	sessionStorage.setItem("subfolder", subfolder);
	sessionStorage.setItem("page", page);

	$('#org_content').empty();
	if(page == 'my_files')
	{
		$('.add-button').show();
	} else {
		$('.add-button').hide();
	}
	rms_reloaderOn();
	setTimeout(function()
	{
		$.post("../Modules/Written_Documents/includes/" + page + "_data.php", { organization: organization, subfolder: subfolder },
		function(data) {
			$('#org_content').html(data);
			rms_reloaderOff();
		});
	},1000);

}
</script>