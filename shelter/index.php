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
$pass = ""; // XAMPP default
$db = "paws_hearts";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Logic for filtering - show all pets EXCEPT current shelter's pets
$species = isset($_GET['species']) ? $_GET['species'] : '';
if ($species != '') {
    $sql = "SELECT * FROM pets WHERE user_id != $currentUserId AND species = '". $conn->real_escape_string($species) ."' ORDER BY name ASC";
} else {
    $sql = "SELECT * FROM pets WHERE user_id != $currentUserId ORDER BY name ASC";
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
    .add-btn {
        border: none;
        outline: none;
        cursor: pointer;
        background-color: #f97316;
        color: white;
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background-color 0.2s, transform 0.1s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        float: right;
        margin-top: -50px;
        margin-bottom: 20px;
    }

    .add-btn:hover {
        background-color: #ea580c;
    }

    .add-btn:active {
        transform: scale(0.98);
    }
    .pet-grid{
        clear: both;
        margin-top: 0px;
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
                <a href="shelter_messages.php">Message</a>
                <div class="user-info">
                    <a href="shelter_profile.php" id="userNameDisplay" class="hov"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>


    <header class="hero">
        <div class="container">
            <h1>Browse Available Pets</h1>
            <p>Discover pets from other shelters</p>
            <div class="search-box">
                <input type="text" id="petSearch" placeholder="Pet name or breed..." oninput="filterPets()">
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

        <a href="add_pet_form.php" class="add-btn" id="addBtn">
            <span style="font-size: 20px;">+</span>
            <span>List New Pet</span>
        </a>

        <div class="pet-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($pet = $result->fetch_assoc()): ?>
                    <div class="pet-card" data-species="<?php echo htmlspecialchars(strtolower($pet['species'])); ?>">
                        <div class="image-wrapper">
                            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="Pet Image">
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
                            <a href="petdetails.php?id=<?php echo $pet['id']; ?>" class="btn-adopt">View <?php echo htmlspecialchars($pet['name']); ?></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No pets listed yet. <a href="add_pet_form.php">Add your first pet!</a></p>
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
            document.getElementById("userNameDisplay").textContent =
                "<?php echo htmlspecialchars($userName); ?>";
            lucide.createIcons();
        });

        function setFilter(type, btn) {
            currentFilter = type;
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
            window.location.href = "../logout.php";
        }

    </script>
</body>

</html>