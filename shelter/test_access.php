<?php
session_start();

// Test page to verify all shelter pages are accessible
echo "<h2>Shelter System Configuration Test</h2>";

$pages = [
    'index.php' => 'Main Entry Point (redirects to shelter_home.php)',
    'shelter_home.php' => 'Homepage - Shows shelter\s pets',
    'add_pet_form.php' => 'Form to add new pets',
    'shelter_profile.php' => 'Profile - Owned & adopted pets',
    'shelter_messages.php' => 'Messaging system'
];

echo "<ul>";
foreach ($pages as $file => $description) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path) ? '✅' : '❌';
    echo "<li>$exists $file - $description</li>";
}
echo "</ul>";

echo "<hr>";
echo "<h3>Session Info</h3>";
echo "<pre>";
echo "User: " . ($_SESSION['userName'] ?? 'Not set') . "\n";
echo "Role: " . ($_SESSION['role'] ?? 'Not set') . "\n";
echo "User ID: " . ($_SESSION['userId'] ?? 'Not set') . "\n";
echo "</pre>";

echo "<hr>";
echo "<h3>Navigation Links</h3>";
echo "<ul>";
echo "<li><a href='index.php'>Go to Shelter Home (index.php)</a></li>";
echo "<li><a href='shelter_home.php'>Shelter Home</a></li>";
echo "<li><a href='add_pet_form.php'>Add Pet Form</a></li>";
echo "<li><a href='shelter_profile.php'>Shelter Profile</a></li>";
echo "<li><a href='shelter_messages.php'>Messages</a></li>";
echo "<li><a href='../logout.php'>Logout</a></li>";
echo "</ul>";
?>
