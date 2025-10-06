<?php
include 'customFunctions.php';

// $data = json_decode(file_get_contents("php://input"), true);
$key = $_POST['page'];
$type = $_POST['type'];
//ast stands for assets types
// $ast = '/documents';

$key = directory($key);

if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {

    // Define the allowed file extensions
    $allowedExtensions = ['xls', 'xlsx', 'doc', 'docx', 'pdf', 'txt'];

    // Get the uploaded file's extension
    $fileExtension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

    // Check if the file extension is in the allowed list
    if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
        $response = [
            'success' => false,
            'message' => 'File type not allowed. Only .xls, .xlsx, .doc, .docx, and .pdf files are allowed.'
        ];
    } else {

        // Retrieve the uploaded file information
        $fullPath = $root_dir . $key;

        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                $response = [
                    'success' => false,
                    'message' => 'Error occurred while creating the directory.'
                ];
                goto t1;
            }
        }
        $fullPath .= $ast;
        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                $response = [
                    'success' => false,
                    'message' => 'Error occurred while creating the directory.'
                ];
                goto t1;
            }
        }

        $newfilename = $_POST['newFileName'];
        // Specify the directory where the uploaded file will be stored
        $uploadDirectory = $root_dir . $key . $ast . "/";

        if (upld_docs($_FILES['file'], $uploadDirectory, $newfilename, $type)) {
            // File upload successful
            $response = [
                'success' => true,
                'message' => 'File uploaded successfully.',
                'filename' => $type . '_' . $newfilename
            ];
        } else {
            // Error while moving the uploaded file
            $response = [
                'success' => false,
                'message' => 'Error occurred while uploading the file.'
            ];
        }
    }
} else {
    // File upload error
    $response = [
        'success' => false,
        'message' => 'Error occurred while uploading the file. Error code: ' . $_FILES['file']['error']
    ];
}
t1:
// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);
