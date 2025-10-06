<?php
include 'customFunctions.php';

// Get the JSON data from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);
// Check if the filename is provided in the JSON data
if (isset($data['filename']) && isset($data['page'])) {
    $pid = $data['page'];

    // Get the filename from the JSON data
    $filename = $data['filename'];

    // Check if the file exists
    if (deleteFilesFromZip($pid, $filename)) {
        // Attempt to delete the file
        $response = array('success' => true, 'message' => $filename . ' is successfully deleted from archive');
    } else {
        $response = array('success' => false, 'message' => 'Filename not provided.');
    }
} else {
    $response = array('success' => false, 'message' => 'Essential data (filename, page) is not provided');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
