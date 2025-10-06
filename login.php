<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MIS | Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
      crossorigin="anonymous"
    />

    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
      crossorigin="anonymous"
      defer
    ></script>
    <script src="resources/js/validation.js" defer></script>
  </head>
  <body>
    <div class="container" id="validation">
      <div class="row justify-content-center">
        <div class="col-lg-4">
          <div class="card mt-5">
            <div class="card-header d-flex">
              <img src="resources/STQC.PNG" alt="SQTC Logo" />
              <p class="small m-0 p-0 ms-2 my-auto">
                Management Information System, ETDC Indranagar, Kunjaban,
                Agartala
              </p>
            </div>
            <div class="card-body">
              <h2 class="card-title text-left fw-bold mb-1">Login</h2>
              <div id="message">
                <p class="small text-secondary">
                  Enter credentials to login...
                </p>
              </div>
              <form id="loginForm" method="post">
                <div class="form-floating mb-3">
                  <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="unknown009"
                    required
                  />
                  <label for="username" class="form-label text-muted">
                    Username
                  </label>
                </div>
                <div class="form-floating mb-3">
                  <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="***"
                    required
                  />
                  <label for="password" class="form-label text-muted">
                    Password
                  </label>
                </div>
                <div class="text-center mt-4">
                  <button
                    type="submit"
                    class="btn btn-primary btn-lg w-100"
                    id="loginBtn"
                  >
                    <span
                      class="spinner-border spinner-border-sm me-2 d-none"
                      role="status"
                      aria-hidden="true"
                    >
                    </span>
                    Authenticate
                  </button>
                </div>
                <div class="text-center mt-3">
                  <a href="signup">No Account ? Signup for new user</a>
                </div>
              </form>
              <div id="msgbox"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
      const user = localStorage.getItem("isLoggedIn");
      setTimeout(() => {
        if (user !== null) {
          fetch("api/check_user.php", {
            method: "POST",
            body: JSON.stringify({ username: user }),
            headers: {
              "Content-Type": "application/json",
            },
          })
            .then((req) => {
              return req.json();
            })
            .then((data) => {
              if (data.success) {
                location.href = "index";
              }
            })
            .catch((error) => {
              localStorage.removeItem("isLoggedIn");
              console.error(error);
            });
        }
      }, 500);
    </script>
    <script>
	<?php if(isset($_GET['db'])): ?>
		document.getElementById("msgbox").innerHTML = `
		<div class="alert alert-danger w-auto m-auto mt-3 text-center" role="alert">
			Server isn't working right now.
		</div>                    
		`;
	<?php endif; ?>
      document
        .getElementById("loginForm")
        .addEventListener("submit", function (event) {
          event.preventDefault(); // Prevent form submission

          // Disable the login button
          document.getElementById("loginBtn").disabled = true;

          // Show the loading spinner
          document.querySelector(".spinner-border").classList.remove("d-none");

          // Get the form data
          var formData = {
            username: document.getElementById("username").value,
            password: document.getElementById("password").value,
          };

          // Send a POST request to the API for validation
          setTimeout(() => {
            fetch("api/login_api.php", {
              method: "POST",
              body: JSON.stringify(formData),
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  document.getElementById("msgbox").innerHTML = `
                        <div class="alert alert-success w-auto m-auto mt-3 text-center" role="alert">
                            ${data.message}
                        </div>                    
                        `;

                  // Add the user to the localStorage for loggedin State management
                  localStorage.setItem(
                    "isLoggedIn",
                    document.getElementById("username").value
                  );

                  // Redirect or perform other actions upon successful authentication
                  location.href = "dashboard";
                } else {
                  document.getElementById("msgbox").innerHTML = `
                        <div class="alert alert-danger w-auto m-auto mt-3 text-center" role="alert">
                            ${data.message}
                        </div>                    
                        `;
                }
              })
              .catch((error) => {
                console.log(error);
                document.getElementById("msgbox").innerHTML = `
                  <div class="alert alert-danger w-auto m-auto mt-3 text-center" role="alert">
					There might be a problem in the server
                  </div>`;
              })
              .finally(() => {
                // Re-enable the login button
                document.getElementById("loginBtn").disabled = false;

                // Hide the loading spinner
                document
                  .querySelector(".spinner-border")
                  .classList.add("d-none");
              });
          }, 500);
        });
    </script>
  </body>
</html>
