<?php
include "connect.php";

$json = file_get_contents('php://input');
$data = json_decode($json, true);
$data = sanitizeValues($data);
if (!empty($data)) {
    $sender = $data['send'];
    $receiver = $data['receive'];
    $category = $data['cat'];
    $subject = $data['sub'];
    $date = $data['date'];
    $time = $data['time'];
    $remarks = $data['remarks'];
    $pid = $data['pid'];
    $sql = "INSERT INTO tbl_log_comms (sender, recevier, category, subject, date, time, remarks, pid) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $sender, $receiver, $category, $subject, $date, $time, $remarks, $pid);

    if (mysqli_stmt_execute($stmt)) {
        $response = array(
            "success" => true,
            "message" => "$sender($category)[Sub: $subject] - was added",
            "data" => array(
                "sender" => $sender,
                "recevier" => $receiver,
                "time" => date('h:i A', strtotime($time)),
                "date" => date('jS F Y', strtotime($date)),
                "category" => $category,
                "subject" => $subject,
                "remarks" => $remarks,
                "id" => mysqli_stmt_insert_id($stmt)
            ),
        );
    } else {
        $response = array(
            "success" => false,
            "message" => "Error inserting data: $sender($category)[Sub: $subject] - was not added"
        );
    }
    mysqli_stmt_close($stmt);
} else {
    $response = array(
        "success" => false,
        "message" => "Communication log API was called with No proper values"
    );
}
header('Content-Type: application/json');
echo json_encode($response);
?>