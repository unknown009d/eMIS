<?php
if(isset($_GET['s'])){
  $page = strtoupper($_GET['s']);
}elseif(isset($_GET['srno'])){
  $page = $_GET['srno'];
}else{
  header("Location: " . dirname($_SERVER['PHP_SELF']) . "/index");
  exit;
}
$cpage = "Service Request for " . $page;
include 'template/header.php';
// include "api/connect.php";

// Selecting all the client details
$query = "SELECT c_name FROM tbl_client ORDER BY c_name ASC";
$result = mysqli_query($conn, $query);
$clients = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selecting all the category types
$query = "SELECT * FROM tbl_ctype";
$result = mysqli_query($conn, $query);
$ctype = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selecting the last SRQ no. details...
$query = "SELECT tr.*, tc.* FROM tbl_register tr JOIN tbl_client tc ON tr.c_code = tc.c_code ORDER BY tr.reg_id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$latestEntry = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selecting the latest nomenclatures entries details...
$query = "SELECT * FROM tbl_testrate ORDER BY id DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$nomLatestEntry = mysqli_fetch_all($result, MYSQLI_ASSOC);

if(isset($_GET['srno'])){
	//Selecting all the details of the sr_no if its an edit operation
	$edit = selectQ($conn, "SELECT * FROM tbl_register WHERE sr_no=?", [$page])[0];
  $cusName = selectQ($conn, "SELECT c_name FROM tbl_client WHERE c_code=?", [$edit['c_code']])[0]['c_name'];
  $nomItems = selectQ($conn, "SELECT * FROM tbl_nom WHERE sr_no=?", [$edit['sr_no']]);
  $no_of_nom_items = count($nomItems);
  $gstCombo = $edit['cgst'] && $edit['sgst'] != 0 ? 2 : ($edit['igst'] != 0 ? 1 : 0);
  $checkDeemed = selectQ($conn,"SELECT * FROM tbl_deemed WHERE sr_no=?", [$page]);
  $checkSingleWindow = selectQ($conn,"SELECT * FROM tbl_single_window WHERE sr_no=?", [$page]);
  $miscCheck = count($checkDeemed) >= 1 ? "d" : (count($checkSingleWindow) >= 1 ? "sw" : null);
}

?>


<div class="col content pb-5" style='z-index: 2;'>
  <form id='service-request-form'>
    <h2 class='pageHeading'>
      <i class="bi bi-ui-checks-grid pe-3"></i>
      Service Request Form for
      <?php echo $page; ?>
    </h2>

    <!-- Service Request Generation Section -->
    <div class="card m-auto dcardform">
      <div class="card-header">
        <h5 class="card-title p-2 m-0 ps-0">Service Request <?= isset($_GET['srno']) ? "Update" : "Generation" ?></h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label for="srqno" class="form-label">
                SRQ No.
				<?php if(!isset($_GET['srno'])): ?>
				<span class="text-success toosmall" style="cursor: help;" 
					title="<?= $latestEntry[0]['c_name'] . " | " . $latestEntry[0]['c_phn'] . " | " . $latestEntry[0]['sr_date'] ?>">
                  <?= "(Last Record " . $latestEntry[0]['sr_no'] . ")" ?>
                  <!-- <span class="tooltip-text bg-dark text-light">
                      This is a custom tooltip
                  </span> -->
                </span>
				<?php endif; ?>
              </label>
              <input type="text" class="form-control" id="srqno" placeholder="eg. IT/06-23/006" readonly>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="date" class="form-label">Date</label>
              <input type="date" class="form-control" id="date" <?= !isset($_GET['srno']) ? 'onchange="getsrqno()"' : 'readonly' ?> >
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="jloc" class="form-label">Job Location</label>
              <div class="input-group">
                <select class="form-select" id="jloc" onchange="mfactorChange(this.value);calculateGST()">
                  <option value="I" selected>InHouse</option>
                  <option value="O">
                    OnSite
                  </option>
                </select>
                <input type="text" id="onsiteValue" class="form-control" oninput="onsiteChange(this)" onblur="calculateGST()">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Client Details form -->
    <div class="card m-auto dcardform mt-4">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title p-2 m-0 ps-0">Client details</h5>
        <button type="button" class="btn text-primary" data-bs-toggle="modal" data-bs-target="#createContactModal">
          <small class="fw-bold">New Contact</small><i class="bi bi-plus-circle-fill ps-1"></i>
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" list="clients" 
                  autocomplete="off"  onblur="fillTheForm(this.value);"  id="name" 
                  placeholder="eg. Jhon Doe" required>
              <?php
              echo "<datalist id='clients'>
                  ";
              foreach ($clients as $client) {
                echo "<option value='" . $client["c_name"] . "'>
                      ";
              }
              echo "</datalist>";
              ?>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control" id="email" placeholder="eg. jhondoe@mail.com" readonly tabindex="-1">
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="phone" class="form-label">Phone number</label>
              <input type="tel" class="form-control" id="phone" placeholder="eg. 8787 598 129" readonly tabindex="-1">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label for="address" class="form-label">Address</label>
              <input type="text" class="form-control" id="address" placeholder="eg. Indranagar, Agartala" readonly tabindex="-1">
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="category" class="form-label">Category</label>
              <select class="form-select" id="category" disabled tabindex="-1">
                <?php
                foreach ($ctype as $type) {
                  echo "<option value='" . $type["ct_code"] . "'>" . $type['ct_desc'] . "</option>
                      ";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="gstno" class="form-label">GST No.</label>
              <input type="tel" class="form-control" id="gstno" placeholder="eg. 0717USA12345NF1" readonly tabindex="-1">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Nomenclature form -->
    <div class="card m-auto dcardform mt-4">
      <div class="card-header d-flex justify-content-between">
        <h5 class="card-title p-2 m-0 ps-0">Nomenclature</h5>
        <button type="button" class="btn text-primary" data-bs-toggle="modal" data-bs-target="#newNomenclature">
          <small class="fw-bold">Tests</small><i class="bi bi-plus-circle-fill ps-1"></i>
        </button>
      </div>
      <div class="card-body">
        <div class="row mt-2">
          <div class="col-3">
            <label class="form-label">Nomenclature </label>
          </div>
          <div class="col">
            <label class="form-label">Rate</label>
          </div>
          <div class="col <?= explode("/", $page)[0] == "IT" ? 'd-none' : '' ?>">
            <label class="form-label">Quantity</label>
          </div>
          <div class="col <?= explode("/", $page)[0] == "IT" ? 'd-none' : '' ?>">
            <label class="form-label">Total</label>
          </div>
          <?php
          $condition = isset($_GET['srno']) ? explode("/", $page)[0] : $page;
          if ($condition == "IT") {
            echo "
                  <div class='col-3'>
                      <label for='url' class='form-label'>URL</label>
                  </div>
                  ";
          }
          ?>
          <div class="col">
            <label class="form-label">Remarks</label>
          </div>
          <div class="col-1">
          </div>
        </div>

        <!-- Nomenclatures -->
        <div id="dynamicRows">
          <div class="row row-item">
            <div class="col-3">
              <div class="mb-3">
                <input type="text" list="nomlist" class="form-control nomname" autocomplete="off" 
                onblur="fillNomenclature(this.value, this.parentNode.parentNode.parentNode)" 
                placeholder="eg. Desktop, Scanner" required>
                <datalist id='nomlist'>
                </datalist>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <input type="number" class="form-control rate" oninput="addTotalNom(this.parentNode.parentNode.parentNode)" min="0" placeholder="" readonly tabindex="-1">
              </div>
            </div>
            <div class="col <?= explode("/", $page)[0] == "IT" ? 'd-none' : '' ?>">
              <div class="mb-3">
                <input type="number" class="form-control quantity" 
                  oninput="addTotalNom(this.parentNode.parentNode.parentNode)" 
                  value="<?= explode("/", $page) == "IT" ? 1 : '' ?>"
                  min="1" max="1000>"> 
              </div>
            </div>
            <div class="col <?= explode("/", $page)[0] == "IT" ? 'd-none' : '' ?>">
              <div class="mb-3">
                <input type="text" class="form-control total" placeholder="" readonly tabindex="-1">
              </div>
            </div>
            <?php
            if ($condition == "IT") {
              echo "
                  <div class='col-3'>
                    <div class='mb-3'>
                      <input type='text' class='form-control url' placeholder='eg. https://www.stqc.gov.in/'>
                    </div>
                  </div>
                  ";
            }
            ?>
            <div class="col">
              <div class="mb-3">
                <input type="text" class="form-control nremarks" maxlength="50" placeholder="">
              </div>
            </div>
            <div class="col-1">
              <button type="button" class="btn btn-danger removeRow" onclick="removeRow(this.parentNode.parentNode)"><i class="bi bi-trash-fill"></i></button>
            </div>
          </div>
        </div>
        <div class="row d-flex justify-content-end">
          <div class="col-1">
            <button type="button" class="btn btn-success" onclick="addMoreBtn()"><i class="bi bi-plus-lg"></i></button>
          </div>
        </div>
      </div>
    </div>

    <!-- GST form -->
    <div class="card m-auto dcardform mt-4">
      <div class="card-header">
        <h5 class="card-title p-2 m-0 ps-0">GST Calculation</h5>
      </div>
      <div class="card-body">
        <!-- GST Calculation -->
        <div class="row mt-2">
          <div class="col">
            <div class="mb-3">
              <label for="calgst" class="form-label">GST (18%) </label>
              <select class="form-select" id="calgst" onchange="calculateGST()">
                <option value="0">NIL</option>
                <option value="1">IGST</option>
                <option value="2">CGST & SGST</option>
              </select>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="calcgst" class="form-label">CGST</label>
              <input type="text" class="form-control" id="calcgst" placeholder="" readonly tabindex="-1">
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="calsgst" class="form-label">SGST</label>
              <input type="text" class="form-control" id="calsgst" placeholder="" readonly tabindex="-1">
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="caligst" class="form-label">IGST</label>
              <input type="text" class="form-control" id="caligst" placeholder="" readonly tabindex="-1">
            </div>
          </div>
          <div class="col-2">
            <div class="mb-3">
              <label for="caltotalcharge" class="form-label">Total Charge</label>
              <div class="input-group mb-3">
                <span class="input-group-text">₹</span>
                <input type="text" id="caltotalcharge" class="form-control" readonly tabindex="-1">
              </div>
            </div>
          </div>
          <div class="col-2">
            <div class="mb-3">
              <label for="calgstrupees" class="form-label">GST</label>
              <div class="input-group mb-3">
                <span class="input-group-text">₹</span>
                <input type="text" id="calgstrupees" class="form-control" readonly tabindex="-1">
              </div>
            </div>
          </div>
          <div class="col-2">
            <div class="mb-3">
              <label for="calgrandtotal" class="form-label">Grand Total</label>
              <div class="input-group mb-3">
                <span class="input-group-text">₹</span>
                <input type="text" id="calgrandtotal" class="form-control" readonly tabindex="-1">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Miscellaneous -->
    <div class="card m-auto dcardform mt-4">
      <div class="card-header">
        <h5 class="card-title p-2 m-0 ps-0">Miscellaneous</h5>
      </div>
      <div class="card-body d-flex flex-column gap-3">
        <div class="row">
          <!-- D : Deemed -->
          <div class="col-2 form-check ms-3">
            <input class="form-check-input" type="checkbox" id="deemedCheckbox">
            <label class="form-check-label" for="deemedCheckbox">Deemed Revenue</label>
          </div>
          <div class="col">
            <div id="deemedTextbox" style="display: none;">
              <label for="deemedrn" class="form-label">Reference No. / Letter no.</label>
              <input type="text" class="form-control" id="deemedrn" placeholder="eg. L2023-001">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-2 form-check ms-3">
            <input class="form-check-input" type="checkbox" id="swCheckbox">
            <label class="form-check-label" for="swCheckbox">Single-window</label>
          </div>
          <!-- SW : SingleWindow -->
          <div id="swTextbox" class="col" style="display: none;">
            <div class="row">
              <div class="col">
                <label for="swShareTxt" class="form-label">Share</label>
                <div class="input-group">
                  <input type="number" class="form-control" id="swSharedTxt" placeholder="eg. 50" min="0" max="100">
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col">
                <label for="swAmountTxt" class="form-label">Amount</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="text" class="form-control" id="swAmountTxt" placeholder="eg. 500" readonly>
                </div>
              </div>
              <div class="col">
                <label for="swRemarksTxt" class="form-label">Remarks</label>
                <input type="text" class="form-control" id="swSRemarksTxt" placeholder="Write you remarks here...">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Submit Card -->
    <div class="m-auto dcardform mt-4">
      <!-- Remarks Input -->
      <div class="row mt-2">
        <div class="col">
          <div class="mb-3">
            <label for='remarks' class='form-label'>Remarks</label>
            <!-- <input type='tel' class='form-control' id='remarks' placeholder='Write something here...'> -->
            <textarea name="" id="remarks" class="form-control" placeholder='Write your remarks here...'><?= isset($_GET['srno']) ? $edit['rmks'] : '' ?></textarea>
          </div>
        </div>
        <!-- Submit Button -->
        <div class="row w-100 d-flex justify-content-end mt-3 gap-2">
          <?php if(isset($_GET['srno'])): ?>
          <a class="btn btn-outline-secondary w-auto" href="servicesView">
            <i class="bi bi-box-arrow-up-left pe-1"></i> Go Back
          </a>
          <?php endif; ?>
          <button type="submit" class="btn btn-success w-auto">
            Save Service Request <i class="bi bi-save ms-1"></i>
          </button>
        </div>
      </div>
    </div>

  </form>

</div>
<!-- Modal Dialoge for Adding new Nomenclature -->
<form class="modal fade" id="newNomenclature" aria-labelledby="newNomenclatureLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="newNomenclatureLabel">Add / View Nomenclatures List</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-3">
            <label for="newNomName" class="form-label">Nomenclature</label>
            <input type="text" class="form-control" id="newNomName" placeholder="eg. Laptop, Printer" required>
          </div>
          <div class="col">
            <label for="newNomDetails" class="form-label">Details</label>
            <input type="text" class="form-control" id="newNomDetails" placeholder="eg. Laptop system">
          </div>
          <div class="col">
            <label for="newPrice" class="form-label">Price</label>
            <div class="input-group mb-3">
              <span class="input-group-text">₹</span>
              <input type="number" class="form-control" id="newPrice" placeholder="" min="0" required>
            </div>
          </div>
          <div class="col">
            <label for="newNomCategory" class="form-label">Category</label>
            <select class="form-select" id="newNomCategory">
              <?php
              foreach ($jobtype as $type) {
                echo "<option value='" . $type["jtcode"] . "' " . ($page == $type["jtcode"] ? "selected" : "") . " >";
                echo $type['jtcode'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col">
            <label for="newNomFayear" class="form-label" title="Financial Year">F-Year</label>
            <input type="text" class="form-control" id="newNomFayear">
            <!-- Cant put value here... -->
          </div>
          <div class="col">
            <label for="newNomRemarks" class="form-label">Remarks</label>
            <input type="text" class="form-control" id="newNomRemarks" placeholder="">
          </div>
        </div>
        <div class="row">
          <div class="col">
            <small class="text-muted">Latest 5 Entries in the Nomenclature :</small>
            <table class="table table-bordered mt-2">
              <thead>
                <tr>
                  <th width="50px" class="text-center bg-light">JType</th>
                  <th class="bg-light">Nomenclature</th>
                  <th class="bg-light">Price</th>
                  <th class="bg-light">Year</th>
                  <th class="bg-light">Remarks</th>
                  <th class="bg-light"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($nomLatestEntry as $key => $data) : ?>
                  <tr id="entry-<?= $data['id'] ?>">
                    <td width="50px" class="text-center">
                      <?= $data['jtype'] ?>
                    </td>
                    <td title="<?= $data['nom_dtls'] ?>">
                      <?= $data['nom'] ?>
                    </td>
                    <td>₹
                      <?= $data['rate'] ?>
                    </td>
                    <td>
                      <?= $data['fyear'] ?>
                    </td>
                    <td width="150px" title="<?= $data['remarks'] ?>">
                      <?= truncateText($data['remarks'], 15) ?>
                    </td>
                    <td width='50px'>
                      <button class="btn btn-sm btn-danger" type="button" onclick="deleteRow('<?= $data['id'] ?>')">
                        <i class="bi bi-trash-fill"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add New <i class="bi bi-plus-lg"></i></button>
      </div>
    </div>
  </div>
</form>
<?php if(!isset($_GET['srno'])): ?>
<!-- Modal Dialoge for Project Profile Creation -->
<div class="modal fade" id="createPP" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createPPLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="createPPLabel">Do you want to create a project profile ? </h1>
        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="submitSuccessfully();">Skip</button>
        <button type="button" class="btn btn-primary" onclick="createProjectProfile(this);">Create Now</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>


  const calculateSingleWindowShare = () => {
    const sharedPercentage = parseFloat($('swSharedTxt').value);
    const totalCharge = parseFloat($('caltotalcharge').value);

    if (!isNaN(sharedPercentage) && !isNaN(totalCharge) && sharedPercentage <= 100) {
      const calculatedAmount = (sharedPercentage / 100) * totalCharge;
      $('swAmountTxt').value = calculatedAmount.toFixed(2);
    } else{
      $('swAmountTxt').value = "NaN";
    }
  };

  $('swSharedTxt').addEventListener("input", (e) => {
    calculateSingleWindowShare();
  });


  document.getElementById("newNomenclature").addEventListener('show.bs.modal', e => {
    e.target.querySelector('#newNomFayear').value = '<?= getCurrentFinancialYear() ?>';
  })

  // Fill up the options in the nomenclature database...
  const getNoms = async () => {
    await fetch("api/nom_list.php", {
      method: "POST",
      body: JSON.stringify({
        "page": "<?= isset($_GET['srno']) ? explode("/", $page)[0] : $page ?>",
      }),
    }).then(req => {
      return req.json();
    }).then(data => {
      if (data.success = true) {
        $('nomlist').innerHTML = '';
        data.data.forEach(d => {
          $('nomlist').innerHTML += `<option value="${d.nom}">${d.nom}</option>`;
        })
      } else {
        showMessage("There was a problem in deleting nomenclature Please check the console...", "warning");
        console.error(data.message);
      }
    }).catch(err => {
      showMessage("There was a problem in deleting nomenclature Please check the console...", "warning");
      console.error(err);
    });
  };
  getNoms();

  let onsiteValue = <?= isset($_GET['srno']) ? $edit['m_factor'] : 1.25 ?>;
  $('jloc').value = "<?= isset($_GET['srno']) ? $edit['j_location'] : "I" ?>";
  //$('jloc').value == 'I' ? $('onsiteValue').style.display = "none" : $('onsiteValue').style.display = "block";
  //$('onsiteValue').value = onsiteValue;
  
  //mfactor calculation
  let mfactor = 1;
  const mfactorChange = (val) => {
    if (val == 'O') $('onsiteValue').style.display = "block";
    else $('onsiteValue').style.display = "none";
    $('onsiteValue').value = onsiteValue;

    mfactor = val == 'O' ? onsiteValue : 1;
  };
	  mfactorChange("<?= isset($_GET['srno']) ? $edit['j_location'] : "I" ?>");

  // This is done to calculate the mfactor with a variable
  const onsiteChange = (val) => {
    onsiteValue = val.value;
    mfactor = $('onsiteValue').value;
  }

  /* This function runs on many of the input fields to maintain the consistency of data binding */
  const calculateGST = () => {
    const rows = document.querySelectorAll(".row-item");
    let totalCharge = 0;

    // Calculate the total charge from all the Nomenclature rows
    rows.forEach((row) => {
      const totalInput = row.querySelector(".total");
      const total = parseFloat(totalInput.value);

      if (!isNaN(total)) {
        totalCharge += total;
      }
    });

    // Calculate the GST amount (18% of total charge)
    let gstAmount = 0;

    // Get the selected option in the calgst select box
    const calgstSelect = document.getElementById("calgst");
    const selectedOption = calgstSelect.value;

    // Assign the GST amount to the corresponding text fields

    totalCharge *= mfactor; // Multiplying with each nomenclature items with onsiteValue
    gstAmount = (totalCharge * 0.18).toFixed(2);

    if (selectedOption === "0") gstAmount = 0;

    document.getElementById("calcgst").value = selectedOption === "2" ? (gstAmount / 2).toFixed(2) : "0";
    document.getElementById("calsgst").value = selectedOption === "2" ? (gstAmount / 2).toFixed(2) : "0";
    document.getElementById("caligst").value = selectedOption === "1" ? gstAmount : "0";



    // Calculate and assign the values to the additional fields
    const totalChargeWithGST = totalCharge + parseFloat(gstAmount);
    document.getElementById("caltotalcharge").value = totalCharge.toFixed(2);
    document.getElementById("calgstrupees").value = gstAmount;
    document.getElementById("calgrandtotal").value = totalChargeWithGST.toFixed(2);

    calculateSingleWindowShare();
  };

  // Get the current date
  let today = new Date();
  let formattedDate = today.toISOString().slice(0, 10);
  // Setting the value of the date input element
  document.getElementById("date").value = formattedDate;

  let formSubmitted = false;
  // FORM-JS getting submitted from here..
  document.getElementById("service-request-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    formSubmitted = true;
    await fetch("api/service_<?= isset($_GET['srno']) ? "edit" : "req" ?>.php", {
        method: "POST",
        body: JSON.stringify({
          "nomdetails": collectRowValues(),
          "srqno": $('srqno').value,
          "ccode": ccode,
          "date": $('date').value,
          "jloc": $('jloc').value,
          "jtype": "<?= isset($_GET['srno']) ? explode("/", $page)[0] : $page ?>",
          "cloc": $('calgst').value == 1 ? "O" : "I",
          "cgst": $('calcgst').value,
          "sgst": $('calsgst').value,
          "igst": $('caligst').value,
          "grandtotal": $('calgrandtotal').value,
          "total": $('caltotalcharge').value,
          "remarks": $('remarks').value,
          "mfactor": mfactor,
          <?= isset($_GET['srno']) ? '"fyear": "' . $edit['fyear'] . '"' : '' ?>
        })
      })
      .then(req => {
        return req.json();
      })
      .then(async data => {
        output = `<div class="alert alert-warning my-3" role="alert">
              <i class="bi bi-exclamation-triangle pe-1"></i> Sorry, there was a problem. Try again later
          </div>
        `;

        <?php if(!isset($_GET['srno'])): ?>
        /* Save service request */
        if (data.success) {
          /* When the data is successfully saved in the database then this happens... */
          <?php if ($page == 'IT') : ?>
            /* Creation of the project profile dialoge box section */
            output = `<p class='text-muted small'>Select the nomenclatures to create a new project</p>
              <table class='table table-bordered table-hover'>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>Nomenclature</th>
                    <th>URL</th>
                  </tr>
                </thead>
                <tbody>`;
            collectRowValues().forEach((d, cnt) => {
              output += `
            <tr onclick="toggleCheckbox(this)">
              <td>
                <input class="form-check-input" type="checkbox" 
                  value="${data.data[cnt]}" 
                  id="rd${cnt}" 
                  ${d.nomenclature.toLowerCase().includes("gigw") ? "checked" : ""}>
              </td>
              <td>
                ${data.data[cnt]}
              </td>
              <td>
                ${d.nomenclature}
              </td>
              <td>
                <a href='${d.url}' target="_blank">${d.url}</a>
              </td>
            </tr>
            `;
            });
            output += `</tbody>
          </table>`;


            // Initializing the create project profile form...
            const createPPForm = new bootstrap.Modal(document.getElementById('createPP'));
            createPPForm.show();
          <?php else : ?>
            submitSuccessfully();
          <?php endif; ?>
        } else {
          console.error(data.message);
          if (confirm("The SRF Number (" + $('srqno').value + ") is already in use. Do you want to generate a new SRF Number ? ")) {
            getsrqno();  
          } else {
            showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> " + data.message, "warning");
          }
        }
        document.querySelector("#createPP .modal-body").innerHTML = output;
        <?php else: ?>
          if(data.success){
            showMessage(data.message, "success");
          } else{
            showMessage("There was a problem. Please check the console", "warning");
            console.error("Error : " + data.message);
          }
        <?php endif; ?>

        <?php if(isset($_GET['srno'])): ?>
          let original = "<?= $miscCheck ?>";
          let changed = $('deemedCheckbox').checked ? 'd' : $('swCheckbox').checked ? 'sw' : '';
        <?php else: ?>
          let original = "0";
          let changed = "1";
        <?php endif; ?>
        if(data.success){
          if(original != changed){
              // API for deemed or single window
              await fetch("api/deemed_single_window.php", {
                  method: "POST",
                  body: JSON.stringify({
                    "srqno": $('srqno').value,
                    "type": $('deemedCheckbox').checked ? 'd' : $('swCheckbox').checked ? 'sw' : 'n',
                    "d_letterno": $('deemedrn').value,
                    "sw_sharedText": $('swSharedTxt').value,
                    "sw_sharedAmount": $('swAmountTxt').value,
                    "sw_tot_amount": $('caltotalcharge').value,
                    "sw_Remarks": $('swSRemarksTxt').value,
                  })
                }).then(req => req.text())
                .then(data => {
                  if (data.success == false) {
                    showMessage("Data wasn't successfully inserted from Miscellaneous section");
                    console.error(data.message);
                  }
                }).catch(err => console.error(err));
          }
        }
      })
      .catch(err => console.error(err));
  });

  <?php if(!isset($_GET['srno'])): ?>
    const submitSuccessfully = () => {
        let a = encodeURIComponent($('srqno').value);
        let b = encodeURIComponent($('name').value);
        let q = "<?= $page ?>";
        location.href = "serviceSuccess?s=" + q + "&srqno=" + a + "&cname=" + b;
    };
  <?php endif; ?>

<?php if(!isset($_GET['srno'])): ?>
  let createProjectProfile = e => {
    // Here we have to create project profile before redirecting...
    let error = false;
    e.previousElementSibling.remove();
    e.disabled = true;
    e.innerHTML = `
    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
    <span class="visually-hidden">Loading...</span>
    `;
    e.parentElement.previousElementSibling.querySelectorAll("table tbody tr").forEach(async d => {
      let isChecked = d.children[0].querySelector("input[type='checkbox']").checked;
      if (!isChecked) return;

      let nom_id = d.children[1].innerText;
      let nom_name = d.children[2].innerText;
      let nom_url = d.children[3].innerText;

      await fetch("api/createProjectProfile.php", {
        method: "POST",
        body: JSON.stringify({
          "sr_no": $('srqno').value,
          "nom_id": nom_id,
        }),
      }).then(req => {
        return req.json();
      }).then(data => {
        if (data.success = true) {
          showMessage("Project Created", "success");
          if (data.pid != undefined || data.pid != null) sendLogs(data.pid, "Project Initiated")
        } else {
          showMessage("There was a problem in creating project Please check the console...", "warning");
          console.error(data.message);
          let error = true;
        }
      }).catch(err => {
        showMessage("There was a problem in creating project Please check the console...", "warning");
        console.error(err);
        let error = true;
      });

    });

    setTimeout(() => {
      if (!error) submitSuccessfully();
    }, 1000);
  };
<?php endif; ?>

<?php if(!isset($_GET['srno'])): ?>
  const getsrqno = async () => {
    /* Getting the client code */
    await fetch("api/get_srq_no.php", {
      method: "POST",
      body: JSON.stringify({
        "q": "<?= $page; ?>",
        "date": $('date').value,
      }),
    }).then(req => {
      return req.json();
    }).then(data => {
      if (data.success = true) {
        document.getElementById("srqno").value = data.message;
      } else {
        showMessage("There was a problem in generating the SRQ No. Please check the console...", "warning");
      }
    }).catch(err => {
      showMessage("There was a problem in generating the SRQ No. Please check the console...", "warning");
      console.error(err)
    });
  }

  // setInterval(() => {
  getsrqno();
  // }, 1000)
<?php else: ?>
  document.getElementById("srqno").value = "<?= $page ?>";
<?php endif; ?>

  const fillTheForm = (value) => {
    if (value == null || value.length <= 0) return;
    fetch("api/getclients.php", {
      method: "POST",
      body: JSON.stringify({
        "username": value,
      }),
    }).then(req => {
      return req.json();
    }).then(data => {
      if (data.success == true) {
        // Fields are being autofilled
        if ($("name").value == data.clients[0]["c_name"]) {
          $("email").value = data.clients[0]["c_email"];
          $("phone").value = data.clients[0]["c_phn"];
          $("address").value = data.clients[0]["c_addr"];
          $("category").value = data.clients[0]["c_cat"];
          $("gstno").value = data.clients[0]["c_gst"];
          ccode = data.clients[0]["c_code"];
        }
      } else {
        $('name').value = '';
        showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> No Client found with this name..", "warning");
      }
    }).catch(err => {
      showMessage("There was a problem in the autocompletion of client details...", "warning");
      console.error(err)
    });
  };

<?php if(isset($_GET['srno'])): ?>
    $("name").value = "<?= $cusName ?>";
    fillTheForm("<?= $cusName ?>");
<?php endif; ?>


  const fillNomenclature = (value, rowElement) => {
    if (value == null || value.length <= 0) return;
    fetch("api/getnom.php", {
        method: "POST",
        body: JSON.stringify({
          id: value,
          category: "<?= $page ?>",
        }),
      })
      .then((req) => req.json())
      .then((data) => {
        if (data.success === true) {
          // Fields are being autofilled within the specific row
          rowElement.querySelector(".nomname").value = data.noms[0]["nom"];
          rowElement.querySelector(".rate").value = data.noms[0]["rate"];
          const quantityInput = rowElement.querySelector(".quantity");
          if (!quantityInput.value) {
            quantityInput.value = 1;
          }
          rowElement.querySelector(".total").value = parseInt(rowElement.querySelector(".rate").value) * rowElement.querySelector(".quantity").value;
          calculateGST();
        } else {
          showMessage("<i class='bi bi-exclamation-triangle pe-1'></i>Non-existence Nomenclature...", "warning");
          rowElement.querySelector(".rate").value = '';
          rowElement.querySelector(".quantity").value = '';
          rowElement.querySelector(".total").value = '';
          rowElement.querySelector(".nomname").value = '';
        }
      })
      .catch((err) => {
        showMessage("There was a problem in the autocompletion of Nomenclature...", "warning");
        console.error(err);
      });
    return false; // Prevent form submission
  };

  // Adding up total Noms
  const addTotalNom = (rowElement) => {
    const rateInput = rowElement.querySelector(".rate");
    const quantityInput = rowElement.querySelector(".quantity");
    const totalInput = rowElement.querySelector(".total");

    const rate = parseFloat(rateInput.value);
    const quantity = parseFloat(quantityInput.value);
    const total = rate * quantity;

    // Check if the total is a valid number
    if (!isNaN(total)) {
      totalInput.value = total;
    } else {
      totalInput.value = "";
    }
    calculateGST();
  }

  const addMoreBtn = () => {
    // Clone the first row
    let newRow = document.querySelector(".row-item").cloneNode(true);

    // Reset the input values in the cloned row
    let inputs = newRow.querySelectorAll("input");
    inputs.forEach(function(input) {
      input.value = "";
    });

    <?php if(isset($_GET['srno'])): ?>
      newRow.querySelector(".nomname").title = "";
      newRow.querySelector(".nomname").disabled = false;
      newRow.querySelector(".rate").title = "";
      newRow.querySelector(".quantity").title = "";
      newRow.querySelector(".quantity").disabled = false;
    <?php endif; ?>

    // Append the cloned row to the dynamicRows container
    document.getElementById("dynamicRows").appendChild(newRow);
    <?php if(!isset($_GET['srno'])): ?>
      inputs[0].focus()
    <?php endif; ?>
    checkEntryCount();
  };

  let removeRow = (rowElement) => {
    if(rowElement.querySelector(".url").value !== ""){
      if(!confirm("Deleting this may cause unusual behaviour in the Project Profile. Are you sure?")) return;
    }
    let entryCount = document.querySelectorAll("#dynamicRows .row").length; // Excluding the template row
    if (entryCount > 1) {
      rowElement.remove();
      checkEntryCount();
    }
    calculateGST();
  };

  // This checks how many Nomenclature are there
  let checkEntryCount = () => {
    let entryCount = document.querySelectorAll("#dynamicRows .row").length; // Excluding the template row
    let removeButtons = document.querySelectorAll("#dynamicRows .removeRow");

    // Show remove buttons if there's more than one entry
    if (entryCount > 1) {
      for (let i = 0; i < removeButtons.length; i++) {
        removeButtons[i].style.display = "block";
      }
    } else {
      for (let i = 0; i < removeButtons.length; i++) {
        removeButtons[i].style.display = "none";
      }
    }
  };
  checkEntryCount();

  // Nomenclature collected values
  const collectRowValues = () => {
    const rows = document.querySelectorAll(".row-item");
    const rowData = [];

    rows.forEach((row) => {
      // Create an object to store the values of the current row
      const rowValues = {
        nomenclature: row.querySelector(".form-control").value,
        rate: row.querySelector(".rate").value,
        quantity: row.querySelector(".quantity").value,
        total: row.querySelector(".total").value,
        remarks: row.querySelector(".nremarks").value,
        url: <?= explode('/',$page)[0] == "IT" ? 'row.querySelector(".url").value' : "null" ?>,
      };

      // Add the row values object to the array
      rowData.push(rowValues);
    });
    return rowData;
    // Process the collected row data
    // console.log(rowData);
    // Perform further operations with the collected data
  };


  /*===== Miscellaneous =====*/
  const deemedCheckbox = document.getElementById('deemedCheckbox');
  const deemedTextbox = document.getElementById('deemedTextbox');
  const swCheckbox = document.getElementById('swCheckbox');
  const swTextbox = document.getElementById('swTextbox');

  deemedCheckbox.addEventListener('change', () => {
    toggleTextbox(deemedCheckbox, deemedTextbox);
    closeMisc(swCheckbox, swTextbox);
  });

  swCheckbox.addEventListener('change', () => {
    toggleTextbox(swCheckbox, swTextbox);
    closeMisc(deemedCheckbox, deemedTextbox);
  });

  /* To check if the checkbox is already checked... */
  toggleTextbox(deemedCheckbox, deemedTextbox);
  toggleTextbox(swCheckbox, swTextbox);

  <?php if(!isset($_GET['srno'])): ?>
  window.addEventListener('load', () => {
    /* Becuase when the page loads some values still stay the same. So changing that 
     Except the *srqno that was automatically generating by PHP
     and the date that was automatically set on startup...

     If you wondering why isn't your scripts or values working for textbox? Please remove this
     if you want to dynamic load content from HTML as <input ... value="abc" ... >
     */
    document.querySelectorAll('input:not(#srqno):not([type="date"])').forEach(data => {
      data.value = '';
    });
  });
  <?php endif; ?>


<?php if(!isset($_GET['srno'])): ?>
  window.onbeforeunload = () => {
    if (!formSubmitted) {
      return "Are you sure you want to leave this page? Your unsaved data will be lost.";
    }
  };
<?php endif; ?>

  const toggleCheckbox = (row) => {
    let target = event.target;
    let excludetags = ['A', 'INPUT']
    // Check if the clicked element is clickable tag
    if (excludetags.includes(target.tagName)) {
      return;
    }

    let checkbox = row.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
  };

  const deleteRow = async (no) => {
    if (confirm("Are you sure want to delete this nomenclature ?")) {
      /* Getting the client code */
      await fetch("api/nom_delete.php", {
        method: "POST",
        body: JSON.stringify({
          "id": no,
        }),
      }).then(req => {
        return req.json();
      }).then(data => {
        if (data.success = true) {
          showMessage("Nomenclature Deleted", "success");
          document.getElementById('entry-' + no).remove();
          getNoms();
        } else {
          showMessage("There was a problem in deleting nomenclature Please check the console...", "warning");
          console.error(data.message);
        }
      }).catch(err => {
        showMessage("There was a problem in deleting nomenclature Please check the console...", "warning");
        console.error(err);
      });
    }
  };

  document.getElementById("newNomenclature").addEventListener("submit", async e => {
    e.preventDefault();
    await fetch("api/nom_insert.php", {
      method: "POST",
      body: JSON.stringify({
        "nom": $('newNomName').value,
        "details": $('newNomDetails').value,
        "rate": $('newPrice').value,
        "jtype": $('newNomCategory').value,
        "fyear": $('newNomFayear').value,
        "remarks": $('newNomRemarks').value,
      }),
    }).then(req => {
      return req.json();
    }).then(data => {
      if (data.success = true) {
        const alreadyTableContent = e.target.children[0].querySelector("table tbody").innerHTML;
        e.target.children[0].querySelector("table tbody").innerHTML = `
        <tr id="entry-${data.data['id']}">
          <td width="50px" class="text-center">
            ${data.data['jtype']}
          </td>
          <td title=${data.data['details']}>
            ${data.data['nom']}
          </td>
          <td>₹ 
            ${data.data['rate']}
          </td>
          <td>
            ${data.data['fyear']}
          </td>
          <td>
            ${data.data['remarks']}
          </td>
          <td width='50px'>
            <button class="btn btn-sm btn-danger" type="button" onclick="deleteRow('${data.data['id']}')" >
            <i class="bi bi-trash-fill"></i>
            </button>
          </td >
        </tr >
        `;
        showMessage("New nomenclature added", "success");
        e.target.children[0].querySelector("table tbody").innerHTML += alreadyTableContent;
        e.target.reset();
        getNoms();
      } else {
        showMessage("There was a problem in adding new nomenclature Please check the console...", "warning");
        console.error(data.message);
      }
    }).catch(err => {
      showMessage("There was a problem in adding new nomenclature Please check the console...", "warning");
      console.error(err);
    });

  });

  <?php if(isset($_GET['srno'])): ?>

    let nomItems = <?= json_encode($nomItems) ?>;

    <?php for ($i=1; $i < $no_of_nom_items; $i++): ?>
      addMoreBtn();
    <?php endfor; ?>

    if(nomItems.length > 0){
    let noEditText = "Changing this will cause unusual behaviour in Project Profile";
      document.querySelectorAll('#dynamicRows .row-item').forEach((item,cnt) => {
          item.querySelector('.nomname').value = nomItems[cnt]["nom"];
          item.querySelector('.nomname').disabled = true;
          item.querySelector('.nomname').title = noEditText;
          item.querySelector('.rate').value = nomItems[cnt]["t_charge"];
          item.querySelector('.rate').title = noEditText;
          item.querySelector('.quantity').value = nomItems[cnt]["qty"];
          item.querySelector('.quantity').disabled = true;
          item.querySelector('.quantity').title = noEditText;
          item.querySelector('.total').value = nomItems[cnt]["t_charge"] * nomItems[cnt]["qty"];
          <?php if(explode('/',$page)[0] == "IT"): ?>
            item.querySelector('.url').value = nomItems[cnt]["url"];
          <?php endif; ?>
          item.querySelector('.nremarks').value = nomItems[cnt]["rmks"];
      });
    }

    $('calgst').value = <?= $gstCombo ?>;
    $('calcgst').value = "<?= $edit['cgst'] ?>";
    $('calsgst').value = "<?= $edit['sgst'] ?>";
    $('caligst').value = "<?= $edit['igst'] ?>";
    $('caltotalcharge').value = "<?= $edit['t_charge'] ?>";
    $('calgstrupees').value = "<?= $gstCombo == 1 ? $edit['igst'] : ($gstCombo == 2 ? ($edit['cgst']+$edit['sgst']) : 0) ?>";
    $('calgrandtotal').value = "<?= $edit['tot_amount'] ?>";


    <?php 
      if(count($checkDeemed) >= 1): ?>
         $('deemedCheckbox').checked = true;
        toggleTextbox(deemedCheckbox, deemedTextbox);
        closeMisc(swCheckbox, swTextbox);
        $('deemedrn').value = "<?= $checkDeemed[0]['Letter_no'] ?>";
    <?php elseif(count($checkSingleWindow) >= 1): ?>
        $('swCheckbox').checked = true;
        toggleTextbox(swCheckbox, swTextbox);
        closeMisc(deemedCheckbox, deemedTextbox);
        $('swSharedTxt').value = '<?= $checkSingleWindow[0]['share']; ?>';
        $('swAmountTxt').value = '<?= $checkSingleWindow[0]['amount']; ?>';
        $('swSRemarksTxt').value = '<?= $checkSingleWindow[0]['remarks']; ?>';
    <?php endif; ?>

  <?php endif; ?>
</script>

<?php
include 'template/footer.php';
?>