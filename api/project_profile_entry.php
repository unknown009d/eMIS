<?php
include 'connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$sanetval = sanitizeValues($data);
$date = date("Y-m-d");
$timestamp = strtotime($date);
$currentmonth = date("m", $timestamp);
$currentyear = date("Y", $timestamp);
$response = '';
$uvar = "GIGW";

// if ($currentmonth < 4) { // if the current month is January, February & March
//     $sql = "SELECT LPAD(COUNT(*)-1, 3, 0) AS srno FROM tbl_it_register WHERE (YEAR(CURDATE())-1)=YEAR(sr_date) OR YEAR(CURDATE())=YEAR(sr_date)";
// } else { // if the current month is April to December
//     $sql = "SELECT LPAD(COUNT(*)+1, 3, 0) AS srno FROM tbl_it_register WHERE MONTH(sr_date)>=4 AND YEAR(sr_date)=YEAR(CURDATE())";
// }

if ($currentmonth < 4) { // if the current month is January, February & March
    $sql = "SELECT LPAD(COUNT(*)-1, 3, 0) AS srno FROM tbl_register WHERE (YEAR(CURDATE())-1)=YEAR(sr_date) OR YEAR(CURDATE())=YEAR(sr_date)";
} else { // if the current month is April to December
    $sql = "SELECT LPAD(COUNT(*)+1, 3, 0) AS srno FROM tbl_register WHERE MONTH(sr_date)>=4 AND YEAR(sr_date)=YEAR(CURDATE())";
}

$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $srno = $row["srno"];
    }
    $response = $uvar . "/" . $currentmonth . "-" . substr($currentyear, 2) . "/" . $srno;
} else {
    $response = "Error executing query: " . $conn->error();
}
// Assuming you have established a database connection
// and prepared the statement
$id = $response; // Example value for id // create id here
$url_id = $sanetval['url_id']; // Example value for url_id
$sr_no = $sanetval['sr_no']; // Example value for sr_no
//$profile_created = date("Y-m-d");  // Example value for profile_created // system date

$stmt = $conn->prepare("INSERT INTO tbl_project_profile (id, url_id, sr_no, profile_created) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $id, $url_id, $sr_no, $date);

$reply = array();
// Execute the statement
$stmt->execute();

// Check if the query was successful
if ($stmt->affected_rows > 0) {
    $reply["messages"] = "Values inserted successfully.";
} else {
    $reply["messages"] = "Failed to insert values.";
}
// Close the statement and database connection
$stmt->close();
$conn->close();
echo $reply['messages'];
//encoding the api
header('Content-Type: application/json');
echo json_encode($reply);
