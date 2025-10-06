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
    if ($srqexist < 1) {
        return [
            'success' => false,
            'message' => "Service Request Number doesn't exist.",
            'error' => 404
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
    $j_type = $data['jtype']; //type of the job (TE/TR/IT/CA)
    $t_charge = $data['total']; //total test charge without GST
    $c_location = $data['cloc']; //location of client
    $cgst = $data['cgst']; //CGST 9% with 9% SGST
    $sgst = $data['sgst']; //SGST 9% with 9% CGST
    $igst = $data['igst']; //IGST [if client is outside the state]
    $tot_amount = $data['grandtotal']; //total amount with GST
    $fyear = $data['fyear'];
    $rmks = $data['remarks']; //remarks on the service request
    $m_factor = $data['mfactor']; //multiplication factor 1 or 1.25

    $nom_old_details = selectQ($conn, "SELECT nom_details FROM tbl_register WHERE sr_no = ?", [$sr_no])[0]['nom_details'];
    if($nom_old_details != $nom_details){

        // Deleting all nomenclature
        $stmt = $conn->prepare("DELETE FROM `tbl_nom` WHERE sr_no=?");
        $stmt->bind_param("s", $sr_no);
        if(!$stmt->execute()){
            return [
                'success' => false,
                'message' => "Updating noms had an issue.",
                'error' => 520 
            ];
        }
        $stmt->close();

        // Setting the auto increment to the Last entry
        $newID = selectQ($conn, "SELECT MAX(id) AS max FROM tbl_nom")[0]['max'] + 1;
        $ai_reset = "ALTER TABLE tbl_nom AUTO_INCREMENT = " . $newID;
        if ($conn->query($ai_reset) == FALSE) {
            return [
                'success' => false,
                'message' => "Error updating the Auto Increment",
            ];
        } 

        $stmt = $conn->prepare("INSERT INTO tbl_nom(sr_no, nom, qty, t_charge, rmks, url) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($nom_array as $nom) {
            $nome = $nom['nomenclature'];
            $nquan = $nom['quantity'];
            $nrate = $nom['rate'];
            $nnom_rmk = $nom['remarks'];
            $nom_url = $nom['url'];
            $stmt->bind_param("ssssss", $sr_no, $nome, $nquan, $nrate, $nnom_rmk, $nom_url);

            if(!$stmt->execute()) {
                return [
                    'success' => false,
                    'message' => "Failed to update nomenclatures",
                ];
            }
        }
        $stmt->close();
    }else{
        $nomids = selectQ($conn, "SELECT id FROM tbl_nom WHERE sr_no = ?", [$sr_no]);
        $stmt = $conn->prepare("UPDATE tbl_nom SET rmks=?, url=? WHERE id=?");
        foreach ($nom_array as $key => $nom) {
            // Updating the changes
            $nnom_rmk = $nom['remarks'];
            $nom_url = $nom['url'];
            $stmt->bind_param("sss", $nnom_rmk, $nom_url, $nomids[$key]['id']);

            if(!$stmt->execute()) {
                return [
                    'success' => false,
                    'message' => "Failed to update nomenclatures",
                ];
            }
        }
        $stmt->close();
    }


    // Inserting into tbl_register
    $stmt = $conn->prepare("UPDATE tbl_register SET c_code=?, nom_details=?, j_type=?, t_charge=?, cgst=?, sgst=?, igst=?, tot_amount=?, rmks=?, fyear=?, j_location=?, m_factor=?, c_location=? WHERE sr_no = ?");
    $stmt->bind_param("ssssssssssssss", $c_code, $nom_details, $j_type, $t_charge, $cgst, $sgst, $igst, $tot_amount, $rmks, $fyear, $j_location, $m_factor, $c_location, $sr_no);

    if(!$stmt->execute()){
        return [
            'success' => false,
            'message' => "Failed to update data into IT Register",
        ];
    }

    $stmt->close();

    return [
        'success' => true,
        'message' => "Details were updated",
    ];

}