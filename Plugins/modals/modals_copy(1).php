<link rel="stylesheet" href="../Plugins/modals/modals.css">
<div class="rms-overlay" id="formodalsm" style="z-index:10000">
	<div class="rms-inner-modal">
		<div class="rms-header-modal">
			<span class="rms-modal-icon" id="formodalsmcon"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="formodalsmtitle">---</span>
			<span class="rms-close-modal" onclick="closeModal('formodalsm')">&times</span>
		</div>
		<div class="rms-content-modal">
			<div id="formodalsm_page"></div>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="headlessmodal">
	<div class="rms-inner-modal">
		<!-- div class="rms-header-modal">
			<span class="rms-modal-icon" id="modalicon"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="modaltitle">---</span>
			<span class="rms-close-modal" onclick="closeModal('formmodal')">&times</span>
		</div -->
		<div class="rms-content-modal">
			<div id="headlessmodal_page"></div>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="formmodal">
	<div class="rms-inner-modal">
		<div class="rms-header-modal">
			<span class="rms-modal-icon" id="modalicon"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="modaltitle">---</span>
			<span class="rms-close-modal" onclick="closeModal('formmodal')">&times</span>
		</div>
		<div class="rms-content-modal">
			<div id="formmodal_page"></div>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="pdfviewer">
	<div class="rms-inner-modal">
		<div class="rms-header-modal">
			<span class="rms-modal-icon" id="modaliconPDF"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="modaltitlePDF">---</span>
			<span class="rms-close-modal" onclick="closeModal('pdfviewer')">&times</span>
		</div>
		<div class="rms-content-modal">
			<div id="pdfviewer_page"></div>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="printPDF">
	<div class="rms-inner-modal">
		<div class="rms-header-modal">
			<span class="rms-modal-icon" id="modaliconPDF"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="modaltitlePDF">---</span>
			<span class="rms-close-modal" onclick="closeModal('printPDF')">&times</span>
		</div>
		<div class="rms-content-modal">
			<iframe id="printPDF_page" style="width:9in; height:5.7in"></iframe>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="genxcel">
	<div class="rms-inner-modal">
		<div class="rms-header-modal">
			<span class="rms-modal-icon" id="modaliconPDF"><i class="fa-brands fa-app-store-ios color-dodger"></i></span>
			<span class="rms-modal-title" id="modaltitlePDF">---</span>
			<span class="rms-close-modal" onclick="closeModal('genxcel')">&times</span>
		</div>
		<div class="rms-content-modal">
			<iframe id="genxcel_page" style="width:8in; height:5in"></iframe>
		</div>
		<!-- div class="rms-footer-modal" id="rmsfooter">footer</div -->
	</div>	
</div>
<div class="rms-overlay" id="reportxcl">
	<div class="rms-inner-modal">
		<div class="rms-content-modal" style="width:3in; height:2.5in;text-align:center">
			<br>
			<p>Please keep this window open while the file is being generated. Feel free to close it once the download of the file has started.</p>
			<button class="btn btn-primary" onclick="closeModal('reportxcl')">Close</button>
			<iframe id="reportxcl_page"></iframe>
		</div>		
	</div>	
</div>
<script>
function closeModal(params)
{
	$( '#' + params ).fadeOut();
//	$( '#' + params + "_page").empty();
}
</script>