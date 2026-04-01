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

$message = "";
$messageType = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $age = $conn->real_escape_string($_POST['age']);
    $species = $conn->real_escape_string($_POST['species']);
    $breed = $conn->real_escape_string($_POST['breed']);
    $size = $conn->real_escape_string($_POST['size']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $image_url = $conn->real_escape_string($_POST['image_url']);
    $description = $conn->real_escape_string($_POST['description']);
    $health_status = $conn->real_escape_string($_POST['health_status']);
    
    $sql = "INSERT INTO pets (user_id, name, age, species, breed, size, gender, image_url, description, health_status) 
            VALUES ($userId, '$name', '$age', '$species', '$breed', '$size', '$gender', '$image_url', '$description', '$health_status')";
    
    if ($conn->query($sql)) {
        $message = "Pet added successfully!";
        $messageType = "success";
        // Redirect after 2 seconds
        header("Refresh: 2; url=index.php");
    } else {
        $message = "Error adding pet: " . $conn->error;
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Pet - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="../index.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1f2937;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.5rem;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-submit {
            background: #f97316;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 1rem;
        }
        .btn-submit:hover {
            background: #ea580c;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            margin-bottom: 1rem;
        }
        .back-link:hover {
            color: #f97316;
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
        <div class="form-container">
            <a href="index.php" class="back-link">
                <i data-lucide="arrow-left"></i> Back to My Pets
            </a>
            
            <h1 style="color: #f97316; margin-bottom: 0.5rem;">Add New Pet</h1>
            <p style="color: #64748b; margin-bottom: 2rem;">Fill in the details to list a new pet for adoption</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label>Pet Name *</label>
                        <input type="text" name="name" required placeholder="e.g., Buddy">
                    </div>
                    <div class="form-group">
                        <label>Age *</label>
                        <input type="text" name="age" required placeholder="e.g., 2 Years">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Species *</label>
                        <select name="species" required>
                            <option value="">Select Species</option>
                            <option value="Dog">Dog</option>
                            <option value="Cat">Cat</option>
                            <option value="Rabbit">Rabbit</option>
                            <option value="Bird">Bird</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Breed *</label>
                        <input type="text" name="breed" required placeholder="e.g., Golden Retriever">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Size *</label>
                        <select name="size" required>
                            <option value="">Select Size</option>
                            <option value="Small">Small</option>
                            <option value="Medium">Medium</option>
                            <option value="Large">Large</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Image URL *</label>
                    <input type="url" name="image_url" required placeholder="https://example.com/pet-image.jpg">
                    <small style="color: #64748b;">Provide a link to the pet's photo</small>
                </div>

                <div class="form-group">
                    <label>Health Status *</label>
                    <input type="text" name="health_status" required placeholder="e.g., Healthy, Vaccinated">
                </div>

                <div class="form-group">
                    <label>About this Pet *</label>
                    <textarea name="description" required placeholder="Describe the pet's personality, behavior, special needs, etc."></textarea>
                </div>

                <button type="submit" name="add_pet" class="btn-submit">
                    <i data-lucide="plus-circle" style="display: inline; width: 20px; height: 20px; vertical-align: middle;"></i>
                    Add Pet
                </button>
            </form>
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
