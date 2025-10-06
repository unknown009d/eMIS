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

    if (!isset($rawdata['srqno'])) {
        return ['success' => false, 'message' => "Please provide the Service Request Number correctly"];
    }

    /* If there is any XSS code it will not let the control pass from this part...
        if(isVulnerable($data)) return ['success' => false, 'message' => "Please don't put unnecessary details here"];
    */

    $data = sanitizeValues($rawdata);

    $sr_no = $data['srqno'];

    // $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_it_register WHERE sr_no = ?");
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_register WHERE sr_no = ?");
    $stmt->bind_param("s", $sr_no);
    $stmt->execute();
    $stmt->bind_result($srqexist);
    $stmt->fetch();
    if ($srqexist >= 1) {
        return [
            'success' => false,
            'message' => "Service Request Number already exist.",
            'error' => 409 // For conflict
        ];
    }
    $stmt->close();

    $nom_array = $data['nomdetails'];
    $nom_details = "";

    foreach ($nom_array as $nom) {
        $nome = $nom['nomenclature'];
        $nquan = $nom['quantity'];
        $nrate = $nom['rate'];
        $nnom_rmk = $nom['remarks'];
        $nom_url = $nom['url'];

        $nom_details .= $nquan . " of " . $nome . "@ " . $nrate . ";";
    }

    $c_code = $data['ccode']; //client code
    $sr_date = $data['date']; //service request date
    $j_location = $data['jloc']; //location of the job Inhouse or onsite
    $j_type = $data['jtype']; //type of the job (TE/TR/IT/CA )
    $t_charge = $data['total']; //total test charge without GST
    $c_location = $data['cloc']; //location of client
    $cgst = $data['cgst']; //CGST 9% with 9% SGST
    $sgst = $data['sgst']; //SGST 9% with 9% CGST
    $igst = $data['igst']; //IGST [if client is outside the state]
    $tot_amount = $data['grandtotal']; //total amount with GST
    $fyear = getFyearLong();
    $rmks = $data['remarks']; //remarks on the service request
    $m_factor = $data['mfactor']; //multiplication factor 1 or 1.25

    // Inserting into tbl_register
    $stmt = $conn->prepare("INSERT INTO tbl_register(sr_no, sr_date, c_code, nom_details, j_type, t_charge, cgst, sgst, igst, tot_amount, rmks, fyear, j_location, m_factor, c_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssssssss", $sr_no, $sr_date, $c_code, $nom_details, $j_type, $t_charge, $cgst, $sgst, $igst, $tot_amount, $rmks, $fyear, $j_location, $m_factor, $c_location);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // // Insertion into tbl_register successful
        // $stmt->close();

        // // Inserting into tbl_it_register
        // $stmt = $conn->prepare("INSERT INTO tbl_it_register(sr_no, sr_date, c_code, nom_details, j_type, t_charge, cgst, sgst, igst, tot_amount, rmks, fyear, j_location, m_factor, c_location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // $stmt->bind_param("sssssssssssssss", $sr_no, $sr_date, $c_code, $nom_details, $j_type, $t_charge, $cgst, $sgst, $igst, $tot_amount, $rmks, $fyear, $j_location, $m_factor, $c_location);
        // $stmt->execute();

        // if ($stmt->affected_rows > 0) {
        // Insertion into tbl_it_register successful
        $stmt->close();

        $stmt = $conn->prepare("INSERT INTO tbl_nom(sr_no, nom, qty, t_charge, rmks, url) VALUES (?, ?, ?, ?, ?, ?)");
        $nom_id = array();
        // Bind parameters
        foreach ($nom_array as $nom) {
            $nome = $nom['nomenclature'];
            $nquan = $nom['quantity'];
            $nrate = $nom['rate'];
            $nnom_rmk = $nom['remarks'];
            $nom_url = $nom['url'];
            $stmt->bind_param("ssssss", $sr_no, $nome, $nquan, $nrate, $nnom_rmk, $nom_url);

            $stmt->execute();

            $nom_id[] = $stmt->insert_id;
        }
        $stmt->close();
        return [
            'success' => true,
            'message' => "Data inserted successfully",
            'data' => $nom_id
        ];
    } else {
        // Insertion into tbl_it_register failed
        $stmt->close();
        return [
            'success' => false,
            'message' => "Failed to Insert data into IT Register",
        ];
    }

    // } else {
    //     // Insertion into tbl_register failed
    //     $stmt->close();
    //     return [
    //         'success' => false,
    //         'message' => "Failed to Insert data into Register",
    //     ];
    // }

}

function getFyearLong()
{
    $cmonth = Date("m");
    $cyear = Date("Y");
    $fyear = "";
    if ($cmonth > 3 && $cmonth <= 12) { //if the month is april to dec
        $fyear = $cyear . "-" . substr(($cyear + 1), 2);
    } else if ($cmonth < 4 && $cmonth >= 1) { //if month jan to march of the next year
        $fyear = ($cyear - 1) . "-" . substr($cyear, 2);
    }
    return $fyear;
}
