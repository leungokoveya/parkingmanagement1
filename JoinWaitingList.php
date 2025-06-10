<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    echo "<script>
        alert('Please log in first.');
        window.location.href = 'Login.php';
    </script>";
    exit;
}

$userID = $_SESSION['UserID'];
$spaceID = $_GET['spaceID'] ?? null;
$bayID = $_GET['bayID'] ?? null;

if (!$spaceID || !$bayID) {
    echo "<script>
        alert('Missing space or bay ID.');
        window.location.href = 'Index.php';
    </script>";
    exit;
}

try {
    // Check if already on waiting list for this bay
    $check = $conn->prepare("SELECT * FROM WaitingList WHERE UserID = :userID AND BayID = :bayID");
    $check->execute(['userID' => $userID, 'bayID' => $bayID]);

    if ($check->rowCount() > 0) {
        echo "<script>
            alert('You are already on the waiting list for this bay.');
            window.location.href = 'Index.php';
        </script>";
        exit;
    }

    // Insert into waiting list
    $insert = $conn->prepare("INSERT INTO WaitingList (UserID, BayID, RequestedAt) VALUES (:userID, :bayID, NOW())");
    $insert->execute(['userID' => $userID, 'bayID' => $bayID]);

    echo "<script>
        alert('You have been added to the waiting list. We will notify you when the bay becomes available.');
        window.location.href = 'Index.php';
    </script>";
} catch (PDOException $e) {
    echo "<script>
        alert('Database error: " . addslashes($e->getMessage()) . "');
        window.location.href = 'Index.php';
    </script>";
}
?>
