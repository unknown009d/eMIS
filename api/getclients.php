<?php
include 'connect.php';

// Retrieve the request payload
$data = json_decode(file_get_contents("php://input"), true);

// Check if the 'username' key exists in the request payload
if (isset($data['username'])) {
    $username = $data['username'];

    // Prepare and execute a database query with proper sanitization
    $query = "SELECT * FROM tbl_client WHERE c_name LIKE ?";
    $stmt = $conn->prepare($query);
    $username = "%" . $username . "%";
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $clients = $result->fetch_all(MYSQLI_ASSOC);
    

    // Check if the user exists
    if (count($clients) > 0) {
        $response = array(
            'success' => true,
            'message' => 'User exists',
            'clients' => $clients
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'User does not exist',
            'clients' => null
        );
    }
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request',
        'clients' => null
    );
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>