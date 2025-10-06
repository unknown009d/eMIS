<?php
include "connect.php";
$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$sql = "DELETE FROM tbl_testrate WHERE id = $id";

$msg = array();
if ($res = mysqli_query($conn, $sql)) {
    $msg = array("success" => true);
} else {
    $msg = array(
        "success" => false,
        "message" => mysqli_error($conn)
    );
}

header('Content-Type: application/json');
echo json_encode($msg);
?>