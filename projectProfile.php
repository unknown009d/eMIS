<?php
$pid = $_GET['pid'];
$cpage = "$pid";
include 'template/header.php';
include 'template/pagination.php';


// Selecting all the details...
$info_all = selectQ($conn, "SELECT * FROM project_all WHERE p_id = ?", [$pid]);
if(count($info_all) <= 0) {
    echo "
    <script>
        alert('Project doesn\'t exist');
        location.href = 'projectProfileList';
    </script>
    ";
}
$dtype = selectQ($conn, "SELECT * FROM tbl_docment_type");

?>

<div class="col content" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-server pe-3"></i>
        Project Profile
        <?= "(" . $pid . ")" ?>
    </h2>
    <ul class="nav nav-tabs mb-5 px-5">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#details">Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#docs">Documents</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#logs" onclick="logsShowLatest()">Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#comms">Communication</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#testreport">Test Report</a>
        </li>
    </ul>
    <div class="tab-content">
        <!-- Basic Details -->
        <form id="details" class="tab-pane fade show active">
            <!-- project profile Generation Section -->
            <!-- Project Profile breif section -->
            <div class="card m-auto dcardform">
                <div class="card-header">
                    <h5 class="card-title p-2 m-0 ps-0">Basic Details</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column mb-3">
                        <div class="form-check form-switch form-check-reverse priorityhigh">
                            <input class="form-check-input" 
                                type="checkbox" role="switch" 
                                id="priority" <?= $info_all[0]['priority'] ? "checked" : "" ?>
                                <?= $info_all[0]['certificate'] != NULL ? 'disabled' : '' ?>
                                >
                            <label class="form-check-label" for="priority">High Priority</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="srno" class="form-label">
                                Service&nbsp;Request&nbsp;No.
                            </label>
                            <input type="text" class="form-control" id="srno" placeholder="eg. IT/06-23/006" value="<?= $info_all[0]['sr_no'] ?>" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="projectCode" class="form-label">
                                Project Code
                            </label>
                            <input type="text" class="form-control fw-bold text-success" id="projectCode" placeholder="eg. PP/06-23/006" value="<?= $info_all[0]['p_id'] ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="projectURL" class="form-label">
                                URL
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="projectURL" placeholder="eg. https://www.stqc.gov.in/" value="<?= $info_all[0]['url'] ?>">
                                <a href="<?= $info_all[0]['url'] ?>" class="btn btn-primary" target="_blank">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="billno" class="form-label">
                                Bill No.
                            </label>
                            <input type="text" class="form-control" id="billno" value="<?= $info_all[0]['bill_no'] ?>" readonly>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="mpr" class="form-label">
                                MPR
                            </label>
                            <input type="text" class="form-control" value="<?= $info_all[0]['mpr'] ?>" id="mpr" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="projectStatus" class="form-label">
                                Status
                            </label>
                            <input type="text" class="form-control" id="projectStatus" placeholder="Project status here..." readonly>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Client Details -->
            <div class="card m-auto dcardform my-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title p-2 m-0 ps-0">Client Details</h5>
                    <i class="bi bi-info-circle text-primary" title="Double click in the text fields to update the client values although You cannot change the customer realated to this project." onclick="alert('Double click in the text fields to update the client values although You cannot change the customer realated to this project.')">
                    </i>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="clientName" class="form-label">
                                Client Name
                            </label>
                            <input type="text" class="form-control" id="clientName" placeholder="eg. Jhon Doe" value="<?= $info_all[0]['c_name'] ?>" ondblclick="removeReadonly(this)" onblur="revertReadonly(this)" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clientAddress" class="form-label">
                                Address
                            </label>
                            <input type="text" class="form-control" id="clientAddress" placeholder="eg. Agartala" value="<?= $info_all[0]['c_addr'] ?>" ondblclick="removeReadonly(this)" onblur="revertReadonly(this)" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clientType" class="form-label">
                                Client Type
                            </label>
                            <input type="text" class="form-control" id="clientType" ondblclick="showMessage('<i class=\'bi bi-exclamation-triangle pe-1\'></i> Field is not editable', 'warning')" placeholder="eg. Government" value="<?= $info_all[0]['ct_desc'] ?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="clientGST" class="form-label">
                                GST
                            </label>
                            <input type="text" class="form-control" id="clientGST" placeholder="eg. 0717USA12345NF1" value="<?= $info_all[0]['c_gst'] ?>" ondblclick="removeReadonly(this)" onblur="revertReadonly(this)" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clientMail" class="form-label">
                                Email
                            </label>
                            <input type="text" class="form-control" id="clientMail" placeholder="eg. your.mail@example.com" value="<?= $info_all[0]['c_email'] ?>" ondblclick="removeReadonly(this)" onblur="revertReadonly(this)" readonly>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="clientPhone" class="form-label">
                                Phone
                            </label>
                            <input type="tel" class="form-control" id="clientPhone" maxlength="15" placeholder="eg. 8787 598 129" value="<?= $info_all[0]['c_phn'] ?>" ondblclick="removeReadonly(this)" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- WIM Details -->
            <div class="card m-auto dcardform my-4">
                <div class="card-header">
                    <h5 class="card-title p-2 m-0 ps-0">WIM Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <label for="wimName" class="form-label">
                                Name
                            </label>
                            <input type="text" class="form-control" id="wimName" placeholder="eg. Jhon Doe" value="<?= $info_all[0]['wim_name'] ?>">
                        </div>
                        <div class="col">
                            <label for="wimDesignation" class="form-label">
                                Designation
                            </label>
                            <input type="text" class="form-control" id="wimDesignation" placeholder="eg. Gazetted Officer" value="<?= $info_all[0]['wim_desg'] ?>">
                        </div>
                        <div class="col">
                            <label for="wimEmail" class="form-label">
                                Email
                            </label>
                            <input type="text" class="form-control" id="wimEmail" placeholder="eg. your.mail@example.com" value='<?= $info_all[0]['wim_email'] ?>'>
                        </div>
                        <div class="col">
                            <label for="wimPhone" class="form-label">
                                Phone
                            </label>
                            <input type="tel" class="form-control" id="wimPhone" placeholder="eg. 8787 598 129" maxlength="10" value="<?= $info_all[0]['wim_phone'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Third Party Vendor Details -->
            <div class="card m-auto dcardform my-4">
                <div class="card-header">
                    <h5 class="card-title p-2 m-0 ps-0">
                        <div class="row">
                            <div class="col d-flex">
                                <div class="form-check form-switch form-check-reverse">
                                    <input class="form-check-input" type="checkbox" role="switch" id="tpv">
                                    <label class="form-check-label" for="tpv"> Third Party Vendor / Agency /
                                        Developer</label>
                                </div>
                            </div>
                        </div>
                    </h5>

                </div>
                <div class="card-body" id="tpvCheck">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tpvName" class="form-label">
                                Name
                            </label>
                            <input type="text" class="form-control" id="tpvName" placeholder="eg. Jhon Doe" value="<?= $info_all[0]['v_name'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tpvAddress" class="form-label">
                                Address
                            </label>
                            <input type="text" class="form-control" id="tpvAddress" placeholder="eg. Agartala" value="<?= $info_all[0]['v_address'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="assignedWork" class="form-label">
                                Assigned work
                            </label>
                            <input type="text" class="form-control" id="assignedWork" placeholder="eg. Website Testing" value="<?= $info_all[0]['v_task_assign'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tpvPhone" class="form-label">
                                Phone Number
                            </label>
                            <input type="tel" class="form-control" id="tpvPhone" maxlength="10" placeholder="eg. +91 5238 492 523" value="<?= $info_all[0]['v_phone'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tpvEmail" class="form-label">
                                Email
                            </label>
                            <input type="text" class="form-control" id="tpvEmail" placeholder="eg. something@example.com" value="<?= $info_all[0]['v_email'] ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tpvGST" class="form-label">
                                GST No.
                            </label>
                            <input type="text" class="form-control" id="tpvGST" placeholder="eg. 829EIC0238CU8" value="<?= $info_all[0]['v_gst'] ?>">
                        </div>
                    </div>
                </div>
            </div>


            <!-- Third Party Vendor Details -->
            <div class="card m-auto dcardform my-4">
                <div class="card-header">
                    <h5 class="card-title p-2 m-0 ps-0">
                        <div class="row">
                            <div class="col d-flex">
                                <div class="form-check form-switch form-check-reverse">
                                    <input class="form-check-input" type="checkbox" role="switch" id="crt">
                                    <label class="form-check-label" for="crt">
                                        Certificate Issued
                                    </label>
                                </div>
                            </div>
                        </div>
                    </h5>

                </div>
                <div class="card-body" id="crtCheck">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="crtName" class="form-label">
                                Certificate No.
                            </label>
                            <input type="text" class="form-control" id="crtName" placeholder="eg. 675839CSJUQ" value="<?= $info_all[0]['certificate'] ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Card -->
            <div class="m-auto dcardform my-4 savechangesarea">
                <!-- Remarks Input -->
                <div class="row mt-2">
                    <!-- Submit Button -->
                    <div class="row w-100 d-flex justify-content-end mt-3 gap-2">
                        <button type="submit" class="btn btn-success w-auto btnMain">
                            Save Changes <i class="bi bi-save ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Documents Details -->
        <form class="card m-auto dcardform my-4 mb-5 tab-pane fade" id="docs" enctype="multipart/form-data">
            <div class="card-header">
                <h5 class="card-title p-2 m-0 ps-0">Documents Upload</h5>
            </div>
            <div class="card-body">
                <div class="row mt-2 mb-5">
                    <div class="col-3">
                        <label for="docType" class="form-label">Document Type</label>
                        <select id="docType" class="form-select text-start" required>
                            <?php foreach ($dtype as $data) : ?>
                                <option value="<?= $data['d_code'] ?>" data-file-size="<?= $data['d_size'] ?>">
                                    <?= $data['d_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="fileInput" class="form-label">Document</label>
                        <input type="file" class="form-control" id="fileInput" name="file" onchange="checkFileSize(this,$('docType').options[$('docType').selectedIndex].getAttribute('data-file-size'))" accept=".xls, .xlsx, .doc, .docx, .pdf" required>
                    </div>
                    <div class="col-5">
                        <label for="fileNameInput" class="form-label" title="Rename the default value for this selected document file">Rename</label>
                        <input type="text" class="form-control" name="fileName" placeholder="Rename the file name" id="fileNameInput" required>
                    </div>
                    <div class="col">
                        <label for="btnUploadDoc" class="form-label" style="opacity: 0;">_</label>
                        <button type="submit" id="btnUploadDoc" class="btn btn-success d-block" title="Upload this file">
                            <i class="bi bi-upload"></i>
                        </button>
                    </div>
                </div>

                <table id="filesTable" class="table table-bordered normal">
                    <thead>
                        <tr class='table-light'>
                            <th id="typechange" class="hoverable">Type <span id="typechangeicon"><i class='bi bi-arrow-down-up'></i></span></th>
                            <th>File Name</th>
                            <th id="modDate" class="hoverable">Modified Date <span id="modDateicon"></span></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="filesBody">
                        <!-- Table content will be added dynamically -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-dark mt-4" type="button" data-bs-toggle="modal" data-bs-target="#archiveModal">
                        <i class="bi bi-file-zip-fill pe-1"></i> Archives <span id="btnShowArchiveSpan"></span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Logs Details -->
        <div class="card m-auto dcardform my-4 tab-pane fade" id='logs'>
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title p-2 m-0 ps-0">Logs</h5>
            </div>
            <div class="card-body">
                <div class="row d-flex row justify-content-end">
                    <div class="col-md-4">
                        <input type="text" id="searchInput" class="form-control mb-3" oninput="highlightText(this)" placeholder="Type here to search...">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div id="fLogs"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Communication Details -->
        <div class="tab-pane fade" id="comms">
            <form class="card m-auto dcardform my-4" id="commsLogs">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title p-2 m-0 ps-0">Communication Logs</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="commSender" class="form-label">
                                Sender
                            </label>
                            <input type="text" class="form-control" id="commSender" placeholder="eg. sen@eg.in, sender name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="commReceiver" class="form-label">
                                Receiver
                            </label>
                            <input type="text" class="form-control" id="commReceiver" placeholder="eg. rec@eg.in, receiver name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="commDate" class="form-label">
                                Date
                            </label>
                            <input type="datetime-local" class="form-control" id="commDate" tabindex="-1">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="commCategory" class="form-label" title="Communication Category">
                                Category
                            </label>
                            <input type="text" class="form-control" id="commCategory" placeholder="eg. General, Payment, Bill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="commSubject" class="form-label">
                                Subject
                            </label>
                            <input type="text" class="form-control" id="commSubject" placeholder="Write your subject here..." required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="commRemarks" class="form-label">
                                Remarks
                            </label>
                            <input type="text" class="form-control" id="commRemarks" placeholder="Write your remarks here...">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col d-flex justify-content-end">
                            <button class="btn btn-success mt-3" type="submit">
                                Save <i class="bi bi-save ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="m-auto dcardform mt-5">
                <div class="row d-flex row justify-content-end">
                    <div class="col-md-4">
                        <input type="text" id="searchComms" class="form-control " placeholder="Type here to search (sender or subject)...">
                    </div>
                </div>
            </div>

            <table id="commsTable" class="table table-bordered normal m-auto dcardform my-4">
                <thead>
                    <tr class='table-light'>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th class='hoverable' onclick="sortCommList()">
                            Date/Time
                            <span id="typechangeicon"><i class='bi bi-arrow-down-up'></i></span>
                        </th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Remarks</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="commsBody"></tbody>
            </table>

            <div class="d-flex justify-content-end mb-4 mt-2 m-auto dcardform">
                <button class="btn btn-success" id="showMoreButton">Show more <i class="bi bi-chevron-down"></i></button>
            </div>
        </div>


        <!-- Test report Details -->
        <div class="m-auto dcardform my-4 mb-5 tab-pane fade" id="testreport">

            <?php if ($info_all[0]['certificate'] == NULL) : ?>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title p-2 m-0 ps-0">Test Report</h5>
                    </div>
                    <div class="card-body">
                        <form id="testUploadForm" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="doucument" class="form-label">Tester</label>
                                        <input type="text" class="form-control" id="doucument" name="test">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cycle" class="form-label">Cycle</label>
                                        <input type="text" list='lstCycle' class="form-control" id="cycle" name="cycle" autocomplete="off">
                                        <datalist id="lstCycle">
                                            <option value="1st"></option>
                                            <option value="2nd"></option>
                                            <option value="3rd"></option>
                                        </datalist>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status" class="form-label">Status</label>
                                        <input type="text" class="form-control" id="status" name="status">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="complete_date" class="form-label">Complete Date</label>
                                        <input type="date" class="form-control" id="complete_date" name="complete_date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="released_date" class="form-label">Released Date</label>
                                        <input type="date" class="form-control" id="released_date" name="released_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="maj" class="form-label">Major</label>
                                        <input type="number" min=0 class="form-control" id="maj" name="maj">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="mec" class="form-label">Medium</label>
                                        <input type="number" min=0 class="form-control" id="mec" name="mec">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="min" class="form-label">Minimum</label>
                                        <input type="number" min=0 class="form-control" id="min" name="min">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="tot" class="form-label">Total</label>
                                        <input type="number" min=0 class="form-control" id="tot" name="tot">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="remarks" class="form-label">Remarks</label>
                                        <input type="text" class="form-control" id="remarks" name="remarks">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="testfile" class="form-label">Test File</label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" id="testfile" onchange="checkFileSize(this,10240)" accept=".xls, .xlsx, .doc, .docx, .pdf" name="file" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="rename" class="form-label">Rename</label>
                                        <input type="text" class="form-control" id="rename" name="rename">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mt-4 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-success">
                                            Save Report <i class="bi bi-cloud-upload ps-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <div class="m-auto mt-5 row d-flex justify-content-end">
                <div class="col-md-4 d-flex justify-content-end">
                    <input type="text" id="searchReports" class="form-control" title="Search for Report Number or Test Name" placeholder="Type here to search (Report No or Test Name) ...">
                </div>
            </div>

            <div class="table-responsive mt-5">
                <table id="testReportTable" class="table table-bordered">
                    <thead>
                        <tr class='table-light'>
                            <th>Report&nbsp;No.</th>
                            <th>Test</th>
                            <th>Cycle</th>
                            <th>Status</th>
                            <th>Start&nbsp;Date</th>
                            <th>Complete&nbsp;Date</th>
                            <th>Released&nbsp;Date</th>
                            <th>Mec</th>
                            <th>Min</th>
                            <th>Maj</th>
                            <th>Total</th>
                            <th>Remarks</th>
                            <th width='50px'></th>
                            <th width='50px'></th>
                        </tr>
                    </thead>
                    <tbody id="testReportBody">
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="archiveModal">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-file-zip-fill pe-1"></i> Archives</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <table id="archiveFilesTable" class="table table-bordered normal">
                    <thead>
                        <tr class='table-light'>
                            <th>Type</th>
                            <th>File Name</th>
                            <th>Size</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="archiveFilesBody">
                        <!-- Table content will be added dynamically -->
                    </tbody>
                </table>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" onclick="confirm('Are you sure want to restore all the files ? ') ? restoreArchive(null, true) : null" title="Restore all files from archive">
                    <i class='bi bi-arrow-counterclockwise pe-1'></i> Restore All
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteAllArchive()" title="Delete all files from archive">
                    <i class='bi bi-trash-fill pe-1'></i> Delete All
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const updateProjectStatus = async () => {
        try {
            const response = await fetch(`api/project_latest_status.php?pid=<?= $pid ?>`);
            const data = await response.json();
            if (data.success) {
                $('projectStatus').value = data.message;
                $('projectStatus').title = data.message;
                $('projectStatus').style.backgroundColor = data.color;
            } else {
                showMessage(data.message, "warning")
                console.error('Error:', data.message);
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    };

    // Call the function to initiate the GET request
    updateProjectStatus();

    const populateTable = async (dataArray = [], searchText = "") => {
        let filteredData = dataArray;

        // If a search text is provided, filter the dataArray
        if (searchText.trim() !== "") {
            filteredData = dataArray.filter((rowData) => {
                // Assuming you want to search through all properties of each object
                const rowValues = Object.values(rowData);
                return rowValues.some((value) =>
                    value.toString().toLowerCase().includes(searchText.toLowerCase())
                );
            });
        }

        const tableBody = document.getElementById("testReportBody");

        if (filteredData.length === 0) {
            try {
                // Fetch data from the API
                const response = await fetch(`api/testrep_list.php?pid=<?= $pid ?>&search=${encodeURIComponent(searchText)}`);
                const data = await response.json();
                if (data.success) {
                    // If the API call was successful, populate the table
                    populateTableRows(data.data);
                } else {
                    console.error("Error fetching data: ", data.message);
                }
            } catch (error) {
                console.error("Error fetching data: ", error);
            }
        } else {
            // If the array is not empty, populate the table with the provided data
            populateTableRows(filteredData);
        }
    };

    const populateTableRows = (data) => {
        const tableBody = document.getElementById("testReportBody");

        // Clear existing table rows
        tableBody.innerHTML = "";

        // Check if the data is an array of objects or an array of arrays
        if (data.length > 0) {
            // If the data is an array of objects, extract values and create rows
            data.forEach((rowData) => {
                const row = document.createElement("tr");

                // Assuming the properties of each object represent the <td> values in the table
                Object.values(rowData).forEach((cellData, cnt) => {
                    const cell = document.createElement("td");
                    if (cnt == 0) {
                        cell.textContent = cellData;
                        cell.classList.add("fw-bold")
                    } else if (cnt == Object.keys(rowData).length - 2) {
                        const btnDoc = document.createElement("a");
                        btnDoc.href = cellData;
                        btnDoc.target = "_blank";
                        btnDoc.rel = "noopener noreferrer";
                        const btnDocIcon = document.createElement("i");
                        btnDocIcon.classList.add("bi");
                        btnDocIcon.classList.add("bi-box-arrow-up-right");
                        btnDoc.classList.add("btn")
                        btnDoc.classList.add("btn-sm")
                        btnDoc.classList.add("btn-success");
                        btnDoc.appendChild(btnDocIcon)
                        cell.appendChild(btnDoc);
                    } else if (cnt == Object.keys(rowData).length - 1) {
                        const btnDoc = document.createElement("button");
                        btnDoc.onclick = async () => {
                            if (confirm("Are you sure want to delete ?")) {
                                await fetch("api/testrep_del.php", {
                                        method: "POST",
                                        body: JSON.stringify({
                                            id: cellData,
                                        }),
                                    })
                                    .then((response) => response.json())
                                    .then((data) => {
                                        if (data.success) {
                                            updateProjectStatus();
                                            showMessage(Object.values(rowData)[1] + " - deleted successfully", "success");
                                            sendLogs("<?= $pid ?>", "[" + Object.values(rowData)[0] + " | " + Object.values(rowData)[1] + "] - test report deleted successfully");
                                            showLogs();
                                        } else {
                                            showMessage(data.message, "warning");
                                        }
                                    })
                                    .catch((error) => {
                                        console.error("Error:", error);
                                    });
                                populateTable();
                            }
                        };
                        btnDoc.type = "button";
                        const btnDocIcon = document.createElement("i");
                        btnDocIcon.classList.add("bi");
                        btnDocIcon.classList.add("bi-trash-fill");
                        btnDoc.classList.add("btn")
                        btnDoc.classList.add("btn-sm")
                        btnDoc.classList.add("btn-danger");
                        btnDoc.appendChild(btnDocIcon)
                        cell.appendChild(btnDoc);
                    } else {
                        cell.textContent = truncateText(cellData, 10);
                        cell.title = cellData;
                    }
                    row.appendChild(cell);
                });

                tableBody.appendChild(row);
            });
        } else {
            tableBody.appendChild(noContent(14, "No Reports Released..."));
        }
    };

    // Call the function with an empty array to fetch data from the API
    populateTable();


    <?php if ($info_all[0]['certificate'] == NULL) : ?>
        $('testfile').addEventListener('change', (e) => {
            var fileName = $('testfile').files[0].name.split('.')[0];
            $('rename').value = fileName.split(' ').join('');
        });

        const calculateTOT = () => {
            const mecValue = parseFloat(mec.value);
            const minValue = parseFloat(min.value);
            const majValue = parseFloat(maj.value);


            const validMecValue = isValidNumber(mecValue) ? mecValue : 0;
            const validMinValue = isValidNumber(minValue) ? minValue : 0;
            const validMajValue = isValidNumber(majValue) ? majValue : 0;

            tot.value = validMecValue + validMinValue + validMajValue;
        }
        let mec = document.querySelector("#testreport #mec");
        let min = document.querySelector("#testreport #min");
        let maj = document.querySelector("#testreport #maj");
        let tot = document.querySelector("#testreport #tot");

        mec.addEventListener('input', calculateTOT);
        min.addEventListener('input', calculateTOT);
        maj.addEventListener('input', calculateTOT);
        tot.readOnly = true;

        $('testUploadForm').addEventListener("submit", async e => {
            e.preventDefault();
            const formData = new FormData($('testUploadForm'));
            formData.append("page", "<?= $pid ?>");
            await fetch("api/testrep_gen.php", {
                    // Replace with the correct PHP script URL
                    method: "POST",
                    body: formData
                }).then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        showMessage("<i class='bi bi-check2-circle pe-1'></i> Test Report Uploaded", "success");
                        populateTable(data.data);
                        $('testUploadForm').reset();
                        let logmsg = "[" + data.rno + " | " + formData.get('test') + "] test report is successfully uploaded";
                        sendLogs("<?= $pid ?>", logmsg);
                        showLogs();
                    } else {
                        showMessage("Error uploading test report", "warning");
                        console.error(data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error: ", error);
                });

            updateProjectStatus();

        });

    <?php endif; ?>


    $('searchReports').addEventListener("input", e => {
        if (e.target.value.length >= 3) {
            populateTable([], e.target.value);
        } else if (e.target.value.length == 0) {
            populateTable();
        }
    });
    $('searchReports').addEventListener("keydown", e => {
        if (e.target.value.length < 3 && e.key === 'Enter') {
            showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> Please enter atleast 3 letters to search...", "warning");
        }
    });

    function highlightText(element) {
        const logContentElement = document.getElementById('fLogs');
        const searchInput = element.value;
        const logContent = logContentElement.textContent;

        if (searchInput.trim() === '') {
            // If search input is empty, remove any existing highlights
            logContentElement.innerHTML = logContent;
            return;
        }

        const regex = new RegExp(searchInput, 'gi');
        const highlightedContent = logContent.replace(regex, match => `<span class="highlight">${match}</span>`);
        logContentElement.innerHTML = highlightedContent;

        // Scroll to the first occurrence of the matched text
        const firstHighlightedElement = logContentElement.querySelector('.highlight');
        if (firstHighlightedElement) {
            const highlightTop = firstHighlightedElement.offsetTop;
            logContentElement.scrollTop = highlightTop - logContentElement.offsetTop;
        }
    }

    updateDate('commDate');

    const logsShowLatest = (logs = 'fLogs') => {
        showLogs();
        $(logs).scrollTop = $(logs).scrollHeight;
    };

    // Logs Functionality
    const showLogs = async () => {
        await fetch("api/logs_show.php", {
                // Replace with the correct PHP script URL
                method: "POST",
                body: JSON.stringify({
                    "page": "<?= $pid ?>"
                }),
            }).then((response) => response.json())
            .then((data) => {
                $("fLogs").textContent = data.message;
                if (!data.success) {
                    showMessage("Error retriving the data from the logs", "warning");
                    console.error(data.message);
                }
                $("fLogs").scrollTop = $("fLogs").scrollHeight;
            })
            .catch((error) => {
                console.error("Error: ", error);
            });
        $("fLogs").scrollTop = $("fLogs").scrollHeight;
    };
    showLogs();


    const removeReadonly = e => {
        showMessage("Content editable enabled...", "success");
        e.readOnly = false;
        e.focus();
    };

    const revertReadonly = e => {
        if (e.readOnly == false) {
            showMessage("Content editable disabled...", "danger");
            e.readOnly = true;
        }
    };

    $('fileInput').addEventListener("change", () => {
        if ($('fileInput').files[0] == undefined || $('fileInput').files[0] == null || $('fileInput').files[0].length <= 0) {
            $('fileNameInput').value = "";
            return;
        }
        $('fileNameInput').value = $('fileInput').files[0].name.split('.')[0].replace(/\s/g, '');
    });

    $('typechange').addEventListener("click", () => {
        getfiles(localStorage.getItem('sort'), localStorage.getItem('sorttype') == 1 ? 0 : 1);
    });

    $('modDate').addEventListener("click", () => {
        getfiles(localStorage.getItem('sort') == 1 ? 0 : 1, localStorage.getItem('sorttype'));
    });


    // Upload Docs
    const form = document.getElementById("docs");

    form.addEventListener("submit", function(e) {
        e.preventDefault();

        const fileInput = document.getElementById("fileInput");
        const fileNameInput = document.getElementById("fileNameInput");
        const file = fileInput.files[0];
        const newFileName = fileNameInput.value.trim();

        if (file && newFileName) {
            const formData = new FormData();
            formData.append("file", file);
            formData.append("newFileName", newFileName);
            formData.append("page", "<?= $pid ?>");
            formData.append("type", $('docType').value);

            fetch("api/upload_docs.php", {
                    // Replace with the correct PHP script URL
                    method: "POST",
                    body: formData,
                }).then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        showMessage(file.name + " - " + data.message, "success");
                        sendLogs("<?= $pid ?>", data.filename + " - document uploaded to the server");
                        showLogs();
                    } else {
                        showMessage(file.name + " - " + data.message, "danger");
                    }
                    getfiles(localStorage.getItem("sort"), localStorage.getItem("sorttype"));
                    $('docs').reset();
                })
                .catch((error) => {
                    console.error("Error:", error);
                    // Handle any error that occurred during the upload
                });
        }
    });

    if (!localStorage.getItem("sort") || !localStorage.getItem("sorttype")) {
        // setting up global states if those doesn't exist
        localStorage.setItem("sort", 0);
        localStorage.setItem("sorttype", 1);
    }

    const getfiles = async (page = 1, type = 1, table = "filesTable", tbody = "filesBody") => {

        await fetch("api/find_docs.php?sort=" + page + "&type=" + type + "&pid=<?= $pid ?>")
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const filesTable = document.getElementById(table);
                    const filesBody = document.getElementById(tbody);
                    filesBody.innerHTML = "";

                    if (data.files.length <= 0) {
                        // If there is no data, display "No data" in the table
                        const noDataRow = document.createElement("tr");
                        const noDataCell = document.createElement("td");
                        noDataCell.innerHTML = `
                        <div class="alert alert-warning text-center m-0">
                            <i class="bi bi-exclamation-triangle d-block" style="font-size: 2rem;"></i> 
                            Currently, there are no documents available.
                        </div>`;
                        noDataCell.colSpan = 4;
                        noDataRow.appendChild(noDataCell);
                        filesBody.appendChild(noDataRow);
                    } else {
                        if (data.sorttype == "1") {
                            $('modDateicon').innerHTML = '';
                        } else {
                            $('modDateicon').innerHTML = '<i class="bi bi-arrow-down-up"></i>';
                        }


                        // Storing the states...
                        localStorage.setItem("sort", data.sort);
                        localStorage.setItem("sorttype", data.sorttype);


                        data.files.forEach((file) => {

                            // Create a new row for each file
                            const row = document.createElement("tr");

                            // Type column
                            const typeCell = document.createElement("td");
                            const filetype = document.createElement("span");
                            filetype.textContent = file.type;

                            typeCell.appendChild(filetype);
                            row.appendChild(typeCell);

                            // File Name column
                            const fileNameCell = document.createElement("td");
                            const fileLink = document.createElement("a");
                            fileLink.href = file.path;
                            fileLink.textContent = file.name;
                            fileLink.target = "_blank";
                            fileNameCell.appendChild(fileLink);
                            row.appendChild(fileNameCell);


                            // Modified Date column
                            const modifiedDateCell = document.createElement("td");
                            const fileModified = document.createElement("span");
                            fileModified.textContent = fdateeasy(file.modifiedDate);
                            modifiedDateCell.appendChild(fileModified);
                            row.appendChild(modifiedDateCell);

                            // Action column
                            const actionCell = document.createElement("td");
                            const deleteButton = document.createElement("button");
                            deleteButton.innerHTML = "<i class='bi bi-trash-fill'></i>";
                            deleteButton.type = "button";
                            deleteButton.classList.add("btn");
                            deleteButton.classList.add("btn-danger");
                            deleteButton.classList.add("btn-sm");
                            deleteButton.title = 'Delete ' + file.name;
                            deleteButton.onclick = function() {
                                if (confirm("Are you sure want to delete " + file.name + " ?")) {
                                    deleteDocs(file.name);
                                }
                                getfiles();
                            };

                            actionCell.width = '50px';
                            actionCell.appendChild(deleteButton);
                            row.appendChild(actionCell);

                            // Add the row to the table body
                            filesBody.appendChild(row);
                        });
                    }
                } else {
                    console.error("Failed to fetch files:", data.error);
                }
            })
            .catch((error) => console.error("An error occurred:", error));
    };
    getfiles(localStorage.getItem("sort"), localStorage.getItem("sorttype"));


    const getArchive = async () => {

        await fetch("api/find_archive.php?&pid=<?= $pid ?>")
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    data = data.message;
                    const filesTable = document.getElementById('archiveFilesTable');
                    const filesBody = document.getElementById('archiveFilesBody');
                    filesBody.innerHTML = "";
                    if (data.length <= 0) {
                        // If there is no data, display "No data" in the table
                        const noDataRow = document.createElement("tr");
                        const noDataCell = document.createElement("td");
                        noDataCell.innerHTML = `
                        <div class="alert alert-warning text-center m-0">
                            <i class="bi bi-exclamation-triangle d-block" style="font-size: 2rem;"></i> 
                            Currently, there are no documents available.
                        </div>`;
                        noDataCell.colSpan = 4;
                        noDataRow.appendChild(noDataCell);
                        filesBody.appendChild(noDataRow);
                    } else {
                        data.forEach((file, count) => {

                            // Create a new row for each file
                            const row = document.createElement("tr");

                            // Type column
                            const typeCell = document.createElement("td");
                            const filetype = document.createElement("span");
                            filetype.textContent = file.type;

                            typeCell.appendChild(filetype);
                            row.appendChild(typeCell);

                            // File Name column
                            const fileNameCell = document.createElement("td");
                            const fileLink = document.createElement("p");
                            fileLink.textContent = file.name;
                            fileNameCell.appendChild(fileLink);
                            row.appendChild(fileNameCell);

                            // File Name column
                            const fileSizeCell = document.createElement("td");
                            const filesize = document.createElement("p");
                            filesize.textContent = file.size;
                            fileSizeCell.appendChild(filesize);
                            row.appendChild(fileSizeCell);

                            // Action column
                            const actionCell = document.createElement("td");
                            const inpGroup = document.createElement("div");
                            inpGroup.classList.add('input-group');
                            const deleteButton = document.createElement("button");
                            deleteButton.innerHTML = "<i class='bi bi-trash-fill'></i>";
                            deleteButton.type = "button";
                            deleteButton.classList.add("btn");
                            deleteButton.classList.add("btn-danger");
                            deleteButton.classList.add("btn-sm");
                            deleteButton.onclick = () => {
                                if (confirm("Are you sure want to permanently delete " + file.name + " ?")) {
                                    deleteArchive(file.name);
                                }
                                getArchive();
                            };

                            const restoreButton = document.createElement("button");
                            restoreButton.innerHTML = "<i class='bi bi-arrow-counterclockwise'></i>";
                            restoreButton.type = "button";
                            restoreButton.classList.add("btn");
                            restoreButton.classList.add("btn-warning");
                            restoreButton.classList.add("btn-sm");
                            restoreButton.onclick = () => {
                                if (confirm("Are you sure want to restore " + file.name + " ?")) {
                                    restoreArchive(file.name);
                                }
                            };


                            actionCell.width = '80px';
                            inpGroup.appendChild(restoreButton);
                            inpGroup.appendChild(deleteButton);
                            actionCell.appendChild(inpGroup);
                            row.appendChild(actionCell);

                            // Add the row to the table body
                            filesBody.appendChild(row);
                        });
                    }

                } else {
                    console.error("Failed to fetch archive:", data.message);
                }
            })
            .catch((error) => console.error("An error occurred while fetching archive:", error));
    };
    getArchive();

    const deleteDocs = async filename => {
        await fetch("api/delete_docs.php", {
                // Replace with the correct PHP script URL
                method: "POST",
                body: JSON.stringify({
                    page: "<?= $pid ?>",
                    filename: filename
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showMessage(filename + " - " + data.message, "success");
                    sendLogs("<?= $pid ?>", filename + " - document moved to archive");
                    showLogs();
                } else {
                    showMessage(data.message, "warning");
                }
                getfiles();
                getArchive();
                // Handle the response from the API
            })
            .catch((error) => {
                console.error("Error:", error);
                // Handle any error that occurred during the upload
            });
        countArchive();
    };

    const restoreArchive = async (filename, all = false) => {
        await fetch("api/restore_archive.php", {
                // Replace with the correct PHP script URL
                method: "POST",
                body: JSON.stringify({
                    page: "<?= $pid ?>",
                    all: all,
                    filename: !all ? filename : null
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showMessage(data.message, "success");
                    sendLogs("<?= $pid ?>", filename + " - was restored from archive");
                    showLogs();
                } else {
                    showMessage(data.message, "warning");
                }
                getArchive();
            })
            .catch((error) => {
                console.error("Error:", error);
                // Handle any error that occurred during the upload
            });
        countArchive();
        getfiles();
    };

    const deleteArchive = async filename => {
        await fetch("api/delete_archive.php", {
                // Replace with the correct PHP script URL
                method: "POST",
                body: JSON.stringify({
                    page: "<?= $pid ?>",
                    filename: filename
                }),
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showMessage(data.message, "success");
                    sendLogs("<?= $pid ?>", filename + " - document permanently removed");
                    showLogs();
                } else {
                    showMessage(data.message, "warning");
                }
                getArchive();
            })
            .catch((error) => {
                console.error("Error:", error);
                // Handle any error that occurred during the upload
            });
        countArchive();
    };

    const deleteAllArchive = async () => {
        if (confirm("Are you sure want to permanently delete all the files ?")) {
            await fetch("api/delete_all_archive.php", {
                method: "POST",
                body: JSON.stringify({
                    "page": '<?= $pid ?>'
                }),
            }).then(req => {
                return req.json();
            }).then(data => {
                if (data.success) {
                    showMessage(data.message, "success");
                    sendLogs("<?= $pid ?>", "All the documents from <?= $pid ?> were permanently removed");
                    showLogs();
                } else {
                    showMessage(data.message, "warning");
                }
                getArchive();
                countArchive();
            }).catch(err => {
                showMessage("There was a problem in deleting the archive Please check the console...", "warning");
                console.error(err);
            });
        }
    };

    const countArchive = async () => {
        await fetch("api/count_archive.php", {
            method: "POST",
            body: JSON.stringify({
                "page": '<?= $pid ?>'
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            $('btnShowArchiveSpan').textContent = "(" + data.message + ")";
        }).catch(err => {
            showMessage("There was a problem in retriving the archive count Please check the console...", "warning");
            console.error(err);
        });
    };
    countArchive();

    // Third party toggle system
    let thirdpartyvendor = <?= $info_all[0]['v_name'] === NULL ? 0 : 1 ?>;
    $('tpv').checked = thirdpartyvendor;
    if (!thirdpartyvendor) toggleTextbox($("tpv"), $("tpvCheck"));
    $("tpv").addEventListener("change", e => {
        if (!e.target.checked) {
            let somevalue = false;
            $('tpvCheck').querySelectorAll(".form-control").forEach(data => {
                if (data.value.length > 0 && data.value != null) somevalue = true;
            });
            if (somevalue) {
                if (!confirm("Disabling this will delete the records stored right now. Do you want to continue?")) {
                    e.target.checked = true;
                    e.preventDefault();
                    return;
                }
            }
        }
        toggleTextbox($("tpv"), $("tpvCheck"));
    });

    // Certificate toggle system
    let certificateToggleSystem = <?= $info_all[0]['certificate'] === NULL ? 0 : 1 ?>;
    $('crt').checked = certificateToggleSystem;
    if (!certificateToggleSystem) toggleTextbox($("crt"), $("crtCheck"));
    $("crt").addEventListener("change", e => {
        if (!e.target.checked) {
            let somevalue = false;
            $('crtCheck').querySelectorAll(".form-control").forEach(data => {
                if (data.value.length > 0 && data.value != null) somevalue = true;
            });
            if (somevalue) {
                if (!confirm("Disabling this will delete the records stored right now. Do you want to continue?")) {
                    e.target.checked = true;
                    e.preventDefault();
                    return;
                }
            }
        }
        toggleTextbox($("crt"), $("crtCheck"));
    });


    const updateData = async (form) => {
        const oldText = form.target.querySelector("button[type='submit'].btnMain").innerHTML;
        const button = form.target.querySelector("button[type='submit'].btnMain");
        button.disabled = true;
        button.innerHTML = `<span class="visually-hidden">Loading...</span> 
        <span class="spinner-border spinner-border-sm pe-2" role="status" aria-hidden="true"></span>
        `;
        /* I have to request different fetch request the nature of the view is such
        that it will only update the values of the similar table. So to change the 
        other values of different table i am requesting 2 Fetch Request */
        // Fetech request to change the project profile
        await fetch("api/updateProjectProfile.php", {
            method: "POST",
            body: JSON.stringify({
                "id": '<?= $info_all[0]['p_id'] ?>',
                "data": {
                    "priority": $('priority').checked ? "1" : "0",
                    "certificate": ($('crt').checked) ? $('crtName').value : "",
                    "wimName": $('wimName').value,
                    "wimDesignation": $('wimDesignation').value,
                    "wimEmail": $('wimEmail').value,
                    "wimPhone": $('wimPhone').value,
                    "tpvName": ($('tpv').checked) ? $('tpvName').value : "",
                    "tpvAddress": ($('tpv').checked) ? $('tpvAddress').value : "",
                    "tpvPhone": ($('tpv').checked) ? $('tpvPhone').value : "",
                    "tpvEmail": ($('tpv').checked) ? $('tpvEmail').value : "",
                    "assignedWork": ($('tpv').checked) ? $('assignedWork').value : "",
                    "tpvGST": ($('tpv').checked) ? $('tpvGST').value : "",
                }
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            return;
            if (data.success = true) {
                showMessage("Changes have been successfully updated...", "success");
                if (data.updated.length > 0) {
                    sendLogs("<?= $pid ?>", data.updated.join(", ") + " " + (data.updated.length > 1 ? "were" : "was") + " updated");
                    showLogs();
                    updateProjectStatus();
                    if ($('crt').checked) {
                        if ($('crtName').value != null || $('crtName').value.length > 0) {
                            sendLogs("<?= $pid ?>", "Project Closed with certificate no.:" + $('crtName').value);
                            showLogs();
                        }
                    } else if ($('crt').checked == false) {
                        if ($('crtName').value.length > 0) {
                            sendLogs("<?= $pid ?>", "Project Re-Initiated");
                            showLogs();
                        }
                    }
                }
            } else {
                showMessage("There was a problem in updating projectprofile Please check the console...", "warning");
                console.error(data.message);
            }
        }).catch(err => {
            showMessage("There was a problem in updating projectprofile Please check the console...", "warning");
            console.error(err);
        });

        // Fetech request to change the client details the project
        await fetch("api/updateProjectProfile.php", {
            method: "POST",
            body: JSON.stringify({
                "id": '<?= $info_all[0]['p_id'] ?>',
                "data": {
                    "clientName": $('clientName').value,
                    "clientAddress": $('clientAddress').value,
                    "clientGST": $('clientGST').value,
                    "clientMail": $('clientMail').value,
                    "clientPhone": $('clientPhone').value,
                }
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            return;
            if (data.success = true) {
                showMessage("Changes have been successfully updated...", "success");
                if (data.updated.length > 0) {
                    sendLogs("<?= $pid ?>", data.updated.join(", ") + " " + (data.updated.length > 1 ? "were" : "was") + " updated");
                    showLogs();
                }
            } else {
                showMessage("There was a problem in updating client details Please check the console...", "warning");
                console.error(data.message);
            }
        }).catch(err => {
            showMessage("There was a problem in client details Please check the console...", "warning");
            console.error(err);
        });

        // Fetech request to change the url of the project
        await fetch("api/updateProjectProfile.php", {
            method: "POST",
            body: JSON.stringify({
                "id": '<?= $info_all[0]['p_id'] ?>',
                "data": {
                    "projectURL": $('projectURL').value,
                }
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            if (data.success = true) {
                showMessage("Changes have been successfully updated...", "success");
                if (data.updated.length > 0) {
                    sendLogs("<?= $pid ?>", data.updated.join(", ") + " " + (data.updated.length > 1 ? "were" : "was") + " updated");
                    showLogs();
                }
            } else {
                showMessage("There was a problem in updating url Please check the console...", "warning");
                console.error(data.message);
            }
        }).catch(err => {
            showMessage("There was a problem in updating url Please check the console...", "warning");
            console.error(err);
        });

        await new Promise(resolve => setTimeout(resolve, 500));
        button.disabled = false;
        button.innerHTML = oldText;
    };

    document.getElementById("details").addEventListener("submit", async (e) => {
        e.preventDefault();
        await updateData(e);
        // await new Promise(resolve => setTimeout(resolve, 100));
        // e.target.reset();
        // location.reload();
    })



    let isSearch = {
        there: false,
        data: null
    };
    const searchComm = (element) => {
        isSearch = {
            there: true,
            data: element.value
        };
        $('commsBody').textContent = '';
        // showNextEntries(0, document.querySelectorAll('#commsBody > tr').length, localStorage.getItem('commSort'), element.value);
        showNextEntries(0, null, localStorage.getItem('commSort'), element.value);
    }
    $('searchComms').addEventListener("input", e => {
        if (e.target.value.length >= 3) {
            searchComm(e.target);
        } else if (e.target.value.length == 0) {
            $('commsBody').textContent = '';
            showNextEntries(0);
            $('showMoreButton').style.display = 'block';
            isSearch = {
                there: false,
                data: null
            };
        }
    });
    $('searchComms').addEventListener("keydown", e => {
        if (e.target.value.length < 3 && e.key === 'Enter') {
            showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> Please enter atleast 3 letters to search...", "warning");
        }
    });


    // Function to fetch and display the next batch of entries
    const showNextEntries = async (start, end = null, sorting = null, searchTerm = null) => {
        let sortValue = '';
        if (sorting != null) sortValue += "&sort=" + sorting;
        end = end == null ? "" : "&end=" + end;

        // Include the searchTerm in the API call if provided
        let searchValue = '';
        if (searchTerm != null) {
            searchValue += "&search=" + encodeURIComponent(searchTerm);
            isSearch = {
                there: true,
                data: searchTerm
            };
        }

        try {
            const response = await fetch(`api/comms_list.php?pid=<?= $pid ?>&start=${start}${end}${sortValue}${searchValue}`);
            const data = await response.json();

            if (end != null && sorting != null) {
                $('commsBody').innerHTML = '';
            }

            if (data.entry_count == 0) {
                $('commsBody').appendChild(noContent());
            }

            if (data.data) {

                // Loop through the fetched data and create table rows for each entry
                data.data.forEach(entry => {
                    const row = document.createElement('tr');

                    const senderCell = document.createElement('td');
                    senderCell.textContent = entry.sender;
                    row.appendChild(senderCell);

                    const receiverCell = document.createElement('td');
                    receiverCell.textContent = entry.recevier;
                    row.appendChild(receiverCell);

                    const dateTimeCell = document.createElement('td');
                    dateTimeCell.textContent = entry.date + " @ " + entry.time;
                    row.appendChild(dateTimeCell);

                    const categoryCell = document.createElement('td');
                    categoryCell.textContent = entry.category;
                    row.appendChild(categoryCell);

                    const subjectCell = document.createElement('td');
                    subjectCell.textContent = entry.subject;
                    row.appendChild(subjectCell);

                    const remarksCell = document.createElement('td');
                    remarksCell.textContent = entry.remarks;
                    row.appendChild(remarksCell);

                    // Create a table cell for the delete button
                    const actionsBtn = document.createElement('td');
                    const deleteButton = document.createElement('button');
                    deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
                    deleteButton.type = "button";
                    const icon = document.createElement('i');
                    icon.classList.add('bi', 'bi-trash-fill');
                    // deleteButton.onclick = "alert('" + entry.id + "', this)";
                    deleteButton.addEventListener("click", e => {
                        deleteComms(entry.id, e.target);
                    });
                    deleteButton.appendChild(icon);
                    actionsBtn.appendChild(deleteButton);
                    row.appendChild(actionsBtn);


                    $('commsBody').appendChild(row);
                });
            }

            if (document.querySelectorAll('#commsBody > tr').length == data.entry_count) {
                $('showMoreButton').style.display = 'none';
            }
            if (data.entry_count <= 0) {
                $('showMoreButton').style.display = 'none';
            }
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    // Initial call to fetch and display the first batch of entries
    showNextEntries(0);

    let sortClicked = false;

    const sortCommList = () => {
        sortClicked = true;
        const entrycount = document.querySelectorAll('#commsBody > tr').length;
        // Check if "commSort" is not set in localStorage or is set to a falsy value (undefined, null, false, 0, "", etc.)
        if (!localStorage.getItem("commSort")) {
            localStorage.setItem("commSort", 1);
        }

        // Parse the "commSort" value to an integer
        let commSortValue = parseInt(localStorage.getItem("commSort"));

        // Toggle the sorting value (1 becomes 0, 0 becomes 1)
        commSortValue = commSortValue === 1 ? 0 : 1;

        // Save the updated sorting value back to localStorage
        localStorage.setItem("commSort", commSortValue);

        // Call the showNextEntries() function with the updated sorting value
        if (isSearch.there) {
            showNextEntries(0, entrycount, commSortValue, isSearch.data);
        } else {
            showNextEntries(0, entrycount, commSortValue);
        }
    }

    // Event listener for the "Show More" button
    $('showMoreButton').addEventListener('click', () => {
        const entrycount = document.querySelectorAll('#commsBody > tr').length;
        let commSortValue = parseInt(localStorage.getItem("commSort"));
        if (sortClicked) {
            showNextEntries(0, entrycount + 10, commSortValue);
        } else {
            showNextEntries(entrycount);
        }
    });

    $('commsLogs').addEventListener("submit", async e => {
        e.preventDefault();
        await fetch("api/comms_insert.php", {
                method: "POST",
                body: JSON.stringify({
                    "send": $("commSender").value,
                    "receive": $("commReceiver").value,
                    "cat": $("commCategory").value,
                    "sub": $("commSubject").value,
                    "date": $("commDate").value.split("T")[0],
                    "time": $("commDate").value.split("T")[1],
                    "remarks": $("commRemarks").value,
                    "pid": "<?= $pid ?>"
                })
            }).then(res => res.json())
            .then(data => {
                sendLogs("<?= $pid ?>", "Communication : " + data.message);
                showLogs();
                if (data.success) {
                    document.querySelectorAll('#commsBody > tr td.noContent').forEach(col => {
                        col.parentElement.remove();
                    });
                    const row = document.createElement('tr');
                    let entry = data.data;

                    const senderCell = document.createElement('td');
                    senderCell.textContent = entry.sender;
                    row.appendChild(senderCell);

                    const receiverCell = document.createElement('td');
                    receiverCell.textContent = entry.recevier;
                    row.appendChild(receiverCell);

                    const dateTimeCell = document.createElement('td');
                    dateTimeCell.textContent = entry.date + " @ " + entry.time;
                    row.appendChild(dateTimeCell);

                    const categoryCell = document.createElement('td');
                    categoryCell.textContent = entry.category;
                    row.appendChild(categoryCell);

                    const subjectCell = document.createElement('td');
                    subjectCell.textContent = entry.subject;
                    row.appendChild(subjectCell);

                    const remarksCell = document.createElement('td');
                    remarksCell.textContent = entry.remarks;
                    row.appendChild(remarksCell);

                    // Create a table cell for the delete button
                    const actionsBtn = document.createElement('td');
                    const deleteButton = document.createElement('button');
                    deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
                    deleteButton.type = "button";
                    const icon = document.createElement('i');
                    icon.classList.add('bi', 'bi-trash-fill');
                    deleteButton.addEventListener("click", e => {
                        deleteComms(entry.id, e.target);
                    });
                    deleteButton.appendChild(icon);
                    actionsBtn.appendChild(deleteButton);
                    row.appendChild(actionsBtn);

                    $('commsBody').prepend(row);
                    $('commsLogs').reset();
                    updateDate('commDate');
                } else {
                    showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> Error occured while saving the communication logs", "danger");
                    console.error(data.message);
                }
            }).catch(err => console.error(err));
    });

    const deleteComms = async (id, btn) => {
        if (confirm("Are you sure want to delete ?")) {
            let row = btn.parentNode.parentNode;
            await fetch("api/comms_delete.php", {
                    method: "POST",
                    body: JSON.stringify({
                        "id": id,
                    })
                }).then(res => res.json())
                .then(data => {
                    if (data.success) {
                        sendLogs("<?= $pid ?>", "Communication : " + data.message);
                        showLogs();
                        row.remove();
                        if (document.querySelectorAll('#commsBody > tr').length <= 0 && $('showMoreButton').style.display == 'none') {

                            $('commsBody').appendChild(noContent());
                        }
                    } else {
                        showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> Error occured while saving the communication logs", "danger");
                        console.error(data.message);
                    }
                }).catch(err => console.error(err));
        }
    }
</script>


<?php

include 'template/footer.php';
?>