<?php
include "api/connect.php";

// Selecting all the job type
$query = "SELECT * FROM tbl_jtype";
$result = mysqli_query($conn, $query);
$jobtype = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <?php
  if(isset($printpage)){
    echo "<title>" . $cpage . "</title>";
  }else{
    echo "<title>MIS | " . $cpage . "</title>";
  }
  ?>
  <!-- <title>MIS | Dashboard</title> -->
  <!-- <link rel="preconnect" href="https://fonts.googleapis.com" /> -->
  <!-- <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /> -->
  <!-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" /> -->
  <link href="resources/style/font.css" rel="stylesheet" />
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="bootstrap-icons/font/bootstrap-icons.css">
  <script src="bootstrap/js/bootstrap.bundle.min.js" defer></script>
  <script src="bootstrap/js/popper.min.js" defer></script>
  <link rel="icon" href="resources/STQC.PNG" type="image/x-icon">

  <link rel="stylesheet" href="resources/style/dashboard.css" />
  <link rel="stylesheet" href="resources/style/loading.css" />
  <link rel="stylesheet" href="resources/style/print.css" />
  <!-- This Validation might make the site slower -->
  <script src="resources/js/validation.js" defer></script>
  <script>
    const updateDate = (element) => {
      // Generating date and time
      const currentDate = new Date();
      const year = currentDate.getFullYear();
      const month = String(currentDate.getMonth() + 1).padStart(2, '0');
      const day = String(currentDate.getDate()).padStart(2, '0');
      const hours = String(currentDate.getHours()).padStart(2, '0');
      const minutes = String(currentDate.getMinutes()).padStart(2, '0');
      const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;
      $(element).value = formattedDate;
    }
    const $ = (elem) => {
      return document.getElementById(elem);
    }
    const toggleTextbox = (checkbox, textbox) => {
      textbox.style.display = checkbox.checked ? 'block' : 'none';
    };
    const closeMisc = (checkbox, textbox) => {
      checkbox.checked = false;
      textbox.style.display = "none";
      textbox.querySelectorAll("input").forEach(element => {
        element.value = "";
      });
    };


    const checkFileSize = (input, maxSizeInKB = 512) => {
      if (input === null) return;

      const files = input.files;
      if (files.length === 0) {
        showMessage('Please select at least one file.', 'warning');
        return;
      }

      let invalidFiles = [];
      let fileList = '';

      for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const fileSizeInKB = file.size / 1024;

        if (fileSizeInKB > maxSizeInKB) {
          invalidFiles.push({
            name: file.name,
            size: fileSizeInKB
          });
        }
      }

      if (invalidFiles.length > 0) {
        showMessage(`Files exceed the maximum limit of ${maxSizeInKB} KB`, 'danger');
        let erMsg = input.nextSibling;

        if (!erMsg || erMsg.tagName !== 'SMALL') {
          erMsg = document.createElement('small');
          erMsg.classList.add('text-danger');
          erMsg.classList.add('pt-1');
          erMsg.classList.add('text-end');
          input.parentNode.insertBefore(erMsg, input.nextSibling);
        }

        for (let i = 0; i < invalidFiles.length; i++) {
          const {
            name,
            size
          } = invalidFiles[i];
          fileList += `${name} (${size.toFixed(2)} KB > ${maxSizeInKB} KB)<br>`;
        }

        erMsg.innerHTML = fileList;

        input.value = ''; // Clear the input value
      } else {
        let erMsg = input.nextSibling;
        if (erMsg && erMsg.tagName === 'SMALL') {
          erMsg.parentNode.removeChild(erMsg);
        }
      }

    };

    const sendLogs = (pid, message) => {
      navigator.sendBeacon('api/logs_send.php',
        new Blob([JSON.stringify({
          "page": pid,
          "msg": message,
          "user": localStorage.getItem("isLoggedIn")
        })], {
          type: "application/json"
        }));
    }
    const createShortForm = (sentence) => {
      return sentence
        .split(" ")
        .filter(word => word.length > 0) // Remove empty words
        .map(word => word[0].toUpperCase()) // Extract and uppercase first letter
        .join(""); // Combine the letters into a short form
    }
    const getUsername = async () => {
      await fetch("api/getusername.php?id=" + localStorage.getItem("isLoggedIn") + "")
      .then(res => res.json())
      .then(data => {
        if(data.success){
          $('userid').textContent = data.message;
          $('userid').title = data.message;
        }else{
          $('userid').textContent = localStorage.getItem("isLoggedIn");
        }
      }).catch(e => console.error("Error : " + e))
    };
    getUsername();
  </script>
</head>

<body>
  <div class="loading-page">
    <div class="loading-spinner"></div>
  </div>
  <main>
    <div class="container-fluid" id='validation'>
      <div class="row">
        <div class="col-md-2 sidebar">
          <div class="sidebar-brand dlogo">
            <img src="resources/STQC.PNG" alt="STQC Logo" />
            <p class="p-0 m-0 fw-bold">MIS - ETDC Agartala</p>
          </div>
          <ul class="sidebar-nav d-flex flex-column justify-content-between" style="height: calc(100% - 110px)">
            <div>
            <li><a href="dashboard"><i class="bi bi-house-fill pe-1"></i> Home</a></li>
            <li><a href="clients"><i class="bi bi-people-fill pe-1"></i> Clients</a></li>
            <li>
              <a href="#servicesSubMenu" data-bs-toggle="collapse" ondblclick="location.href = 'servicesView'" title="Double click to open the service view list">
                <i class="bi bi-ui-checks-grid pe-1"></i>
                Service Request
                <i class="bi bi-caret-down-fill"></i>
              </a>
              <ul class="collapse submenu" id="servicesSubMenu">
                <?php
                foreach ($jobtype as $type) {
                  echo "<li><a href='service?s=" . $type["jtcode"] . "'>" . $type['jtcode'] . " Request</a></li>
                        ";
                }
                ?>
                <li><a href="servicesView">View all</a></li>
              </ul>
            </li>
            <li>
              <a href="projectProfileList">
                <i class="bi bi-server pe-1"></i>
                Project Profile
              </a>
            </li>
            <!-- <li>
              <a href="#financeSubMenu" data-bs-toggle="collapse">
                <i class="bi bi-cash-stack pe-1"></i>
                Finance
                <i class="bi bi-caret-down-fill"></i>
              </a>
              <ul class="collapse submenu" id="financeSubMenu">
                <li><a href="#">Billing</a></li>
                <li><a href="#">Payment</a></li>
              </ul>
            </li>
            <li>
              <a href="#">
                <i class="bi bi-file-earmark-spreadsheet-fill pe-1"></i>
                MPR
              </a>
            </li>
            <li>
              <a href="#">
                <i class="bi bi-question-square-fill pe-1"></i>
                Queries & Reports
              </a>
            </li> -->
            <li>
              <a href="#gatepassService" data-bs-toggle="collapse">
                <i class="bi bi-receipt pe-1"></i>
                Gatepass
                <i class="bi bi-caret-down-fill"></i>
              </a>
              <ul class="collapse submenu" id="gatepassService">
                <li><a href="gatepass">Register</a></li>
                <li><a href="gatepassList">View all</a></li>
              </ul>
            </li>
            </div>
            <li class="d-flex align-items-center justify-content-between m-0 bg-dark ps-2 rounded">
              <p class="m-0 text-white d-flex align-items-center justify-content-start gap-2 flex-grow-1">
                <i class="bi bi-person-circle fs-5"></i>
                <small class="text-sm truncate-text d-inline-block" style="width: 70%" id="userid">
                  <em>void</em>
                </small> 
              </p>
              <button class="btn btn-danger btn-sm" onclick="logoutuser()" title="Logout user">
                <i class="bi bi-box-arrow-left fs-6 me-1"></i>
              </button>
            </li>
          </ul>
        </div>
