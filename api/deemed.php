<?php
include 'connect.php'; // Include the file with the database connection

$data = json_decode(file_get_contents("php://input"), true);
$srno = $data['sr_no'];
//assigning the the letter no
$letter = $data['letter'];
$stmt = $conn->prepare("INSERT INTO tbl_deemed (sr_no, Letter_no) VALUES (?, ?)");
$stmt->bind_param("ss", $srno, $letter);
$stmt->execute();
$response;
$check = 0;
if ($stmt->affected_rows > 0) {
    $checkval = 1;
} else {
    $response = array(
        "success" => false,
        "message" => mysqli_stmt_error($stmt)
    );
}
if ($checkval == 1) {
    $resp = "SELECT * FROM tbl_deemed ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $resp);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $response = array(
            "success" => true,
            "data" => array(
                "id" => $row['id'],
                "srno" => $row['sr_no'],
                "letterno" => $row['Letter_no']
            )
        );
    }
} else {
    $response = array(
        "success" => false,
        "message" => "Failed to retrieve the deemed values"
    );
}

echo json_encode($response);
?>