<?php
include 'connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$sanetval = $data;
//inserting the nomenclature name
$n_name = $sanetval['nom'];
//assigning the price of the noenclature
$n_dtls = $sanetval['details'];
//assigning the job type of the nomnclature
$j_type = $sanetval['jtype'];
$rate=$sanetval['rate'];
$year=$sanetval['fyear'];
$rmkrs=$sanetval['remarks'];
//date of the nomenclatre
$stmt = $conn->prepare("INSERT INTO tbl_testrate (nom, nom_dtls, jtype, rate, fyear, remarks) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssdss", $n_name, $n_dtls, $j_type, $rate, $year, $rmkrs);
$stmt->execute();
$checkval = 0;
$response = array();
// Check if the query was successful
if ($stmt->affected_rows > 0) {
    $checkval = 1;
} else {
    $response = array(
        "success" => false,
        "message" => mysqli_stmt_error($stmt)
    );
}
//query for returning the latest id form the nom table
if ($checkval == 1) {
    $resp = "SELECT * FROM tbl_testrate ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($conn, $resp);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $response = array(
            "success" => true,
            "data" => array(
                "id" => $row['id'],
                "nom" => $row['nom'],
                "details" => $row['nom_dtls'],
                "jtype" => $row['jtype'],
                "rate" => $row['rate'],
                "fyear" => $row['fyear'],
                "remarks"=> truncateText($row['remarks'], 15)
                )
            );
        }
        
    } 
    else {
        $response = array(
            "success" => false,
            "message" => "Failed to retrieve the nom values"
        );
    }
//echo $reply;
header('Content-Type: application/json');
echo json_encode($response);
