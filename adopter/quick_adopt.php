<?php
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: ../login__.php");
    exit();
}

$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'];

// DB Connection
$conn = new mysqli("localhost", "root", "", "paws_hearts");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create adoptions table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS adoptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_id INT NOT NULL,
    adopter_id INT NOT NULL,
    adoption_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (pet_id) REFERENCES pets(id) ON DELETE CASCADE,
    FOREIGN KEY (adopter_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_adoption (pet_id, adopter_id)
)");

// Handle quick adopt
if (isset($_POST['quick_adopt'])) {
    $petId = intval($_POST['pet_id']);
    $status = $_POST['status'] ?? 'completed';
    
    $stmt = $conn->prepare("INSERT INTO adoptions (pet_id, adopter_id, status, notes) VALUES (?, ?, ?, 'Test adoption') ON DUPLICATE KEY UPDATE status = ?");
    $stmt->bind_param("iiss", $petId, $userId, $status, $status);
    
    if ($stmt->execute()) {
        $message = "✅ Successfully adopted!";
    } else {
        $message = "❌ Error: " . $conn->error;
    }
}

// Get available pets
$pets = $conn->query("SELECT p.*, 
    (SELECT COUNT(*) FROM adoptions WHERE pet_id = p.id AND adopter_id = $userId) as already_adopted
    FROM pets p ORDER BY p.name");

// Get current adoptions
$myAdoptions = $conn->query("SELECT p.*, a.adoption_date, a.status 
    FROM pets p 
    INNER JOIN adoptions a ON p.id = a.pet_id 
    WHERE a.adopter_id = $userId 
    ORDER BY a.adoption_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Adopt - Test Helper</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f8fafc; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #f97316; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin: 2rem 0; }
        .card { background: white; border-radius: 8px; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 200px; object-fit: cover; border-radius: 6px; margin-bottom: 0.5rem; }
        .btn { background: #f97316; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
        .btn:disabled { background: #cbd5e1; cursor: not-allowed; }
        .btn-green { background: #10b981; }
        .alert { padding: 1rem; background: #dcfce7; color: #166534; border-radius: 6px; margin-bottom: 1rem; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .adopted-mark { background: #10b981; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; margin-top: 0.5rem; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Quick Adopt - Test Helper</h1>
        <p>Logged in as: <strong><?php echo htmlspecialchars($userName); ?></strong> (ID: <?php echo $userId; ?>)</p>
        
        <?php if (isset($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <h2>Your Adopted Pets (<?php echo $myAdoptions->num_rows; ?>)</h2>
        <?php if ($myAdoptions->num_rows > 0): ?>
            <div class="grid">
                <?php while ($pet = $myAdoptions->fetch_assoc()): ?>
                    <div class="card">
                        <img src="<?php echo htmlspecialchars($pet['image_url'] ?? $pet['image_urrl'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                        <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                        <p><?php echo htmlspecialchars($pet['breed']); ?> • <?php echo htmlspecialchars($pet['age']); ?></p>
                        <span class="badge badge-<?php echo $pet['status'] == 'completed' ? 'success' : 'pending'; ?>">
                            <?php echo strtoupper($pet['status']); ?>
                        </span>
                        <p style="font-size: 0.85rem; color: #64748b; margin-top: 0.5rem;">
                            Adopted: <?php echo date('M d, Y', strtotime($pet['adoption_date'])); ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="color: #64748b;">You haven't adopted any pets yet. Click "Quick Adopt" below!</p>
        <?php endif; ?>

        <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e2e8f0;">

        <h2>Available Pets - Click to Adopt</h2>
        <div class="grid">
            <?php while ($pet = $pets->fetch_assoc()): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($pet['image_url'] ?? $pet['image_urrl'] ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                    <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                    <p><?php echo htmlspecialchars($pet['breed'] ?? 'Unknown'); ?> • <?php echo htmlspecialchars($pet['age'] ?? 'Unknown'); ?></p>
                    <p style="font-size: 0.85rem; color: #64748b;"><?php echo htmlspecialchars($pet['species']); ?></p>
                    
                    <?php if ($pet['already_adopted']): ?>
                        <div class="adopted-mark">✓ Already Adopted by You</div>
                    <?php else: ?>
                        <form method="POST" style="margin-top: 0.5rem;">
                            <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" name="quick_adopt" class="btn">Quick Adopt</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>

        <hr style="margin: 2rem 0;">
        <p style="text-align: center;">
            <a href="profile.php" class="btn btn-green">View My Profile</a>
            <a href="index.php" class="btn" style="background: #64748b; margin-left: 1rem;">Back to Home</a>
        </p>
    </div>
</body>
</html>
