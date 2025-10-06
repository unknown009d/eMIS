<?php
$cpage = "Project Profile List";
include 'template/header.php';
include 'template/pagination.php';
// include 'api/connect.php';

$j_type = "IT";

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
// Number of records to show on each page
$records_per_page = 10;
// Get the search text
$searchTxt = isset($_POST["searchTxt"]) ? $_POST["searchTxt"] : ""; // getting search text

// Prepare the SQL statement with placeholders
// $query = "SELECT * FROM srq_all WHERE j_status <> 'cancelled' AND c_name LIKE ? ORDER BY fyear ASC, reg_id DESC LIMIT ?, ?";
$query = "SELECT * FROM project_all WHERE c_name LIKE ? ORDER BY p_id DESC LIMIT ?, ?";
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
$query = "SELECT COUNT(*) FROM project_all";
$stmt = $conn->prepare($query);

$stmt->execute();
$stmt->bind_result($num_records);
$stmt->fetch();
$stmt->close();

/* ---------- END -------------- */

$pgname = basename($_SERVER['PHP_SELF'], ".php"); // Getting the current page name 



?>

<div class="col content" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-server pe-3"></i>
        Project Profile List
    </h2>
    <div class='px-4 pb-4'>
        <form action="<?= $pgname ?>" method="POST" class='table-search-btn' onsubmit="return validateSearch(3)">
            <div>
                <button type="button" class="btn btn-dark" type="button" data-bs-toggle="modal"
                    data-bs-target="#ppModal">
                    New Project Profile <i class="bi bi-plus-lg ps-1"></i>
                </button>
            </div>
            <div class="input-group">
                <input type="text" id="searchTxt" name="searchTxt" class="form-control"
                    placeholder="Search for client name..." value="<?php echo $searchTxt; ?>" />
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        <?php if ($num_records <= 0 || count($resData) <= 0): ?>
            <div class="alert alert-warning my-3" role="alert">
                <i class="bi bi-exclamation-triangle pe-1"></i> Sorry, we couldn't locate any data you were looking for.
            </div>
        <?php else: ?>

            <table class="table table-bordered mt-4 rounded">
                <thead class='table-light'>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Project&nbsp;No.</th>
                        <th scope="col">SrNo.</th>
                        <th scope="col">Date</th>
                        <th scope="col">Client</th>
                        <th scope="col">Contact</th>
                        <th scope="col">Organization</th>
                        <th scope="col">Nomenclature</th>
                        <th scope="col" width='50px'></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $sn = 1;
                    $sn = ($page - 1) * $records_per_page + $sn;
                    foreach ($resData as $data): 
                    $certificate = selectQ($conn, "SELECT * FROM `tbl_project_profile` WHERE p_id=? LIMIT 1", [$data['p_id']])[0]['certificate'];
                    ?>
                        <tr title="<?= $certificate != NULL ? 'Project is closed' : ($data['priority'] ? 'Project is high priority' : '') ?>"
                        class="<?= $certificate != NULL ? 'table-success' : ($data['priority'] ? 'table-danger' : '') ?>" data-priority="<?= $data["priority"] ?>"
                            <?php if($certificate == NULL): ?>
                                ondblclick='setHighPriority("<?= $data["p_id"] ?>", this);'>
                            <?php else: ?>
                                ondblclick="showMessage('Priority can\'t be updated after project completion', 'warning')">
                            <?php endif; ?>
                            <td class='table-light'>
                                <?php
                                echo $sn++;
                                ?>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['p_id'] ?>')">
                                    <?= $data['p_id'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['sr_no'] ?>')">
                                    <?= $data['sr_no'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['p_date'] ?>')">
                                    <?= $data['p_date'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['c_name'] ?>')">
                                    <?= $data['c_name'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['c_phn'] ?>')">
                                    <?= $data['c_phn'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['ct_desc']; ?>')">
                                    <?= $data['ct_desc'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="editable" onclick="copytoclip('<?= $data['nom'] ?>')">
                                    <?= $data['nom'] ?>
                                </span>
                            </td>
                            <td class="actions" style="white-space: nowrap;">
                                <a href="projectProfile?pid=<?= $data['p_id'] ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?= makePagination($num_records, $records_per_page, $page, $pgname); ?>

        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="ppModal">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header">
                <h4 class="modal-title"><i class="bi bi-server pe-1"></i> New Project Profile</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="row d-flex justify-content-end">
                    <div class="col">
                        <form class="input-group mb-3" id="frmNonProjectSearch">
                            <input type="text" id="NonPPsearchInput" class="form-control"
                                placeholder="Enter SrqNo. to search">
                            <button class="btn btn-primary" title="Search list with query"><i class="bi bi-search"></i></button>
                        </form>
                    </div>
                </div>
                <table id="projectProfileTable" class="table table-bordered normal table-long-fix">
                    <thead>
                        <tr class='table-light'>
                            <th>ID</th>
                            <th>SrqNo.</th>
                            <th>Nomenclature</th>
                            <th>URL</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="projectProfileBody">
                        <!-- Table content will be added dynamically -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-end align-items-center">
                    <button id="showMoreBtn" class="btn btn-primary">
                        Show More
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

    const listAllNonProject = async () => {
        let currentPage = 1;

        const fetchAndRenderData = async (page, searchQuery = '') => {
            try {
                // Modify the fetch URL to include the search query as a parameter
                const res = await fetch(`api/list_non_projects.php?page=${page}&search=${searchQuery}`);
                let data = await res.json();

                // Check if data is available for the current page
                if (data.length > 0) {
                    const tableBody = $('projectProfileBody');

                    // Clear the table body for new data
                    if (page === 1) {
                        tableBody.innerHTML = '';
                    }

                    data.forEach((entry) => {
                        const row = document.createElement('tr');

                        // Create table cells for each data field
                        const col_id = document.createElement('td');
                        col_id.textContent = entry.id;
                        row.appendChild(col_id);

                        const col_srno = document.createElement('td');
                        col_srno.textContent = entry.srno;
                        row.appendChild(col_srno);

                        const col_nom = document.createElement('td');
                        col_nom.textContent = entry.nom;
                        row.appendChild(col_nom);

                        const col_url = document.createElement('td');
                        if (entry.url != null) {
                            const a_url = document.createElement('a');
                            a_url.href = entry.long_url;
                            a_url.textContent = entry.url;
                            a_url.target = '_blank';
                            col_url.appendChild(a_url);
                        } else {
                            col_url.textContent = 'NIL';
                        }
                        col_url.title = entry.long_url;
                        row.appendChild(col_url);

                        const col_action = document.createElement('td');
                        const btnMakeProject = document.createElement('button');
                        btnMakeProject.classList.add('btn', 'btn-success', 'btn-sm', 'rounded');
                        btnMakeProject.title = 'Create project profile for ' + entry.srno;
                        btnMakeProject.onclick = () => {
                            createProjectProfile(entry.id, entry.srno);
                        }
                        const btnIcon = document.createElement('i');
                        btnIcon.classList.add('bi', 'bi-plus-circle');
                        btnMakeProject.appendChild(btnIcon);
                        col_action.append(btnMakeProject);
                        row.appendChild(col_action);

                        tableBody.appendChild(row);
                    });

                } else {
                    if (page == 1) {
                        // If no data is available, display a message
                        $('projectProfileBody').innerHTML = '';
                        $('projectProfileBody').appendChild(noContent());
                        $('showMoreBtn').style.display = 'none';
                    }
                }
                // Check if there are more entries left for the next page
                if (data.length >= 10) {
                    // If there are more entries, show the "Show More" button
                    $('showMoreBtn').style.display = 'block';
                } else {
                    // If there are no more entries, hide the "Show More" button
                    $('showMoreBtn').style.display = 'none';
                }

            } catch (err) {
                console.error(err);
            }
        };

        // Function to load the next page on clicking the "Show More" button
        const showMore = async () => {
            currentPage++;
            await fetchAndRenderData(currentPage, $('NonPPsearchInput').value);
            $('projectProfileBody').parentNode.parentNode.scrollTop = $('projectProfileBody').parentNode.parentNode.scrollHeight;
        };

        // Attach the "Show More" button click event
        $('showMoreBtn').addEventListener('click', showMore);

        // Attach the search button click event
        $('frmNonProjectSearch').addEventListener('submit', (e) => {
            e.preventDefault();
            // When search button is clicked, fetch data for the first page with the search query
            currentPage = 1;
            const searchQuery = $('NonPPsearchInput').value;
            fetchAndRenderData(currentPage, searchQuery);
        });

        // Fetch and render data for the first page (without any search query initially)
        fetchAndRenderData(currentPage);
    };

    listAllNonProject();

    const createProjectProfile = async (id, srno) => {
        if (!confirm("Are you sure want to create a project for " + srno)) return;
        await fetch("api/createProjectProfile.php", {
            method: "POST",
            body: JSON.stringify({
                "sr_no": srno,
                "nom_id": id,
            }),
        }).then(req => {
            return req.json();
        }).then(data => {
            if (data.success = true) {
                showMessage("Project Created", "success");
                if (data.pid != undefined || data.pid != null) {
                    sendLogs(data.pid, "Project Initiated")
                    location.href = 'projectProfile.php?pid=' + data.pid;
                }
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
    }

    const setHighPriority = async (pid, row) => {
        let priority = row.getAttribute("data-priority");
        let sure = confirm(`Are you sure you want to toggle ${pid} priority?`);
        if (sure) {
            await fetch("api/updateProjectProfile.php", {
                method: "POST",
                body: JSON.stringify({
                    "id": pid,
                    "data": {
                        "priority": priority == 0 ? 1 : 0,
                    }
                }),
            }).then(req => {
                return req.json();
            }).then(data => {
                if (data.success == true) {
                    showMessage(`<i class="bi bi-graph-up-arrow pe-1"></i> ${pid} priority is changed`, "success");
                    row.classList.toggle('table-danger');
                    row.setAttribute("data-priority", priority == 0 ? 1 : 0);
                    sendLogs(pid, "priority was updated from list");
                } else {
                    showMessage("There was a problem in updating projectprofile Please check the console...", "warning");
                    console.error(data.message);
                }
            }).catch(err => {
                showMessage("There was a problem in updating projectprofilelist Please check the console...", "warning");
                console.error(err);
            });
        }
    }
</script>

<?php
include 'template/footer.php';
?>
