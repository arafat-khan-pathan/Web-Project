<?php
session_start();

// Check if user is logged in and is a shelter
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login__.php");
    exit();
}

$currentUserId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? 'Shelter User';

// DB Connection
$host = "localhost";
$user = "root";
$pass = ""; 
$db = "paws_hearts";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle message sending
if (isset($_POST['send_message'])) {
    $receiverId = $_POST['receiver_id'];
    $messageText = $_POST['message_text'];
    
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $currentUserId, $receiverId, $messageText);
    $stmt->execute();
    $stmt->close();
    
    header("Location: shelter_messages.php?chat=" . $receiverId);
    exit();
}

// Create messages table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_conversation (sender_id, receiver_id, created_at)
)";
$conn->query($createTableSQL);

/** 
 * Get List of Conversations (for shelter - conversations with adopters about pets)
 */
$convoQuery = "
    SELECT DISTINCT 
        u.id, 
        CONCAT(u.first_name, ' ', u.last_name) as userName,
        (SELECT message_text FROM messages 
         WHERE (sender_id = $currentUserId AND receiver_id = u.id) 
            OR (sender_id = u.id AND receiver_id = $currentUserId)
         ORDER BY created_at DESC LIMIT 1) as lastMessage,
        (SELECT created_at FROM messages 
         WHERE (sender_id = $currentUserId AND receiver_id = u.id) 
            OR (sender_id = u.id AND receiver_id = $currentUserId)
         ORDER BY created_at DESC LIMIT 1) as lastMessageTime
    FROM users u
    INNER JOIN messages m ON (m.sender_id = u.id AND m.receiver_id = $currentUserId) 
                           OR (m.sender_id = $currentUserId AND m.receiver_id = u.id)
    WHERE u.id != $currentUserId
    ORDER BY lastMessageTime DESC
";

$convos = $conn->query($convoQuery);
$conversationList = [];
if ($convos && $convos->num_rows > 0) {
    while ($convo = $convos->fetch_assoc()) {
        $conversationList[] = $convo;
    }
}

// Get selected chat user
$chatUserId = $_GET['chat'] ?? ($conversationList[0]['id'] ?? null);
$petId = $_GET['pet_id'] ?? null;

$selectedChat = null;
$messages = [];
$petInfo = null;

if ($chatUserId) {
    // Get the chat partner's info
    $userQuery = "SELECT id, first_name, last_name FROM users WHERE id = $chatUserId LIMIT 1";
    $userResult = $conn->query($userQuery);
    $selectedChat = $userResult->fetch_assoc();
    
    // Get messages for this conversation
    $msgQuery = "SELECT * FROM messages 
                 WHERE (sender_id = $currentUserId AND receiver_id = $chatUserId)
                    OR (sender_id = $chatUserId AND receiver_id = $currentUserId)
                 ORDER BY created_at ASC";
    $msgResult = $conn->query($msgQuery);
    if ($msgResult && $msgResult->num_rows > 0) {
        while ($msg = $msgResult->fetch_assoc()) {
            $messages[] = $msg;
        }
    }
    
    // Mark messages as read
    $readQuery = "UPDATE messages SET is_read = TRUE 
                  WHERE receiver_id = $currentUserId AND sender_id = $chatUserId";
    $conn->query($readQuery);
}

// Get pet info if pet_id provided
if ($petId) {
    $petQuery = "SELECT id, name FROM pets WHERE id = '$petId' AND user_id = $currentUserId LIMIT 1";
    $petResult = $conn->query($petQuery);
    if ($petResult && $petResult->num_rows > 0) {
        $petInfo = $petResult->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        .messaging-layout {
            display: grid;
            grid-template-columns: 320px 1fr;
            height: calc(100vh - 140px);
            margin: 20px 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .messaging-layout {
                grid-template-columns: 1fr;
            }
            .convos-sidebar {
                display: none;
            }
        }

        .convos-sidebar {
            border-right: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            background: #fcfdfe;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }

        .convos-list {
            flex: 1;
            overflow-y: auto;
        }

        .convo-item {
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f8fafc;
            text-decoration: none;
        }

        .convo-item:hover {
            background: #f1f5f9;
        }

        .convo-item.active {
            background: #fff7ed;
            border-left: 4px solid #f97316;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            flex-shrink: 0;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .convo-info {
            flex: 1;
            min-width: 0;
        }

        .convo-name {
            font-weight: 600;
            font-size: 0.95rem;
            color: #1f2937;
        }

        .convo-preview {
            font-size: 0.85rem;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message-area {
            display: flex;
            flex-direction: column;
            background: white;
        }

        .message-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fcfdfe;
        }

        .message-header-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .message-header-name {
            font-weight: 600;
            color: #1f2937;
        }

        .message-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message-group {
            display: flex;
            gap: 0.75rem;
            align-items: flex-end;
        }

        .message-group.own {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: 70%;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            word-wrap: break-word;
        }

        .message-bubble.received {
            background: #e2e8f0;
            color: #1f2937;
        }

        .message-bubble.sent {
            background: #f97316;
            color: white;
        }

        .message-input-area {
            padding: 1.5rem;
            border-top: 1px solid #f1f5f9;
            background: #fcfdfe;
            display: flex;
            gap: 0.75rem;
        }

        .message-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            resize: none;
            max-height: 120px;
        }

        .btn-send {
            padding: 0.75rem 1.5rem;
            background: #f97316;
            color: white;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-send:hover {
            background: #ea580c;
        }

        .empty-chat {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.php" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.php" class="hov">My Pets</a>
                <a href="shelter_messages.php" class="hov">Messages</a>
                <div class="user-info">
                    <a href="shelter_profile.php" id="userNameDisplay" class="hov"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="location.href='../logout.php'" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="messaging-layout">
            <!-- Conversations Sidebar -->
            <div class="convos-sidebar">
                <div class="sidebar-header">
                    <h2>Messages</h2>
                </div>
                <div class="convos-list">
                    <?php if (count($conversationList) > 0): ?>
                        <?php foreach ($conversationList as $convo): ?>
                            <a href="?chat=<?php echo $convo['id']; ?><?php echo $petId ? '&pet_id=' . urlencode($petId) : ''; ?>" 
                               class="convo-item <?php echo ($chatUserId == $convo['id']) ? 'active' : ''; ?>">
                                <div class="avatar">
                                    <?php echo strtoupper(substr($convo['userName'], 0, 1)); ?>
                                </div>
                                <div class="convo-info">
                                    <div class="convo-name"><?php echo htmlspecialchars($convo['userName']); ?></div>
                                    <div class="convo-preview"><?php echo htmlspecialchars(substr($convo['lastMessage'] ?? '', 0, 40)); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding: 1.5rem; text-align: center; color: #94a3b8;">
                            No conversations yet
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Message Area -->
            <div class="message-area">
                <?php if ($selectedChat): ?>
                    <div class="message-header">
                        <div class="message-header-info">
                            <div class="avatar"><?php echo strtoupper(substr($selectedChat['first_name'], 0, 1)); ?></div>
                            <div class="message-header-name">
                                <?php echo htmlspecialchars($selectedChat['first_name'] . ' ' . $selectedChat['last_name']); ?>
                                <?php if ($petInfo): ?>
                                    <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">About: <?php echo htmlspecialchars($petInfo['name']); ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="message-body">
                        <?php foreach ($messages as $msg): ?>
                            <div class="message-group <?php echo ($msg['sender_id'] == $currentUserId) ? 'own' : ''; ?>">
                                <div class="message-bubble <?php echo ($msg['sender_id'] == $currentUserId) ? 'sent' : 'received'; ?>">
                                    <?php echo htmlspecialchars($msg['message_text']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="message-input-area">
                        <textarea class="message-input" id="messageInput" placeholder="Type a message..."></textarea>
                        <button class="btn-send" onclick="sendMessage(<?php echo $chatUserId; ?>, '<?php echo $petId ?? ''; ?>')">
                            <i data-lucide="send" style="width: 18px; height: 18px;"></i>
                            Send
                        </button>
                    </div>
                <?php else: ?>
                    <div class="empty-chat">
                        <div>
                            <i data-lucide="message-circle" style="width: 64px; height: 64px; margin-bottom: 1rem; color: #cbd5e1;"></i>
                            <h3>No conversations yet</h3>
                            <p>Start messaging adopters about their inquiries</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const messageBody = document.querySelector('.message-body');
        if (messageBody) {
            messageBody.scrollTop = messageBody.scrollHeight;
        }

        function sendMessage(receiverId, petId) {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;

            const formData = new FormData();
            formData.append('send_message', true);
            formData.append('receiver_id', receiverId);
            formData.append('message_text', message);

            fetch('shelter_messages.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                input.value = '';
                const reloadUrl = petId ? `?chat=${receiverId}&pet_id=${petId}` : `?chat=${receiverId}`;
                window.location.href = 'shelter_messages.php' + reloadUrl;
            });
        }

        // Remove auto-refresh - it causes interruptions while typing
    </script>
</body>
</html>
