<?php
$cpage = "Clients List";
include 'template/header.php';
include 'template/pagination.php';

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
// Number of records to show on each page
$records_per_page = 10;
// Get the search text
$searchTxt = isset($_POST["searchTxt"]) ? $_POST["searchTxt"] : ""; // getting search text

// Prepare the SQL statement with placeholders
$query = "SELECT tbl_client.*, tbl_ctype.ct_desc FROM tbl_client LEFT JOIN tbl_ctype ON tbl_ctype.ct_code=tbl_client.c_cat WHERE tbl_client.c_name LIKE ? OR tbl_client.c_code LIKE ? ORDER BY tbl_client.c_name LIMIT ?, ?";

$searchTxtNew = '%' . $searchTxt . '%'; // Modified the wildcard placement

$startLimit = (($page - 1) * $records_per_page);
$limit = $records_per_page;

/* ------- For searching users -------- */
// Prepare the statement
$stmt = $conn->prepare($query);
$stmt->bind_param("ssii", $searchTxtNew, $searchTxtNew, $startLimit, $limit); // Updated the bind_param to include two strings
// Execute the statement
$stmt->execute();
// Get the result
$result = $stmt->get_result();
// Fetch the records
$clients = $result->fetch_all(MYSQLI_ASSOC);
// Free the result and close the statement
$result->free_result();
$stmt->close();
/* ------- END -------- */



/* ----------- 
Get the total number of clients, this is so we can determine whether there should be a next and previous button 
------------- */
$query = "SELECT COUNT(*) FROM tbl_client";
$whereClause = "";

if ($searchTxt != "") {
    $whereClause = " WHERE c_name LIKE ? OR c_code LIKE ?";
}

$query .= $whereClause;

$stmt = $conn->prepare($query);

if ($searchTxt != "") {
    $searchParam = '%' . $searchTxt . '%';
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

$stmt->execute();
$stmt->bind_result($num_clients);
$stmt->fetch();
$stmt->close();

/* ---------- END -------------- */

$pgname = basename($_SERVER['PHP_SELF'], ".php"); // Getting the current page name 

?>

<div class="col content table-responsive" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-people-fill pe-3"></i>
        Client List
    </h2>
    <div class='px-4 pb-4'>
        <form action="<?= $pgname ?>" method="POST" class='table-search-btn' onsubmit="return validateSearch(2)">
            <button class="btn btn-dark" type='button' data-bs-toggle="modal" data-bs-target="#createContactModal">
                Create Contact
                <i class="bi bi-person-plus ps-1"></i>
            </button>
            <div class="input-group">
                <input type="text" id="searchTxt" name="searchTxt" class="form-control" placeholder="Search for Clients name..." value="<?php echo $searchTxt; ?>" />
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <?php if ($num_clients >= 1) : ?>
            <table class="table table-bordered mt-4 rounded">
                <thead class='table-light'>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Code</th>
                        <th scope="col">Client Name &amp; Address</th>
                        <th scope="col">Contact</th>
                        <th scope="col">GST</th>
                        <th scope="col">Organization</th>
                        <th scope="col">Remarks</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1;
                    $sn = ($page - 1) * $records_per_page + $sn;
                    foreach ($clients as $client) : ?>
                        <tr>
                            <td class='table-light'>
                                <?= $sn++; ?>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['c_code'] ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['c_code'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['c_name'] . '; ' . $client['c_addr']; ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['c_name'] . ";  " . $client['c_addr'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['c_phn'] . '; ' . $client['c_email']; ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['c_phn'] . ";  " . $client['c_email'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['c_gst']; ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['c_gst'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['ct_desc']; ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['ct_desc'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $client['c_rmk']; ?>')" ondblclick="makeEditable(this)">
                                    <?= $client['c_rmk'] ?>
                                </span>
                            </td>
                            <td class="actions" width='90px'>
                                <button type="button" class="btn btn-warning btn-sm" onclick="updateClient('<?= $client['c_code'] ?>', 
                                    '<?= $client['c_name'] ?>', '<?= $client['c_addr'] ?>', 
                                    '<?= $client['c_email'] ?>', '<?= $client['c_phn'] ?>', 
                                    '<?= $client['c_gst'] ?>', '<?= $client['c_rmk'] ?>', 
                                    '<?= $client['c_cat'] ?>', '<?= $client['c_pan'] ?>')">
                                    <i class=" bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteClient('<?= $client['c_code'] ?>')">
                                    <i class=" bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else : ?>
            <div class="alert alert-warning mt-3"> <i class="bi bi-exclamation-circle pe-1"></i> No records found here...
            </div>
        <?php endif; ?>

        <?= makePagination($num_clients, $records_per_page, $page, $pgname); ?>

    </div>
</div>


<script>
    const updateClient = (cid, name, address, email, phone, gst, remarks, cat, pan) => {
        const client_modal = new bootstrap.Modal($('createContactModal'));
        const cmodal = client_modal._element;
        cmodal.querySelector(".modal-title").innerHTML = `
            <i class="bi bi-person-fill-up pe-1"></i>
            Update Client Details for ${cid}
        `;

        $("oop_clientname").value = name;
        $("oop_clientemail").value = email;
        $("oop_clientphone").value = phone;
        $("oop_clientaddress").value = address;
        $("oop_clientpan").value = pan;
        $("oop_clientgst").value = gst;
        $("oop_clientcat").value = cat;
        $("oop_clientremark").value = remarks;

        cmodal.querySelector("form").setAttribute("data-update", true);
        cmodal.querySelector("form").setAttribute("data-id", cid);

        client_modal.show();
    };

    const deleteClient = async (cid) => {
        if (confirm("Are you sure want to delete this client ?")) {
            await fetch('api/client_delete.php', {
                    method: "POST",
                    body: JSON.stringify({
                        "c_code": cid,
                    })
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        showMessage("There was a problem in deleting the new client...");
                        console.error(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    }

    const makeEditable = (element) => {
        let div = document.createElement("div");
        div.classList.add("input-group");

        let input = document.createElement("textarea");
        input.type = "text";
        input.value = element.innerText;
        input.classList.add("form-control");
        input.classList.add("form-control-sm"); // Add form-control-sm class
        input.style.fontSize = "0.8rem";

        let button1 = document.createElement("button");
        button1.classList.add("btn");
        button1.classList.add("btn-success");
        button1.classList.add("btn-sm");
        button1.innerHTML = "<i class='bi bi-check-lg'></i>";

        let button2 = document.createElement("button");
        button2.classList.add("btn");
        button2.classList.add("btn-danger");
        button2.classList.add("btn-sm");
        button2.innerHTML = "<i class='bi bi-x-lg'></i>";

        div.appendChild(button2);
        div.appendChild(input);
        div.appendChild(button1);
        element.parentNode.replaceChild(div, element);
        input.focus();

        const revertToSpan = () => {
            let span = document.createElement("span");
            span.classList.add("editable");
            span.innerText = input.value;
            span.ondblclick = () => makeEditable(span);

            div.parentNode.replaceChild(span, div);
        };

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                revertToSpan();
            }
        });

        button2.addEventListener("click", () => {
            revertToSpan(); // Cancel Button Clicked
        })

        button1.addEventListener("click", () => {
            alert("data");
        })
    }
</script>

<?php

include 'template/footer.php';
?>
