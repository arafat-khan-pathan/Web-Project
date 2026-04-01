<?php
/**
 * DATABASE SETUP (Run this in your XAMPP/phpMyAdmin SQL tab):
 * * CREATE DATABASE IF NOT EXISTS paws_hearts;
 * USE paws_hearts;
 * * CREATE TABLE IF NOT EXISTS users (
 * id INT AUTO_INCREMENT PRIMARY KEY,
 * first_name VARCHAR(50) NOT NULL,
 * last_name VARCHAR(50) NOT NULL,
 * email VARCHAR(100) NOT NULL UNIQUE,
 * password VARCHAR(255) NOT NULL,
 * role ENUM('admin', 'adopter') DEFAULT 'adopter',
 * created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
 * );
 * * -- Optional: Create a default admin
 * -- INSERT INTO users (first_name, last_name, email, password, role) 
 * -- VALUES ('System', 'Admin', 'admin@test.com', '$2y$10$Ynd6RzR5eHhyeHhyeHhyeHh6eeFqY7U8M.r.fI7yvGzKqFp.oF.eS', 'admin'); 
 * -- (Password for above is admin123)
 */

session_start();

// Database Connection
$host = "localhost";
$db_user = "root"; // Default XAMPP user
$db_pass = "";     // Default XAMPP password
$db_name = "paws_hearts";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// 1. HANDLE SIGN UP
if (isset($_POST['signup'])) {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($pass !== $confirm) {
        $message = "Passwords do not match!";
    } else {
        // Check if email exists
        $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        if ($checkEmail->get_result()->num_rows > 0) {
            $message = "Email already registered!";
        } else {
            // Store plain password (no hashing for testing)
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, role, is_approve) VALUES (?, ?, ?, ?, 'adopter', 'false')");
            $stmt->bind_param("ssss", $fname, $lname, $email, $pass);
            
            if ($stmt->execute()) {
                $_SESSION['userName'] = $fname . " " . $lname;
                $_SESSION['role'] = 'adopter';
                header("Location: login__.php"); // Redirect to home
                exit();
            } else {
                $message = "Error creating account.";
            }
        }
    }
}

// 2. HANDLE LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // Compare plain text password
        if ($pass === $user['password']) {
            // ===== SET SESSION DATA FROM DATABASE =====
            $_SESSION['userName'] = $user['first_name'] . " " . $user['last_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            
            // IMPORTANT: Store user ID from database for tracking and relationships
            $_SESSION['userId'] = $user['id']; // Auto-increment ID from users table
            // ==========================================
            
            // Approval gate for adopters
            if ($user['role'] === 'adopter') {
                $approved = isset($user['is_approve']) && ($user['is_approve'] === 'true' || $user['is_approve'] === '1' || $user['is_approve'] === 1);
                if (!$approved) {
                    $message = "Your account is pending admin approval.";
                    session_unset();
                } else {
                    header("Location: adopter/index.php");
                    exit();
                }
            } else {
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/admin.php");
                }
                else if ($user['role'] === 'shelter') {
                    header("Location: shelter/index.php");
                }
                else if ($user['role'] === 'guest') {
                    header("Location: guest/index.php");
                }
                exit();
            }
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "User not found!";
    }
}

// 3. GUEST LOGIN (Non-database)
if (isset($_GET['action']) && $_GET['action'] == 'guest') {
    $_SESSION['userName'] = "Guest User";
    $_SESSION['role'] = 'guest';
    header("Location: guest/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --bg: #fff7ed;
            --text: #1f2937;
            --gray: #6b7280;
            --white: #ffffff;
            --border: #e5e7eb;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
            box-sizing: border-box;
        }

        .auth-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .logo h1 { font-size: 1.5rem; color: var(--text); margin: 0; }
        .subtitle { color: var(--gray); font-size: 0.9rem; margin-bottom: 1.5rem; }

        .form-row { display: flex; gap: 1rem; margin-bottom: 1.25rem; }
        .form-group { text-align: left; margin-bottom: 1.25rem; flex: 1; }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 0.4rem;
            color: var(--text);
            text-transform: uppercase;
        }

        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.6rem;
            font-size: 0.95rem;
            box-sizing: border-box;
            outline: none;
        }

        .btn-primary {
            width: 100%;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.85rem;
            border-radius: 0.6rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 0.5rem;
            font-size: 1rem;
        }

        .btn-secondary {
            width: 95%;
            background: transparent;
            color: var(--text);
            border: 1px solid var(--border);
            padding: 0.75rem;
            border-radius: 0.6rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-bottom: 1rem;
        }

        .alert {
            background: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .divider { margin: 1.5rem 0; display: flex; align-items: center; color: var(--gray); font-size: 0.75rem; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1px; background: var(--border); }
        .divider span { padding: 0 0.75rem; }
        .footer-text { font-size: 0.875rem; color: var(--gray); margin-top: 1.5rem; }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .hidden { display: none; }
        .role-badge { display: inline-block; padding: 0.35rem 0.75rem; background: #fff7ed; color: var(--primary); border-radius: 2rem; font-size: 0.7rem; font-weight: 800; margin-bottom: 1rem; border: 1px solid #ffedd5; }
    </style>
</head>
<body>

    <!-- SIGN IN CARD -->
    <div id="signin-container" class="auth-card <?php echo isset($_POST['signup']) ? 'hidden' : ''; ?>">
        <div class="logo">
            <i data-lucide="paw-print"></i>
            <h1>Paws & Hearts</h1>
        </div>
        <p class="subtitle">Welcome back! Sign in to continue</p>

        <?php if($message && !isset($_POST['signup'])): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="admin@test.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn-primary">Sign In</button>
        </form>

        <div class="divider"><span>OR</span></div>
        <a href="?action=guest" class="btn-secondary">Browse as Guest</a>
        <p class="footer-text">Need an account? <a href="#" onclick="toggleAuth()">Create Adopter Account</a></p>
    </div>

    <!-- SIGN UP CARD -->
    <div id="signup-container" class="auth-card <?php echo isset($_POST['signup']) ? '' : 'hidden'; ?>">
        <div class="logo">
            <i data-lucide="paw-print"></i>
            <h1>Paws & Hearts</h1>
        </div>
        <div class="role-badge">NEW ADOPTER ACCOUNT</div>

        <?php if($message && isset($_POST['signup'])): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="fname" placeholder="John" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lname" placeholder="Doe" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="signup" class="btn-primary">Request to Create Account</button>
        </form>

        <p class="footer-text">Already have an account? <a href="#" onclick="toggleAuth()">Sign In</a></p>
    </div>

    <script>
        lucide.createIcons();
        function toggleAuth() {
            document.getElementById('signin-container').classList.toggle('hidden');
            document.getElementById('signup-container').classList.toggle('hidden');
        }
    </script>
</body>
</html>