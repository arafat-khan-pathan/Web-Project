<?php
session_start();

// DB Connection
$host = "localhost";
$user = "root";
$pass = ""; 
$db = "paws_hearts";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>MESSAGE SYSTEM DEBUG</h2>";
echo "<style>body{font-family:arial;padding:20px;} table{border-collapse:collapse;} td,th{border:1px solid #ddd;padding:8px;} th{background:#f97316;color:white;}</style>";

// Check if you're logged in
if (!isset($_SESSION['userId'])) {
    echo "<p style='color:red;'>❌ You are NOT logged in! Please login first.</p>";
    echo "<a href='../login__.php'>Go to Login</a>";
    exit();
}

echo "<p style='color:green;'>✅ You are logged in as User ID: <strong>" . $_SESSION['userId'] . "</strong> (" . $_SESSION['userName'] . ")</p>";

// Check if messages table exists
$checkTable = $conn->query("SHOW TABLES LIKE 'messages'");
if ($checkTable->num_rows == 0) {
    echo "<p style='color:red;'>❌ Messages table does NOT exist!</p>";
    echo "<p>Run this SQL in phpMyAdmin:</p>";
    echo "<pre>CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (sender_id),
    INDEX (receiver_id)
);</pre>";
} else {
    echo "<p style='color:green;'>✅ Messages table exists</p>";
}

// Show all users
echo "<h3>All Users in Database:</h3>";
$users = $conn->query("SELECT id, first_name, last_name, email, role FROM users ORDER BY id");
if ($users->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
    while ($u = $users->fetch_assoc()) {
        $highlight = ($u['id'] == $_SESSION['userId']) ? "style='background:#ffffcc;'" : "";
        echo "<tr $highlight><td>{$u['id']}</td><td>{$u['first_name']} {$u['last_name']}</td><td>{$u['email']}</td><td>{$u['role']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p>No users found!</p>";
}

// Show all messages
echo "<h3>All Messages in Database:</h3>";
$messages = $conn->query("SELECT m.*, 
    CONCAT(u1.first_name, ' ', u1.last_name) as sender_name,
    CONCAT(u2.first_name, ' ', u2.last_name) as receiver_name
    FROM messages m
    LEFT JOIN users u1 ON m.sender_id = u1.id
    LEFT JOIN users u2 ON m.receiver_id = u2.id
    ORDER BY m.created_at DESC");

if ($messages && $messages->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>From</th><th>To</th><th>Message</th><th>Time</th></tr>";
    while ($m = $messages->fetch_assoc()) {
        echo "<tr><td>{$m['id']}</td><td>{$m['sender_name']} (ID:{$m['sender_id']})</td><td>{$m['receiver_name']} (ID:{$m['receiver_id']})</td><td>{$m['message_text']}</td><td>{$m['created_at']}</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:orange;'>⚠️ No messages found in database!</p>";
    
    // Show SQL to insert test messages
    echo "<h4>To create test messages, run this SQL:</h4>";
    $currentId = $_SESSION['userId'];
    
    // Find another user to message with
    $otherUser = $conn->query("SELECT id, first_name, last_name FROM users WHERE id != $currentId LIMIT 1");
    if ($otherUser && $otherUser->num_rows > 0) {
        $other = $otherUser->fetch_assoc();
        $otherId = $other['id'];
        $otherName = $other['first_name'] . ' ' . $other['last_name'];
        
        echo "<pre>-- Messages between you (ID:$currentId) and $otherName (ID:$otherId)
INSERT INTO messages (sender_id, receiver_id, message_text) VALUES
($currentId, $otherId, 'Hi! Is this pet still available?'),
($otherId, $currentId, 'Yes it is! Would you like to meet?'),
($currentId, $otherId, 'That would be great!');</pre>";
        
        echo "<form method='POST' style='margin-top:20px;'>
            <button type='submit' name='create_test' style='background:#f97316;color:white;padding:10px 20px;border:none;border-radius:5px;cursor:pointer;'>
                ✨ Create Test Messages Now
            </button>
        </form>";
        
        if (isset($_POST['create_test'])) {
            $conn->query("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES
                ($currentId, $otherId, 'Hi! Is this pet still available?'),
                ($otherId, $currentId, 'Yes it is! Would you like to meet?'),
                ($currentId, $otherId, 'That would be great!')");
            echo "<p style='color:green;'>✅ Test messages created! <a href='messages.php'>Go to Messages</a></p>";
        }
    } else {
        echo "<p>Need at least 2 users in database to test messaging!</p>";
    }
}

echo "<hr><a href='messages.php'>← Back to Messages</a> | <a href='index.php'>← Back to Home</a>";
?>
