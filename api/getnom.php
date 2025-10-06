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

    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'];
    $cat = strtoupper($data['category']);

    // Prepare and execute a database query
    // $query = "SELECT DISTINCT sr_no,nom,t_charge FROM tbl_nom WHERE nom='" . $nom . "' ORDER BY nom ASC";
    // $query = "SELECT DISTINCT sr_no,nom,t_charge FROM tbl_nom WHERE nom='" . $nom . "' ORDER BY nom ASC";
    $query = "SELECT DISTINCT nom,rate FROM tbl_testrate WHERE id='" . $id . "' OR nom LIKE '%" . $id . "%' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);
    $nomenclatures = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Check if the user exists
    $response = (count($nomenclatures) > 0) ? [
        'success' => true,
        'noms' => $nomenclatures
    ] : array(
        'success' => false,
        'noms' => null
    );

    return $response;
}
?>