<?php
$cpage = "Print Request Form";
include 'template/header.php';

// include "api/connect.php";

//get parameters
$sr_no = isset($_GET['id']) ? $_GET['id'] : '';

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

/* Replaces multiple consecutive whitespace characters in the value of $srq[0]['j_type']
 with a single space and assigns the modified value to $page */
$page = preg_replace('/\s+/', ' ', $srq[0]['j_type']);

?>

<div class="col content p-4">
    <div class="print_area text-black">
        <?php foreach ($srq as $s): ?>

            <div class="text-center mx-3 parts">
                <p class="m-0 fs-4 fw-bold">GOVERNMENT OF INDIA</p>
                <p class="m-0 fs-6">MINISTRY OF ELECTRONICS & INFORMATION TECHNOLOGY</p>
                <p class="m-0 fs-4 fw-bold">STQC Directorate</p>
                <p class="m-0 fs-6"> Electronics Test & Development Center, Agartala</p>
                <p class="m-0 fs-6">Indranagar, P.O. Kunjaban, Agartala, 799006</p>
                <p class="m-0 fs-6">etdcag@stqc.gov.in | 0381-235 9140</p>
            </div>
            <h4 class="text-center p-3 my-4 bg-pcolor fw-bold">SERVICE REQUEST FORM</h4>
            <div class="row mx-3 text-start">
                <p class="m-0 col text-left">
                    <label class="fw-bold"> SRF No.:</label>
                    <?= $s['sr_no'] ?>
                </p>
                <p class="m-0 col text-center">
                    <label class="fw-bold">Date : </label>
                    <?= $s['sr_date'] ?>
                </p>
                <p class="m-0 col text-end">
                    <label class="fw-bold">Job Location : </label>
                    <?= ($s['j_location'] == 'I') ? 'Inhouse' : (($s['j_location'] == 'O') ? 'Onsite' : '') ?>
                </p>
            </div>
            <?php if (!empty($s['rmks'])): ?>
                <div class="row mx-3 text-justify">
                    <p class="m-0 col">
                        <label class="fw-bold">Remarks : </label>
                        <?= $s['rmks'] ?>
                    </p>
                </div>
            <?php endif; ?>
            <!-- client details -->
            <p class="fw-bold fs-6 text-center m-0 mt-4 py-3 border-top bg-light">Details of Individual |
                Organization/Office |
                Department/Institute
            </p>
            <table class="table table-bordered">
                <thead class="table-header table-light">
                    <tr>
                        <th class="fw-bold">Name</th>
                        <th class="fw-bold">Address</th>
                        <th class="fw-bold">Category</th>
                        <th class="fw-bold">Phone</th>
                        <th class="fw-bold">Email</th>
                        <th class="fw-bold">GST&nbsp;No.</th>
                        <?php if (!empty($s['c_rmk'])): ?>
                            <th class="fw-bold">Remarks</th>
                        <?php endif; ?>
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
                            <?= $s['ct_desc'] ?>
                        </td>
                        <td>
                            <?= $s['c_phn'] ?>
                        </td>
                        <td>
                            <?= $s['c_email'] ?>
                        </td>
                        <td>
                            <?= $s['c_gst'] ?>
                        </td>
                        <?php if (!empty($s['c_rmk'])): ?>
                            <td>
                                <?= $s['c_rmk'] ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                </tbody>
            </table>
            <!-- end of client details -->
            <!-- nomenclature details -->
            <p class="fw-bold fs-6 text-center m-0 py-3 border-top bg-light">
                Nomenclature details / Item details
            </p>
            <table class="table table-bordered">
                <thead class="table-header table-light">
                    <tr>
                        <th class="fw-bold" colspan="3">Nomenclature</th>
                        <th class="fw-bold">Rate</th>
                        <th class="fw-bold">Quantity</th>
                        <th class="fw-bold">Total</th>
                        <th class="fw-bold">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1;
                    foreach ($srq_nom as $snom): ?>
                        <tr>
                            <td colspan="3">
                                <?= $snom['nom'] ?>
                            </td>
                            <td>
                                <?= $snom['t_charge'] ?>
                            </td>
                            <td>
                                <?= $snom['qty'] ?>
                            </td>
                            <td>
                                <?= $snom['t_charge'] * $snom['qty'] ?>
                            </td>
                            <td>
                                <?= $s['rmks'] ?>
                            </td>
                        </tr>
                        <?php $count++;
                    endforeach; ?>
                </tbody>
            </table>

            <!-- Total Calculation for nomenclature -->
            <table class="table table-bordered table-top-border-none">
                <tbody>
                    <tr>
                        <td colspan="5" class="table-light">
                            Total Charge
                        </td>
                        <td colspan="2">
                            &#8377;
                            <?= $s['t_charge'] ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="table-light">
                            GST :
                            <?php if ($s['igst'] > 0): ?>
                                IGST (18%) [ &#8377;
                                <?= $s['igst'] ?> ]
                            <?php elseif ($s['cgst'] > 0 || $s['sgst'] > 0): ?>
                                CSGT (9%) [ <strong>&#8377;
                                    <?= $s['cgst'] ?>
                                </strong> ]
                                & SGST (9%) [ <strong>&#8377;
                                    <?= $s['cgst'] ?>
                                </strong> ]
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
                        <td colspan="5" class="table-light fw-bold text-success">
                            Grand Total :
                        </td>
                        <td colspan="2" class="fw-bold text-success">
                            &#8377;
                            <?= $s['tot_amount'] ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- end of nomenclature details -->
            <!-- job location & GST -->
        <?php endforeach; ?>
        <!-- END -->
    </div>
    <br>
    <div class='d-flex gap-2 justify-content-center actionContainer mt-3'>
        <a class="btn btn-outline-secondary" href='service?s=<?= $page ?>'>
            <i class="bi bi-box-arrow-up-left pe-1"></i> Go to request form
        </a>
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer-fill pe-1"></i> Print
        </button>
    </div>
</div>


<?php
include 'template/footer.php';
?>