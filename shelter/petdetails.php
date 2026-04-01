<?php

session_start();
// Check if user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: ../login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Shelter User';
$currentUserId = $_SESSION['userId'] ?? null;

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "paws_hearts";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pet = null;
$petOwner = null;
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    // Get pet details along with owner (shelter) information
    $sql = "SELECT p.*, u.id as owner_id, u.first_name, u.last_name, u.email 
            FROM pets p 
            LEFT JOIN users u ON p.user_id = u.id 
            WHERE p.id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
        $petOwner = [
            'id' => $pet['owner_id'],
            'name' => $pet['first_name'] . ' ' . $pet['last_name'],
            'email' => $pet['email']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Details - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        .details-wrapper { padding: 2rem 0 3rem; }
        .back-link { display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; color: #64748b; font-weight: 600; margin-bottom: 1.5rem; transition: all 0.2s; }
        .back-link:hover { transform: translateX(-5px); color: #f97316; }
        .details-card { background: white; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; }
        @media (min-width: 900px) { .details-card { flex-direction: row; min-height: 500px; } }
        .pet-gallery { flex: 0 0 450px; background: #f8fafc; position: relative; min-height: 300px; }
        .pet-gallery img { width: 100%; height: 100%; object-fit: cover; }
        .pet-info { flex: 1; padding: 2.5rem; display: flex; flex-direction: column; gap: 2rem; }
        .info-header h1 { font-size: 2.5rem; color: #7c2d12; margin-bottom: 0.5rem; }
        .details-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; }
        .detail-item { background: #f8fafc; padding: 0.8rem; border-radius: 0.6rem; border: 1px solid #f1f5f9; }
        .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; margin-bottom: 0.3rem; }
        .detail-value { font-weight: 700; color: #1e293b; }
        .info-section h3 { font-size: 1.1rem; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem; }
        .info-section p { color: #475569; line-height: 1.7; }
        .error-message { text-align: center; padding: 5rem; }
        .action-area { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn-large { flex: 1; padding: 1rem 1.5rem; border-radius: 0.5rem; font-weight: 700; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.6rem; border: none; cursor: pointer; }
        .btn-large:hover { transform: translateY(-2px); filter: brightness(1.1); }
        .btn-view-messages { background: #0ea5e9; color: white; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2); }
        .btn-back { background: #f97316; color: white; box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
        a { text-decoration: none; }
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
                <a href="index.php" class="active hov">Browse Pets</a>
                <a href="shelter_messages.php">Message</a>
                <div class="user-info">
                    <a href="shelter_profile.php" id="userNameDisplay" class="hov"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container details-wrapper">
        <a href="index.php" class="back-link">
            <i data-lucide="arrow-left"></i>
            Back to Browse
        </a>

        <?php if ($pet): ?>
            <div class="details-card">
                <div class="pet-gallery">
                    <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                </div>
                <div class="pet-info">
                    <div class="info-header">
                        <h1><?php echo htmlspecialchars($pet['name']); ?></h1>
                        <p style="color: #64748b; font-size: 1rem;">
                            <?php echo $pet['gender'] == 'Male' ? '♂️ Male' : '♀️ Female'; ?> • 
                            <?php echo htmlspecialchars($pet['age']); ?> old
                        </p>
                    </div>

                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-label">Species</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pet['species']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Breed</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pet['breed']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Size</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pet['size']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Health</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pet['health_status'] ?? 'Good'); ?></div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3>
                            <i data-lucide="info" size="20"></i>
                            About <?php echo htmlspecialchars($pet['name']); ?>
                        </h3>
                        <p><?php echo htmlspecialchars($pet['description'] ?? 'No description available'); ?></p>
                    </div>

                    <div class="action-area">
                        <?php 
                        // Check if current user owns this pet
                        $isOwner = ($currentUserId && $petOwner && $petOwner['id'] == $currentUserId);
                        ?>
                        
                        <?php if ($isOwner): ?>
                            <!-- If shelter owns this pet, show view messages -->
                            <a href="shelter_messages.php?pet_id=<?php echo $pet['id']; ?>" class="btn-large btn-view-messages">
                                <i data-lucide="message-circle"></i>
                                View Messages About This Pet
                            </a>
                            <a href="shelter_profile.php" class="btn-large btn-back">
                                <i data-lucide="user"></i>
                                My Profile
                            </a>
                        <?php else: ?>
                            <!-- If shelter doesn't own this pet, show message owner button -->
                            <a href="shelter_messages.php?chat=<?php echo $petOwner['id']; ?>" class="btn-large btn-view-messages">
                                <i data-lucide="message-circle"></i>
                                Message Shelter (<?php echo htmlspecialchars($petOwner['name']); ?>)
                            </a>
                            <a href="index.php" class="btn-large btn-back">
                                <i data-lucide="arrow-left"></i>
                                Back to Browse
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <i data-lucide="alert-circle" size="64" style="color: #cbd5e1; margin-bottom: 1rem;"></i>
                <h2>Pet not found</h2>
                <p>The pet you're looking for doesn't exist or you don't have permission to view it.</p>
                <a href="index.php" class="btn-back" style="display: inline-block; margin-top: 2rem; padding: 0.8rem 2rem;">Go Back Home</a>
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="logo">
                        <i data-lucide="paw-print"></i>
                        <span>Paws & Hearts</span>
                    </div>
                    <p>Connecting loving families with pets in need. Our mission is to ensure every animal finds their forever home.</p>
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
                    <p style="margin-bottom: 1rem; font-size: 0.85rem;">Follow our social media for daily pet updates!</p>
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
        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("userNameDisplay").textContent = "<?php echo htmlspecialchars($userName); ?>";
            lucide.createIcons();
        });

        function logout() {
            window.location.href = "../logout.php";
        }
    </script>
</body>

</html>
