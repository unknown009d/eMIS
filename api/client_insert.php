<?php
include 'connect.php';

// Function for formatting the string
function formatString($val)
{
    $len = strlen($val);
    $formattedValue = "000";
    switch ($len) {
        case 1:
            $formattedValue = "00" . $val;
            break;
        case 2:
            $formattedValue = "0" . $val;
            break;
        default:
            $formattedValue = $val;
    }
    return $formattedValue;
}

// Function to fetch the client number from the database
function fetchClientNumber($conn)
{
    $stmt_count_client = mysqli_prepare($conn, "SELECT COUNT(*) as cc FROM tbl_client");
    mysqli_stmt_execute($stmt_count_client);
    $count_client_result = mysqli_stmt_get_result($stmt_count_client);
    $count_client = mysqli_fetch_assoc($count_client_result);
    $client_num = formatString($count_client['cc'] + 1);
    return $client_num;
}

// Function to generate the c_code based on the input text and client number
function generateCcode($text, $client_num)
{
    $myArray = explode(" ", $text); //text split
    $len = count($myArray); //splitted array length
    $code = ""; //variable initialize

    $str = $client_num;
    $n = strlen((string)$str);

    for ($i = 0; $i < $len; $i++) { //a for loop
        if (strtolower($myArray[$i]) != "and" && strtolower($myArray[$i]) != "of") {
            $code .= strtoupper(substr($myArray[$i], 0, 1)); //c_code
        }
    }

    $result = "CUS/" . $str . "/" . $code;
    return $result;
}


$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if the required fields are present in the JSON data
if (!empty($data)) {
    $data = sanitizeValues($data);
    
    $client_num = fetchClientNumber($conn);
    $cCode = generateCcode($data['client_name'], $client_num);

    // Check if the c_code already exists in the database
    $checkQuery = "SELECT * FROM tbl_client WHERE c_code = '$cCode'";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // The c_code already exists, prevent insertion
        $response = array(
            "success" => false,
            "message" => "Duplicate c_code. Insertion failed."
        );
    } else {
        // Prepare the values for insertion
        $clientName = $data['client_name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $address = $data['address'];
        $pan = $data['pan'];
        $gst = $data['gst'];
        $categ = $data['cat'];
        $remark = $data['remark'];

        // Construct the insertion query
        $insertQuery = "INSERT INTO tbl_client (c_code, c_name, c_email, c_phn, c_addr, c_pan, c_gst, c_cat, c_rmk)
                        VALUES ('$cCode', '$clientName', '$email', '$phone', '$address', '$pan', '$gst', '$categ', '$remark')";

        // Execute the insertion query
        if (mysqli_query($conn, $insertQuery)) {
            // Insertion successful
            $insertedId = mysqli_insert_id($conn); // Get the ID of the last inserted row

            // Fetch the inserted data from the database
            $selectQuery = "SELECT * FROM tbl_client WHERE id = $insertedId";
            $result = mysqli_query($conn, $selectQuery);
            $insertedData = mysqli_fetch_assoc($result);

            // Include the inserted data in the response
            $response = array(
                "success" => true,
                "message" => "Data inserted successfully",
                "data" => $insertedData
            );
        } else {
            // Insertion failed
            $response = array("success" => false, "message" => "Failed to insert data");
        }
    }
} else {
    // Missing JSON data
    $response = array("success" => false, "message" => "No data received");
}

// Set the response content type and echo the response as JSON
header('Content-Type: application/json');

echo json_encode($response);
?>
