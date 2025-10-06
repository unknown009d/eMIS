<?php

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Process the data and determine the appropriate value
    $result = process_data($_GET);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Return an error response if the request method is not GET
    header("HTTP/1.1 405 Method Not Allowed");
    echo "<h2>Error 405: Method Not Allowed</h2>";
}

function process_data($data)
{
    // Process the data here as needed
    // For example, you can access query parameters using $data['param_name']

    // Include the necessary files (Moved from outside the function)
    include "connect.php";

    // Retrieve 'pid' parameter or set default message
    $pid = isset($_GET['pid']) ? $_GET['pid'] : null;

    // Initialize the response array
    $response = array();

    // Check if pid is provided
    if ($pid !== null) {
        // Query the database to get the latest test report
        $latest = selectQ($conn, "SELECT * FROM `tbl_test_report` WHERE p_id=? ORDER BY id DESC LIMIT 1", [$pid]);
        $certificate = selectQ($conn, "SELECT * FROM `tbl_project_profile` WHERE p_id=? LIMIT 1", [$pid])[0]['certificate'];
        // Check if there are records in the result
        if (count($latest) > 0) {
            if ($certificate == NULL) {
                $response['success'] = true;
                $response['color'] = '#dedbff';
                $response['message'] = $latest[0]['cycle'] . " cycle @ " . $latest[0]['status'];
            } else {
                $response['success'] = true;
                $response['color'] = '#c8ffb3';
                $response['message'] = "Project Closed  @ " . $latest[0]['cycle'] . " cycle";
            }
        } elseif ($certificate != NULL) {
            $response['success'] = true;
            $response['color'] = '#c8ffb3';
            $response['message'] = "Project Closed";
        } else {
            $response['success'] = true;
            $response['color'] = 'lightblue';
            $response['message'] = "Project Initiation";
        }
    } else {
        $response['success'] = false;
        $response['color'] = '#ffb4ad';
        $response['message'] = "Project ID is not provided";
    }

    // Close the database connection
    $conn->close();

    // Return the processed data
    return $response;
}
