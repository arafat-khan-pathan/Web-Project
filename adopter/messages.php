<?php
/**
 * MESSAGES TABLE SETUP (Run this in phpMyAdmin SQL tab if not exists):
 * 
 * CREATE TABLE IF NOT EXISTS messages (
 *   id INT AUTO_INCREMENT PRIMARY KEY,
 *   sender_id INT NOT NULL,
 *   receiver_id INT NOT NULL,
 *   message_text TEXT NOT NULL,
 *   is_read BOOLEAN DEFAULT FALSE,
 *   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 *   FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
 *   FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
 *   INDEX idx_conversation (sender_id, receiver_id, created_at)
 * );
 */

session_start();

// 1. Check Login
if (!isset($_SESSION['userId'])) {
    header("Location: ../login__.php");
    exit();
}

$currentUserId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? 'User';

// 2. DB Connection
$host = "localhost";
$user = "root";
$pass = ""; 
$db = "paws_hearts";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
 * Logic to get List of Conversations
 * This finds all unique users the current user has exchanged messages with
 */
$convoQuery = "
    SELECT DISTINCT 
        u.id, 
        CONCAT(u.first_name, ' ', u.last_name) as userName,
        (SELECT message_text FROM messages 
         WHERE (sender_id = u.id AND receiver_id = $currentUserId) 
            OR (sender_id = $currentUserId AND receiver_id = u.id) 
         ORDER BY created_at DESC LIMIT 1) as last_msg,
        (SELECT created_at FROM messages 
         WHERE (sender_id = u.id AND receiver_id = $currentUserId) 
            OR (sender_id = $currentUserId AND receiver_id = u.id) 
         ORDER BY created_at DESC LIMIT 1) as last_time
    FROM users u
    JOIN messages m ON (u.id = m.sender_id OR u.id = m.receiver_id)
    WHERE (m.sender_id = $currentUserId OR m.receiver_id = $currentUserId)
    AND u.id != $currentUserId
    ORDER BY last_time DESC";

$conversations = $conn->query($convoQuery);

// DEBUG: Check for errors and data
if (!$conversations) {
    die("Query Error: " . $conn->error);
}

// Check if we need to auto-open a chat (from pet details page)
$autoOpenChat = isset($_GET['chat']) ? intval($_GET['chat']) : null;
$autoOpenName = '';

if ($autoOpenChat) {
    // Get the user name for auto-open
    $userQuery = $conn->query("SELECT CONCAT(first_name, ' ', last_name) as name FROM users WHERE id = $autoOpenChat");
    if ($userQuery && $userQuery->num_rows > 0) {
        $autoOpenName = $userQuery->fetch_assoc()['name'];
    }
}

// DEBUG: Display session info (remove after testing)
// echo "<!-- Debug: UserID=" . $currentUserId . ", Conversations=" . $conversations->num_rows . " -->";
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
        :root {
            --primary: #f97316;
            --primary-light: #fff7ed;
            --text-muted: #64748b;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .messaging-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 160px);
            margin: 20px 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .convos-sidebar { border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; background: #fcfdfe; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .convos-list { flex: 1; overflow-y: auto; }
        .convo-item { padding: 1rem 1.5rem; display: flex; align-items: center; gap: 1rem; cursor: pointer; transition: all 0.2s; border-bottom: 1px solid #f8fafc; }
        .convo-item:hover { background: #f1f5f9; }
        .convo-item.active { background: #fff7ed; border-left: 4px solid var(--primary); }
        .avatar { width: 45px; height: 45px; border-radius: 50%; background: #e2e8f0; overflow: hidden; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .convo-info { flex: 1; min-width: 0; }
        .convo-name { font-weight: 600; font-size: 0.95rem; display: flex; justify-content: space-between; }
        .convo-time { font-size: 0.7rem; color: var(--text-muted); }
        .convo-msg { font-size: 0.85rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .chat-view { display: flex; flex-direction: column; background: white; height: 100%; overflow: hidden; }
        .chat-header { padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 1rem; }
        .chat-body { flex: 1; padding: 1.5rem; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 0.8rem; }
        .bubble { max-width: 75%; padding: 0.8rem 1rem; border-radius: 15px; font-size: 0.95rem; line-height: 1.4; }
        .bubble.received { align-self: flex-start; background: white; border: 1px solid #e2e8f0; border-bottom-left-radius: 2px; }
        .bubble.sent { align-self: flex-end; background: var(--primary); color: white; border-bottom-right-radius: 2px; }
        .timestamp { display: block; font-size: 0.65rem; margin-top: 4px; opacity: 0.7; }
        .chat-footer { padding: 1rem; border-top: 1px solid #f1f5f9; display: flex; gap: 0.8rem; }
        .chat-input { flex: 1; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 25px; outline: none; }
        .btn-send { background: var(--primary); color: white; border: none; width: 45px; height: 45px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%; color: var(--text-muted); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.php" class="logo"><i data-lucide="paw-print"></i> <span>Paws & Hearts</span></a>
            <div class="nav-links">
                <a href="index.php">Browse Pets</a>
                <a href="messages.php" class="active">Messages</a>
                <div class="user-info">
                    <a href="profile.php" id="userNameDisplay"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="location.href='../logout.php'" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="messaging-layout">
            <aside class="convos-sidebar">
                <div class="sidebar-header"><h2>Messages</h2></div>
                <div class="convos-list">
                    <?php if ($conversations && $conversations->num_rows > 0): ?>
                        <?php while($row = $conversations->fetch_assoc()): ?>
                            <?php $isAutoOpen = ($autoOpenChat && $row['id'] == $autoOpenChat) ? 'active' : ''; ?>
                            <div class="convo-item <?php echo $isAutoOpen; ?>" onclick="loadChat(<?php echo $row['id']; ?>, '<?php echo addslashes($row['userName']); ?>')">
                                <div class="avatar">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="convo-info">
                                    <div class="convo-name">
                                        <?php echo htmlspecialchars($row['userName']); ?>
                                        <span class="convo-time"><?php echo $row['last_time'] ? date('H:i', strtotime($row['last_time'])) : ''; ?></span>
                                    </div>
                                    <div class="convo-msg"><?php echo htmlspecialchars($row['last_msg'] ?? 'No messages yet'); ?></div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: gray;">
                            No conversations yet.<br>
                            <small>Start chatting with shelter owners about their pets!</small>
                        </p>
                        
                        <?php if ($autoOpenChat && $autoOpenName): ?>
                            <!-- Show the new conversation even if no messages exist yet -->
                            <div class="convo-item active" onclick="loadChat(<?php echo $autoOpenChat; ?>, '<?php echo addslashes($autoOpenName); ?>')">
                                <div class="avatar">
                                    <i data-lucide="user"></i>
                                </div>
                                <div class="convo-info">
                                    <div class="convo-name">
                                        <?php echo htmlspecialchars($autoOpenName); ?>
                                        <span class="convo-time">Now</span>
                                    </div>
                                    <div class="convo-msg">Start a conversation...</div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="chat-view">
                <div id="chatWindow" style="display: none; height: 100%; flex-direction: column;">
                    <div class="chat-header" id="activeChatHeader"></div>
                    <div class="chat-body" id="chatDisplay"></div>
                    <form class="chat-footer" onsubmit="sendMessage(event)">
                        <input type="text" id="messageInput" class="chat-input" placeholder="Type a message..." required autocomplete="off">
                        <button type="submit" class="btn-send"><i data-lucide="send"></i></button>
                    </form>
                </div>
                <div id="noChatSelected" class="empty-state">
                    <i data-lucide="message-square" size="48"></i>
                    <p>Select a conversation to start chatting</p>
                </div>
            </section>
        </div>
    </main>

    <script>
        let currentReceiverId = null;
        let pollInterval = null;

        async function loadChat(userId, userName) {
            currentReceiverId = userId;
            document.getElementById('noChatSelected').style.display = 'none';
            document.getElementById('chatWindow').style.display = 'flex';
            
            // Update Header
            document.getElementById('activeChatHeader').innerHTML = `
                <div class="avatar"><i data-lucide="user"></i></div>
                <div><strong>${userName}</strong><br><small style="color:#10b981">Active Now</small></div>
            `;
            
            fetchMessages();
            lucide.createIcons();

            // Clear old interval and start new polling
            if(pollInterval) clearInterval(pollInterval);
            pollInterval = setInterval(fetchMessages, 3000); 
        }

        async function fetchMessages() {
            if (!currentReceiverId) return;
            try {
                const response = await fetch(`chat_handler.php?receiver_id=${currentReceiverId}`);
                const messages = await response.json();
                const display = document.getElementById('chatDisplay');
                
                const html = messages.map(m => `
                    <div class="bubble ${m.sender_id == <?php echo $currentUserId; ?> ? 'sent' : 'received'}">
                        ${m.message_text}
                        <span class="timestamp">${m.time}</span>
                    </div>
                `).join('');
                
                // Only update if content changed to prevent scroll jumping
                if (display.innerHTML !== html) {
                    display.innerHTML = html;
                    display.scrollTop = display.scrollHeight;
                }
            } catch (e) { console.error("Error fetching messages", e); }
        }

        async function sendMessage(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const text = input.value.trim();
            if (!text || !currentReceiverId) return;

            const formData = new FormData();
            formData.append('receiver_id', currentReceiverId);
            formData.append('message', text);

            input.value = ""; // Clear input immediately

            try {
                await fetch('chat_handler.php', { method: 'POST', body: formData });
                fetchMessages(); // Refresh chat
            } catch (e) { console.error("Error sending message", e); }
        }

        lucide.createIcons();
        
        // Auto-open chat if coming from pet details page
        <?php if ($autoOpenChat && $autoOpenName): ?>
        window.addEventListener('DOMContentLoaded', function() {
            loadChat(<?php echo $autoOpenChat; ?>, '<?php echo addslashes($autoOpenName); ?>');
        });
        <?php endif; ?>
    </script>
</body>
</html>