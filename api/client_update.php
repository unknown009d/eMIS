<?php
// Assuming you have established a database connection
include "connect.php";
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Helper function to handle empty strings and convert them to NULL
function handleEmptyString($value) {
    return ($value !== '' && strlen(trim($value)) > 0) ? $value : null;
}

// Check if the required fields are present in the JSON data
if (!empty($data)) {
    $cCode = $data['c_code'];

    // Prepare the values for update, handling empty strings
    $clientName = handleEmptyString($data['client_name']);
    $email = handleEmptyString($data['email']);
    $phone = handleEmptyString($data['phone']);
    $address = handleEmptyString($data['address']);
    $pan = handleEmptyString($data['pan']);
    $gst = handleEmptyString($data['gst']);
    $categ = handleEmptyString($data['cat']);
    $remark = handleEmptyString($data['remark']);

    // Construct the update query with NULLIF function to handle empty strings
    $updateQuery = "UPDATE tbl_client 
                   SET c_name = NULLIF('$clientName', ''),
                       c_email = NULLIF('$email', ''),
                       c_phn = NULLIF('$phone', ''),
                       c_addr = NULLIF('$address', ''),
                       c_pan = NULLIF('$pan', ''),
                       c_gst = NULLIF('$gst', ''),
                       c_cat = NULLIF('$categ', ''),
                       c_rmk = NULLIF('$remark', '')
                   WHERE c_code = '$cCode'";

    // Execute the update query
    if (mysqli_query($conn, $updateQuery)) {
        // Update successful

        // Fetch the updated data from the database
        $selectQuery = "SELECT * FROM tbl_client WHERE c_code = '$cCode'";
        $result = mysqli_query($conn, $selectQuery);
        $updatedData = mysqli_fetch_assoc($result);

        // Include the updated data in the response
        $response = array(
            "success" => true,
            "message" => "Data updated successfully",
            "data" => $updatedData
        );
    } else {
        // Update failed
        $response = array("success" => false, "message" => "Failed to update data");
    }
} else {
    // Missing JSON data
    $response = array("success" => false, "message" => "No data received");
}

// Set the response content type and echo the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
