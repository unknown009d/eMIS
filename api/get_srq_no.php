<?php
// include 'connect.php'; // Include the file with the database connection

// $data = json_decode(file_get_contents("php://input"), true);
// $selectedVal = $data['q'];
// $date = $data['date'];
// $timestamp = strtotime($date);
// $currentmonth = date("m", $timestamp);
// $currentyear = date("Y", $timestamp);

// // $currentmonth = date('m');
// // $currentyear = date('Y');
// $srno = "";
// //echo $currentyear . "-" . $currentmonth;

// $response = array();

// if ($currentmonth < 4) { // if the current month is January, February & March
//     $sql = "SELECT LPAD(COUNT(*)-1, 3, 0) AS srno FROM tbl_register WHERE (YEAR(CURDATE())-1)=YEAR(sr_date) OR YEAR(CURDATE())=YEAR(sr_date)";
// } else { // if the current month is April to December
//     $sql = "SELECT LPAD(COUNT(*)+1, 3, 0) AS srno FROM tbl_register WHERE MONTH(sr_date)>=4 AND YEAR(sr_date)=YEAR(CURDATE())";
// }

// $result = $conn->query($sql);

// if ($result) {
//     while ($row = $result->fetch_assoc()) {
//         $srno = $row["srno"];
//     }
//     $result->free();
//     $response['success'] = true;
//     $response['message'] = $selectedVal . "/" . $currentmonth . "-" .substr($currentyear, 2) . "/" . $srno;
// } else {
//     $response['success'] = false;
//     $response['message'] = "Error executing query: " . $conn->error();
// }

// $conn->close();

// header('Content-Type: application/json');
// echo json_encode($response);

include 'connect.php'; // Include the file with the database connection

$data = json_decode(file_get_contents("php://input"), true);
$selectedVal = $data['q'];
$date = $data['date'];
$timestamp = strtotime($date);
$currentmonth = date("m", $timestamp);
$currentyear = date("Y", $timestamp);

$srno = "";
$response = array();

if ($currentmonth < 4) { // if the current month is January, February & March
    $sqlLast = "SELECT MAX(SUBSTRING_INDEX(sr_no, '/', -1)) AS last_srno FROM tbl_register WHERE (YEAR(CURDATE())-1)=YEAR(sr_date) OR YEAR(CURDATE())=YEAR(sr_date)";
} else { // if the current month is April to December
    $sqlLast = "SELECT MAX(SUBSTRING_INDEX(sr_no, '/', -1)) AS last_srno FROM tbl_register WHERE MONTH(sr_date)>=4 AND YEAR(sr_date)=YEAR(CURDATE())";
}

$resultLast = $conn->query($sqlLast);

if ($resultLast) {
    $rowLast = $resultLast->fetch_assoc();
    $lastSrno = intval($rowLast["last_srno"]);
    $newSrno = $lastSrno + 1;

    $srno = str_pad($newSrno, 3, '0', STR_PAD_LEFT); // Pad with zeros to ensure three-digit format
    $resultLast->free();

    $response['success'] = true;
    $response['message'] = $selectedVal . "/" . $currentmonth . "-" . substr($currentyear, 2) . "/" . $srno;
} else {
    $response['success'] = false;
    $response['message'] = "Error executing query: " . $conn->error;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);


?>
