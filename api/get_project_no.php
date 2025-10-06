<?php
include 'connect.php'; // Include the file with the database connection

$data = json_decode(file_get_contents("php://input"), true);
$date = $data['date'];
$timestamp = strtotime($date);
$currentmonth = date("m", $timestamp);
$currentyear = date("Y", $timestamp);
$response=array();
//it is the unique letter of the begging in unique id
$uvar="GIGW";

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
    $response['sucess']=true;
    $response['message']= $uvar . "/" . $currentmonth . "-" .substr($currentyear, 2) . "/" . $srno;
}
else{
    $response['sucess']=false;
    $response['message'] = "Error executing query: " . $conn->error();
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);

?>