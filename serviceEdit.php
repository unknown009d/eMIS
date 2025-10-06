<?php
$page = strtoupper($_GET['srno']);
$cpage = "Service Request for " . $page;
include 'template/header.php';
// include "api/connect.php";

$editData = selectQ($conn, "SELECT * FROM tbl_register WHERE sr_no = ?", [$page])[0];

// Selecting all the client details
$query = "SELECT c_name FROM tbl_client ORDER BY c_name ASC";
$result = mysqli_query($conn, $query);
$clients = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selecting all the category types
$query = "SELECT * FROM tbl_ctype";
$result = mysqli_query($conn, $query);
$ctype = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Selecting the latest nomenclatures entries details...
$query = "SELECT * FROM tbl_testrate ORDER BY id DESC LIMIT 5";
$result = mysqli_query($conn, $query);
$nomLatestEntry = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        <h5 class="card-title p-2 m-0 ps-0">Service Request Generation</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label for="srqno" class="form-label">
                SRQ No.
              </label>
			  <input type="text" class="form-control" id="srqno" placeholder="eg. IT/06-23/006" value="<?= $page ?>" readonly>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="date" class="form-label">Date</label>
			  <input type="date" class="form-control" id="date" value="<?= $editData['sr_date'] ?>">
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="jloc" class="form-label">Job Location</label>
              <div class="input-group">
                <select class="form-select" id="jloc" onchange="mfactorChange(this);calculateGST()">
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
              <input type="text" class="form-control" list="clients" autocomplete="off" onblur="fillTheForm(this.value);" id="name" placeholder="eg. Jhon Doe" required>
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
          <div class="col">
            <label class="form-label">Quantity</label>
          </div>
          <div class="col">
            <label class="form-label">Total</label>
          </div>
          <?php
          if ($page == "IT") {
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
                <input type="text" list="nomlist" class="form-control nomname" autocomplete="off" onblur="fillNomenclature(this.value, this.parentNode.parentNode.parentNode)" placeholder="eg. Desktop, Scanner" required>
                <datalist id='nomlist'>
                </datalist>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <input type="number" class="form-control rate" oninput="addTotalNom(this.parentNode.parentNode.parentNode)" min="0" placeholder="" readonly tabindex="-1">
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <input type="number" class="form-control quantity" oninput="addTotalNom(this.parentNode.parentNode.parentNode)" min="1" max="<?= $page == "IT" ? 1 : 1000 ?>">
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <input type="text" class="form-control total" placeholder="" readonly tabindex="-1">
              </div>
            </div>
            <?php
            if ($page == "IT") {
              echo "
                  <div class='col-3'>
                    <div class='mb-3'>
                      <input type='text' class='form-control url' id='url' placeholder='eg. https://www.stqc.gov.in/'>
                    </div>
                  </div>
                  ";
            }
            ?>
            <div class="col">
              <div class="mb-3">
                <input type="text" class="form-control nremarks" placeholder="">
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
                  <input type="number" class="form-control" id="swSharedTxt" placeholder="eg. 50" min="0" max="100" value="100">
                  <span class="input-group-text">%</span>
                </div>
              </div>
              <div class="col">
                <label for="swAmountTxt" class="form-label">Amount</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="text" class="form-control" id="swAmountTxt" placeholder="eg. 500">
                </div>
              </div>
              <div class="col">
                <label for="swRemarksTxt" class="form-label">Remarks</label>
                <input type="text" class="form-control" id="swSRemarksTxt" placeholder="Write you remarks here...">
              </div>
              <?php
              // if ($page == "IT") {
              //   echo "
              //     <div class='col'>
              //       <label for='swURLTxt' class='form-label'>URL</label>
              //       <input type='text' class='form-control' id='swURLTxt' placeholder='eg. https://www.stqc.gov.in/'>
              //     </div>
              //     ";
              // }
              ?>
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
            <textarea name="" id="remarks" class="form-control" placeholder='Write your remarks here...'></textarea>
          </div>
        </div>
        <!-- Submit Button -->
        <div class="row w-100 d-flex justify-content-end mt-3 gap-2">
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

<script>
  const calculateSingleWindowShare = () => {
    const sharedPercentage = parseFloat($('swSharedTxt').value);
    const totalCharge = parseFloat($('caltotalcharge').value);

    if (!isNaN(sharedPercentage) && !isNaN(totalCharge)) {
      const calculatedAmount = (sharedPercentage / 100) * totalCharge;
      $('swAmountTxt').value = calculatedAmount.toFixed(2);
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
        "page": '<?= $page ?>',
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

  let onsiteValue = <?= $editData['m_factor'] ?>;
  $('jloc').value = "<?= $editData['j_location'] ?>";
  $('jloc').value == 'I' ? $('onsiteValue').style.display = "none" : $('onsiteValue').style.display = "block";
  $('onsiteValue').value = onsiteValue;
  //mfactor calculation
  let mfactor = 1;
  const mfactorChange = (val) => {
    if (val.value == 'O') $('onsiteValue').style.display = "block";
    else $('onsiteValue').style.display = "none";
    $('onsiteValue').value = onsiteValue;

    mfactor = val.value == 'O' ? $('onsiteValue').value : 1;
  }
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
  /* document.getElementById("date").value = formattedDate; */

  let formSubmitted = false;
  // FORM-JS getting submitted from here..
  document.getElementById("service-request-form").addEventListener("submit", async (e) => {
    e.preventDefault();
    formSubmitted = true;
    // console.log(collectRowValues());
    await fetch("api/service_edit.php", {
        method: "POST",
        body: JSON.stringify({
          "nomdetails": collectRowValues(),
          "srqno": $('srqno').value,
          "ccode": ccode,
          "date": $('date').value,
          "jloc": $('jloc').value,
          "jtype": "<?php echo $page ?>",
          "cloc": $('calgst').value == 1 ? "O" : "I",
          "cgst": $('calcgst').value,
          "sgst": $('calsgst').value,
          "igst": $('caligst').value,
          "grandtotal": $('calgrandtotal').value,
          "total": $('caltotalcharge').value,
          "remarks": $('remarks').value,
          "mfactor": mfactor
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

            // API for deemed or single window
            await fetch("api/deemed_single_window.php", {
                method: "POST",
                body: JSON.stringify({
                  "srqno": $('srqno').value,
                  "type": $('deemedCheckbox').checked ? 'd' : $('swCheckbox').checked ? 'sw' : '',
                  "d_letterno": $('deemedrn').value,
                  "sw_sharedText": $('swSharedTxt').value,
                  "sw_sharedAmount": $('swAmountTxt').value,
                  "sw_Remarks": $('swSRemarksTxt').value,
                })
              }).then(req => req.json())
              .then(data => {
                if (data.success == false) {
                  showMessage("Data wasn't successfully inserted from Miscellaneous section");
                  console.error(data.message);
                }
              }).catch(err => console.error(err));

            // Initializing the create project profile form...
            const createPPForm = new bootstrap.Modal(document.getElementById('createPP'));
            createPPForm.show();
          <?php else : ?>
            submitSuccessfully();
          <?php endif; ?>
        } else {
          console.error(data.message);
        }
        document.querySelector("#createPP .modal-body").innerHTML = output;

      })
      .catch(err => console.error(err));



  });

  const submitSuccessfully = () => {
    let a = encodeURIComponent($('srqno').value);
    let b = encodeURIComponent($('name').value);
    let q = "<?= $page ?>";
    location.href = "serviceSuccess?s=" + q + "&srqno=" + a + "&cname=" + b;
  };

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
        if (document.getElementById("name").value == data.clients[0]["c_name"]) {
          document.getElementById("email").value = data.clients[0]["c_email"];
          document.getElementById("phone").value = data.clients[0]["c_phn"];
          document.getElementById("address").value = data.clients[0]["c_addr"];
          document.getElementById("category").value = data.clients[0]["c_cat"];
          document.getElementById("gstno").value = data.clients[0]["c_gst"];
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

    // Append the cloned row to the dynamicRows container
    document.getElementById("dynamicRows").appendChild(newRow);
    inputs[0].focus()
    checkEntryCount();
  };

  let removeRow = (rowElement) => {
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
        url: <?= $page == "IT" ? 'row.querySelector(".url").value' : "null" ?>,
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
</script>

<?php
include 'template/footer.php';
?>
