<?php
include "customFunctions.php";

// Get the JSON data from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if the filename is provided in the JSON data
if (isset($data['page'])) {

    $pid = $data['page'];

    if (isset($data['filename']) && $data['filename'] != null) {
        // Get the filename from the JSON data
        $filename = $data['filename'];
        $response = restoreFileFromArchive($pid, $filename);
    }

    if (isset($data['all']) && $data['all']) {
        $response = extractArchive($pid);
    }
} else {
    $response = array('success' => false, 'message' => 'ProjectID is not given properly');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
