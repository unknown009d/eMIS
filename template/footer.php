<!-- Bootstrap Modal -->
<div class="modal fade" id="createContactModal">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <form class="modal-content" id="clientFormModal">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="bi bi-person-plus-fill pe-1"></i>
          Create new client
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body px-5">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="oop_clientname" class="form-label">Client Name</label>
            <input type="text" class="form-control" id="oop_clientname" placeholder="eg. John Doe" required>
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientemail" class="form-label">Email</label>
            <input type="text" class="form-control" id="oop_clientemail" placeholder="eg. jhondoe@example.in">
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientphone" class="form-label">Phone</label>
            <input type="tel" class="form-control" id="oop_clientphone" maxlength="15" placeholder="eg. 8493583912" required>
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientaddress" class="form-label">Address</label>
            <input class="form-control" id="oop_clientaddress" placeholder="eg. Indranagar">
          </div>
          <div class="col-md-4 mb-3">
            <label for="clientpan" class="form-label">PAN</label>
            <input type="text" class="form-control" id="oop_clientpan" placeholder="eg. ABCTY1234D">
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientgst" class="form-label">GST</label>
            <input type="text" class="form-control" id="oop_clientgst" placeholder="eg. 29GGGGG1314R9Z6">
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientcat" class="form-label">Category</label>
            <select type="text" class="form-select" id="oop_clientcat" required>
              <?php
              $categoryShow = selectQ($conn, "SELECT * FROM tbl_ctype");
              foreach ($categoryShow as $key => $data) {
                echo "<option value='" . $data['ct_code'] . "'>" . $data['ct_desc'] . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="oop_clientremark" class="form-label">Remarks</label>
            <input type="text" class="form-control" id="oop_clientremark" placeholder="You can type anything here...">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success" id="submitBtn">Save Contact <i class='bi bi-save'></i> </button>
      </div>
    </form>
  </div>
</div>


</div>
<div id="messageBox" class="position-fixed bottom-0 end-0 p-3"></div>
</div>
</main>


<script>
  // Attach a function to execute when the modal is closed
  $('createContactModal').addEventListener("hidden.bs.modal", (e) => {
    $('clientFormModal').reset();
    if ($('clientFormModal').getAttribute("data-update")) {
      $('clientFormModal').removeAttribute("data-update")
      // location.href = location.href;
      location.reload();
    }
  });


  if($('clientFormModal')){
    // Submit button click event handler
    $('clientFormModal').addEventListener('submit', async e => {
      e.preventDefault();
      if (e.target.getAttribute("data-update")) {
        await fetch('api/client_update.php', {
            method: "POST",
            body: JSON.stringify({
              "c_code": $('clientFormModal').getAttribute("data-id"),
              "client_name": $('oop_clientname').value,
              "email": $('oop_clientemail').value,
              "phone": $('oop_clientphone').value,
              "address": $('oop_clientaddress').value,
              "pan": $('oop_clientpan').value,
              "gst": $('oop_clientgst').value,
              "cat": $('oop_clientcat').value,
              "remark": $('oop_clientremark').value
            })
          }).then(response => response.json())
          .then(data => {
            if (data.success) {
              e.target.reset();
              alert("Client ID : " + data.data.c_code + " has been updated successfully...");
              copytoclip(data.data.c_code);
              location.reload();
            } else {
              showMessage("There was a problem in updating client information...");
              console.error(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        await fetch('api/client_insert.php', {
            method: "POST",
            body: JSON.stringify({
              "client_name": $('oop_clientname').value,
              "email": $('oop_clientemail').value,
              "phone": $('oop_clientphone').value,
              "address": $('oop_clientaddress').value,
              "pan": $('oop_clientpan').value,
              "gst": $('oop_clientgst').value,
              "cat": $('oop_clientcat').value,
              "remark": $('oop_clientremark').value
            })
          }).then(response => response.json())
          .then(data => {
            if (data.success) {
              alert("Client ID : " + data.data.c_code + " has been saved successfully...");
              copytoclip(data.data.c_code);
              location.reload();
            } else {
              showMessage("There was a problem in saving the new client...");
              console.error(data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      }
    });
  }

  // Checking if user is logged in...
  const user = localStorage.getItem("isLoggedIn");
  if (user !== null) {
    fetch("api/check_user.php", {
        method: "POST",
        body: JSON.stringify({
          username: user
        }),
        headers: {
          "Content-Type": "application/json",
        },
      })
      .then((req) => {
        return req.json();
      })
      .then((data) => {
        if (data.success) {
          document.querySelector(".loading-page").style.display = 'none';
        } else {
          localStorage.removeItem("isLoggedIn");
          alert("User doesn't exist... Please login properly");
          location.href = "index";
        }
      })
      .catch((error) => {
        localStorage.removeItem("isLoggedIn");
        console.error(error);
      });
  } else {
    localStorage.removeItem("isLoggedIn");
    alert("User doesn't exist... Please login properly");
    location.href = "index";
  }

  const logoutuser = () => {
    localStorage.removeItem("isLoggedIn");
    location.href = 'index'
  };

  // Alert Messages
  const showMessage = (message, type, duration = 3000) => {
    const messageBox = document.getElementById('messageBox');
    // const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const messageHTML = `<div class="alert alert-${type}">${message}</div>`;

    messageBox.innerHTML = messageHTML;

    setTimeout(() => {
      messageBox.innerHTML = '';
    }, duration);

  };

  const showError = () => {
    showMessage('An error has occurred!', 'danger');
  };

  const showSuccess = () => {
    showMessage('Success!', 'success');
  };

  const copytoclip = (text) => {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    showMessage("<i class='bi bi-clipboard-check pe-1'></i> " + text + " is copied to clipboard successfully.", "success", 1000);
  };


  const validateSearch = (num_of_letters) => {
    if (document.getElementById('searchTxt').value.length < num_of_letters) {
      showMessage("<i class='bi bi-exclamation-triangle pe-1'></i> Please enter at least " + parseInt(num_of_letters) + " characters to perform the search.", "warning");
      return false; // Prevent form searching
    }
    return true; // Allow form searching
  }

  function fdateeasy(inputDate) {
    const dateObj = new Date(inputDate);

    // Get day, month, and year
    const day = dateObj.getDate();
    const month = dateObj.toLocaleString('default', {
      month: 'long'
    });
    const year = dateObj.getFullYear();

    // Format day with suffix
    let dayWithSuffix = day + 'th';
    if (day === 1 || day === 21 || day === 31) {
      dayWithSuffix = day + 'st';
    } else if (day === 2 || day === 22) {
      dayWithSuffix = day + 'nd';
    } else if (day === 3 || day === 23) {
      dayWithSuffix = day + 'rd';
    }

    // Get hours and minutes
    const hours = dateObj.getHours();
    const minutes = dateObj.getMinutes();

    // Convert hours to 12-hour format and determine AM/PM
    const ampm = hours >= 12 ? 'pm' : 'am';
    const formattedHours = hours % 12 || 12;

    // Combine formatted parts into the desired output string
    const formattedDate = `${dayWithSuffix} ${month} ${year} @ ${formattedHours}:${minutes.toString().padStart(2, '0')}${ampm}`;

    return formattedDate;
  }

  const noContent = (colSpan = 7, message = "No entries...") => {
    const row = document.createElement('tr');
    const noContent = document.createElement('td');
    noContent.classList.add("p-3");
    noContent.classList.add("text-center");
    noContent.classList.add("noContent")
    noContent.innerHTML = "<i class='bi bi-exclamation-triangle pe-1'></i> " + message;
    noContent.colSpan = colSpan;
    row.appendChild(noContent);
    return row;
  }

  function truncateText(text, limit) {
    if (text == null) return;
    if (text.length > limit) {
      text = text.substring(0, limit) + '...';
    }
    return text;
  }

  // Input validation
  const isValidNumber = (value) => !isNaN(value) && isFinite(value);

</script>
</body>

</html>
<?php mysqli_close($conn); ?>