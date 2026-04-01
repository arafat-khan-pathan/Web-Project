<?php
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
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    $sql = "SELECT * FROM pets WHERE id = '$id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
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
        .btn-adopt-now { background: #f97316; color: white; padding: 1rem 2rem; border-radius: 0.5rem; font-weight: 700; text-align: center; display: block; margin-top: 2rem; }
        a{
            text-decoration: none;
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
                <a href="index.php" class="active hov">Browse Pets</a>
                <a href="guidelines__.php">Guidelines</a>
                <div class="user-info">
                    <span id="userNameDisplay" class="hov">Guest User</span>
                    <button onclick="logout()" class="btn-outline">Login</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container details-wrapper">
        <?php if ($pet): ?>
            <a href="index.php" class="back-link">
                <i data-lucide="arrow-left"></i> Back to Browse
            </a>

            <div class="details-card">
                <div class="pet-gallery">
                    <img src="<?php echo htmlspecialchars($pet['image_urrl'] ?? $pet['image_urrl']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                </div>
                
                <div class="pet-info">
                    <div class="info-header">
                        <h1><?php echo htmlspecialchars($pet['name']); ?></h1>
                        <div class="details-grid">
                            <div class="detail-item"><div class="detail-label">Species</div><div class="detail-value"><?php echo htmlspecialchars($pet['species']); ?></div></div>
                            <div class="detail-item"><div class="detail-label">Breed</div><div class="detail-value"><?php echo htmlspecialchars($pet['breed']); ?></div></div>
                            <div class="detail-item"><div class="detail-label">Size</div><div class="detail-value"><?php echo htmlspecialchars($pet['size']); ?></div></div>
                            <div class="detail-item"><div class="detail-label">Gender</div><div class="detail-value"><?php echo htmlspecialchars($pet['gender']); ?></div></div>
                            <div class="detail-item"><div class="detail-label">Age</div><div class="detail-value"><?php echo htmlspecialchars($pet['age']); ?></div></div>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3><i data-lucide="info"></i> About <?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($pet['description'] ?? "No description available for this friendly pet yet.")); ?></p>
                    </div>

                    <div class="info-section">
                        <h3><i data-lucide="heart"></i> Health & Status</h3>
                        <p><strong>Health Condition:</strong> <?php echo htmlspecialchars($pet['health_status'] ?? 'Good'); ?></p>
                    </div>

                    <!-- <a href="apply.php?id=<?php echo $pet['id']; ?>" class="btn-adopt-now">Apply to Adopt <?php echo htmlspecialchars($pet['name']); ?></a> -->
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <h2>Pet Not Found</h2>
                <p>The pet you are looking for doesn't exist or has been adopted.</p>
                <br>
                <a href="index.php" class="btn-primary">Return Home</a>
            </div>
        <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "Guest User";
            lucide.createIcons();
        });

         function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }
    </script>
</body>
</html>


<!-- 

598 no line  
<div class="medical-update-form">
    <h4>Update Medical Record</h4>
    <div class="form-group-flex">
        <input type="date" id="newLogDate" value="${new Date().toISOString().split('T')[0]}">
        <input type="text" id="newLogNote" placeholder="Enter health update or clinical note...">
        <button class="btn-add-log" onclick="addMedicalLog('${petId}')">Update</button>
    </div>
</div> -->