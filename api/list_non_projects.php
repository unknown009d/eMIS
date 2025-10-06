<?php
include "connect.php";

// Get the page number from the query parameter or default to 1
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Get the search query from the query parameter, if provided
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Define the number of results per page
$resultsPerPage = 10;

// Calculate the OFFSET for the current page
$offset = ($page - 1) * $resultsPerPage;

/* $sql = "SELECT tbl_nom.* */
/*     FROM tbl_nom */
/*     LEFT JOIN tbl_project_profile ON tbl_nom.id = tbl_project_profile.nom_id */
/*     WHERE tbl_project_profile.nom_id IS NULL */
/*     AND tbl_nom.sr_no LIKE 'IT%' */
/*     AND tbl_nom.sr_no LIKE ?"; */

$sql = "SELECT tbl_nom.*
    FROM tbl_nom
    LEFT JOIN tbl_project_profile ON tbl_nom.id = tbl_project_profile.nom_id
    LEFT JOIN tbl_register ON tbl_nom.sr_no = tbl_register.sr_no
    WHERE tbl_project_profile.nom_id IS NULL
    AND tbl_nom.sr_no LIKE 'IT%'
    AND tbl_nom.sr_no LIKE ?
    AND tbl_register.sr_no IS NOT NULL";

// If a search query is provided, concatenate it with '%' for partial search
$searchParam = '%' . $searchQuery . '%';

$sql .= " ORDER BY tbl_nom.id DESC
    LIMIT $resultsPerPage OFFSET $offset";

$noms = selectQ($conn, $sql, [$searchParam]);

$rows = [];
foreach ($noms as $key => $data) {
    $rows[$key]['id'] = $data['id'];
    $rows[$key]['srno'] = $data['sr_no'];
    $rows[$key]['nom'] = $data['nom'];
    $rows[$key]['url'] = truncateText($data['url'], 25);
    $rows[$key]['long_url'] = $data['url'];
}

echo json_encode($rows);
?>
