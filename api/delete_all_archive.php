<?php
include 'customFunctions.php';

// Get the JSON data from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);
// Check if the filename is provided in the JSON data
if (isset($data['page'])) {
    $pid = $data['page'];

    // Check if the file exists
    if (deleteFilesFromZip($pid)) {
        // Attempt to delete the file
        $response = array('success' => true, 'message' => 'Files inside the archive are permanently removed');
    } else {
        $response = array('success' => false, 'message' => 'There was a problem inside the archive and are not removed');
    }
} else {
    $response = array('success' => false, 'message' => 'Error : Page name not provided');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
