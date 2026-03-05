<?php
include '../../../init.php';
$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);	
require $_SERVER['DOCUMENT_ROOT']."/Modules/Frozen_Dough_Management/class/Class.functions.php";
$function = new FDSFunctions;
$_SESSION['FDS_REPORT_PAGE'] = $_POST['reports'];
$_SESSION['FDS_RECIPIENT_REPORT'] = $_POST['recipient'];
$_SESSION['FDS_BRANCH_REPORT'] = $_POST['branch'];

$selectedcluster = @$_SESSION['FDS_SUMMARY_SELECTEDCLUSTER'];

$datefrom = @$_SESSION['FDS_SUMMARY_DATEFROM'];
$dateto = @$_SESSION['FDS_SUMMARY_DATETO'];

?>
<style>
.subpage-wrapper {margin-top:5px;border:1px solid #aeaeae;background:#fff;}
.tableFixHead {margin-top:5px;background:#fff;}
.tableFixHead  { overflow: auto; height: calc(100vh - 272px); width:100% }
.tableFixHead thead th { position: sticky; top: 0; z-index: 1; background:green; color:#fff }
.tableFixHead table  { border-collapse: collapse;}
.tableFixHead th, .tableFixHead td { font-size:14px; white-space:nowrap } 
.subpage-wrapper {display: flex;gap: 5px;white-space:nowrap;border:1px solid #aeaeae;border-bottom: 3px solid #aeaeae;padding:10px;
background: #fff;min-width:600px;overflow-x:auto;}

.cbx {
    position: relative;
    top: 1px;
    width: 27px;
    height: 27px;
    border: 1px solid #c8ccd4;
    border-radius: 3px;
    vertical-align: middle;
    transition: background 0.1s ease;
    cursor: pointer;
    display: block;
}

.cbx:after {
    content: '';
    position: absolute;
    top: 2px;
    left: 8px;
    width: 7px;
    height: 14px;
    opacity: 0;
    transform: rotate(45deg) scale(0);
    border-right: 2px solid #fff;
    border-bottom: 2px solid #fff;
    transition: all 0.3s ease;
    transition-delay: 0.15s;
}

.lbl {
    margin-left: 5px;
    vertical-align: middle;
    cursor: pointer;
}

input:checked + .cbx {
    border-color: transparent;
    background: #6871f1;
    animation: jelly 0.6s ease;
}

input:checked + .cbx:after {
    opacity: 1;
    transform: rotate(45deg) scale(1);
}

.cntr {
    position: relative;
    display: inline-block;
    margin-right: 0px;
}

@keyframes jelly {
    from {
        transform: scale(1, 1);
    }
    30% {
        transform: scale(1.25, 0.75);
    }
    40% {
        transform: scale(0.75, 1.25);
    }
    50% {
        transform: scale(1.15, 0.85);
    }
    65% {
        transform: scale(0.95, 1.05);
    }
    75% {
        transform: scale(1.05, 0.95);
    }
    to {
        transform: scale(1, 1);
    }
}

.hidden-xs-up {
    display: none !important;
}


</style>

<div class="subpage-wrapper">

	<div class="cntr">
	    <input type="checkbox" id="cbxbranch" class="hidden-xs-up" checked onclick="toggleCheckbox(this)">
	    <label for="cbxbranch" class="cbx" id="cbxbranchlabel"></label>
	</div>
	<label for="cbxbranch">Branch</label>

	<div class="cntr">
	    <input type="checkbox" id="cbxcluster" class="hidden-xs-up" onclick="toggleCheckbox(this)">
	    <label for="cbxcluster" class="cbx" id="cbxclusterlabel"></label>
	</div>
	<label for="cbxcluster">Cluster</label>

	<div class="cntr">
        <input type="checkbox" id="cbxallbranch" class="hidden-xs-up" onclick="toggleCheckbox(this)">
        <label for="cbxallbranch" class="cbx" id="cbxallbranchlabel"></label>
    </div>
    <label for="cbxallbranch">All Branch</label>

	<div id="branchWrapper">
	    <input id="selectedbranch" type="text" list="selectedbranchList" style="width:260px" class="form-control form-control-sm" placeholder="---Select Branch---" value="<?php echo @$selectedbranch ?>" autocomplete="off">
	    <datalist id="selectedbranchList">
	        <?php echo $function->GetBranch($selectedbranch, $db) ?>
	    </datalist>
	</div>
	
	<div id="clusterWrapper" style="display: none;">
	    <input id="selectedcluster" type="text" list="selectedclusterList" style="width:260px" class="form-control form-control-sm" placeholder="---Select Cluster---" value="<?php echo @$selectedcluster ?>" autocomplete="off" disabled>
	    <datalist id="selectedclusterList">
	        <?php echo $function->GetCluster($selectedcluster, $db) ?>
	    </datalist>
	</div>
	
	
	<input type="date" id="datefrom" class="form-control form-control-sm" style="width:150px" placeholder="Date from" value="<?php echo @$datefrom ?>">
	<input type="date" id="dateto" class="form-control form-control-sm" style="width:150px" placeholder="Date to" value="<?php echo @$dateto ?>">
	
	<button id="searchbtn" class="btn btn-info btn-sm" onclick="searchItem()">
		<i class="fa fa-search" aria-hidden="true"></i> Search
	</button>
</div>

<div class="tableFixHead" id="iwd"></div>


<script>


function toggleCheckbox(clickedCheckbox) {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        if (checkbox !== clickedCheckbox) {
            checkbox.checked = false;
        }
    });

    let selectedFilter = clickedCheckbox.checked ? clickedCheckbox.id : null;
    let clusterWrapper = document.getElementById("clusterWrapper");
    let branchWrapper = document.getElementById("branchWrapper");
    let selectedclusterInput = document.getElementById("selectedcluster");
    let selectedbranchInput = document.getElementById("selectedbranch");

    if (selectedFilter === "cbxcluster") {
        clusterWrapper.style.display = "block";
        selectedclusterInput.disabled = false;
        branchWrapper.style.display = "none";
        selectedbranchInput.disabled = true;
        selectedbranchInput.value = "";
    } else if (selectedFilter === "cbxbranch") {
        branchWrapper.style.display = "block";
        selectedbranchInput.disabled = false;
        clusterWrapper.style.display = "none";
        selectedclusterInput.disabled = true;
        selectedclusterInput.value = "";
    } else {
        clusterWrapper.style.display = "none";
        branchWrapper.style.display = "none";
    }
}


function searchItem() {
    let selectedFilters = [];
    let clusterInput = document.getElementById("selectedcluster").value.trim();
    let branchInput = document.getElementById("selectedbranch").value.trim();
    let dateFrom = document.getElementById("datefrom").value;
    let dateTo = document.getElementById("dateto").value;

    if (document.getElementById("cbxbranch").checked) selectedFilters.push("branch");
    if (document.getElementById("cbxcluster").checked) selectedFilters.push("cluster");
    if (document.getElementById("cbxallbranch").checked) selectedFilters.push("allbranch");

    if (selectedFilters.length === 0) {
        app_alert("System Message", "Please select at least one filter: Branch, Cluster, or All Branch.", "warning");
        return;
    }

    if (selectedFilters.includes("cluster") && clusterInput === "") {
        app_alert("System Message", "Please select a Cluster.", "warning");
        return;
    }

    if (selectedFilters.includes("branch") && branchInput === "") {
        app_alert("System Message", "Please select a Branch.", "warning");
        return;
    }

    let fromDate = new Date(dateFrom);
    let toDate = new Date(dateTo);

    if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime())) {
        app_alert("System Message", "Please enter valid dates in both fields.", "warning");
        return;
    }

    if (fromDate > toDate) {
        app_alert("System Message", "Date From must be earlier than Date To.", "warning");
        return;
    }

    if ((toDate - fromDate) / (1000 * 3600 * 24) > 30) {
        app_alert("System Message", "Must be within 31 days maximum.", "warning");
        return;
    }


	rms_reloaderOn("Searching...");

	$.post("./Modules/Frozen_Dough_Management/fds_report/search_processor.php", {
		selectedFilter: JSON.stringify(selectedFilters),
		selectedbranch: branchInput,
		selectedcluster: clusterInput,
		dateFrom: dateFrom,
		dateTo: dateTo
	}, function(response) {
		let data = JSON.parse(response);
		let loadCount = 0;

		function checkIfAllLoaded() {
			loadCount--;
			if (loadCount === 0) {
				rms_reloaderOff();
			}
		}

		if (data.branch_page) {
			loadCount++;
			$("#iwd").load("./Modules/Frozen_Dough_Management/fds_report/" + data.branch_page, {
				selectedbranch: branchInput,
				dateFrom: dateFrom,
				dateTo: dateTo
			}, checkIfAllLoaded);
		}

		if (data.cluster_page) {
			loadCount++;
			$("#iwd").load("./Modules/Frozen_Dough_Management/fds_report/" + data.cluster_page, {
				clusterInput: clusterInput,
				dateFrom: dateFrom,
				dateTo: dateTo
			}, checkIfAllLoaded);
		}

		if (data.allbranch_page) {
			loadCount++;
			$("#iwd").load("./Modules/Frozen_Dough_Management/fds_report/" + data.allbranch_page, {
				selectedallbranch: document.getElementById("cbxallbranch").checked,
				dateFrom: dateFrom,
				dateTo: dateTo
			}, checkIfAllLoaded);
		}

		if (loadCount === 0) {
			rms_reloaderOff();
		}
	});

}

</script>

