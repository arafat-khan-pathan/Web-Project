<?php
session_start();

// Check if user is logged in and is a shelter
if (!isset($_SESSION['userName']) || $_SESSION['role'] !== 'shelter') {
    header("Location: ../login__.php");
    exit();
}

$userName = $_SESSION['userName'] ?? 'Shelter User';
$userId = $_SESSION['userId'] ?? null;

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

// Get filter
$speciesFilter = $_GET['species'] ?? 'all';

// Build query
$sql = "SELECT * FROM pets WHERE user_id = $userId";
if ($speciesFilter !== 'all') {
    $sql .= " AND species = '" . $conn->real_escape_string($speciesFilter) . "'";
}
$sql .= " ORDER BY id DESC";

$result = $conn->query($sql);

// Get total count
$countSql = "SELECT COUNT(*) as total FROM pets WHERE user_id = $userId";
$countResult = $conn->query($countSql);
$totalPets = $countResult->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Pets - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        .stats-card {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-info h2 {
            margin: 0 0 0.5rem 0;
            font-size: 2.5rem;
        }
        .stats-info p {
            margin: 0;
            opacity: 0.9;
        }
        .btn-add {
            background: white;
            color: #f97316;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-add:hover {
            background: #fff7ed;
        }
        .filters {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #cbd5e1;
            background: white;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
            text-decoration: none;
            color: #64748b;
        }
        .filter-btn.active {
            background: #f97316;
            color: white;
            border-color: #f97316;
        }
        .filter-btn:hover {
            border-color: #f97316;
        }
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .pet-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            transition: transform 0.2s;
        }
        .pet-card:hover {
            transform: translateY(-4px);
        }
        .pet-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .pet-info {
            padding: 1rem;
        }
        .pet-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        .pet-details {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 0.75rem;
        }
        .pet-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background: #fff7ed;
            color: #f97316;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }
        .pet-actions {
            display: flex;
            gap: 0.5rem;
        }
        .btn-view {
            flex: 1;
            padding: 0.5rem;
            background: #f97316;
            color: white;
            text-align: center;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-view:hover {
            background: #ea580c;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 1rem;
        }
        .empty-state i {
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        .empty-state h3 {
            color: #64748b;
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
                <a href="index.php" class="hov">My Pets</a>
                <a href="shelter_messages.php">Messages</a>
                <div class="user-info">
                    <a href="shelter_profile.php" id="userNameDisplay" class="hov"><?php echo htmlspecialchars($userName); ?></a>
                    <button onclick="location.href='../logout.php'" class="btn-outline">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="container">
        <div class="stats-card">
            <div class="stats-info">
                <h2><?php echo $totalPets; ?></h2>
                <p>Total Pets Listed</p>
            </div>
            <a href="add_pet_form.php" class="btn-add">
                <i data-lucide="plus-circle"></i>
                Add New Pet
            </a>
        </div>

        <div class="filters">
            <a href="?species=all" class="filter-btn <?php echo $speciesFilter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="?species=Dog" class="filter-btn <?php echo $speciesFilter === 'Dog' ? 'active' : ''; ?>">Dogs</a>
            <a href="?species=Cat" class="filter-btn <?php echo $speciesFilter === 'Cat' ? 'active' : ''; ?>">Cats</a>
            <a href="?species=Rabbit" class="filter-btn <?php echo $speciesFilter === 'Rabbit' ? 'active' : ''; ?>">Rabbits</a>
            <a href="?species=Bird" class="filter-btn <?php echo $speciesFilter === 'Bird' ? 'active' : ''; ?>">Birds</a>
        </div>

        <div class="pets-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($pet = $result->fetch_assoc()): ?>
                    <div class="pet-card">
                        <img src="<?php echo htmlspecialchars($pet['image_url']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="pet-image">
                        <div class="pet-info">
                            <h3 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h3>
                            <div class="pet-details">
                                <span class="pet-badge">
                                    <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                    <?php echo htmlspecialchars($pet['age']); ?>
                                </span>
                                <span class="pet-badge">
                                    <i data-lucide="tag" style="width: 14px; height: 14px;"></i>
                                    <?php echo htmlspecialchars($pet['breed']); ?>
                                </span>
                                <span class="pet-badge">
                                    <i data-lucide="ruler" style="width: 14px; height: 14px;"></i>
                                    <?php echo htmlspecialchars($pet['size']); ?>
                                </span>
                            </div>
                            <div class="pet-actions">
                                <a href="petdetails.php?id=<?php echo $pet['id']; ?>" class="btn-view">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state" style="grid-column: 1/-1;">
                    <i data-lucide="inbox" style="width: 64px; height: 64px;"></i>
                    <h3>No pets listed yet</h3>
                    <p style="color: #94a3b8;">Start by adding your first pet for adoption</p>
                    <a href="add_pet_form.php" class="btn-add" style="margin-top: 1rem;">
                        <i data-lucide="plus-circle"></i>
                        Add New Pet
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
