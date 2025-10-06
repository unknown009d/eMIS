<?php
$page = strtoupper($_GET['s']);
$cpage = "Service Saved Successfully";
include 'template/header.php';

$srqno = $_GET["srqno"];
$cname = $_GET["cname"];

?>

<div class="col content d-flex align-items-center flex-column justify-items-center bg-transparent mt-5"
    style='z-index: 2;'>
    <div class='alert alert-success m-4 d-flex flex-column align-items-center justify-content-center'
        style=" max-width: 420px;" role="alert">
        <i class='bi bi-check2-circle pe-1' style='font-size: 4rem'></i>
        <br>
        <h5 class="fw-bold">Service Successfully Saved</h5>
        <span class='text-center'>
            Service Request of
            <?= $srqno ?> is assigned to
            <?= $cname ?> successfully.
        </span>
    </div>
    <div class='d-flex gap-2'>
        <a class="btn btn-outline-secondary" href='service?s=<?= $page ?>'>
            <i class="bi bi-box-arrow-up-left pe-1"></i> Go to previous page
        </a>
        <a class="btn btn-primary" href='servicePrint?id=<?= $srqno ?>&srqgen=true'>
            <i class="bi bi-printer-fill pe-1"></i> Print this Request
        </a>
    </div>
</div>

<?php
include 'template/footer.php'
    ?>