<?php
// encrypt-passwords.php

require 'db.php'; // Your existing PDO connection file

echo "<h2>Password Encryption Utility</h2>";

try {
    $stmt = $conn->query("SELECT UserID, Passwords FROM Users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $updatedCount = 0;
    foreach ($users as $user) {
        $userID = $user['UserID'];
        $plainPassword = $user['Passwords'];
    
        // Check if the password looks like a bcrypt or argon2 hash
        $isProbablyHashed = preg_match('/^\$2[ayb]\$/', $plainPassword) || preg_match('/^\$argon2/', $plainPassword);
        if ($isProbablyHashed) {
            echo "User $userID: Already hashed. ✅<br>";
            continue;
        }
    
        // Hash and update
        $hashed = password_hash($plainPassword, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE Users SET Passwords = ? WHERE UserID = ?");
        $update->execute([$hashed, $userID]);
    
        echo "User $userID: Password encrypted 🔐<br>";
        $updatedCount++;
    }
    

    echo "<br><strong>✔️ $updatedCount passwords were encrypted.</strong><br>";
    echo "<strong>🧹 You should now delete or rename this file for security.</strong>";

} catch (PDOException $e) {
    echo "<div style='color:red;'>❌ Error: " . $e->getMessage() . "</div>";
}
?>
