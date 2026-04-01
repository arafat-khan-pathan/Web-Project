<?php

session_start();
// Check if user is logged in
if (!isset($_SESSION['userName'])) {
    header("Location: ./login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Guest User';
$currentUserId = $_SESSION['userId'] ?? null;


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

// Logic for filtering - show all pets from shelters
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
    <title>Home - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        a{
            text-decoration: none;
        }
    .btn-adopt{
        text-decoration: none;
        cursor: pointer;
        text-align: center;
    }
    </style>
</head>
<body>
     <nav class="navbar">
        <div class="container nav-flex">
            <a href="index.html" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.php" class="active hov">Browse Pets</a>
                <!-- <a href="dashboard__.php">Dashboard</a> -->
                <!-- <a href="shelter.html">Shelter</a> -->
                <a href="messages.php">Message</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="profile.php" id="userNameDisplay" class="hov ">User Name</a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>


    <header class="hero">
        <div class="container">
            <h1>Find Your New Best Friend</h1>
            <p>Ready to meet your perfect companion?</p>
            <div class="search-box">
                <input type="text" id="petSearch" placeholder="Breed or species..." oninput="filterPets()">
                <button class="btn-primary" onclick="filterPets()">Search</button>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="filter-section">
            <button class="filter-btn active" onclick="setFilter('all', this)">All</button>
            <button class="filter-btn" onclick="setFilter('dog', this)">Dogs</button>
            <button class="filter-btn" onclick="setFilter('cat', this)">Cats</button>
            <button class="filter-btn" onclick="setFilter('rabbit', this)">Rabbits</button>
        </section>

        <div class="pet-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($pet = $result->fetch_assoc()): ?>
                    <div class="pet-card" data-species="<?php echo htmlspecialchars(strtolower($pet['species'])); ?>">
                        <div class="image-wrapper">
                            <!-- Ensure your DB column name is image_url -->
                            <img src="<?php echo htmlspecialchars($pet['image_urrl'] ?? $pet['image_urrl']); ?>" alt="Pet Image">
                            <div class="gender-icon"><?php echo ($pet['gender'] ?? 'Male') == 'Male' ? '♂️' : '♀️'; ?></div>
                        </div>
                        <div class="card-content">
                            <div class="card-header-flex">
                                <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                                <span class="pet-age"><?php echo htmlspecialchars($pet['age']); ?></span>
                            </div>
                            <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></p>
                            <div class="card-tags">
                                <span class="tag"><?php echo htmlspecialchars($pet['size']); ?></span>
                                <span class="tag"><?php echo htmlspecialchars($pet['species']); ?></span>
                            </div>
                            <!-- LINK TO DETAILS PAGE WITH ID -->
                            <a href="petdetails.php?id=<?php echo $pet['id']; ?>" class="btn-adopt">Meet <?php echo htmlspecialchars($pet['name']); ?></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pets found.</p>
            <?php endif; ?>
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
        let currentFilter = "all";

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "<?php echo htmlspecialchars($userName); ?>";
            lucide.createIcons();
        });

        function setFilter(type, btn) {
            currentFilter = type.toLowerCase();
            document.querySelectorAll(".filter-btn").forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            filterPets();
        }

        function filterPets() {
            const search = document.getElementById("petSearch").value.toLowerCase();
            document.querySelectorAll(".pet-card").forEach(card => {
                const species = card.dataset.species;
                const text = card.innerText.toLowerCase();
                const matchSpecies = currentFilter === "all" || species === currentFilter;
                const matchSearch = text.includes(search);
                card.style.display = matchSpecies && matchSearch ? "block" : "none";
            });
        }

        function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }
    </script>
</body>
</html>