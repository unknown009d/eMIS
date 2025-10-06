<?php 
include "connect.php";
$res = array();
if(isset($_GET['id'])){
    $id = $_GET['id'];

    $username = selectQ($conn, "SELECT e_name FROM tbl_employee WHERE uid = ?", [$id]);

    if(count($username) > 0){
        $res = [
            "success" => true,
            "message" => $username[0]['e_name']
        ];
    }else{
        $res = [
            "success" => false,
            "message" => "User doesn't exist"
        ];
    }


}else{
    $res = [
        "success" => false,
        "message" => "Insufficient values provided"
    ];
}

echo json_encode($res);

?>