<?php
/**
 * File: find_docs.php
 * Description: Retrieves and organizes files in a directory based on certain criteria.
 * Dependencies: connect.php
 *
 * @param $_GET['pid'] (string) - The project ID for which files need to be retrieved.
 * @param $_GET['sort'] (int) - Optional. Sorting order for the files. Default is 1 (newest first).
 * @param $_GET['type'] (int) - Optional. Sorting type for the files. Default is 0 (modified date).
 *
 * @return JSON-encoded response containing file details and status.
 */

// Include database connection and utility functions
include 'connect.php';

// Function to check if a string starts with a specific prefix
function startsWith($string, $prefix)
{
    return strncmp($string, $prefix, strlen($prefix)) === 0;
}

// Function to check if a string ends with a specific suffix
function endsWith($string, $suffix)
{
    return substr_compare($string, $suffix, -strlen($suffix)) === 0;
}

// Get the project ID from the request parameters
$pid = directory($_GET['pid']) . '/documents';

// Construct the directory path based on the project ID
$directory = 'assets/' . $pid;
$sortingOrder = $_GET['sort'] ?? 1; // Default to sorting by newest
$sortType = $_GET['type'] ?? 0; // Default to sorting by modified date
$fulldirectory = $root_dir . $pid;

// Initialize the response array
$response = [];

// Check if the directory exists
if (is_dir($fulldirectory)) {
    $response['success'] = true;
    $response['files'] = [];

    // Get the list of files in the directory
    $files = scandir($fulldirectory);

    // Loop through each file to gather relevant information
    foreach ($files as $file) {
        if (
            is_file($fulldirectory . '/' . $file) &&
            !endsWith($file, '.log') &&
            !endsWith($file, '.zip') &&
            !startsWith($file, '.')
        ) {
            $filePath = $fulldirectory . '/' . $file;
            $outputpath = $directory . '/' . $file;

            // Fetch the document type from the database based on the filename
            $dtype = selectQ($conn, "SELECT d_name FROM tbl_docment_type WHERE d_code='" . explode('_', $file)[0] . "'");


            // Get the file's modified time and convert it to a human-readable date format
            $fileModifiedTime = filemtime($filePath);
            $fileModifiedDate = date('Y-m-d H:i:s', $fileModifiedTime);

            // Add file details to the response array
            $response['files'][] = [
                'name' => $file,
                'path' => $outputpath,
                'type' => $dtype[0]['d_name'],
                'modifiedDate' => $fileModifiedDate,
            ];
        }
    }

    // Sort the files based on the chosen sorting type
    if ($sortType) {
        // Function to sort files based on the first part of the filename (before the underscore '_')
        function sortByFirstPart($a, $b)
        {
            $aFirstPart = explode('_', $a['name'])[0];
            $bFirstPart = explode('_', $b['name'])[0];

            // Compare the first parts of the filenames
            return strcmp($aFirstPart, $bFirstPart);
        }

        // Sort the files based on the first part of the filename
        usort($response['files'], 'sortByFirstPart');
    } else {
        // Invalid sort type provided, sort by modified date as default
        usort($response['files'], function ($a, $b) use ($sortingOrder) {
            $comparison = strtotime($a['modifiedDate']) - strtotime($b['modifiedDate']);
            return ($sortingOrder == 1) ? -$comparison : $comparison;
        });
    }

} else {
    // If the directory does not exist, attempt to create it
    if (mkdir($fulldirectory, 0755, true)) {
        $response['success'] = true;
        $response['files'] = [];
    } else {
        // If creating the directory fails, set the response status to indicate failure
        $lastError = error_get_last();

        $response['success'] = false;
        // $response['error'] = 'Failed to create directory.';
        $response['error'] = 'Failed to create directory. Error: ' . $lastError['message'];

    }
}

// Add sorting information to the response
$response['sort'] = $sortingOrder;
$response['sorttype'] = $sortType;

// Set the response content type to JSON and encode the response array
header('Content-Type: application/json');
echo json_encode($response);
?>