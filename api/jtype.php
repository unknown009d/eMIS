<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the necessary files
    include 'connect.php';

    // Get the data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Process the data and determine the appropriate value
    $result = process_data($data, $conn);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Return an error response if the request method is not POST
    header("HTTP/1.1 405 Method Not Allowed");
    echo "<h2>Error 405: Method Not Allowed</h2>";
}

function process_data($data, $conn)
{
    // Extract the necessary values from the $data array
    $too = $data['too'];
    $jtcode= $data['jtcode'] ?? null;
    $jtdesc = $data['jtdesc'] ?? null;
    $jtcode = strtoupper($jtcode);

    if($too == 1)
    {
        $stmt = $conn->prepare("INSERT INTO tbl_jtype (jtcode, jtdesc) VALUES (?, ?)");
        $stmt->bind_param("ss", $jtcode, $jtdesc);
    }  elseif($too == 2){
        $stmt = $conn->prepare("DELETE FROM tbl_jtype WHERE jtcode=?");
        $stmt->bind_param("s", $jtcode);
    } else{
        $success = false;
        $op = "Invalid Type of Operation";
        goto end;
    }
    if($stmt->execute()){
        $success = true;
        $op = "Data " . ($too == 1 ? 'Inserted' : ($too == 2 ? 'Updated' : 'Deleted')) . " successfully";
    }else{
        $success = false;
        $op = "Data Isn't " . ($too == 1 ? 'Inserted' : ($too == 2 ? 'Updated' : 'Deleted')) . " successfully";
    }

    end:
    // Format the response
    $response = [
        'success' => $success,
        'message' => $op ?? null,
    ];

    // Return the processed data
    return $response;
}


?>