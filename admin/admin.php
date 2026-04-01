<?php
session_start();

$host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "paws_hearts";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login__.php");
    exit();
}

$alert = $_SESSION['alert'] ?? null;
if (isset($_SESSION['alert'])) {
    unset($_SESSION['alert']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_shelter'])) {
    $shelterId = (int) ($_POST['shelter_id'] ?? 0);
    if ($shelterId > 0) {
        $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ? AND role = "shelter"');
        $deleteStmt->bind_param('i', $shelterId);
        if ($deleteStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Shelter deleted successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to delete shelter.'];
        }
        $deleteStmt->close();
    }
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_adopter'])) {
    $adopterId = (int) ($_POST['adopter_id'] ?? 0);
    if ($adopterId > 0) {
        $approveStmt = $conn->prepare('UPDATE users SET is_approve = 1 WHERE id = ? AND role = "adopter"');
        $approveStmt->bind_param('i', $adopterId);
        if ($approveStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Adopter approved successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to approve adopter.'];
        }
        $approveStmt->close();
    }
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_adopter'])) {
    $adopterId = (int) ($_POST['adopter_id'] ?? 0);
    if ($adopterId > 0) {
        $rejectStmt = $conn->prepare('DELETE FROM users WHERE id = ? AND role = "adopter"');
        $rejectStmt->bind_param('i', $adopterId);
        if ($rejectStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Adopter rejected and removed.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to reject adopter.'];
        }
        $rejectStmt->close();
    }
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_adopter'])) {
    $adopterId = (int) ($_POST['adopter_id'] ?? 0);
    if ($adopterId > 0) {
        $deleteStmt = $conn->prepare('DELETE FROM users WHERE id = ? AND role = "adopter"');
        $deleteStmt->bind_param('i', $adopterId);
        if ($deleteStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Adopter deleted successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to delete adopter.'];
        }
        $deleteStmt->close();
    }
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_category'])) {
    $categoryName = trim($_POST['category_name'] ?? '');
    $categoryDesc = trim($_POST['category_desc'] ?? '');

    if ($categoryName === '') {
        $alert = ['type' => 'error', 'message' => 'Category name is required.'];
    } else {
        $insertCategory = $conn->prepare('INSERT INTO categories (name, description) VALUES (?, ?)');
        $insertCategory->bind_param('ss', $categoryName, $categoryDesc);
        if ($insertCategory->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Category added successfully.'];
            header('Location: admin.php');
            exit();
        } else {
            $alert = ['type' => 'error', 'message' => 'Failed to add category.'];
        }
        $insertCategory->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_category'])) {
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    if ($categoryId > 0) {
        $deleteCategory = $conn->prepare('DELETE FROM categories WHERE id = ?');
        $deleteCategory->bind_param('i', $categoryId);
        if ($deleteCategory->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Category deleted successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to delete category.'];
        }
        $deleteCategory->close();
    }
    header('Location: admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_adopter'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $profilePicPath = null;

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $alert = ['type' => 'error', 'message' => 'Please fill in all required fields.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert = ['type' => 'error', 'message' => 'Please enter a valid email address.'];
    } elseif ($password !== $confirmPassword) {
        $alert = ['type' => 'error', 'message' => 'Password and confirm password must match.'];
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $alert = ['type' => 'error', 'message' => 'Email is already registered.'];
        }
        $checkStmt->close();
    }

    if (!$alert && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK && $_FILES['profile_pic']['size'] > 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($_FILES['profile_pic']['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            $alert = ['type' => 'error', 'message' => 'Profile picture must be an image (jpg, png, gif, webp).'];
        } else {
            $uploadDir = __DIR__ . '/uploads/profile_pics';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $fileName = 'adopter_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $targetPath = $uploadDir . '/' . $fileName;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
                $profilePicPath = 'uploads/profile_pics/' . $fileName;
            } else {
                $alert = ['type' => 'error', 'message' => 'Failed to upload profile picture.'];
            }
        }
    }

    if (!$alert) {
        $insertStmt = $conn->prepare('INSERT INTO users (first_name, last_name, email, password, role, profile_pic, is_approve) VALUES (?, ?, ?, ?, "adopter", ?, 0)');
        $insertStmt->bind_param('sssss', $firstName, $lastName, $email, $password, $profilePicPath);
        if ($insertStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Adopter created successfully.'];
            header('Location: admin.php');
            exit();
        } else {
            $alert = ['type' => 'error', 'message' => 'Failed to create adopter. Please try again.'];
        }
        $insertStmt->close();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_shelter'])) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $profilePicPath = null;

    if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $alert = ['type' => 'error', 'message' => 'Please fill in all required fields.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alert = ['type' => 'error', 'message' => 'Please enter a valid email address.'];
    } elseif ($password !== $confirmPassword) {
        $alert = ['type' => 'error', 'message' => 'Password and confirm password must match.'];
    } else {
        $checkStmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $alert = ['type' => 'error', 'message' => 'Email is already registered.'];
        }
        $checkStmt->close();
    }

    if (!$alert && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK && $_FILES['profile_pic']['size'] > 0) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mime = mime_content_type($_FILES['profile_pic']['tmp_name']);
        if (!in_array($mime, $allowed, true)) {
            $alert = ['type' => 'error', 'message' => 'Profile picture must be an image (jpg, png, gif, webp).'];
        } else {
            $uploadDir = __DIR__ . '/uploads/profile_pics';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $fileName = 'shelter_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
            $targetPath = $uploadDir . '/' . $fileName;
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
                $profilePicPath = 'uploads/profile_pics/' . $fileName;
            } else {
                $alert = ['type' => 'error', 'message' => 'Failed to upload profile picture.'];
            }
        }
    }

    if (!$alert) {
        $insertStmt = $conn->prepare('INSERT INTO users (first_name, last_name, email, password, role, profile_pic) VALUES (?, ?, ?, ?, "shelter", ?)');
        $insertStmt->bind_param('sssss', $firstName, $lastName, $email, $password, $profilePicPath);
        if ($insertStmt->execute()) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Shelter created successfully.'];
            header('Location: admin.php');
            exit();
        } else {
            $alert = ['type' => 'error', 'message' => 'Failed to create shelter. Please try again.'];
        }
        $insertStmt->close();
    }
}

$sheltersData = [];
$shelterSql = "
    SELECT 
        u.id,
        u.first_name,
        u.last_name,
        u.email,
        u.profile_pic,
        u.created_at,
        COALESCE(p.pet_count, 0) AS pet_count
    FROM users u
    LEFT JOIN (
        SELECT user_id, COUNT(*) AS pet_count
        FROM pets
        GROUP BY user_id
    ) p ON p.user_id = u.id
    WHERE u.role = 'shelter'
    ORDER BY u.created_at DESC
";

if ($result = $conn->query($shelterSql)) {
    while ($row = $result->fetch_assoc()) {
        $sheltersData[] = [
            'id' => (int) $row['id'],
            'first_name' => $row['first_name'] ?? '',
            'last_name' => $row['last_name'] ?? '',
            'name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: 'Shelter',
            'email' => $row['email'] ?? '',
            'profile_pic' => $row['profile_pic'] ?? '',
            'pets' => (int) $row['pet_count'],
            'status' => 'active',
            'created_at' => $row['created_at'] ?? '',
            'location' => 'N/A',
            'phone' => ''
        ];
    }
    $result->free();
}

$adoptersData = [];
$adopterSql = "
    SELECT 
        u.id,
        u.first_name,
        u.last_name,
        u.email,
        u.profile_pic,
        u.created_at,
        u.is_approve,
        COALESCE(p.pets_adopted_count, 0) AS adoption_count
    FROM users u
    LEFT JOIN (
        SELECT adopter_id, COUNT(*) AS pets_adopted_count
        FROM pets
        WHERE adopter_id IS NOT NULL
        GROUP BY adopter_id
    ) p ON p.adopter_id = u.id
    WHERE u.role = 'adopter'
    ORDER BY u.created_at DESC
";

if ($result = $conn->query($adopterSql)) {
    while ($row = $result->fetch_assoc()) {
        $rawApprove = $row['is_approve'] ?? 'false';
        $isApproved = ($rawApprove === 'true' || $rawApprove === 1 || $rawApprove === '1');
        $adoptersData[] = [
            'id' => (int) $row['id'],
            'first_name' => $row['first_name'] ?? '',
            'last_name' => $row['last_name'] ?? '',
            'name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')) ?: 'Adopter',
            'email' => $row['email'] ?? '',
            'profile_pic' => $row['profile_pic'] ?? '',
            'pets_adopted' => (int) $row['adoption_count'],
            'location' => 'N/A',
            'date' => isset($row['created_at']) ? date('Y-m-d', strtotime($row['created_at'])) : '',
            'status' => $isApproved ? 'approved' : 'pending',
            'is_approve' => $isApproved ? 1 : 0,
            'created_at' => $row['created_at'] ?? ''
        ];
    }
    $result->free();
}

$petsData = [];
$petSql = "
    SELECT 
        p.id,
        p.name,
        p.breed,
        p.species,
        p.gender,
        p.image_url,
        p.about,
        p.user_id,
        CONCAT(u.first_name, ' ', u.last_name) AS shelter_name
    FROM pets p
    LEFT JOIN users u ON u.id = p.user_id
    ORDER BY p.created_at DESC
";

if ($result = $conn->query($petSql)) {
    while ($row = $result->fetch_assoc()) {
        $petsData[] = [
            'id' => (int) $row['id'],
            'name' => $row['name'] ?: 'Pet',
            'breed' => $row['breed'] ?? ($row['species'] ?? ''),
            'shelter' => trim($row['shelter_name'] ?? '') ?: 'Unknown Shelter',
            'category' => $row['species'] ?: 'Other',
            'status' => 'available',
            'img' => $row['image_url'] ?: 'https://via.placeholder.com/300x150?text=Pet',
            'species' => $row['species'] ?? ''
        ];
    }
    $result->free();
}

$categoriesData = [];
$categorySql = "
    SELECT
        c.id,
        c.name,
        c.description,
        c.created_at,
        COALESCE(p.total, 0) AS total,
        COALESCE(p.available, 0) AS available,
        COALESCE(p.adopted, 0) AS adopted
    FROM categories c
    LEFT JOIN (
        SELECT
            species,
            COUNT(*) AS total,
            SUM(CASE WHEN adopter_id IS NULL THEN 1 ELSE 0 END) AS available,
            SUM(CASE WHEN adopter_id IS NOT NULL THEN 1 ELSE 0 END) AS adopted
        FROM pets
        GROUP BY species
    ) p ON p.species = c.name
    ORDER BY c.created_at DESC
";

if ($result = $conn->query($categorySql)) {
    while ($row = $result->fetch_assoc()) {
        $categoriesData[] = [
            'id' => (int) $row['id'],
            'name' => $row['name'] ?? 'Category',
            'description' => $row['description'] ?? '',
            'total' => (int) ($row['total'] ?? 0),
            'available' => (int) ($row['available'] ?? 0),
            'adopted' => (int) ($row['adopted'] ?? 0),
            'created_at' => $row['created_at'] ?? ''
        ];
    }
    $result->free();
}

$pendingApprovals = 0;
foreach ($adoptersData as $a) {
    if (isset($a['is_approve']) && (int) $a['is_approve'] === 0) {
        $pendingApprovals++;
    }
}

$approvedAdoptersCount = 0;
foreach ($adoptersData as $a) {
    if (isset($a['is_approve']) && (int) $a['is_approve'] === 1) {
        $approvedAdoptersCount++;
    }
}

$stats = [
    'shelters' => count($sheltersData),
    'pets' => count($petsData),
    'adopters' => $approvedAdoptersCount,
    'pending' => $pendingApprovals
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Paws & Hearts</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #f97316;
            --primary-dark: #ea580c;
            --primary-light: #ffedd5;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
            --bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text-main);
        }

        .admin-layout {
            display: grid;
            grid-template-columns: 250px 1fr;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border-right: 1px solid var(--border);
            padding: 1.5rem;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 2rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .nav-item:hover, .nav-item.active {
            background: var(--primary-light);
            color: var(--primary);
        }

        /* Main Content */
        .main-content {
            padding: 2rem;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-main);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        /* Section */
        .section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Buttons */
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-outline {
            background: white;
            border: 1px solid var(--border);
            color: var(--text-main);
        }

        /* Table */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        th {
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-muted);
            background: var(--bg);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .close-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        /* Charts */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid var(--border);
        }

        .chart-container {
            position: relative;
            height: 300px;
        }

        /* View Sections */
        .view-section {
            display: none;
        }

        .view-section.active {
            display: block;
        }

        /* Pet Grid */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
        }

        .pet-card {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .pet-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .pet-card-body {
            padding: 1rem;
        }

        .pet-card-title {
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .pet-card-meta {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: relative;
                height: auto;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <i data-lucide="paw-print"></i>
                <span>Admin Panel</span>
            </div>
            <nav>
                <div class="nav-item active" onclick="showView('overview')">
                    <i data-lucide="layout-dashboard" size="18"></i>
                    <span>Overview</span>
                </div>
                <div class="nav-item" onclick="showView('shelters')">
                    <i data-lucide="home" size="18"></i>
                    <span>Shelters</span>
                </div>
                <div class="nav-item" onclick="showView('adopters')">
                    <i data-lucide="users" size="18"></i>
                    <span>Adopters</span>
                </div>
                <div class="nav-item" onclick="showView('pets')">
                    <i data-lucide="heart" size="18"></i>
                    <span>All Pets</span>
                </div>
                <div class="nav-item" onclick="showView('categories')">
                    <i data-lucide="tag" size="18"></i>
                    <span>Categories</span>
                </div>
                <div class="nav-item" onclick="showView('reports')">
                    <i data-lucide="bar-chart-2" size="18"></i>
                    <span>Reports</span>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1 id="pageTitle">Dashboard Overview</h1>
                <div class="user-info">
                    <span style="font-weight: 600;">Admin User</span>
                    <button class="btn btn-outline" onclick="logout()">
                        <i data-lucide="log-out" size="16"></i>
                        Logout
                    </button>
                </div>
            </div>

            <?php if ($alert): ?>
                <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid <?php echo $alert['type'] === 'success' ? '#d1fae5' : '#fee2e2'; ?>; background: <?php echo $alert['type'] === 'success' ? '#ecfdf3' : '#fef2f2'; ?>; color: <?php echo $alert['type'] === 'success' ? '#065f46' : '#991b1b'; ?>;">
                    <?php echo htmlspecialchars($alert['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Overview View -->
            <div id="overview" class="view-section active">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: #fff7ed; color: var(--primary);">
                                <i data-lucide="home" size="20"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="totalShelters"><?php echo $stats['shelters']; ?></div>
                        <div class="stat-label">Active Shelters</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: #dbeafe; color: var(--info);">
                                <i data-lucide="heart" size="20"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="totalPets"><?php echo $stats['pets']; ?></div>
                        <div class="stat-label">Total Pets</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: #d1fae5; color: var(--success);">
                                <i data-lucide="users" size="20"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="totalAdopters"><?php echo $stats['adopters']; ?></div>
                        <div class="stat-label">Approved Adopters</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-icon" style="background: #fef3c7; color: var(--warning);">
                                <i data-lucide="clock" size="20"></i>
                            </div>
                        </div>
                        <div class="stat-value" id="pendingApprovals"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Pending Approvals</div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3 style="margin-bottom: 1rem;">Adoption Trends</h3>
                        <div class="chart-container">
                            <canvas id="adoptionChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-card">
                        <h3 style="margin-bottom: 1rem;">Pet Distribution</h3>
                        <div class="chart-container">
                            <canvas id="petDistChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="activity" size="20"></i>
                            Recent Activity
                        </h2>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="activityTable">
                                <tr>
                                    <td>2024-01-10</td>
                                    <td>New shelter registration</td>
                                    <td>Happy Paws Shelter</td>
                                    <td><span class="status-badge status-pending">Pending</span></td>
                                </tr>
                                <tr>
                                    <td>2024-01-10</td>
                                    <td>Adopter approved</td>
                                    <td>John Smith</td>
                                    <td><span class="status-badge status-active">Approved</span></td>
                                </tr>
                                <tr>
                                    <td>2024-01-09</td>
                                    <td>Pet added</td>
                                    <td>Rescue Haven</td>
                                    <td><span class="status-badge status-active">Active</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Shelters View -->
            <div id="shelters" class="view-section">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="home" size="20"></i>
                            Manage Shelters
                        </h2>
                        <button class="btn btn-primary" onclick="openModal('addShelter')">
                            <i data-lucide="plus" size="16"></i>
                            Add Shelter
                        </button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Pets</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sheltersTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Adopters View -->
            <div id="adopters" class="view-section">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="users" size="20"></i>
                            Manage Adopters
                        </h2>
                        <button class="btn btn-primary" onclick="openModal('addAdopter')">
                            <i data-lucide="plus" size="16"></i>
                            Add Adopter
                        </button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="adoptersTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pets View -->
            <div id="pets" class="view-section">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="heart" size="20"></i>
                            All Pets Across Shelters
                        </h2>
                        <select id="shelterFilter" onchange="filterPetsByShelter(this.value)" style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border);">
                            <option value="all">All Shelters</option>
                        </select>
                    </div>
                    <div class="pet-grid" id="allPetsGrid"></div>
                </div>
            </div>

            <!-- Categories View -->
            <div id="categories" class="view-section">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="tag" size="20"></i>
                            Pet Categories
                        </h2>
                        <button class="btn btn-primary" onclick="openModal('addCategory')">
                            <i data-lucide="plus" size="16"></i>
                            Add Category
                        </button>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Total Pets</th>
                                    <th>Available</th>
                                    <th>Adopted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTable"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Reports View -->
            <div id="reports" class="view-section">
                <div class="section">
                    <div class="section-header">
                        <h2 class="section-title">
                            <i data-lucide="bar-chart-2" size="20"></i>
                            Adoption Statistics & Reports
                        </h2>
                    </div>
                    
                    <!-- Monthly Stats -->
                    <div class="stats-grid" style="margin-bottom: 2rem;">
                        <div class="stat-card">
                            <div class="stat-value">42</div>
                            <div class="stat-label">This Month's Adoptions</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">89%</div>
                            <div class="stat-label">Adoption Rate</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">18</div>
                            <div class="stat-label">Avg. Days to Adopt</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">$12,450</div>
                            <div class="stat-label">Adoption Fees Collected</div>
                        </div>
                    </div>

                    <!-- Detailed Charts -->
                    <div class="charts-grid">
                        <div class="chart-card">
                            <h3 style="margin-bottom: 1rem;">Shelter Performance</h3>
                            <div class="chart-container">
                                <canvas id="shelterPerfChart"></canvas>
                            </div>
                        </div>
                        <div class="chart-card">
                            <h3 style="margin-bottom: 1rem;">Monthly Trends (6 Months)</h3>
                            <div class="chart-container">
                                <canvas id="monthlyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Shelter Modal -->
    <div id="addShelterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Shelter</h3>
                <button class="close-btn" onclick="closeModal('addShelter')">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="create_shelter" value="1">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label>Profile Picture (optional)</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Shelter</button>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Category</h3>
                <button class="close-btn" onclick="closeModal('addCategory')">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="create_category" value="1">
                <div class="form-group">
                    <label>Category Name</label>
                    <input type="text" name="category_name" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="category_desc"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add Category</button>
            </form>
        </div>
    </div>

    <!-- View & Delete Shelter Modal -->
    <div id="viewShelterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Shelter Details</h3>
                <button class="close-btn" onclick="closeModal('viewShelter')">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            <div id="shelterDetailsContent" style="margin-bottom: 1.5rem;">
                <!-- Details populated by JavaScript -->
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: space-between;">
                <button class="btn btn-outline" onclick="closeModal('viewShelter')" style="flex: 1;">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeleteShelter()" style="flex: 1;">Delete Shelter</button>
            </div>
        </div>
    </div>

    <!-- Add Adopter Modal -->
    <div id="addAdopterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Add New Adopter</h3>
                <button class="close-btn" onclick="closeModal('addAdopter')">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="create_adopter" value="1">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <div class="form-group">
                    <label>Profile Picture (optional)</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Create Adopter</button>
            </form>
        </div>
    </div>

    <!-- View & Delete Adopter Modal -->
    <div id="viewAdopterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Adopter Details</h3>
                <button class="close-btn" onclick="closeModal('viewAdopter')">
                    <i data-lucide="x" size="20"></i>
                </button>
            </div>
            <div id="adopterDetailsContent" style="margin-bottom: 1.5rem;">
                <!-- Details populated by JavaScript -->
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: space-between;">
                <button class="btn btn-outline" onclick="closeModal('viewAdopter')" style="flex: 1;">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDeleteAdopter()" style="flex: 1;">Delete Adopter</button>
            </div>
        </div>
    </div>

    <script>
        // Data Management
        const statsFromServer = <?php echo json_encode($stats, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let shelters = <?php echo json_encode($sheltersData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let adopters = <?php echo json_encode($adoptersData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let categories = <?php echo json_encode($categoriesData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        let allPets = <?php echo json_encode($petsData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

        // Navigation
        function showView(view) {
            document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
            document.getElementById(view).classList.add('active');
            event.target.closest('.nav-item').classList.add('active');

            const titles = {
                overview: 'Dashboard Overview',
                shelters: 'Manage Shelters',
                adopters: 'Manage Adopters',
                pets: 'All Pets',
                categories: 'Pet Categories',
                reports: 'Reports & Statistics'
            };
            document.getElementById('pageTitle').textContent = titles[view];

            if (view === 'shelters') renderShelters();
            if (view === 'adopters') renderAdopters();
            if (view === 'pets') renderAllPets();
            if (view === 'categories') renderCategories();
            if (view === 'reports') initReportCharts();
        }

        // Modals
        function openModal(type) {
            document.getElementById(type + 'Modal').classList.add('show');
        }

        function closeModal(type) {
            document.getElementById(type + 'Modal').classList.remove('show');
        }

        // Shelters
        function renderShelters() {
            const tbody = document.getElementById('sheltersTable');
            tbody.innerHTML = shelters.map(s => `
                <tr>
                    <td><strong>${s.name}</strong></td>
                    <td>${s.email}</td>
                    <td>${s.pets}</td>
                    <td><span class="status-badge status-active">${s.status}</span></td>
                    <td>
                        <button class="btn btn-outline" onclick="viewShelter(${s.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <i data-lucide="eye" size="14"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteShelterWithDetails(${s.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <i data-lucide="trash-2" size="14"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            lucide.createIcons();
        }

        let currentShelterToDelete = null;

        function viewShelter(id) {
            const shelter = shelters.find(s => s.id === id);
            if (!shelter) return;
            
            currentShelterToDelete = id;
            const html = `
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <p><strong>Name:</strong> ${htmlEscape(shelter.name)}</p>
                    <p><strong>Email:</strong> ${htmlEscape(shelter.email)}</p>
                    <p><strong>Pets Listed:</strong> ${shelter.pets}</p>
                    <p><strong>Status:</strong> <span class="status-badge status-active">${htmlEscape(shelter.status)}</span></p>
                    <p><strong>Joined:</strong> ${new Date(shelter.created_at).toLocaleDateString()}</p>
                </div>
            `;
            document.getElementById('shelterDetailsContent').innerHTML = html;
            openModal('viewShelter');
        }

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function confirmDeleteShelter() {
            if (!currentShelterToDelete) return;
            
            if (!confirm('Are you sure you want to permanently delete this shelter? This cannot be undone.')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            form.innerHTML = `
                <input type="hidden" name="delete_shelter" value="1">
                <input type="hidden" name="shelter_id" value="${currentShelterToDelete}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function deleteShelterWithDetails(id) {
            const shelter = shelters.find(s => s.id === id);
            if (!shelter) return;
            
            const detailsMsg = `
Shelter Details:
━━━━━━━━━━━━━━━━━━━━━━
Name: ${shelter.name}
Email: ${shelter.email}
Pets Listed: ${shelter.pets}
Status: ${shelter.status}
Joined: ${new Date(shelter.created_at).toLocaleDateString()}
━━━━━━━━━━━━━━━━━━━━━━

Are you sure you want to permanently delete this shelter?
This action cannot be undone.`;

            if (confirm(detailsMsg)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php';
                form.innerHTML = `
                    <input type="hidden" name="delete_shelter" value="1">
                    <input type="hidden" name="shelter_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function removeShelter(id) {
            if (confirm('Are you sure you want to remove this shelter?')) {
                shelters = shelters.filter(s => s.id !== id);
                renderShelters();
                updateStats();
            }
        }

        // Adopters
        function getAdopterStatusMeta(adopter) {
            const raw = adopter.is_approve;
            const isApproved = raw === 1 || raw === '1' || raw === true || raw === 'true';
            return {
                isApproved,
                className: isApproved ? 'status-active' : 'status-rejected',
                label: isApproved ? 'Accepted' : 'Pending'
            };
        }

        function renderAdopters() {
            const tbody = document.getElementById('adoptersTable');
            tbody.innerHTML = adopters.map(a => {
                const statusMeta = getAdopterStatusMeta(a);
                return `
                    <tr>
                        <td><strong>${a.name}</strong></td>
                        <td>${a.email}</td>
                        <td>${a.date}</td>
                        <td><span class="status-badge ${statusMeta.className}">${statusMeta.label}</span></td>
                        <td>
                            <button class="btn btn-outline" onclick="viewAdopter(${a.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                <i data-lucide="eye" size="14"></i>
                            </button>
                            ${statusMeta.isApproved ? '' : `
                                <button class="btn btn-success" onclick="approveAdopter(${a.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                    <i data-lucide="check" size="14"></i>
                                </button>
                            `}
                            <button class="btn btn-danger" onclick="deleteAdopterDirect(${a.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                <i data-lucide="trash-2" size="14"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        }

        let currentAdopterToDelete = null;

        function viewAdopter(id) {
            const adopter = adopters.find(a => a.id === id);
            if (!adopter) return;
            
            currentAdopterToDelete = id;
            const statusMeta = getAdopterStatusMeta(adopter);
            const html = `
                <div style="background: #f8fafc; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <p><strong>Name:</strong> ${htmlEscape(adopter.name)}</p>
                    <p><strong>Email:</strong> ${htmlEscape(adopter.email)}</p>
                    <p><strong>Pets Adopted:</strong> ${adopter.pets_adopted || 0}</p>
                    <p><strong>Status:</strong> <span class="status-badge ${statusMeta.className}">${statusMeta.label}</span></p>
                    <p><strong>Joined:</strong> ${new Date(adopter.created_at).toLocaleDateString()}</p>
                </div>
            `;
            document.getElementById('adopterDetailsContent').innerHTML = html;
            openModal('viewAdopter');
        }

        function confirmDeleteAdopter() {
            if (!currentAdopterToDelete) return;
            
            if (!confirm('Are you sure you want to permanently delete this adopter? This cannot be undone.')) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            form.innerHTML = `
                <input type="hidden" name="delete_adopter" value="1">
                <input type="hidden" name="adopter_id" value="${currentAdopterToDelete}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function approveAdopter(id) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            form.innerHTML = `
                <input type="hidden" name="approve_adopter" value="1">
                <input type="hidden" name="adopter_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function rejectAdopter(id) {
            if (!confirm('Reject this adopter? This will remove the account.')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            form.innerHTML = `
                <input type="hidden" name="reject_adopter" value="1">
                <input type="hidden" name="adopter_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        function deleteAdopterDirect(id) {
            const adopter = adopters.find(a => a.id === id);
            if (!adopter) return;
            
            const statusMeta = getAdopterStatusMeta(adopter);
            const detailsMsg = `
Adopter Details:
━━━━━━━━━━━━━━━━━━━━━━
Name: ${adopter.name}
Email: ${adopter.email}
Pets Adopted: ${adopter.pets_adopted || 0}
Status: ${statusMeta.label}
Applied Date: ${adopter.date}
━━━━━━━━━━━━━━━━━━━━━━

Are you sure you want to permanently delete this adopter?
This action cannot be undone.`;

            if (confirm(detailsMsg)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'admin.php';
                form.innerHTML = `
                    <input type="hidden" name="delete_adopter" value="1">
                    <input type="hidden" name="adopter_id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // All Pets
        function renderAllPets(filter = 'all') {
            const grid = document.getElementById('allPetsGrid');
            const filtered = filter === 'all' ? allPets : allPets.filter(p => p.shelter === filter);
            grid.innerHTML = filtered.map(p => `
                <div class="pet-card">
                    <img src="${p.img}" alt="${p.name}">
                    <div class="pet-card-body">
                        <div class="pet-card-title">${p.name}</div>
                        <div class="pet-card-meta">${p.breed} • ${p.category}</div>
                        <div class="pet-card-meta" style="margin-top: 0.5rem;">
                            <strong>Shelter:</strong> ${p.shelter}
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <span class="status-badge status-active">${p.status}</span>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function filterPetsByShelter(shelter) {
            renderAllPets(shelter);
        }

        function populateShelterFilter() {
            const select = document.getElementById('shelterFilter');
            if (!select) return;
            const existing = new Set();
            shelters.forEach(s => {
                if (!s.name || existing.has(s.name)) return;
                existing.add(s.name);
                const opt = document.createElement('option');
                opt.value = s.name;
                opt.textContent = s.name;
                select.appendChild(opt);
            });
        }

        // Categories
        function renderCategories() {
            const tbody = document.getElementById('categoriesTable');
            tbody.innerHTML = categories.map(c => `
                <tr>
                    <td><strong>${c.name}</strong></td>
                    <td>${c.total}</td>
                    <td>${c.available}</td>
                    <td>${c.adopted}</td>
                    <td>
                        <button class="btn btn-danger" onclick="deleteCategory(${c.id})" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                            <i data-lucide="trash-2" size="14"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            lucide.createIcons();
        }

        function deleteCategory(id) {
            if (!confirm('Are you sure you want to delete this category?')) return;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'admin.php';
            form.innerHTML = `
                <input type="hidden" name="delete_category" value="1">
                <input type="hidden" name="category_id" value="${id}">
            `;
            document.body.appendChild(form);
            form.submit();
        }

        // Stats Update
        function updateStats() {
            document.getElementById('totalShelters').textContent = shelters.length;
            document.getElementById('totalPets').textContent = allPets.length;
            document.getElementById('totalAdopters').textContent = adopters.filter(a => a.status !== 'rejected').length;
            const pendingFromDb = statsFromServer && typeof statsFromServer.pending === 'number' ? statsFromServer.pending : 0;
            const pendingFromList = adopters.filter(a => a.status === 'pending').length;
            document.getElementById('pendingApprovals').textContent = Math.max(pendingFromDb, pendingFromList);
        }

        // Charts
        function initCharts() {
            // Adoption Trends
            new Chart(document.getElementById('adoptionChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Adoptions',
                        data: [28, 35, 42, 38, 45, 42],
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });

            // Pet Distribution
            new Chart(document.getElementById('petDistChart'), {
                type: 'doughnut',
                data: {
                    labels: categories.map(c => c.name),
                    datasets: [{
                        data: categories.map(c => c.total),
                        backgroundColor: ['#f97316', '#3b82f6', '#10b981', '#f59e0b']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }

        function initReportCharts() {
            setTimeout(() => {
                // Shelter Performance
                new Chart(document.getElementById('shelterPerfChart'), {
                    type: 'bar',
                    data: {
                        labels: shelters.length ? shelters.map(s => s.name) : ['No data'],
                        datasets: [{
                            label: 'Pets Listed',
                            data: shelters.length ? shelters.map(s => s.pets || 0) : [0],
                            backgroundColor: '#f97316'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } }
                    }
                });

                // Monthly Trends
                new Chart(document.getElementById('monthlyTrendsChart'), {
                    type: 'line',
                    data: {
                        labels: ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'New Pets',
                            data: [15, 22, 18, 28, 25, 30],
                            borderColor: '#3b82f6',
                            tension: 0.4
                        }, {
                            label: 'Adoptions',
                            data: [12, 19, 15, 25, 22, 28],
                            borderColor: '#10b981',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }, 100);
        }

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../logout.php';
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updateStats();
            initCharts();
            renderShelters();
            renderAdopters();
            populateShelterFilter();
            renderAllPets();
            renderCategories();
        });
    </script>
</body>
</html>