<?php
session_start();
if (!isset($_SESSION['userId'])) exit;

$host = "localhost";
$user = "root";
$pass = ""; 
$db = "paws_hearts";
$conn = new mysqli($host, $user, $pass, $db);

$currentUserId = $_SESSION['userId'];

// SEND MESSAGE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = intval($_POST['receiver_id']);
    $message = $conn->real_escape_string($_POST['message']);
    
    $sql = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES ($currentUserId, $receiverId, '$message')";
    $conn->query($sql);
    echo json_encode(['status' => 'success']);
    exit;
}

// FETCH MESSAGES (GET)
if (isset($_GET['receiver_id'])) {
    $receiverId = intval($_GET['receiver_id']);
    
    $sql = "SELECT *, DATE_FORMAT(created_at, '%h:%i %p') as time 
            FROM messages 
            WHERE (sender_id = $currentUserId AND receiver_id = $receiverId)
               OR (sender_id = $receiverId AND receiver_id = $currentUserId)
            ORDER BY created_at ASC";
            
    $result = $conn->query($sql);
    $messages = [];
    while($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($messages);
    exit;
}