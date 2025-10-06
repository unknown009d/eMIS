<?php
include 'connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'];

// Prepare and execute a database query
$stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_employee WHERE uid = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

// Check if the user exists
if ($count > 0) {
    $response = array(
        'success' => true,
        'message' => 'User exists'
    );
} else {
    $response = array(
        'success' => false,
        'message' => 'User does not exist'
    );
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
