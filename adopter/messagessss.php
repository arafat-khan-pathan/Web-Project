<?php

session_start();
// Check if user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: ./login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Guest User';

// DB Connection
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP default
$db = "paws_hearts";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Check if user is logged in
// if (!isset($_SESSION['userId'])) {
//     header("Location: ./login__.php");
//     exit();
// }

// Logic for filtering
$species = isset($_GET['species']) ? $_GET['species'] : '';
if ($species != '') {
    $sql = "SELECT * FROM pets WHERE species = '". $conn->real_escape_string($species) ."' ORDER BY name ASC";
} else {
    $sql = "SELECT * FROM pets ORDER BY name ASC";
}

$result = $conn->query($sql);
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
            grid-template-columns: 350px 1fr;
            height: calc(100vh - 140px);
            margin: 20px 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .convos-sidebar {
            border-right: 1px solid #f1f5f9;
            display: flex;
            flex-direction: column;
            background: #fcfdfe;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .sidebar-header h2 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
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
        }

        .convo-item:hover {
            background: #f1f5f9;
        }

        .convo-item.active {
            background: #fff7ed;
            border-left: 4px solid var(--primary);
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .convo-info {
            flex: 1;
            min-width: 0;
        }

        .convo-name {
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            justify-content: space-between;
        }

        .convo-time {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .convo-msg {
            font-size: 0.85rem;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-view {
            display: flex;
            flex-direction: column;
            background: white;
            height: 100%;
            overflow: hidden;
        }

        .chat-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-shrink: 0;
        }

        .chat-body {
            flex: 1;
            padding: 1.5rem;
            overflow-y: auto;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .bubble {
            max-width: 70%;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            font-size: 0.95rem;
            position: relative;
        }

        .bubble.received {
            align-self: flex-start;
            background: white;
            border: 1px solid #e2e8f0;
            border-bottom-left-radius: 2px;
        }

        .bubble.sent {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 2px;
        }

        .timestamp {
            display: block;
            font-size: 0.65rem;
            margin-top: 0.4rem;
            opacity: 0.7;
        }

        .chat-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            gap: 0.8rem;
            flex-shrink: 0;
            background: white;
        }

        .chat-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
        }

        .btn-send {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .back-link {
            position: relative;
            top: 20px;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        @media (max-width: 768px) {
            .messaging-layout {
                grid-template-columns: 80px 1fr;
            }

            .convo-info,
            .sidebar-header h2 {
                display: none;
            }
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
                <a href="index.php" class=" hov">Browse Pets</a>
                <!-- <a href="dashboard__.php">Dashboard</a> -->
                <!-- <a href="shelter.html">Shelter</a> -->
                <a href="messages.php" class="active hov">Message</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="profile.php" id="userNameDisplay" class="hov ">User Name</a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="controls-flex">
            <a href="index.php" class="back-link">
                <i data-lucide="arrow-left" size="20"></i> Back to Browse
            </a>

        </div>
        <div class="messaging-layout">
            <aside class="convos-sidebar">
                <div class="sidebar-header">
                    <h2>Messages</h2>
                </div>
                <div class="convos-list" id="convosList">
                    <!-- Dynamic List -->
                </div>
            </aside>

            <section class="chat-view">
                <div class="chat-header" id="activeChatHeader">
                    <!-- Dynamic Header -->
                </div>
                <div class="chat-body" id="chatDisplay">
                    <!-- Dynamic Messages -->
                </div>
                <form class="chat-footer" onsubmit="handleSendMessage(event)">
                    <input type="text" id="messageInput" class="chat-input" placeholder="Type a message..." required
                        autocomplete="off">
                    <button type="submit" class="btn-send"><i data-lucide="send" size="18"></i></button>
                </form>
            </section>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <i data-lucide="paw-print"></i>
                        <span>Paws & Hearts</span>
                    </div>
                    <p>Connecting loving families with pets in need. Our mission is to ensure every animal finds their
                        forever home.</p>
                </div>
                <div>
                    <h4 class="footer-heading">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#">Browse All Pets</a></li>
                        <li><a href="#">Adoption Process</a></li>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Resources</h4>
                    <ul class="footer-links">
                        <li><a href="#">Pet Care Tips</a></li>
                        <li><a href="#">Vaccination Guide</a></li>
                        <li><a href="#">Success Stories</a></li>
                        <li><a href="#">Volunteer</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Connect With Us</h4>
                    <p style="margin-bottom: 1rem; font-size: 0.85rem;">Follow our social media for daily pet updates!
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-btn"><i data-lucide="facebook" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="instagram" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="twitter" size="18"></i></a>
                        <a href="#" class="social-btn"><i data-lucide="mail" size="18"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Paws & Hearts Adoption. All rights reserved. Made with ❤️ for animals.</p>
            </div>
        </div>
    </footer>

    <script>
        // Data structure for the demo
        const conversations = [
            {
                id: 1,
                name: "Happy Tails Shelter",
                type: "shelter",
                avatar: "https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=100",
                messages: [
                    { text: "Hello! We saw your application for Max.", time: "10:30 AM", sender: "other" },
                    { text: "He is a very energetic pup!", time: "10:31 AM", sender: "other" }
                ],
                reply: "Thank you for reaching out! We will check Max's schedule for a meetup."
            },
            {
                id: 2,
                name: "Sarah Miller (Owner)",
                type: "user",
                avatar: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100",
                messages: [
                    { text: "Hi, is Buddy friendly with cats?", time: "Yesterday", sender: "me" },
                    { text: "Yes, he grew up with two cats!", time: "Yesterday", sender: "other" }
                ],
                reply: "Buddy is very social. Would you like to see a video of him playing?"
            }
        ];

        let activeId = 1;

        function init() {
         document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "<?php echo htmlspecialchars($userName); ?>";
            renderSidebar();
            renderChat();
            lucide.createIcons();
        }



        

        function renderSidebar() {
            const list = document.getElementById('convosList');
            list.innerHTML = conversations.map(c => `
                <div class="convo-item ${c.id === activeId ? 'active' : ''}" onclick="switchChat(${c.id})">
                    <div class="avatar">
                        ${c.avatar ? `<img src="${c.avatar}">` : `<i data-lucide="${c.type === 'shelter' ? 'home' : 'user'}"></i>`}
                    </div>
                    <div class="convo-info">
                        <div class="convo-name">${c.name} <span class="convo-time">${c.messages[c.messages.length - 1].time}</span></div>
                        <div class="convo-msg">${c.messages[c.messages.length - 1].text}</div>
                    </div>
                </div>
            `).join('');
            lucide.createIcons();
        }

        function renderChat() {
            const chat = conversations.find(c => c.id === activeId);
            const header = document.getElementById('activeChatHeader');
            const display = document.getElementById('chatDisplay');

            header.innerHTML = `
                <div class="avatar">${chat.avatar ? `<img src="${chat.avatar}">` : `<i data-lucide="user"></i>`}</div>
                <div><strong>${chat.name}</strong><br><small style="color:#10b981">● Online</small></div>
            `;

            display.innerHTML = chat.messages.map(m => `
                <div class="bubble ${m.sender === 'me' ? 'sent' : 'received'}">
                    ${m.text}
                    <span class="timestamp">${m.time}</span>
                </div>
            `).join('');

            // Timeout to ensure DOM is rendered before scrolling
            setTimeout(() => {
                display.scrollTop = display.scrollHeight;
            }, 0);

            lucide.createIcons();
        }

        function switchChat(id) {
            activeId = id;
            renderSidebar();
            renderChat();
        }

        function handleSendMessage(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const text = input.value.trim();
            if (!text) return;

            const chat = conversations.find(c => c.id === activeId);
            const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            chat.messages.push({ text, time, sender: "me" });
            input.value = "";
            renderChat();
            renderSidebar();

            // Auto reply
            setTimeout(() => {
                chat.messages.push({ text: chat.reply, time: "Just now", sender: "other" });
                renderChat();
                renderSidebar();
            }, 1000);
        }

        function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }

        init();
    </script>
</body>

</html>