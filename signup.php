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
              <h2 class="card-title text-left fw-bold mb-1">
                Create an account
              </h2>
              <div id="message">
                <p class="small text-secondary">
                  Please provide the following information...
                </p>
              </div>
              <form id="signupForm" method="post">
                <div class="form-floating mb-3">
                  <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="unknown009"
                    required
                  />
                  <label for="username" class="form-label text-muted"
                    >Username</label
                  >
                </div>
                <div class="form-floating mb-3">
                  <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="***"
                    pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{4,20}$"
                    aria-labelledby="passwordHelpBlock"
                    required
                  />
                  <label for="password" class="form-label text-muted"
                    >Password</label
                  >

                  <div id="passwordHelpBlock" class="form-text">
                    <ul>
                      <li class="small">
                        Your password must be 4-20 characters long
                      </li>
                      <li class="small">
                        Password must Contain letters and numbers
                      </li>
                      <li class="small">Password must not contain spaces</li>
                    </ul>
                  </div>
                </div>
                <div class="text-center mt-4">
                  <input
                    type="submit"
                    value="Register new account"
                    class="btn btn-primary btn-lg w-100"
                  />
                </div>
                <div class="text-center mt-3">
                  <a href="index">Already have an account? Login user</a>
                </div>
              </form>
              <div id="msgbox"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      document
        .getElementById("signupForm")
        .addEventListener("submit", function (event) {
          event.preventDefault(); // Prevent form submission

          // Get the form data
          var formData = {
            username: document.getElementById("username").value,
            password: document.getElementById("password").value,
          };

          // Send a POST request to the signup API
          fetch("api/signup_api.php", {
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

                // Redirect or perform other actions upon successful authentication
              } else {
                document.getElementById("msgbox").innerHTML = `
                        <div class="alert alert-warning w-auto m-auto mt-3 text-center" role="alert">
                            ${data.message}
                        </div>                    
                        `;
              }
            })
            .catch((error) => {
              console.log(error);
              document.getElementById("msgbox").innerHTML =
                "An error occurred during the signup process.";
            });
        });
    </script>
  </body>
</html>
