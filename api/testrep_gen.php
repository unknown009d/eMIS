<?php

include "connect.php";
function getTotalRecords()
{
    global $conn;

    // SQL query to count total records in the table
    $sql = "SELECT COUNT(*) AS total_records FROM tbl_test_report";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch the result row as an associative array
        $row = mysqli_fetch_assoc($result);

        // Get the value of the 'total_records' column
        $totalRecords = $row['total_records'];
    } else {
        // Set default value to 0 in case of an error
        $totalRecords = 0;
    }


    return $totalRecords;
}

function getIdNo()
{

    global $conn;

    // SQL query to find the maximum value of the field
    $sql = "SELECT MAX(id) AS max_value FROM tbl_test_report";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch the result row as an associative array
        $row = mysqli_fetch_assoc($result);

        // Get the value of the 'max_value' column
        $maxValue = $row['max_value'];
        if ($maxValue > getTotalRecords()) {
            return $maxValue + 1;
        } else {
            return getTotalRecords() + 1;
        }
    } else {

        return false;
    }
}
function format($num)
{
    // Convert the number to a string
    $numStr = (string) $num;

    // Check the number of bits
    $numBits = strlen($numStr);

    // Determine the number of leading zeros to add
    $leadingZeros = '';
    if ($numBits == 1) {
        $leadingZeros = '00';
    } elseif ($numBits == 2) {
        $leadingZeros = '0';
    }

    // Add leading zeros and return the formatted number
    return $leadingZeros . $numStr;
}
function getTotalElements($date, $form, $tableName)
{
    $timestamp = strtotime($date);
    $currentmonth = date("m", $timestamp);
    $currentyear = date("Y", $timestamp);

    return $currentmonth . "-" . substr($currentyear, 2) . "/" . $form . "-" . format(getIdNo());
}

$json = file_get_contents('php://input');
$direc = "testreport/";
$data = json_decode($json, true);

if (isset($_POST['page'])) {
    $key = $_POST['page'];
    $page = $key;
    $key = directory($key);
    if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
        // Retrieve the uploaded file information
        $tempFile = $_FILES['file']['tmp_name'];
        $originalFileName = $_FILES['file']['name'];
        $fileInfo = pathinfo($originalFileName);
        // Extract the file extension
        $fileExtension = $fileInfo['extension'];
        $filename = isset($_POST['rename']) ? $_POST['rename'] : $originalFileName;
        $uniqueno = '_' . $currentDateTime = date('YmdHis');
        $filename .= $uniqueno;
        // Create a new file name with the extension
        $fileNameWithExtension = $filename . '.' . $fileExtension;
        $fullPath = $root_dir . $key . '/' . $direc . $fileNameWithExtension;
        //  echo $fullPath;
        if (!is_dir($root_dir . $key)) {
            if (!mkdir($root_dir . $key, 0755, true)) {
                $response = [
                    'success' => false,
                    'message' => 'Error occurred while creating the directory.'
                ];
                goto t1;
            }
        }
        if (!is_dir($root_dir . $key . '/' . $direc)) {
            if (!mkdir($root_dir . $key . '/' . $direc, 0755, true)) {
                $response = [
                    'success' => false,
                    'message' => 'Error occurred while creating the directory.'
                ];
                goto t1;
            }
        }
        // Move the uploaded file to the destination folder
        if (move_uploaded_file($tempFile, $fullPath)) {
            // Insert data into the tbl_test_report table
            $id = getIdNo(); //this function hasbenn call from the file idno.php   
            $fullPath = substr($fullPath, $root_dir_remove_length);
            $stdate = ($_POST['start_date'] == '') ? null : $_POST['start_date'];
            $cmpdate = ($_POST['complete_date'] == '') ? null : $_POST['complete_date'];
            $reldate = ($_POST['released_date'] == '') ? null : $_POST['released_date'];
            $cycle = ($_POST['cycle'] == '') ? null : $_POST['cycle'];
            $status = ($_POST['status'] == '') ? null : $_POST['status'];
            $maj = ($_POST['maj'] == '') ? null : $_POST['maj'];
            $mec = ($_POST['mec'] == '') ? null : $_POST['mec'];
            $min = ($_POST['min'] == '') ? null : $_POST['min'];
            $tot = ($_POST['tot'] == '') ? null : $_POST['tot'];
            $test = ($_POST['test'] == '') ? null : $_POST['test'];
            $remark = ($_POST['remarks'] == '') ? null : $_POST['remarks'];

            $rno = getTotalElements($_POST['start_date'], "TAR", "tbl_test_report");
            $sql = "INSERT INTO tbl_test_report (r_no, p_id, documents , cycle, start_date, complete_date, maj, mec, min, tot, status, test, remarks, released_date, id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssiiiissssi",
                $rno,
                $page,
                $fullPath,
                $cycle,
                $stdate,
                $cmpdate,
                $maj,
                $mec,
                $min,
                $tot,
                $status,
                $test,
                $remark,
                $reldate,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                $response = [
                    'success' => true,
                    'message' => 'Test report uploaded successfully.',
                    'rno' => $rno,
                    'data' => getTestReportByPid($conn, $_POST['page'])
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error occurred while uploading test report : ' . mysqli_error($conn)
                ];
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            $response = [
                'success' => false,
                'message' => 'Error occurred while moving the file.'
            ];
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Error occurred during file upload.'
        ];
    }
    t1:
} else {
    // Handle the case when 'page' is not set in the POST request
    $response = [
        'success' => false,
        'message' => 'Error: Please ensure you provide all the necessary data required for file upload.'
    ];
}
// Optionally, you can send the response back to the client as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>