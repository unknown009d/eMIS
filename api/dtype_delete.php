<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Process the data and determine the appropriate value
    $result = process_data($data);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Return an error response if the request method is not POST
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method Not Allowed"));
}

// Function to process the data and determine the appropriate value
function process_data($data)
{
    include 'connect.php';
    $sanetval = $data;
    $id = $sanetval['id'];

    try {
        // Query to delete the record from tbl_docment_type
        $stmt = $conn->prepare("DELETE FROM tbl_docment_type WHERE d_code = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();

        $affectedRows = $stmt->affected_rows;

        if ($affectedRows > 0) {
            $response = array(
                "success" => true,
                "message" => "Record deleted successfully"
            );
        } else {
            $response = array(
                "success" => false,
                "message" => "Record not found or already deleted"
            );
        }

        return $response;
    } catch (mysqli_sql_exception $ex) {
        // Handle database-related errors
        return array(
            "success" => false,
            "message" => "Database error occurred"
        );
    }
}
