<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    echo "<h2>Error 405 : Method Not Allowed</h2>";
}

// Function to process the data and determine the appropriate value
function process_data($data)
{
    include 'connect.php';

    $rawdata = json_decode(file_get_contents("php://input"), true);

    $data = sanitizeValues($rawdata);

    $datetime = explode("T", $data['datetime'])[0] . " " . explode("T", $data['datetime'])[1];
    $returnable = $data['returnable'];
    $org_name = $data['org_name'];
    $reference = $data['reference'] ?: null; 
    $gtpremarks = $data['gtpremarks'] ?: null; 

    $items = $data['items'];

    // Adjust the auto-increment value to start from the last updated value or from 0 if there's no previous data
    $sql = "SELECT MAX(slno) AS highest_value FROM tbl_gatepass";
    $result = $conn->query($sql);
    $sqlrow = $result->fetch_assoc();
    $highestValue = $sqlrow['highest_value'];

    // Set the auto-increment value based on existing data or start from 1
    if ($highestValue !== null) {
        $newAutoIncrement = $highestValue + 1;
    } else {
        $newAutoIncrement = 1;
    }

    // Adjust the auto-increment value
    $sql = "ALTER TABLE tbl_gatepass AUTO_INCREMENT = $newAutoIncrement";
    if ($conn->query($sql) === FALSE) {
        return [
            "success" => false,
            "message" => "Couldn't update the Auto Increment Value"
        ];
    } 

    // Inserting into tbl_register
    $stmt = $conn->prepare("INSERT INTO tbl_gatepass(returnable,org_name,reference,remarks,datetime) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $returnable, $org_name, $reference, $gtpremarks, $datetime);
    $stmt->execute();
    $slno = $conn->insert_id;

    if ($stmt->affected_rows > 0) {
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO tbl_gatepass_items(slno,particular,modelno,serialno,quantity,remarks) VALUES (?,?,?,?,?,?)");
        $nom_id = array();
        // Bind parameters
        foreach ($items as $nom) {
            $particular = $nom['particulars']; 
            $modelno = $nom['modelno'] ?: null; 
            $serialno = $nom['slno'] ?: null; 
            $quantity = $nom['quantity']; 
            $remarks = $nom['remarks'] ?: null; 

            $stmt->bind_param("ssssss", $slno, $particular, $modelno, $serialno, $quantity, $remarks);
            $stmt->execute();
        }
        $stmt->close();
        return [
            'success' => true,
            'message' => "Data inserted successfully",
            'id' => $slno
        ];
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => "There was a problem in saving the data of Gatepass",
        ];
    }
}

?>