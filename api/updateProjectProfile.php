<?php
include 'connect.php';
$data = json_decode(file_get_contents("php://input"), true);
$data = sanitizeValues($data);

$pk = $data['id'];
$all_data = $data['data'];

$setcl = "";
$cvalues = array(); // Initialize an empty array to store changed columns

// Fetch the previous data from the view
$sql_prev = "SELECT * FROM project_all WHERE p_id='" . $pk . "'";
$result_prev = $conn->query($sql_prev);
$row_prev = $result_prev->fetch_assoc();

foreach ($all_data as $column => $newValue) {
    if (mapdata($column) == null)
        continue;

    $column = mapdata($column);

    if (strlen($newValue) <= 0) {
        if ($row_prev[$column] !== NULL) {
            $cvalues[] = $column; // Add the column name to the array of changed columns
        }
        $setcl .= "`$column` = NULL, ";
        continue;
    }
    if ($column == 'sr_no' || $column == 'p_id' || $column == 'nom_id')
        continue;

    // Check if the new value is different from the previous value
    if ($row_prev[$column] !== $newValue) {
        $cvalues[] = $column;
    }

    $setcl .= "`$column` = '$newValue', ";
}

$response = array();
$setCl = rtrim($setcl, ', ');
$setcl = substr_replace($setcl, "", -2);

// Sanitizing so that none of the fields in the database is shown in the front end...
foreach ($cvalues as $key => $value) {
    $cvalues[$key] = mapfromdb($value);
}

$sql = "UPDATE project_all SET $setcl WHERE p_id='" . $pk . "'";
if ($conn->query($sql) == TRUE) {
    $response = array(
        "success" => true,
        "message" => "Update success",
        "updated" => $cvalues // Add the changed column names to the response
    );
} else {
    $response = array(
        "success" => false,
        "message" => "Error updating record: " . $conn->error(),
        "code" => 500
    );
}
end:
echo json_encode($response);
// Close the connection
$conn->close();

function mapdata($col)
{
    $dataset = array(
        "projectURL" => "url",
        "priority" => "priority",
        "certificate" => "certificate",
        "wimName" => "wim_name",
        "wimDesignation" => "wim_desg",
        "wimEmail" => "wim_email",
        "wimPhone" => "wim_phone",
        "tpvName" => "v_name",
        "tpvAddress" => "v_address",
        "tpvEmail" => "v_email",
        "tpvPhone" => "v_phone",
        "assignedWork" => "v_task_assign",
        "tpvGST" => "v_gst",
        "clientName" => "c_name",
        "clientAddress" => "c_addr",
        "clientPhone" => "c_phn",
        "clientMail" => "c_email",
        "clientGST" => "c_gst",
    );
    return $dataset[$col] ?? null;
}
function mapfromdb($col)
{
    $dataset = array(
        "url" => "projectURL",
        "priority" => "priority",
        "certificate" => "certificate",
        "wim_name" => "wimName",
        "wim_desg" => "wimDesignation",
        "wim_email" => "wimEmail",
        "wim_phone" => "wimPhone",
        "v_name" => "tpvName",
        "v_address" => "tpvAddress",
        "v_email" => "tpvEmail",
        "v_phone" => "tpvPhone",
        "v_task_assign" => "assignedWork",
        "v_gst" => "tpvGST",
        "c_name" => "clientName",
        "c_addr" => "clientAddress",
        "c_phn" => "clientPhone",
        "c_email" => "clientMail",
        "c_gst" => "clientGST",
    );
    return $dataset[$col] ?? null;
}

?>