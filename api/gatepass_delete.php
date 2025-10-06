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

    if(!$data['slno']) return [
        "success" => false,
        "message" => "Please provide the proper slno."
    ];

    $slno= $data['slno'];

    $stmt = $conn->prepare("DELETE FROM `tbl_gatepass_items` WHERE slno = ?");
    $stmt->bind_param("s", $slno);
    

    if ($stmt->execute()) {
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM `tbl_gatepass` WHERE slno = ?");
        $stmt->bind_param("s", $slno);
        if($stmt->execute()){
            return [
                'success' => true,
                'message' => "Data deleted successfully",
            ];
        }else{
            return [
                'success' => false,
                'message' => "There was a problem in deleting from the Gatepass",
            ];
        }
    } else {
        $stmt->close();
        return [
            'success' => false,
            'message' => "There was a problem in deleting the data of Gatepass items",
        ];
    }
}

?>