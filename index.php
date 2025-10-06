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
    <link rel="stylesheet" href="resources/style/loading.css" />
  </head>
  <body>
    <div class="loading-page">
      <img src="resources/STQC.PNG" alt="SQTC Logo" />
      <div class="loading-spinner"></div>
    </div>

    <script>
      const user = localStorage.getItem("isLoggedIn");
	  let counter = 0;
      setInterval(() => {
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
              // User Exist in the database
              let user = localStorage.getItem("isLoggedIn");
              if (user !== null) {
                // User Exist in the Database and is logged in
                location.href = "dashboard";
              } else {
                // User exist in database but not logged in
                localStorage.removeItem("isLoggedIn");
                location.href = "login";
              }
            } else {
              // User doesn't exist in database but not logged in
              localStorage.removeItem("isLoggedIn");
              location.href = "login";
            }
          })
          .catch((error) => {
            localStorage.removeItem("isLoggedIn");
            console.error(error);
          });
	    counter++;
		if(counter > 10) {
			localStorage.removeItem("isLoggedIn");
			location.href = "login?db=1";
		}
      }, 1000);
    </script>
  </body>
</html>
