<?php
include 'connect.php';
$rawdata = json_decode(file_get_contents("php://input"), true);
$data = sanitizeValues($rawdata);
$date = date("Y-m-d");
$timestamp = strtotime($date);
$currentmonth = date("m", $timestamp);
$currentyear = date("Y", $timestamp);
$response = array();
$uvar = "PP";

if ($currentmonth < 4) { // if the current month is January, February & March
    $sql = "SELECT LPAD(COUNT(*)-1, 3, 0) AS srno FROM tbl_project_profile WHERE (YEAR(CURDATE())-1)=YEAR(p_date) OR YEAR(CURDATE())=YEAR(p_date)";
} else { // if the current month is April to December
    $sql = "SELECT LPAD(COUNT(*)+1, 3, 0) AS srno FROM tbl_project_profile WHERE MONTH(p_date)>=4 AND YEAR(p_date)=YEAR(CURDATE())";
}
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $srno = $row["srno"];
    }
    $reply = $uvar . "/" . $currentmonth . "-" . substr($currentyear, 2) . "/" . $srno;
} else {
    $response = array(
        "success" => false,
        "message" => mysqli_errno($conn)
    );
    goto error;
}
// Assuming you have established a database connection
// and prepared the statement
// Example value for id // create id here
$response = array();
//assigning the project id that has been generated
$data['p_id'] = $reply;
//assigning the current date
$data['p_date'] = $date;
if (isset($data['sr_no'])) {
    $userData = $data;
    $sql = "INSERT INTO tbl_project_profile (";
    $sql .= implode(", ", array_keys($userData));
    $sql .= ") VALUES (";
    $sql .= str_repeat("?, ", count($userData) - 1) . "?";
    $sql .= ")";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat("s", count($userData)), ...array_values($userData));
    $isDirRemoved = removeDirectory($reply);
    if($isDirRemoved[0]){
        if (!$stmt->execute()) {
            $response = array(
                "success" => false,
                "message" => $stmt->errno
            );
            goto error;
        } else {
            $response = array(
                "success" => true,
                "message" => "Project created successfully",
                "pid" => "$reply"
            );
        }
    } else {
        $response = array(
            "success" => false,
            "message" => "PID = " . $reply . ", Error : " . $isDirRemoved[1]
        );
        goto error;
    }
} else {
    $response = array(
        "success" => false,
        "message" => "The sr_no is not set"
    );
    goto error;
}

// Close the statement and database connection
$stmt->close();
$conn->close();
//echo $reply['messages'];
//encoding the api

//the goto statement will redirect to the next line
error:
header('Content-Type: application/json');
echo json_encode($response);
?>