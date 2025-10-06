<?php

include 'connect.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the request
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];

    // Prepare and execute the SQL statement
    $stmt = mysqli_prepare($conn, "SELECT * FROM tbl_employee WHERE uid = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    // Check if the user exists and the password is correct
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        // if (password_verify($password, $row["password"])) {
        //     // Authentication successful
        //     $response = array("success" => true, "message" => "Authentication successful");
        // } else {
        //     $response = array("success" => false, "message" => "Invalid password");
        // }
        if ($password == $row["upwd"]) {
            // Authentication successful
            $response = array("success" => true, "message" => "Authentication was successful");
        } else {
            $response = array("success" => false, "message" => "Invalid password");
        }
    } else {
        $response = array("success" => false, "message" => "Invalid username");
    }

    // Send the JSON response
    header("Content-Type: application/json");
    echo json_encode($response);
}

// Close the connection
mysqli_close($conn);
?>