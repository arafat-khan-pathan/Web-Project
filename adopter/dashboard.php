<?php
// Example protected page - this will redirect to login if not logged in
require_once '../check_login.php';

// Get current user info
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Paws & Hearts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar h1 {
            color: #f97316;
        }
        
        .navbar .user-section {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .navbar .logout-btn {
            background: #f97316;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
        }
        
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background: white;
            padding: 2rem;
            border-radius: 0.8rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .user-info {
            background: #fff7ed;
            padding: 1.5rem;
            border-radius: 0.8rem;
            margin-bottom: 2rem;
        }
        
        .user-info h2 {
            color: #f97316;
            margin-bottom: 1rem;
        }
        
        .info-row {
            margin: 0.5rem 0;
            color: #333;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>🐾 Paws & Hearts</h1>
        <div class="user-section">
            <span>Welcome, <?php echo htmlspecialchars($user['first_name']); ?></span>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="user-info">
            <h2>Your Account Information</h2>
            <div class="info-row"><strong>Name:</strong> <?php echo htmlspecialchars($user['user_name']); ?></div>
            <div class="info-row"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
            <div class="info-row"><strong>Account Type:</strong> <?php echo ucfirst($user['user_type']); ?></div>
            <div class="info-row"><strong>User ID:</strong> <?php echo $user['id']; ?></div>
        </div>
        
        <div class="card">
            <h2>Dashboard</h2>
            <p>This is a protected page. Only logged-in users can see this.</p>
            
            <?php if($user['user_type'] === 'adopter'): ?>
                <p><strong>Adopter Features:</strong></p>
                <ul>
                    <li>Browse available pets</li>
                    <li>Apply for adoption</li>
                    <li>View application status</li>
                    <li>Message shelters</li>
                </ul>
            <?php elseif($user['user_type'] === 'shelter'): ?>
                <p><strong>Shelter Features:</strong></p>
                <ul>
                    <li>Add pets for adoption</li>
                    <li>Manage pet listings</li>
                    <li>Review adoption applications</li>
                    <li>Message adopters</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
