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
    <title>Apply to Adopt - Paws & Hearts</title>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- External CSS -->
    <link rel="stylesheet" href="../index.css">

    <style>
        .adoption-wrapper {
            padding: 3rem 0;
            max-width: 800px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 600;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }
          .back-link {
            position: relative;
            top: 10px;
        }

        .back-link:hover {
            transform: translateX(-5px);
        }

        .form-card {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: var(--shadow);
            border: 1px solid #e2e8f0;
        }

        .form-header {
            border-bottom: 2px solid #fff7ed;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-header h1 {
            color: #7c2d12;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: var(--text-muted);
        }

        .adoption-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        label {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        input, select, textarea {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .btn-apply {
            grid-column: span 2;
            background: var(--primary);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 0.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.2s;
        }

        .btn-apply:hover {
            background: var(--primary-dark);
        }

        @media (max-width: 600px) {
            .adoption-form {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
            .btn-apply {
                grid-column: span 1;
            }
        }
    </style>
    <link rel="stylesheet" href="../home.css">
    <link rel="stylesheet" href="../index.css">
</head>

<body>

     <nav class="navbar">
        <div class="container nav-flex">
            <a href="#" class="logo">
                <i data-lucide="paw-print"></i>
                <span>Paws & Hearts</span>
            </a>
            <div class="nav-links">
                <a href="index.php" class="active hov">Browse Pets</a>
                <!-- <a href="dashboard__.html">Dashboard</a> -->
                <!-- <a href="shelter.html">Shelter</a> -->
                <a href="messages.php">Message</a>
                <!-- <a href="guidelines__.html">Guidelines</a> -->
                <div class="user-info">
                    <a href="profile.php" id="userNameDisplay" class="hov "><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="logout()" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container adoption-wrapper">
        <a href="javascript:history.back()" class="back-link">
            <i data-lucide="arrow-left"></i> Back to Details
        </a>

        <div class="form-card">
            <div class="form-header">
                <h1>Adoption Application</h1>
                <p>Please provide your accurate information to proceed with the adoption process.</p>
            </div>

            <form id="adoptionForm" class="adoption-form" onsubmit="handleApply(event)">
                <div class="form-group">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName"  placeholder="Enter first name">
                </div>

                <div class="form-group">
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName"  placeholder="Enter last name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email"  placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label for="contactNumber">Contact Number</label>
                    <input type="tel" id="contactNumber"  placeholder="+880 1XXX-XXXXXX">
                </div>

                <div class="form-group">
                    <label for="age">Age</label>
                    <input type="number" id="age"  min="18" placeholder="Must be 18+">
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" >
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nid">NID Number</label>
                    <input type="text" id="nid"  placeholder="National ID Card Number">
                </div>

                <div class="form-group">
                    <label for="location">Location (City/Area)</label>
                    <input type="text" id="location"  placeholder="e.g. Uttara, Dhaka">
                </div>

                <div class="form-group">
                    <label for="homeDistrict">Home District</label>
                    <input type="text" id="homeDistrict"  placeholder="Your home district">
                </div>

                <div class="form-group">
                    <label for="presentAddress">Present Address</label>
                    <textarea id="presentAddress"  rows="2" placeholder="House, Road, Block..."></textarea>
                </div>

                <button type="submit" class="btn-apply">Submit Application</button>
            </form>
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
        document.addEventListener('DOMContentLoaded', () => {
            // document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "Guest User";
             document.getElementById("userNameDisplay").textContent = localStorage.getItem("userName") || "<?php echo htmlspecialchars($userName); ?>";
            lucide.createIcons();
        });

        function handleApply(event) {
            event.preventDefault();
            
            // Simple submission simulation
            const formData = {
                firstName: document.getElementById('firstName').value,
                lastName: document.getElementById('lastName').value,
                email: document.getElementById('email').value,
                nid: document.getElementById('nid').value
            };

            // In a real app, you'd send this to a database
            alert(`Application submitted successfully for ${formData.firstName}! We will contact you soon.`);
            window.location.href = "index.php";
        }

        function logout() {
            localStorage.clear();
            window.location.href = "../login__.php";
        }
    </script>
</body>

</html>