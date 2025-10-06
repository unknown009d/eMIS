<?php
$cpage = "List of Gatepass";
include 'template/header.php';
include 'template/pagination.php';
// include 'api/connect.php';

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
// Number of records to show on each page
$records_per_page = 10;
// Get the search text
$searchTxt = isset($_POST["searchTxt"]) ? $_POST["searchTxt"] : ""; // getting search text

// Prepare the SQL statement with placeholders
$query = "SELECT * FROM tbl_gatepass WHERE org_name LIKE ? ORDER BY slno DESC LIMIT ?, ?";
$searchTxtNew = '' . $searchTxt . '%';
$startLimit = (($page - 1) * $records_per_page);
$limit = $records_per_page;


/* ------- For searching users -------- */
// Prepare the statement
$searchTxtNew = '' . $searchTxt . '%';
$stmt = $conn->prepare($query);
$stmt->bind_param("sii", $searchTxtNew, $startLimit, $limit);
// Execute the statement
$stmt->execute();
// Get the result
$result = $stmt->get_result();
// Fetch the records
$resData = $result->fetch_all(MYSQLI_ASSOC);
// Free the result and close the statement
$result->free_result();
$stmt->close();
/* ------- END -------- */

/* ----------- 
Get the total number of records, this is so we can determine whether there should be a next and previous button 
------------- */
$query = "SELECT COUNT(*) FROM tbl_gatepass";
$whereClause = "";

if ($searchTxt != "") {
    $whereClause = " WHERE org_name LIKE ?";
}

$query .= $whereClause;

$stmt = $conn->prepare($query);

if ($searchTxt != "") {
    $searchParam = '' . $searchTxt . '%';
    $stmt->bind_param("s", $searchParam);
}

$stmt->execute();
$stmt->bind_result($num_records);
$stmt->fetch();
$stmt->close();

/* ---------- END -------------- */

$pgname = basename($_SERVER['PHP_SELF'], ".php"); // Getting the current page name 

?>

<div class="col content table-responsive" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-receipt pe-3"></i>
        Gatepass List
    </h2>
    <div class='px-4 pb-4'>
        <form action="<?= $pgname ?>" method="POST" class='table-search-btn' onsubmit="return validateSearch(3)">
            <div>
                <a href="gatepass" class="btn btn-dark">Generate Gatepass <i class="bi bi-receipt ps-1"></i> </a>
            </div>
            <div class="input-group">
                <input type="text" id="searchTxt" name="searchTxt" class="form-control" placeholder="Search for client name..." value="<?php echo $searchTxt; ?>" />
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <?php if ($num_records <= 0 || count($resData) <= 0) : ?>

            <div class="alert alert-warning my-3" role="alert">
                <i class="bi bi-exclamation-triangle pe-1"></i> Sorry, we couldn't locate any data you were looking for.
            </div>

        <?php else : ?>

            <table class="table table-bordered mt-4 rounded">
                <thead class='table-light'>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Sl&nbsp;No.</th>
                        <th scope="col">Returnable?</th>
                        <th scope="col">Client&nbsp;Name</th>
                        <th scope="col">Reference</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">DateTime</th>
                        <th scope="col" width='80px'></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1;
                    $sn = ($page - 1) * $records_per_page + $sn;
                    foreach ($resData as $data) : ?>
                        <tr>
                            <td class='table-light'>
                                <?php
                                echo $sn++;
                                ?>
                            </td>
                            <td>
                                <span>
                                    <?= addZeros($data['slno'], 4) ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?= $data['returnable'] ? "Returnable" : "Non-Returnable" ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= $data['org_name'] ?>">
                                    <?= truncateText($data['org_name'], 40) ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= $data['reference'] ?>">
                                    <?= truncateText($data['reference'] ?? "NIL", 40) ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= $data['remarks'] ?>">
                                    <?= truncateText($data['remarks'] ?? "NIL", 30) ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?= fdateeasy($data['datetime']) ?>
                                </span>
                            </td>
                            <td class="actions" style="white-space: nowrap;">
                                <a class="btn btn-outline-secondary btn-sm" href='gatepassPrint?id=<?= $data['slno'] ?>'>
                                    <i class="bi bi-printer-fill"></i>
                                </a>
                                <button class="btn btn-danger btn-sm" onclick="deleteGatepass('<?= $data['slno'] ?>')">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?= makePagination($num_records, $records_per_page, $page, $pgname); ?>

        <?php endif; ?>

    </div>
</div>
</div>

<script>

const deleteGatepass = async slno => {
    if(!confirm("Are you sure want to delete this gatepass ?")) return;
    await fetch("api/gatepass_delete.php",{
        method: "POST",
        body: JSON.stringify({
            slno: slno
        })
    }).then(res => res.json())
    .then(data => {
        if(data.success){
            showMessage(data.message, "success");
            location.reload();
        }else{
            showMessage("There was a problem in deleting the gatepass. Please Check console", "danger");
            console.error(data.message);
        }
    }).catch(e => {
        showMessage("There was a problem in the server...");
        console.error(e);
    })
};


</script>

<?php
include 'template/footer.php';
?>
