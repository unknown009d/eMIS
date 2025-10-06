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
    $page = $data['page'] ?? null;

    // Perform the necessary database query
    if($page != null){
        $query = "SELECT DISTINCT nom FROM tbl_testrate WHERE jtype='" . $page . "' ORDER BY id DESC";
    }else{
        $query = "SELECT DISTINCT nom FROM tbl_testrate ORDER BY id DESC";
    }
    // $query = "SELECT DISTINCT id,nom FROM tbl_testrate ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $nom = mysqli_fetch_all($result, MYSQLI_ASSOC);


    // Check if the query was successful
    $success = ($result !== false);

    // Retrieve the MySQL warnings if any
    $warnings = [];
    if ($success) {
        $warnings = mysqli_warning_count($conn) > 0 ? mysqli_get_warnings($conn) : [];
    }

    // Format the response
    $response = [
        'success' => $success,
        'data' => $success ? $nom : null,
        'warnings' => $success ? $warnings : null,
    ];

    // Return the processed data
    return $response;
}


?>