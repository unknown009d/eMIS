<?php
$slno = isset($_GET['id']) ? $_GET['id'] : '';
$printpage = true;
$cpage = "Gatepass_" . str_pad(str_replace(["/","-"], "_",$slno), 4, "0", STR_PAD_LEFT);
include 'template/header.php';


//fetch the srq details
$stmt_main = $conn->prepare("SELECT * FROM tbl_gatepass WHERE slno=?");
$stmt_main->bind_param("s", $slno);
$stmt_main->execute();
$main = $stmt_main->get_result()->fetch_all(MYSQLI_ASSOC); 

//fetch the nomenclature details by sr_no
$stmt_item = $conn->prepare("SELECT * FROM tbl_gatepass_items WHERE slno=?");
$stmt_item->bind_param("s", $slno);
$stmt_item->execute();
$srq_items = $stmt_item->get_result()->fetch_all(MYSQLI_ASSOC);

/* When there are more noms. in the table 
then we'll redirect to the next page for all the entries */
$no_of_items = count($srq_items);
$max_entries_allowed = 7;


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

  <?php foreach ($main as $key => $s): ?>

    <div class="actionContainer text-center">
      <i class="text-muted px-4 pt-3 italic d-block">
        <strong>Remarks : </strong>
        <?= $s['remarks'] ?? "NIL" ?>
      </i>
    </div>

    <div class="print_area pb-4 <?= ($key + 1 == count($main)) ? "" : "page-break-after" ?>">
      <!-- Header portion for Details of the Center and service request no. -->
      <div class="row border-niche px-2">
        <div class="col-12 border-pore text-center">
          <div class="d-flex justify-content-between align-items-center">
            <img src="resources/STQC.PNG" alt="STQC_Logo">
            <div class="text-end p-2">
              <small class="d-block">
                Form No. ETDC(AGT)/QA/09
              </small>
              <small class="d-block">
                (To be raised in Quarduplicate)
              </small>
              <small class="d-block">
                दूरभाष/ Phone (0381) 235 9140
              </small>
              <small class="d-block">
                फैक्स/Fax: (0381) 2350058
              </small>
            </div>
          </div>
          <div style="transform: translateY(-8%)">
            <p class="m-0 phead">इलेक्ट्रॉनिकी परीक्षण तथा विकास केन्द्र</p>
            <p class="m-0 phead text-uppercase">Electronics Test and Development Center</p>
            <p class="m-0">(मा.प.गु.प्र निदेशालय / S.T.Q.C. Directorate)</p>
            <p class="m-0 wide-text fw-bold">
              इलेक्ट्रॉनिक्स और सूचना प्रोद्योगिकी मंत्रालय
            </p>
            <p class="m-0 lh-1">Ministry of Electronics & Information Technology</p>
            <p class="m-0">भरत सरकार / Government of India</p>
            <p class="m-0">इन्द्रनगर, पो० ओ० कुन्जवन, अगरतला-6 / Indranagar, P.O. Kunjaban, Agartala 799006</p>
            <p class="m-0">यन्त्र /सामग्री निकासी / Movement of Equipments / Materials</p>
          </div>
        </div>
      </div>

      <!-- Heading for the document -->
      <div class="row px-3 pt-3">
        <div class="col-3">
          <p class="m-0">प्रत्यावर्तनिय / अप्रत्यावर्तनीय</p>
        </div>
        <div class="col text-end">
          <p class="m-0">
            दिनांक एवं समय / Date & time (taken)
            <span class="textondots fw-bold" id="gtpDateTime">
              <?= date("d/m/Y @ h:ia", strtotime($s['datetime'])) ?>
            </span>
            <?= getdots(45); //date("d/m/Y", strtotime(explode(" ", $s['datetime'])[0])) ?>
          </p>
        </div>
      </div>

      <div class="row px-3 pt-2">
        <div class="col-4">
          <p class="m-0">
            <span class="<?= $s['returnable'] ? "fw-bold" : "text-strikeout" ?>">Returnable</span>/ 
            <span class="<?= $s['returnable'] ? "text-strikeout" : "fw-bold" ?>">Non-Returnable</span></p>
        </div>
        <div class="col text-end">
          <p class="m-0 prelative">
            संख्या का नाम / Name of the organisation
            <span class="textondots fw-bold" id="orgname"><?= $s['org_name'] ?></span>
            <?= getdots(60); ?>
          </p>
        </div>
      </div>

      <div class="row px-3 pt-2">
        <div class="col">
          <p class="m-0">निकासी पत्र क्रम संख्या ई टी डी सी (गुवाहाटी) <?= getdots(142) ?> </p>
        </div>
      </div>
      
      <div class="row px-3 pt-2">
        <div class="col">
          <p class="m-0">
            Gate Pass Serial No. ETDC (AGT)
            <span class="textondots fw-bold fs-5 ms-2"><?= addZeros($s['slno']) ?></span>
            <?= getdots(147) ?> </p>
        </div>
      </div>

      <div class="row px-3 pt-2">
        <div class="col-3">
          <p class="m-0">सेवा में / To</p>
        </div>
        <div class="col text-end">
          <p class="m-0">
            संदर्भ / and reference (if any)
            <span class="textondots fw-bold"><?= $s['reference'] ?></span>
            <?= getdots(40); ?>
          </p>
        </div>
      </div>

      <div class="row px-3 pt-0">
        <div class="col">
          <p class="m-0">ई.टि.डि.सी / ETDC</p>
        </div>
      </div>

      <div class="row px-3 pt-0">
        <div class="col">
          <p class="m-0">अगरतला / Agartala-799006</p>
        </div>
      </div>

      <div class="row px-3 pt-0">
        <div class="col">
          <p class="m-0">कृपया निम्नलिखित, सामान को ई० टी० डी० सी० परिसर से बाहर जाने की अनुमति प्रदान करे / Please only allow the following materials to leave the Complex:</p>
        </div>
      </div>


      <!-- Items -->
      <div class="row">
        <div class="col mt-2">
            <table class="dtable w-100">
              <thead>
                <tr>
                  <th class="ps-3" width="70px">
                    <span class="d-block">क्रम&nbsp;सं०</span>
                    Sl. No.
                  </th>
                  <th colspan="2">
                    <span class="d-block">विवरण</span>
                    Particulars
                  </th>
                  <th>
                    <span class="d-block">माडल नं०</span>
                    Model No.
                  </th>
                  <th>
                    <span class="d-block">क्रम&nbsp;सं०</span>
                    Sl. No.
                  </th>
                  <th>
                    <span class="d-block">संख्या</span>
                    Quantity
                  </th>
                  <th class="pe-3 item-remarks">Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($i = 0; $i < min($no_of_items, $max_entries_allowed); $i++): ?>
                  <tr>
                    <td class="text-center">
                      <?= $i+1 ?>
                    </td>
                    <td colspan="2">
                      <?= $srq_items[$i]['particular'] ?>
                    </td>
                    <td>
                      <?= $srq_items[$i]['modelno'] ?? "NIL" ?>
                    </td>
                    <td>
                      <?= $srq_items[$i]['serialno'] ?? "NIL" ?>
                    </td>
                    <td>
                      <?= $srq_items[$i]['quantity'] ?? "NIL" ?>
                    </td>
                    <td class="pe-3 item-remarks">
                      <?= $srq_items[$i]['remarks'] ?? "NIL" ?>
                    </td>
                  </tr>
                  <?php
                endfor; ?>
                <?php if ($no_of_items > $max_entries_allowed): ?>
                  <tr>
                    <td id="extratext" colspan="7" class="text-muted text-center">
                      <em>Additional items are attacted to Annexure-A</em>
                    </td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
        </div>
      </div>

      <div class="row px-3 pt-3">
        <div class="col">
          <p class="m-0">प्रविष्टि सं० प्रवेश/निकासी बही Entry No. In / Out register</p>
        </div>
      </div>

      <div class="row px-3 pt-3">
        <div class="col">
          <p class="m-0">निकासी अधिकारी के हस्ताक्षर</p>
          <p class="m-0">Signature of Officer Authorising Movements</p>
        </div>
        <div class="col text-end">
          <p class="m-0">प्राप्तकर्ता के हस्ताक्षर / Signature of Receipent</p>
        </div>
      </div>

      <div class="row px-3 pt-2 align-items-end">
        <div class="col">
          <p class="m-0">नाम एवं पद नाम / Name & Designation</p>
        </div>
        <div class="col text-end">
          <p class="m-0">सुरक्षा पर्यवेक्षक के हस्ताक्षर</p>
          <p class="m-0">Signature of Security Supervisor</p>
        </div>
      </div>

    </div>
  <?php endforeach; ?>
  <?php if($no_of_items > $max_entries_allowed): ?>
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
            <th class="ps-3" width="70px">
              <span class="d-block">क्रम&nbsp;सं०</span>
              Sl. No.
            </th>
            <th colspan="2">
              <span class="d-block">विवरण</span>
              Particulars
            </th>
            <th>
              <span class="d-block">माडल नं०</span>
              Model No.
            </th>
            <th>
              <span class="d-block">क्रम&nbsp;सं०</span>
              SerialNo.
            </th>
            <th>
              <span class="d-block">संख्या</span>
              Quantity
            </th>
            <th class="pe-3 item-remarks">Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; $i < $no_of_items; $i++): ?>
              <tr>
                <td class="text-center">
                  <?= $i+1 ?>
                </td>
                <td colspan="2">
                  <?= $srq_items[$i]['particular'] ?>
                </td>
                <td>
                  <?= $srq_items[$i]['modelno'] ?? "NIL" ?>
                </td>
                <td>
                  <?= $srq_items[$i]['serialno'] ?? "NIL" ?>
                </td>
                <td>
                  <?= $srq_items[$i]['quantity'] ?? "NIL" ?>
                </td>
                <td class="pe-3 item-remarks">
                  <?= $srq_items[$i]['remarks'] ?? "NIL" ?>
                </td>
              </tr>
            <?php endfor; ?>
        </tbody>
      </table>
    </div>
    <br>
  </div>
  <?php endif; ?>
  <br>

  <div class='d-flex gap-2 justify-content-center actionContainer my-3'>
    <a class="btn btn-outline-secondary" 
    <?php if(isset($_GET["goback"])): ?>
      href='gatepass'
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
        <label class="form-check-label" for="chkShowRemarks">Remove Items Remarks</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="chkTimeToggle">
        <label class="form-check-label" for="chkTimeToggle">Remove Time</label>
      </div>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" role="switch" id="chkOrgName">
        <label class="form-check-label" for="chkOrgName">Short <attr title="Organization">org.</attr> name</label>
      </div>
    </div>
  </div>

</div>

<script>

  const gtpDateTime = $('gtpDateTime');
  const originalText = gtpDateTime.innerText; // Store the original full text

  $('chkTimeToggle').addEventListener("change", e => {
      if (e.target.checked) {
          gtpDateTime.innerText = originalText.slice(0, -10); // Truncate when checked
      } else {
          gtpDateTime.innerText = originalText; // Restore original text when unchecked
      }
      localStorage.setItem('chkTimeToggle', e.target.checked);
  });

  let orgname = $('orgname').innerText;

  $("chkOrgName").addEventListener("change", e => {
    $('orgname').innerText = e.target.checked ? createShortForm(orgname) : orgname;
  });

</script>
<script src="resources/js/print-common.js"></script>
<script>
  setTimeout(() => {
    stateRestore("chkTimeToggle");
  }, 200);
</script>

<?php
include 'template/footer.php';
?>