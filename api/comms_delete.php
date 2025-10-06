<?php
include "connect.php";
$json = file_get_contents('php://input');
$data = json_decode($json, true);

$response = array(); // Initialize the $response variable

if (isset($data['id'])) {
    $com_id = $data['id'];

    // Retrieve the sender, category, and date information
    $select_sql = "SELECT sender, category, subject FROM tbl_log_comms WHERE id='$com_id'";
    $result = mysqli_query($conn, $select_sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $sender = $row['sender'];
        $category = $row['category'];
        $subject = $row['subject'];

        // Now you have the sender, category, and date information, proceed with the delete operation
        $delete_sql = "DELETE FROM tbl_log_comms WHERE id='$com_id'";

        if (mysqli_query($conn, $delete_sql)) {
            $affected_rows = mysqli_affected_rows($conn);
            if ($affected_rows > 0) {
                $response_message = "$sender($category)[Sub: $subject] - was removed";
                $response = array(
                    "success" => true,
                    "message" => $response_message,
                );
            } else {
                $response = array(
                    "success" => false,
                    "message" => "$sender($category)[Sub: $subject] - Deletion Failed: No matching record found"
                );
            }
        } else {
            $response = array(
                "success" => false,
                "message" => "Failed to delete the records of the communication log"
            );
        }
    } else {
        $response = array(
            "success" => false,
            "message" => "Failed to fetch information from the database"
        );
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
