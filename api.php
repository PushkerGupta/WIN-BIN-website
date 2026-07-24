<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// --- GET REQUEST: FETCH BALANCE ---
if ($method === 'GET') {
    if (!isset($_GET['mobile'])) {
        echo json_encode(["status" => "error", "message" => "Missing mobile parameters"]);
        exit();
    }
    $mobile = $_GET['mobile'];
    $stmt = $conn->prepare("SELECT name, points FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["status" => "success", "name" => $user['name'], "points" => (int)$user['points']]);
    } else {
        echo json_encode(["status" => "success", "name" => "Guest User", "points" => 0]);
    }
}

// --- POST REQUEST: DEDUCT POINTS ---
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!isset($input['mobile']) || !isset($input['points_to_deduct'])) {
        echo json_encode(["status" => "error", "message" => "Incomplete request parameters"]);
        exit();
    }

    $mobile = $input['mobile'];
    $deduct = (int)$input['points_to_deduct'];

    $stmt = $conn->prepare("SELECT points FROM users WHERE mobile = ?");
    $stmt->bind_param("s", $mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Profile entry not found"]);
        exit();
    }

    $user = $result->fetch_assoc();
    $current_points = (int)$user['points'];

    if ($current_points < $deduct) {
        echo json_encode(["status" => "error", "message" => "Insufficient balance fields available"]);
        exit();
    }

    $new_points = $current_points - $deduct;
    $update = $conn->prepare("UPDATE users SET points = ? WHERE mobile = ?");
    $update->bind_param("is", $new_points, $mobile);
    
    if ($update->execute()) {
        echo json_encode(["status" => "success", "message" => "Points cleared cleanly", "remaining_points" => $new_points]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database save execution failed"]);
    }
}
?>