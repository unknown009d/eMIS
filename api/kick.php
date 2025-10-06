<?php
include 'connect.php';
// $rawdata = json_decode(file_get_contents("php://input"), true);

$name = "ABC";
$address = "Agartala";
$gst = "ABC163234";
$phone = "136324";
$email = "asdi@mcil.co";

$stmt = $conn->prepare("INSERT INTO `tbl_vendor`(`name`, `address`, `gst`, `phone`, `email`) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssss", $name, $address, $gst, $phone, $email);
$stmt->execute();

echo $stmt->insert_id;

?>