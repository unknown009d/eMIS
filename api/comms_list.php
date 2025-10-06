<?php
include "connect.php";

if (!isset($_GET['pid'])) {
    $clientList = array();
    goto end;
}
$pid = $_GET['pid'];

// Get the start and limit parameters from the query string
$start = isset($_GET['start']) ? intval($_GET['start']) : 0;
$limit = isset($_GET['end']) ? intval($_GET['end']) : 10; // Number of entries to retrieve at once

// Get the search parameter from the query string
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Fetch the data from the database using LIMIT and OFFSET clauses
if (isset($_GET['sort'])) {
    if ($_GET['sort'] == 1 || $_GET['sort'] == 'desc') {
        $srtTech = "DESC";
    } else {
        $srtTech = "ASC";
    }
    if (!empty($search)) {
        $sql = "SELECT * FROM tbl_log_comms WHERE pid='$pid' AND (sender LIKE '%$search%' OR subject LIKE '%$search%') ORDER BY date $srtTech, time $srtTech, id $srtTech LIMIT $limit OFFSET $start";
    } else {
        $sql = "SELECT * FROM tbl_log_comms WHERE pid='$pid' ORDER BY date $srtTech, time $srtTech, id $srtTech LIMIT $limit OFFSET $start";
    }
} else {
    if (!empty($search)) {
        $sql = "SELECT * FROM tbl_log_comms WHERE pid='$pid' AND (sender LIKE '%$search%' OR subject LIKE '%$search%') ORDER BY id DESC LIMIT $limit OFFSET $start";
    } else {
        $sql = "SELECT * FROM tbl_log_comms WHERE pid='$pid' ORDER BY id DESC LIMIT $limit OFFSET $start";
    }
}
$result = mysqli_query($conn, $sql);

// Prepare the data to be sent as JSON
$clientList = [];
while ($row = mysqli_fetch_assoc($result)) {
    $row['time'] = date('h:i A', strtotime($row['time']));
    $row['date'] = date('jS F Y', strtotime($row['date']));
    $clientList['data'][] = $row;
}

// Get the total number of entries for pagination
$sql = "SELECT COUNT(*) AS entry_count FROM tbl_log_comms WHERE pid='$pid'";
if (!empty($search)) {
    $sql .= " AND (sender LIKE '%$search%' OR subject LIKE '%$search%')";
}
$result = mysqli_query($conn, $sql);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $clientList['entry_count'] = $row['entry_count'];
} else {
    $clientList['entry_count'] = 0;
}

end:
// Return the data as JSON
header('Content-Type: application/json');
echo json_encode($clientList);
?>
