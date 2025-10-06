<?php
include "connect.php";
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (isset($data['c_code'])) {
    $client_code = $data['c_code'];
    $sql = "DELETE FROM tbl_client WHERE c_code='$client_code'";

    if (mysqli_query($conn, $sql)) {
        $affected_rows = mysqli_affected_rows($conn);
        if ($affected_rows > 0) {
            $response = array(
                "success" => true,
                "message" => $data['c_code'] . " client details have been permanently removed from our records"
            );
        } else {
            $response = array(
                "success" => false,
                "message" => "No matching client record found for c_code '$client_code'. The specified client ID does not exist"
            );
        }
    } else {
        $response = array(
            "success" => false,
            "message" => "Failed to delete the records of the client"
        );
    }
}else{
    $response = array(
        "success" => false,
        "message" => "Please provide the clientID"
    );
}
echo json_encode($response);
?>
