<?php
$sr_no = isset($_GET['id']) ? $_GET['id'] : '';
$printpage = true;
$cpage = "SRF_" . str_replace(["/","-"], "_",$sr_no);
include 'template/header.php';

//fetch the srq details
$stmt_srq = $conn->prepare("SELECT * FROM srq_all WHERE sr_no=?");
$stmt_srq->bind_param("s", $sr_no);
$stmt_srq->execute();
$srq = $stmt_srq->get_result()->fetch_all(MYSQLI_ASSOC); //get the master srq details

//fetch the nomenclature details by sr_no
$stmt_nom = $conn->prepare("SELECT * FROM tbl_nom WHERE sr_no=?");
$stmt_nom->bind_param("s", $sr_no);
$stmt_nom->execute();
$srq_nom = $stmt_nom->get_result()->fetch_all(MYSQLI_ASSOC); //get the job type

/* When there are more noms. in the table 
then we'll redirect to the next page for all the entries */
$no_of_noms = count($srq_nom);
$max_entries_allowed = 5;

/* Replaces multiple consecutive whitespace characters in the value of $srq[0]['j_type']
 with a single space and assigns the modified value to $page */
$page = preg_replace('/\s+/', ' ', $srq[0]['j_type']);

function getdots($no_of_dots)
{
  $output = "";
  for ($i = 0; $i < $no_of_dots; $i++) {
    $output .= ".";
  }
  return $output;
}

?>

<div class="col">

  <?php foreach ($srq as $key => $s): ?>
    <div class="print_area <?= ($key + 1 == count($srq)) ? "" : "page-break-after" ?>">
      <!-- Header portion for Details of the Center and service request no. -->
      <div class="row border-niche px-2">
        <div class="col-7 border-pore">
          <p class="m-0">GOVERNMENT OF INDIA</p>
          <p class="m-0">MINISTRY OF ELECTRONICS & INFORMATION TECHNOLOGY</p>
          <h5 class="m-0">S.T.Q.C. Directorate</h5>
          <p class="m-0">Electronics Test and Development Centre (Agartala)</p>
          <p class="m-0">Indranagar, P.O. Kunjaban, Agartala - 799 006</p>
          <p class="m-0">Ph. : 235 9140 / 235 0058 (O)</p>
        </div>
        <div class="col-5">
          <p class="fw-bold my-2 fs-7"><em>SERVICE REQUEST NO.</em></p>
          <p class="dbb fs-6 fw-bold">
            <?= $s['sr_no'] ?>
          </p>
          <p>Date <span class="dbb"><?= date("d-m-Y", strtotime($s['sr_date'])) ?></span></p>
        </div>
      </div>

      <!-- Heading for the document -->
      <div class="row border-niche px-2">
        <div class="col">
          <h6 class="m-0 py-4 srf_title">SERVICE REQUEST FORM</h6>
        </div>
      </div>

      <!-- Customer details section -->
      <div class="row">
        <div class="col mt-2">
          <strong class='mt-0 px-2'>To be filled up by the customer</strong>
          <ol class="cus-list">
            <li class="px-2">Calibration / Test / Service Requested by</li>
            <table class="dtable w-100 mb-1">
              <thead>
                <tr>
                  <th>Name of the Organisation</th>
                  <th>Address</th>
                  <th>Phone No.</th>
                  <th>Represented by</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <?= $s['c_name'] ?>
                  </td>
                  <td>
                    <?= $s['c_addr'] ?>
                  </td>
                  <td>
                    <?= $s['c_phn'] ?>
                  </td>
                  <td>
                    <?= $s['ct_desc'] ?>
                  </td>
                </tr>
              </tbody>
            </table>
            <li class="px-2">Description and identification of items </li>
            <table class="dtable w-100">
              <thead>
                <tr>
                  <th colspan="3">Nomenclature</th>
                  <th>Rate</th>
                  <th>Quantity</th>
                  <th class="nomTotalCharge">Total</th>
                  <th class="item-remarks">Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i = 0; $i < min($no_of_noms, $max_entries_allowed); $i++): ?>
                  <tr>
                    <td colspan="3">
                      &emsp;
                      <?= $i + 1 . ".    " . $srq_nom[$i]['nom'] ?>
                    </td>
                    <td>
                      &#8377;
                      <?= $srq_nom[$i]['t_charge'] ?>
                    </td>
                    <td>
                      <?= $srq_nom[$i]['qty'] ?>
                    </td>
                    <td class="nomTotalCharge">
                      &#8377;
                      <?= $srq_nom[$i]['t_charge'] * $srq_nom[$i]['qty'] ?>
                    </td>
                    <td class="item-remarks">
                      <?= $srq_nom[$i]['rmks'] ?>
                    </td>
                  </tr>
                  <?php
                endfor; ?>
                <?php if ($no_of_noms > $max_entries_allowed): ?>
                  <tr>
                    <td id="extratext" colspan="7" class="text-muted text-center">
                      <em>Additional items are attacted to Annexure-A</em>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
            <?php
              $bigCol = 5;
              $smallCol = 2;
            ?>
            <table class="dtable w-100 mb-1">
              <tbody>
                <?php if($s['m_factor'] > 1): ?>
                <tr class="item-onsite">
                  <td colspan="<?= $bigCol ?>" class="dtable-light">
                    OnSite (Multiplication Factor)
                  </td>
                  <td colspan="<?= $smallCol ?>">
                    <?= $s['m_factor'] ?>
                  </td>
                </tr>
                <?php endif; ?>
                <tr>
                  <td colspan="<?= $bigCol ?>" class="dtable-light">
                    Total Charge
                  </td>
                  <td colspan="<?= $smallCol ?>">
                    &#8377;
                    <?= $s['t_charge'] ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="<?= $bigCol ?>" class="dtable-light">
                    GST :
                    <?php if ($s['igst'] > 0): ?>
                      IGST (18%)
                    <?php elseif ($s['cgst'] > 0 || $s['sgst'] > 0): ?>
                      CSGT (9%)
                      [ &#8377;
                      <?= $s['cgst'] ?> ]
                      & SGST (9%)
                      [ &#8377;
                      <?= $s['sgst'] ?> ]
                    <?php else: ?>
                      <i>NIL</i>
                    <?php endif; ?>
                  </td>
                  <td colspan="<?= $smallCol ?>">
                    &#8377;
                    <?= $s['tot_amount'] - $s['t_charge'] ?>
                  </td>
                </tr>
                <tr>
                  <td colspan="<?= $bigCol ?>" class="dtable-light fw-bold text-success">
                    Grand Total
                  </td>
                  <td colspan="<?= $smallCol ?>" class="fw-bold text-success">
                    &#8377;
                    <?= $s['tot_amount'] ?>
                  </td>
                </tr>
              </tbody>
            </table>
          </ol>
        </div>
      </div>

      <!-- Customer verification section -->
      <div class="row mt-5 pb-2 border-niche px-2">
        <div class="col d-flex gap-2 justify-content-between">
          <span>
            Dated
            <?= getdots(40); ?>
          </span>
          <span>
            Signature
            <?= getdots(60); ?>
          </span>
          <span>
            Designation
            <?= getdots(40) ?>
          </span>
        </div>
      </div>

      <!-- Test Planning office section -->
      <div class="row mt-2 pb-2 border-niche px-2">
        <div class="col">
          <p class="m-0"><strong>To be filled up by the Test Planning Office</strong></p>
          <p>The charges for the above test / calibration in Rupees
            <span class="dbb">
              <em>
                <?= convertToRupees($s['tot_amount']) ?>
              </em>
            </span>
            only.
          </p>
          <div class="d-flex gap-2 justify-content-between mt-4">
            <span>
              Dated
              <?= getdots(40); ?>
            </span>
            <span>
              Signature of Test Planning & Co-ordination Officer
              <?= getdots(50) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- Terms and condition section -->
      <div class="row mt-2 ps-2">
        <div class="col termsncon">
          <label class='fw-bold mb-1'>Terms & Conditions : </label>
          <ol>
            <li>
              All equipments submitted for test/calibration must be in working order.
            </li>
            <li>
              Service charges / GST are payable as per instruction given at the time of Billing.
            </li>
            <li>
              All possible care will be taken in handling the equipments, but the risk of damage in transit or in testing
              must be assured by applicant.
            </li>
            <li>
              An instrument accepted for testing may be returned untested/uncalibrated, under circumstances beyond
              control.
            </li>
            <li>
              The test report is not to be used for any legal purpose and shall not be pronounced in the court of law.
            </li>
            <li>
              The equipments are accepted and delivered back to ETDC (AGT) only and the same should be checked before
              taking delivery.
            </li>
            <li>
              After the completion of test & issue of report the applicant shall collect back tested/calibrated items
              within one month under his own arrangements.
            </li>
            <li>
              In case of any dispute, the decision of the Director, of the Laboratory shall be final.
            </li>
            <li>
              The conditions are subject to any change/modification without any prior notice.
            </li>
          </ol>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if($no_of_noms > $max_entries_allowed): ?>
  <div class="print_area px-2 py-3 pb-5 page-break-before">
    <div class="row ms-2">
      <div class="col">
        <h4 class='fw-bold m-0'>Annexure-A</h4>
      </div>
    </div>
    <div class="row ms-2">
      <div class="col">
        <p class="text-muted mb-3">Here are the list of items in the items...</p>
      </div>
    </div>
    <div class="row">
      <table class="dtable w-100">
        <thead>
          <tr>
            <th colspan="3">Nomenclature</th>
            <th>Rate</th>
            <th>Quantity</th>
            <th>Total</th>
            <th class="item-remarks">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; $i < $no_of_noms; $i++): ?>
            <tr>
              <td colspan="3">
                &emsp;
                <?= $i + 1 . ".    " . $srq_nom[$i]['nom'] ?>
              </td>
              <td>
                &#8377;
                <?= $srq_nom[$i]['t_charge'] ?>
              </td>
              <td>
                <?= $srq_nom[$i]['qty'] ?>
              </td>
              <td>
                &#8377;
                <?= $srq_nom[$i]['t_charge'] * $srq_nom[$i]['qty'] ?>
              </td>
              <td class="item-remarks">
                <?= $srq_nom['rmks'] ?>
              </td>
            </tr>
            <?php
          endfor; ?>
        </tbody>
      </table>
      <table class="dtable w-100 mb-1">
        <tbody>
          <tr>
            <td colspan="5" class="dtable-light">
              Total Charge
            </td>
            <td colspan="2">
              &#8377;
              <?= $s['t_charge'] ?>
            </td>
          </tr>
          <tr>
            <td colspan="5" class="dtable-light">
              GST :
              <?php if ($s['igst'] > 0): ?>
                IGST (18%)
              <?php elseif ($s['cgst'] > 0 || $s['sgst'] > 0): ?>
                CSGT (9%)
                [ &#8377;
                <?= $s['cgst'] ?> ]
                & SGST (9%)
                [ &#8377;
                <?= $s['sgst'] ?> ]
              <?php else: ?>
                <i>NIL</i>
              <?php endif; ?>
            </td>
            <td colspan="2">
              &#8377;
              <?= $s['tot_amount'] - $s['t_charge'] ?>
            </td>
          </tr>
          <tr>
            <td colspan="5" class="dtable-light fw-bold text-success">
              Grand Total
            </td>
            <td colspan="2" class="fw-bold text-success">
              &#8377;
              <?= $s['tot_amount'] ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <br>
  </div>
  <?php endif; ?>
  <br>
  <div class='d-flex gap-2 justify-content-center actionContainer my-3'>
    <a class="btn btn-outline-secondary" 
    <?php if(isset($_GET["srqgen"])): ?>
      href='service?s=<?= $page ?>'
      <?php  else: ?>
      onclick='history.back()'
      <?php  endif; ?>
      >
      <i class="bi bi-box-arrow-up-left pe-1"></i> Go Back
    </a>
    <button class="btn btn-primary" onclick="window.print()">
      <i class="bi bi-printer-fill pe-1"></i> Print
    </button>
  </div>

  <div class="actionContainer mt-5 bg-white border">
    <p class="w-100 text-center pt-3 m-0">
      <small class="text-muted">* Double click in the items to make the content editable (temporarily)</small>
    </p>
    <div class="px-4 py-3 d-flex justify-content-center align-items-center gap-3">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="chkShowRemarks">
        <label class="form-check-label" for="chkShowRemarks">Remove Remarks</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="chkShowOnSiteMultiplication">
        <label class="form-check-label" for="chkShowOnSiteMultiplication">Remove On-Site Multiplication</label>
      </div>
    </div>
  </div>


</div>

<script src="resources/js/print-common.js"></script>
<script>

  $("chkShowOnSiteMultiplication").addEventListener("change", e => {
    document.querySelectorAll(".item-onsite").forEach(d => {
        d.style.display = e.target.checked ? "none" : "table-row";
    });
    localStorage.setItem('chkShowOnSiteMultiplication', e.target.checked);
  });
  stateRestore("chkShowOnSiteMultiplication");

  $("chkShowRemarks").addEventListener('change', () => {
    extendTotalCol();
  })
  const extendTotalCol = () => {
      document.querySelectorAll('.nomTotalCharge').forEach(item => {
        if($("chkShowRemarks").checked == true){
          item.colSpan = 2;
        }else{
          item.colSpan = 1;
        }
      })
  };
  extendTotalCol();
</script>


<?php
include 'template/footer.php';
?>