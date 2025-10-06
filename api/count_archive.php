<?php
include 'customFunctions.php';
$json = file_get_contents('php://input');
$data = json_decode($json, true);

header('Content-Type: application/json');
echo json_encode(countFilesInZipArchive($data['page']));
//echo "Number of files in the directory: ",archiveNo($key);
?>