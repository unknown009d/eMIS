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
    echo json_encode(array("error" => "Method Not Allowed"));
}

// Function to process the data and determine the appropriate value
function process_data($data)
{
    include 'connect.php';
    $sanetval = $data;
    $id = $sanetval['id'];
    $name = $sanetval['name'];
    $size = $sanetval['size'] ?? '';

    try {
        // date of the nomenclature
        $stmt = $conn->prepare("INSERT INTO tbl_docment_type (d_code, d_name, d_size) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $id, $name, $size);
        $stmt->execute();

        // query for returning the latest id from the nom table
        $resp = "SELECT * FROM tbl_docment_type WHERE d_code='$id' LIMIT 1";
        $result = mysqli_query($conn, $resp);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $response = array(
                "success" => true,
                "data" => array(
                    "id" => $row['d_code'],
                    "name" => $row['d_name'],
                    "size" => $row['d_size'],
                )
            );
        } else {
            $response = array(
                "success" => false,
                "message" => "Failed to retrieve the nom values"
            );
        }

        return $response;
    } catch (mysqli_sql_exception $ex) {
        // Handle the duplicate entry error
        if ($ex->getCode() == 1062) {
            return array(
                "success" => false,
                "message" => "Duplicate entry for 'd_code'"
            );
        } else {
            // Handle other database-related errors
            return array(
                "success" => false,
                "message" => "Database error occurred"
            );
        }
    }
}

?>