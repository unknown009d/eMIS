<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the necessary files

    // Get the data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Process the data and determine the appropriate value
    $result = process_data($data);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Return an error response if the request method is not POST
    header("HTTP/1.1 405 Method Not Allowed");
    echo "<h2>Error 405: Method Not Allowed</h2>";
}

function cactn($value)
{
    if ($value === null || $value === '' || strlen($value) < 1) {
        return null;
    }
    return sanitizeValues($value);
}

function process_data($data)
{
    include 'connect.php';

    // Extract the necessary values from the $data array
    $type = $data['type'];
    $srqno = $data['srqno'];

    // Deleting all nomenclature
    $stmt = $conn->prepare("DELETE FROM tbl_deemed WHERE sr_no=?");
    $stmt->bind_param("s", $srqno);
    if(!$stmt->execute()){
        return [
            'success' => false,
            'message' => "Deleting tbl_deemed had an issue.",
            'error' => 500 
        ];
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM tbl_single_window WHERE sr_no=?");
    $stmt->bind_param("s", $srqno);
    if(!$stmt->execute()){
        return [
            'success' => false,
            'message' => "Deleting tbl_single_window had an issue.",
            'error' => 500 
        ];
    }
    $stmt->close();

    $response['type'][] = $type;

    if($type == 'n'){
        return [
            "success" => true,
            "message" => "Data was removed"
        ];
    }

    if ($type == "d") {
        $letterno = cactn($data['d_letterno']);
        $stmt = $conn->prepare("INSERT INTO tbl_deemed (sr_no, Letter_no) VALUES (?, ?)");
        $stmt->bind_param("ss", $srqno, $letterno);
    } elseif ($type == "sw") {
        $sw1 = cactn($data['sw_sharedText']);
        $sw2 = cactn($data['sw_sharedAmount']);
        $sw3 = cactn($data['sw_tot_amount']);
        $sw4 = cactn($data['sw_Remarks']);
        $stmt = $conn->prepare("INSERT INTO tbl_single_window (sr_no, share, amount, tot_amount, remarks) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $srqno, $sw1, $sw2, $sw3, $sw4);
    }

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response = array(
            "success" => true,
            "message" => $type . " was added"
        );
    } else {
        $response = array(
            "success" => false,
            "message" => mysqli_stmt_error($stmt)
        );
    }

    // Return the processed data
    return $response;
}
