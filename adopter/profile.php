<?php

session_start();
// Check if user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: ./login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Guest User';
$email = $_SESSION['email']; //?? 'guest@example.com';
$userId = $_SESSION['userId'] ?? null;

if (!$userId) {
    header("Location: ../login__.php");
    exit();
}

// DB Connection
$host = "localhost";
$user = "root";
$pass = ""; // XAMPP default
$db = "paws_hearts";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create adoptions table if it doesn't exist
$createAdoptionsTable = "CREATE TABLE IF NOT EXISTS adoptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    adopter_id INT NOT NULL,
    adoption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (adopter_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_adoption (pet_id, adopter_id)
)";
$conn->query($createAdoptionsTable);

// Get pets adopted by current adopter (from adoptions table)
$sql = "SELECT p.*, a.adoption_date, a.status,
        CONCAT(u.first_name, ' ', u.last_name) as shelter_name
        FROM pets p 
        INNER JOIN adoptions a ON p.id = a.pet_id 
        LEFT JOIN users u ON p.user_id = u.id
        WHERE a.adopter_id = $userId 
        AND a.status IN ('approved', 'completed')
        ORDER BY a.adoption_date DESC";
$result = $conn->query($sql);

// Get adoption statistics
$statsQuery = "SELECT 
    COUNT(*) as total_adopted,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_requests
    FROM adoptions WHERE adopter_id = $userId";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
$totalAdopted = $stats['total_adopted'] ?? 0;
$pendingRequests = $stats['pending_requests'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - PetAdopt</title>
   
    

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="style.css">
        <link rel="stylesheet" href="../index.css">

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
                <a href="messages.php">Message</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="profile.php" id="userNameDisplay" class="hov active ">
                        <?php echo htmlspecialchars($userName); ?>
                    </a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Breadcrumb/Back -->
        <!-- <a href="#" class="back-link">
            <svg style="width:20px; height:20px; margin-right:8px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Home
        </a> -->

        <div class="profile-grid">
            <!-- LEFT COLUMN: Profile Summary -->
            <div class="sidebar">
                <div class="card">
                    <div class="avatar-container">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="user-name">
                        <?php echo htmlspecialchars($userName); ?>
                    </h2>
                    <p class="user-title">Pet Enthusiast</p>
                    <div class="center">
                        <span class="badge">Verified User</span>
                    </div>

                    <ul class="info-list">
                        <li class="info-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            <?php echo htmlspecialchars($email); ?>
                        </li>
                        <li class="info-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Dhaka, Bangladesh
                        </li>
                    </ul>
                </div>

                <div class="card">
                    <h3 style="font-size: 1rem; margin-bottom: 12px;">About</h3>
                    <p style="font-size: 0.85rem; color: #6b7280;">Animal lover and advocate for pet adoption. Looking
                        to provide a forever home for furry friends in need.</p>
                </div>


                <div class="stats-row">

                    <div class="stat-box stat-fav">
                        <div class="stat-num" style="color: #2563eb;"><?php echo $totalAdopted; ?></div>
                        <div class="stat-label" style="color: #1e40af;">Pets Adopted</div>
                    </div>
                    <div class="stat-box stat-adopted">
                        <div class="stat-num" style="color: #e11d48;"><?php echo $pendingRequests; ?></div>
                        <div class="stat-label" style="color: #9f1239;">Pending Requests</div>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Content -->
            <div class="main-content">
                <div class="card">
                    <div class="section-header">
                        <div class="icon-box">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700;">Adopted Pets</h3>
                            <p style="font-size: 0.875rem; color: #6b7280;">Pets adopted by <?php echo htmlspecialchars($userName); ?> from shelters.</p>
                        </div>
                    </div>

                    <div class="pet-grid" id="pets-container">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($pet = $result->fetch_assoc()): ?>
                                <!-- Pet Card -->
                                <div class="pet-card" onclick="location.href='petdetails.php?id=<?php echo $pet['id']; ?>'">
                                    <!-- 183 1st urrl = url -->
                                    <img src="<?php echo htmlspecialchars($pet['image_urrl'] ?? $pet['image_urrl'] ?? 'https://via.placeholder.com/400'); ?>" 
                                         alt="<?php echo htmlspecialchars($pet['name']); ?>" 
                                         class="pet-img">
                                    <div class="pet-info">
                                        <div class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></div>
                                        <div class="pet-details"><?php echo htmlspecialchars($pet['breed'] ?? 'Unknown'); ?> • <?php echo htmlspecialchars($pet['age'] ?? 'Unknown'); ?></div>
                                        <div class="home-date">
                                            <svg viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                            </svg>
                                            Adopted <?php echo date('M Y', strtotime($pet['adoption_date'])); ?>
                                        </div>
                                        <?php if (!empty($pet['shelter_name'])): ?>
                                            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">
                                                From: <?php echo htmlspecialchars($pet['shelter_name']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #6b7280;">
                                <svg style="width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <h4 style="margin-bottom: 0.5rem;">No Adopted Pets Yet</h4>
                                <p>You haven't adopted any pets yet. <a href="index.php" style="color: #f97316; font-weight: 600;">Browse available pets from shelters</a> to find your perfect companion!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>




                <div class="card carrrrd">
                    <div class="section-header">
                        <div class="icon-box" style="background: #fef3c7;">
                            <i data-lucide="history" style="color: #d97706;"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700;">Adoption History</h3>
                            <p style="font-size: 0.875rem; color: #6b7280;">A timeline of your journey with Paws &
                                Hearts.</p>
                        </div>
                    </div>

                    <div class="history-timeline">
                        <!-- History Item 1 -->
                        <div class="history-item">
                            <div class="history-marker completed"></div>
                            <div class="history-content">
                                <div class="history-header">
                                    <span class="history-date">March 12, 2024</span>
                                    <span class="status-pill status-completed">Finalized</span>
                                </div>
                                <div class="history-body">
                                    <div
                                        style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="ghost" size="18" color="#9ca3af"></i>
                                    </div>
                                    <div>
                                        <h4>Luna - Siamese Cat</h4>
                                        <p>Adoption process completed. Luna has successfully transitioned to her forever
                                            home.</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- History Item 2 -->
                        <div class="history-item">
                            <div class="history-marker completed"></div>
                            <div class="history-content">
                                <div class="history-header">
                                    <span class="history-date">January 20, 2024</span>
                                    <span class="status-pill status-completed">Finalized</span>
                                </div>
                                <div class="history-body">
                                    <div
                                        style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="ghost" size="18" color="#9ca3af"></i>
                                    </div>
                                    <div>
                                        <h4>Buddy - Golden Retriever</h4>
                                        <p>Adoption process completed. Welcome to the family, Buddy!</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- History Item 3 -->
                        <div class="history-item">
                            <div class="history-marker archived"></div>
                            <div class="history-content">
                                <div class="history-header">
                                    <span class="history-date">November 05, 2023</span>
                                    <span class="status-pill status-archived">Withdrawn</span>
                                </div>
                                <div class="history-body" style="opacity: 0.7;">
                                    <div
                                        style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i data-lucide="ghost" size="18" color="#9ca3af"></i>
                                    </div>
                                    <div>
                                        <h4>Charlie - Beagle</h4>
                                        <p>Application withdrawn by user due to travel schedule changes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- NEW SECTION: Adoption Approvals -->
                <div class="card carrrrd">
                    <div class="section-header">
                        <div class="icon-box" style="background: #ecfdf5;">
                            <i data-lucide="file-check" style="color: #10b981;"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 700;">Adoption Approvals</h3>
                            <p style="font-size: 0.875rem; color: #6b7280;">View and print your official adoption
                                agreements.</p>
                        </div>
                    </div>

                    <div class="approval-list">
                        <!-- Approval Item 1 -->
                        <div class="approval-item">
                            <div class="approval-info">
                                <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=100" alt="Buddy"
                                    class="approval-pet-thumb">
                                <div class="approval-text">
                                    <h4>Buddy - Golden Retriever</h4>
                                    <p>Approved on Jan 15, 2024</p>
                                </div>
                            </div>
                            <div class="approval-actions">
                                <!-- <a href="#" class="btn-sm btn-view">
                                    <i data-lucide="eye" size="16"></i> View Agreement
                                </a> -->
                                <button onclick="window.print()" class="btn-sm btn-print">
                                    <i data-lucide="printer" size="16"></i> Print
                                </button>
                            </div>
                        </div>

                        <!-- Approval Item 2 -->
                        <div class="approval-item">
                            <div class="approval-info">
                                <img src="https://images.unsplash.com/photo-1513245543132-31f507417b26?w=100" alt="Luna"
                                    class="approval-pet-thumb">
                                <div class="approval-text">
                                    <h4>Luna - Siamese Cat</h4>
                                    <p>Approved on Mar 10, 2024</p>
                                </div>
                            </div>
                            <div class="approval-actions">
                                <!-- <a href="#" class="btn-sm btn-view">
                                    <i data-lucide="eye" size="16"></i> View Agreement
                                </a> -->
                                <button onclick="window.print()" class="btn-sm btn-print">
                                    <i data-lucide="printer" size="16"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

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
        lucide.createIcons();
        // Simple script to handle dynamic interactions if needed
        document.addEventListener('DOMContentLoaded', () => {
            console.log("Profile page loaded successfully.");

            // Example: click interaction for pet cards
            const petCards = document.querySelectorAll('.pet-card');
            petCards.forEach(card => {
                card.addEventListener('click', () => {
                    const name = card.querySelector('.pet-name').textContent;
                    console.log(`Viewing details for ${name}`);
                });
            });
        });
        function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }

    </script>
</body>

</html>