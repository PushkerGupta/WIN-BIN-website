<?php
$host = "sql108.infinityfree.com"; 
$user = "if0_42253514";             
$pass = "12345678DCsac"; // Put your actual account password here
$dbname = "if0_42253514_winbin_db";   

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}
?>