<div class="col content table-responsive" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-clipboard-data pe-3"></i>
        Service List
    </h2>
    <div class='px-4 pb-4'>
        <form action="<?= $pgname ?>" method="POST" class='table-search-btn' onsubmit="return validateSearch(3)">
            <div class="dropdown">
                <button type="button" class="btn btn-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    New Service Request <i class="bi bi-chevron-down ps-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($jobtype as $type) {
                        echo "<li><a class='dropdown-item' 
                            href='service?s=" . $type["jtcode"] . "'>" . 
                            $type['jtcode'] . " Request</a></li>";
                    }
                    ?>
                    <li><a class='dropdown-item' href="jobType">Job Types (Add/Remove)</a>
                </ul>
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
                        <th scope="col">SrNo.</th>
                        <th scope="col">Date</th>
                        <th scope="col">Client Details</th>
                        <th scope="col">Nom. Details</th>
                        <th scope="col">Total(₹)</th>
                        <th scope="col">Payment</th>
                        <th scope="col" width='50px'></th>
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
                                $jtype = $data['j_type'];
                                ?>
                            </td>
                            <td>
                                <span>
                                    <?= $data['sr_no'] ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?= $data['sr_date'] ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= $data['c_details'] ?>">
                                    <?= truncateText($data['c_details'], 40) ?>
                                </span>
                            </td>
                            <td>
                                <span title="<?= $data['nom_details'] ?>">
                                    <?= truncateText($data['nom_details'], 40) ?>
                                </span>
                            </td>
                            <td>
                                <span>
                                    <?= $data['tot_amount'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($data['deemed_Letter_no'] != NULL) : ?>
                                    <span title="Letter No : <?= $data['deemed_Letter_no'] ?>">
                                <?php else : ?>
                                    <span title="<?= $data['single_window_share'] . "% share of ₹" . $data['single_window_amount'] . " = " . $data['single_window_tot_amount'] ?>">
                                <?php endif; ?>
                                    <?= $data['payment_type'] ?>
                                </span>
                            </td>
                            <td class="actions" style="white-space: nowrap;">
                                <a class="btn btn-outline-secondary btn-sm" href='servicePrint?id=<?= $data['sr_no'] ?>'>
                                    <i class="bi bi-printer-fill"></i>
                                </a>
                                <!-- <button type="button" class="btn btn-warning btn-sm" onclick="makeEditableRow(this)">
                                    <i class="bi bi-pencil-square"></i>
                                </button> -->
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
    let makeEditable = (element) => {
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

        // div.appendChild(button2);
        div.appendChild(input);
        // div.appendChild(button1);
        element.parentNode.replaceChild(div, element);
        input.focus();

        const revertToSpan = () => {
            let span = document.createElement("span");
            span.classList.add("editable");
            span.innerText = input.value;
            span.onclick = () => copytoclip(span.innerHTML.trim());
            span.ondblclick = () => makeEditable(span);

            div.parentNode.replaceChild(span, div);
        };

        document.addEventListener("keydown", (event) => {
            if (event.key === "Escape") {
                revertToSpan();
            }
        });

        input.addEventListener("blur", () => {
            revertToSpan();
        })

        input.addEventListener("keydown", e => {
            if (e.key === "Enter") {
                // Handle the Enter key press here
                e.preventDefault(); // Prevent the default behavior of the Enter key
                if (input.value != element.innerText.trim()) {
                    showMessage("It changed...", "warning");
                    revertToSpan();
                }
                // console.log("Haha");
            }
        });

        button2.addEventListener("click", () => {
            revertToSpan(); // Cancel Button Clicked
        })

        button1.addEventListener("click", () => {
            alert("Not working at this moment...");
        })
    }
</script>




                <button type="button" class="btn btn-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    New Service Request <i class="bi bi-chevron-down ps-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($jobtype as $type) {
                        echo "<li><a class='dropdown-item' 
                            href='service?s=" . $type["jtcode"] . "'>" . 
                            $type['jtcode'] . " Request</a></li>";
                    }
                    ?>
                    <li><a class='dropdown-item' href="jobType">Job Types (Add/Remove)</a>
                </ul>
            </div>
