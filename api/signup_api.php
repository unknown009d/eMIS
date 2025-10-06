<?php

include 'connect.php';

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from the request
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];

    // Check if the username is already taken
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $response = array("success" => false, "message" => "Username already taken");
    } else {
        // Hash the password
        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);

        $response = array("success" => true, "message" => "Account Created Successfully");
    }

    // Send the JSON response
    header("Content-Type: application/json");
    echo json_encode($response);
}

// Close the connection
mysqli_close($conn);

?>