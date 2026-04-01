<?php
session_start();

// Check if user is logged in and is a shelter
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Shelter User';
$userId = $_SESSION['userId'] ?? null;
$userEmail = $_SESSION['email'] ?? '';

if (!$userId) {
    header("Location: ../login__.php");
    exit();
}

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "paws_hearts";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create adoption_requests table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS adoption_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    user_id INT NOT NULL,
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (pet_id) REFERENCES pets(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$conn->query($createTableSQL);

// Get owned pets (currently listed)
$ownedSql = "SELECT * FROM pets WHERE user_id = $userId ORDER BY id DESC";
$ownedResult = $conn->query($ownedSql);

// Get adopted pets (pets that were yours but got adopted)
$adoptedSql = "SELECT p.*, a.adoption_date, a.adopter_id, 
               CONCAT(u.first_name, ' ', u.last_name) as adopter_name
               FROM pets p 
               INNER JOIN adoptions a ON p.id = a.pet_id 
               INNER JOIN users u ON a.adopter_id = u.id
               WHERE p.user_id = $userId 
               ORDER BY a.adoption_date DESC";
$adoptedResult = $conn->query($adoptedSql);

// Get adoption requests for shelter's pets
$requestsSql = "SELECT ar.*, p.name as pet_name, p.image_url, 
                CONCAT(u.first_name, ' ', u.last_name) as adopter_name, u.id as adopter_id
                FROM adoption_requests ar
                INNER JOIN pets p ON ar.pet_id = p.id
                INNER JOIN users u ON ar.user_id = u.id
                WHERE p.user_id = $userId
                ORDER BY ar.request_date DESC";
$requestsResult = $conn->query($requestsSql);

// Get shelter user details
$userSql = "SELECT * FROM users WHERE id = $userId";
$userResult = $conn->query($userSql);
$userInfo = $userResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        .profile-header {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .profile-info {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .profile-details h1 {
            margin: 0 0 0.5rem 0;
            color: #1f2937;
        }
        .profile-details p {
            margin: 0.25rem 0;
            color: #64748b;
        }
        .section {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f5f9;
        }
        .section-header h2 {
            margin: 0;
            color: #f97316;
        }
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }
        .pet-card {
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pet-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        .pet-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .pet-info {
            padding: 1rem;
        }
        .pet-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .pet-meta {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }
        .pet-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            background: #fff7ed;
            color: #f97316;
            border-radius: 0.375rem;
            font-size: 0.8rem;
        }
        .adopted-info {
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.75rem;
            font-size: 0.875rem;
        }
        .adopted-info strong {
            display: block;
            margin-bottom: 0.25rem;
        }
        .btn-message {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.5rem;
            background: #f97316;
            color: white;
            text-align: center;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            justify-content: center;
        }
        .btn-message:hover {
            background: #ea580c;
        }
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #94a3b8;
        }
        .empty-state i {
            color: #cbd5e1;
            margin-bottom: 0.5rem;
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
                <a href="index.php">Browse Pets</a>
                <a href="shelter_messages.php">Messages</a>
                <div class="user-info">
                    <a href="shelter_profile.php" id="userNameDisplay" class="hov"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="location.href='../logout.php'" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="profile-header">
            <div class="profile-info">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($userName, 0, 1)); ?>
                </div>
                <div class="profile-details">
                    <h1><?php echo htmlspecialchars($userName); ?></h1>
                    <p><i data-lucide="mail" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> <?php echo htmlspecialchars($userEmail); ?></p>
                    <p><i data-lucide="shield" style="width: 16px; height: 16px; display: inline; vertical-align: middle;"></i> Shelter Account</p>
                </div>
            </div>
        </div>

        <!-- Currently Listed Pets -->
        <div class="section">
            <div class="section-header">
                <h2>My Listed Pets (<?php echo $ownedResult->num_rows; ?>)</h2>
                <a href="add_pet_form.php" style="color: #f97316; text-decoration: none; font-weight: 600;">
                    <i data-lucide="plus-circle" style="width: 20px; height: 20px; display: inline; vertical-align: middle;"></i>
                    Add Pet
                </a>
            </div>
            
            <?php if ($ownedResult && $ownedResult->num_rows > 0): ?>
                <div class="pets-grid">
                    <?php while ($pet = $ownedResult->fetch_assoc()): ?>
                        <div class="pet-card">
                            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
                            <div class="pet-info">
                                <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                                <div class="pet-meta">
                                    <span class="pet-badge">
                                        <i data-lucide="calendar" style="width: 12px; height: 12px;"></i>
                                        <?php echo htmlspecialchars($pet['age']); ?>
                                    </span>
                                    <span class="pet-badge">
                                        <i data-lucide="tag" style="width: 12px; height: 12px;"></i>
                                        <?php echo htmlspecialchars($pet['breed']); ?>
                                    </span>
                                </div>
                                <a href="petdetails.php?id=<?php echo $pet['id']; ?>" style="color: #f97316; text-decoration: none; font-size: 0.875rem;">View Details →</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="inbox" style="width: 48px; height: 48px;"></i>
                    <p>No pets listed yet. Add your first pet!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Adopted Pets -->
        <div class="section">
            <div class="section-header">
                <h2>Successfully Adopted Pets (<?php echo $adoptedResult->num_rows; ?>)</h2>
            </div>
            
            <?php if ($adoptedResult && $adoptedResult->num_rows > 0): ?>
                <div class="pets-grid">
                    <?php while ($pet = $adoptedResult->fetch_assoc()): ?>
                        <div class="pet-card">
                            <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
                            <div class="pet-info">
                                <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                                <div class="pet-meta">
                                    <span class="pet-badge">
                                        <i data-lucide="calendar" style="width: 12px; height: 12px;"></i>
                                        <?php echo htmlspecialchars($pet['age']); ?>
                                    </span>
                                    <span class="pet-badge">
                                        <i data-lucide="tag" style="width: 12px; height: 12px;"></i>
                                        <?php echo htmlspecialchars($pet['breed']); ?>
                                    </span>
                                </div>
                                <div class="adopted-info">
                                    <strong>✓ Adopted!</strong>
                                    <span>By <?php echo htmlspecialchars($pet['adopter_name']); ?></span><br>
                                    <small>On <?php echo date('M d, Y', strtotime($pet['adoption_date'])); ?></small>
                                </div>
                                <a href="shelter_messages.php?chat=<?php echo $pet['adopter_id']; ?>" class="btn-message">
                                    <i data-lucide="message-circle" style="width: 18px; height: 18px;"></i>
                                    Message Adopter
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="heart" style="width: 48px; height: 48px;"></i>
                    <p>No pets adopted yet. Keep listing pets to find them homes!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Adoption Requests -->
        <div class="section">
            <div class="section-header">
                <h2>Adoption Requests (<?php echo $requestsResult->num_rows; ?>)</h2>
            </div>
            
            <?php if ($requestsResult && $requestsResult->num_rows > 0): ?>
                <div class="pets-grid">
                    <?php while ($request = $requestsResult->fetch_assoc()): ?>
                        <div class="pet-card">
                            <img src="<?php echo htmlspecialchars($request['image_url']); ?>" alt="<?php echo htmlspecialchars($request['pet_name']); ?>" class="pet-image">
                            <div class="pet-info">
                                <h3 class="pet-name"><?php echo htmlspecialchars($request['pet_name']); ?></h3>
                                <div class="adopted-info">
                                    <strong>📋 Adoption Request</strong>
                                    <span>From: <?php echo htmlspecialchars($request['adopter_name']); ?></span><br>
                                    <small>Requested: <?php echo date('M d, Y', strtotime($request['request_date'])); ?></small><br>
                                    <small style="color: <?php echo $request['status'] == 'pending' ? '#f97316' : ($request['status'] == 'approved' ? '#10b981' : '#ef4444'); ?>">
                                        Status: <?php echo ucfirst($request['status']); ?>
                                    </small>
                                </div>
                                <a href="shelter_messages.php?chat=<?php echo $request['adopter_id']; ?>" class="btn-message">
                                    <i data-lucide="message-circle" style="width: 18px; height: 18px;"></i>
                                    Message Requester
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="inbox" style="width: 48px; height: 48px;"></i>
                    <p>No adoption requests yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
