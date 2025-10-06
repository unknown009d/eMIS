<?php
include "connect.php";


$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (isset($data['id'])) {
    $id_no = $data['id'];
    $sql = "SELECT documents FROM tbl_test_report WHERE id = '$id_no'";
    $doc = "";
    // Execute the query
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        // Fetch the documents field value from the result
        $row = mysqli_fetch_assoc($result);
        $doc = $row['documents'];
    }
    if ($doc != "") {
        $doc_path = '../' . $doc;

        // Delete the record from the database using the id_no
        $delete_sql = "DELETE FROM tbl_test_report WHERE id = '$id_no'";
        $delete_result = mysqli_query($conn, $delete_sql);

        if ($delete_result) {
            $response['success'] = true;
            //echo " Record with id $id_no has been deleted from the database.";
            if (file_exists($doc_path)) {
                if (unlink($doc_path)) {
                    $response['success'] = true;
                    // File deleted successfully.
                } else {
                    $response['success'] = false;
                    // Failed to delete the file.
                }
            } else {
                $response['success'] = true;
                // File does not exist.
            }
        } else {
            $response['success'] = false;
            //echo "Failed to delete the record from the database.";
        }
    } else {
        $response['success'] = false;
        //echo "The doc variable is empty";
    }
}

header('Content-Type: application/json');
echo json_encode($response);
