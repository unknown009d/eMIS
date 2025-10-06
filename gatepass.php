<?php
$cpage = "Gatepass";
include 'template/header.php';

// Selecting all the client details
$query = "SELECT c_name FROM tbl_client ORDER BY c_name ASC";
$result = mysqli_query($conn, $query);
$clients = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<form class="col content mb-4" id="frmGatepass" >
    <h2 class='pageHeading'>
        <i class="bi bi-receipt pe-3"></i>
        Gate Pass
    </h2>

    <div class="m-auto dcardform mb-3">
        <div class="d-flex justify-content-end">
            <a href="gatepassList">View all gatepass</a>
        </div>
    </div>

    <div class="card m-auto dcardform">
        <div class="card-header">
            <h5 class="card-title p-2 m-0 ps-0">Gate Pass Form</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12 col-md-8 col-lg-4 mb-3">
                    <label for="gtpdate" class="form-label">Date & Time (taken)</label>
                    <input type="datetime-local" class="form-control" id="gtpdate">
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <label for="gtpret" class="form-label">Returnable / Non-Returnable</label>
                    <select id="gtpret" class="form-select">
                        <option value="1">Returnable</option>
                        <option value="0">Non-Returnable</option>
                    </select>
                </div>
                <div class="col-sm-12 col-md-8 col-lg-4 mb-4">
                    <label for="gtpname" class="form-label">Name of the <abbr title="Organization">org.</abbr></label>
                    <input type="text" list='clients' class="form-control" id="gtpname" placeholder="eg. STQC, NIC, etc..." required>
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
                <div class="col-sm-12 col-md-8 col-lg-4 mb-3">
                    <label for="gtpremarks" class="form-label">Remarks</label>
                    <input type="text" class="form-control" id="gtpremarks" placeholder="Enter your remarks here">
                </div>
                <div class="col-sm-12 col-md-8 col-lg-4 mb-3">
                    <label for="gtpreference" class="form-label">Reference (if)</label>
                    <input type="text" class="form-control" id="gtpreference" placeholder="Enter a reference no. (if any)">
                </div>
            </div>
        </div>
    </div>

    <div class="card m-auto dcardform mt-4">
        <div class="card-header d-flex justify-content-between">
            <h5 class="card-title p-2 m-0 ps-0">Materials for gatepass</h5>
        </div>
        <div class="card-body">
            <div class="row mt-2">
                <div class="col-3">
                    <label class="form-label">Particulars</label>
                </div>
                <div class="col">
                    <label class="form-label">Model No.</label>
                </div>
                <div class="col">
                    <label class="form-label">Sl. No.</label>
                </div>
                <div class="col">
                    <label class="form-label">Quantity</label>
                </div>
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
                            <input type="text" list="nomlist" class="form-control particulars" autocomplete="off" placeholder="eg. Desktop, Scanner" required>
                            <datalist id='nomlist'>
                            </datalist>
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <input type="text" class="form-control modelno" min="0" placeholder="">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <input type="text" class="form-control slno">
                        </div>
                    </div>
                    <div class="col">
                        <div class="mb-3">
                            <input type="number" class="form-control quantity" min=1 value="1" required>
                        </div>
                    </div>
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

    <!-- Submit Card -->
    <div class="m-auto dcardform mt-4 mb-5">
        <div class="row mt-2">
            <!-- Submit Button -->
            <div class="row w-100 d-flex justify-content-end mt-3 gap-2">
                <button type="submit" class="btn btn-success w-auto">
                    Generate Gatepass <i class="bi bi-pass-fill ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</form>

<script>

    $('frmGatepass').addEventListener("submit", async e => {
        e.preventDefault();
        await fetch("api/gatepass_insert.php", {
            method: "POST",
            body: JSON.stringify({
                items: collectRowValues(),
                returnable: $('gtpret').value,
                datetime: $('gtpdate').value,
                org_name: $('gtpname').value,
                reference: $('gtpreference').value,
                gtpremarks: $('gtpremarks').value
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            if (data.success = true) {
                showMessage("Gatepass entry was successfully completed...", "success");
                location.reload();
                location.href = "gatepassPrint.php?id=" + data.id + "&goback=1";
            } else {
                showMessage("There was a problem in inserting gatepass, Please check the console...", "warning");
                console.error(data.message);
            }
        }).catch(err => {
            showMessage("There was a problem in inserting gatepass, Please check the console...", "warning");
            console.error(err);
        });
    });

    updateDate('gtpdate');

    const getNoms = async () => {
        await fetch("api/nom_list.php", {
            method: "POST",
            body: JSON.stringify({}),
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
        newRow.querySelector(".quantity").value = 1;
        checkEntryCount();
    };

    let removeRow = (rowElement) => {
        let entryCount = document.querySelectorAll("#dynamicRows .row").length; // Excluding the template row
        if (entryCount > 1) {
            rowElement.remove();
            checkEntryCount();
        }
    };

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

    const collectRowValues = () => {
        const rows = document.querySelectorAll(".row-item");
        const rowData = [];

        rows.forEach((row) => {
            const rowValues = {
                particulars: row.querySelector(".particulars").value,
                modelno: row.querySelector(".modelno").value,
                slno: row.querySelector(".slno").value,
                quantity: row.querySelector(".quantity").value,
                remarks: row.querySelector(".nremarks").value,
            };
            rowData.push(rowValues);
        });
        return rowData;
    };
</script>


<?php
include 'template/footer.php';
?>