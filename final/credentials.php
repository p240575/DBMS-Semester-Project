<?php
// Temporary script to show and reset customer credentials
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Show existing customers
$stmt = $db->query("SELECT user_id, user_name, email, phone, role FROM Users WHERE role = 'customer' ORDER BY user_id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Current Customers</h2><table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>";
foreach ($users as $u) {
    echo "<tr><td>{$u['user_id']}</td><td>{$u['user_name']}</td><td>{$u['email']}</td><td>{$u['phone']}</td></tr>";
}
echo "</table>";

// Reset all customer passwords to: Test@1234
$newHash = password_hash('Test@1234', PASSWORD_BCRYPT);
$db->exec("UPDATE Users SET password = '$newHash' WHERE role = 'customer'");

// If no customers, insert 2 demo ones
if (count($users) == 0) {
    $hash1 = password_hash('Test@1234', PASSWORD_BCRYPT);
    $db->exec("INSERT INTO Users (user_name, email, phone, password, role) VALUES ('Ali Khan', 'ali@test.com', '03001234567', '$hash1', 'customer')");
    $uid1 = $db->lastInsertId();
    $db->exec("INSERT INTO Addresses (user_id, full_name, phone, address_line, city, province, zipcode, is_default) VALUES ($uid1, 'Ali Khan', '03001234567', '123 Main Street', 'Lahore', 'Punjab', '54000', 1)");
    
    $hash2 = password_hash('Test@1234', PASSWORD_BCRYPT);
    $db->exec("INSERT INTO Users (user_name, email, phone, password, role) VALUES ('Sara Ahmed', 'sara@test.com', '03111234567', '$hash2', 'customer')");
    $uid2 = $db->lastInsertId();
    $db->exec("INSERT INTO Addresses (user_id, full_name, phone, address_line, city, province, zipcode, is_default) VALUES ($uid2, 'Sara Ahmed', '03111234567', '456 Garden Road', 'Karachi', 'Sindh', '75000', 1)");
    
    echo "<br><p style='color:green;'>✅ Created 2 demo customers.</p>";
}

// Re-fetch
$stmt2 = $db->query("SELECT user_id, user_name, email, phone FROM Users WHERE role = 'customer' ORDER BY user_id");
$finalUsers = $stmt2->fetchAll(PDO::FETCH_ASSOC);

echo "<br><h2>✅ Customer Login Credentials (password reset)</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr style='background:#eee;'><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Password</th></tr>";
foreach ($finalUsers as $u) {
    echo "<tr><td>{$u['user_id']}</td><td>{$u['user_name']}</td><td>{$u['email']}</td><td>{$u['phone']}</td><td><strong>Test@1234</strong></td></tr>";
}
echo "</table>";
echo "<br><p>Admin login: <strong>username: admin</strong> | <strong>password: admin123</strong></p>";
echo "<br><p><a href='/'>Go to Homepage</a> | <a href='/auth/login'>Go to Login</a></p>";
?>
