<?php
include 'customFunctions.php';

// Get the JSON data from the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if the 'page' parameter is present in the JSON data
if (isset($data['page'])) {
    $key = $data['page'];
    $key = str_replace(array('/', '-'), '', $key);
    $file_name = $key . ".log";
    $location = $root_dir . $key . '/';

    // If the directory doesn't exist, create it
    if (!is_dir($location)) {
        mkdir($location, 0777, true);
    }

    $location = $location . $file_name;

    // Check if the file exists, if not, create it
    if (!file_exists($location)) {
        file_put_contents($location, ''); // Create an empty file
    }

    // Read the text from the file
    $text = file_get_contents($location);

    // Create the response data
    $response = array(
        "success" => true,
        "message" => $text
    );
} else {
    // Missing 'page' parameter in the JSON data
    $response = array(
        "success" => false,
        "message" => "Page parameter is missing"
    );
}

// Set the response content type and echo the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
