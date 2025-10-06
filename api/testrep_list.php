<?php

include "connect.php";

if (isset($_GET['pid'])) {
    $pid = $_GET['pid'];
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    $response['success'] = true;
    $response['data'] = getTestReportByPid($conn, $pid, $search);
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = "Invalid or missing 'pid' parameter in the request.";
    echo json_encode($response);
}

?>