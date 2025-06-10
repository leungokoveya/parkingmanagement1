<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['UserID'])) {
    die("Unauthorized access.");
}

$userID = $_SESSION['UserID'];
$role = $_SESSION['Role'] ?? 'Guest';

$spaceID = $_POST['spaceID'] ?? null;
$bayID = $_POST['bayID'] ?? null;
$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;
$priority = isset($_POST['priority']) ? 1 : 0;

if (!$spaceID || !$bayID || !$start || !$end) {
    die("Missing required booking information.");
}

// Check if user already has active booking (unless Facility Manager)
if ($role !== 'Facility Manager') {
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT * FROM Bookings WHERE UserID = :userID AND BookingEnd > :now");
    $stmt->execute(['userID' => $userID, 'now' => $now]);
    if ($stmt->rowCount() > 0) {
        echo "<script>
            alert('You already have an active booking. Cancel it before booking another bay.');
            window.location.href = 'Index.php';
        </script>";
        exit;
    }
}

try {
    // Insert booking
    $stmt = $conn->prepare("
        INSERT INTO Bookings (UserID, BayID, BookingStart, BookingEnd, PriorityStatus)
        VALUES (:userID, :bayID, :start, :end, :priority)
    ");
    $stmt->execute([
        'userID' => $userID,
        'bayID' => $bayID,
        'start' => $start,
        'end' => $end,
        'priority' => $priority
    ]);

    // Mark bay as unavailable
    $updateStmt = $conn->prepare("UPDATE Bays SET IsAvailable = 0 WHERE BayID = :bayID");
    $updateStmt->execute(['bayID' => $bayID]);

    echo "<script>
        alert('Booking confirmed!');
        window.location.href = 'Index.php';
    </script>";
} catch (PDOException $e) {
    echo "Error booking: " . $e->getMessage();
}
?>
