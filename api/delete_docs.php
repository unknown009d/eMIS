<?php
include 'customFunctions.php';

// Get the JSON data from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);
$key = sanitizeValues($data['page']);
$key = directory($key);
// $ast = "documents/";
// Check if the filename is provided in the JSON data
if (isset($data['filename'])) {
    // Get the filename from the JSON data
    $filename = $data['filename'];

    // Set the source and destination paths
    $sourceFolder = $root_dir . $key . '/' . $ast;
    $sourceFile = $sourceFolder . $filename;
    $zipname = $sourceFolder;
    // Move the file
    if (file_exists($sourceFile)) {
        if (del_docs($zipname, $sourceFolder, $filename)) {
            $response = array('success' => true, 'message' => 'file deleted succesfully.');
        }
    } else {
        $response = array('success' => false, 'message' => 'File does not exist.');
    }
} else {
    $response = array('success' => false, 'message' => 'Filename not provided.');
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>