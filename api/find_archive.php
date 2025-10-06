<?php
include 'connect.php';
$json = file_get_contents('php://input');

if (isset($_GET['pid']) || $_GET['pid'] == null) {

    $list = listFilesInZipArchive($_GET['pid']);

    $files = [];

    // var_dump($list["message"]);
    foreach ($list['message'] as $key => $data) {
        try {
            $dtype = selectQ($conn, "SELECT d_name FROM tbl_docment_type WHERE d_code='" . explode('_', $data['name'])[0] . "'");
            $files[$key]["type"] = $dtype[0]['d_name'];
            $files[$key]["name"] = $data['name'];
            $files[$key]["size"] = $data['size'];
        } catch (Exception $e) {
            $response = [
                "success" => false,
                "message" => "There was a problem listing the archive files..."
            ];
            goto end;
        }
    }

    $response = [
        "success" => true,
        "message" => $files
    ];
} else {
    $response = [
        "success" => false,
        "message" => "There was a problem listing the archive files..."
    ];
}

end:
header('Content-Type: application/json');
echo json_encode($response);
//echo "Number of files in the directory: ",archiveNo($key);
?>