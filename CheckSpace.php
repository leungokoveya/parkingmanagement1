<?php
session_start();
require_once 'db.php';

if (!isset($_GET['bayID']) || !is_numeric($_GET['bayID'])) {
    die("❌ Invalid bay ID.");
}

$bayID = intval($_GET['bayID']);

if (!isset($_SESSION['UserID'])) {
    echo "<script>
        alert('Please log in to proceed.');
        window.location.href = 'Login.php';
    </script>";
    exit;
}

$userID = $_SESSION['UserID'];
$role = $_SESSION['Role'] ?? 'Guest';

try {
    $stmt = $conn->prepare("SELECT BayID, SpaceID, IsAvailable FROM Bays WHERE BayID = :bayID");
    $stmt->execute(['bayID' => $bayID]);
    $bay = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$bay) {
        throw new Exception("Bay not found.");
    }

    $spaceID = $bay['SpaceID'];
    $isAvailable = $bay['IsAvailable'];

    // ✅ Bay is available
    if ($isAvailable) {
        // Check for existing booking (for normal users only)
        if ($role !== 'Facility Manager') {
            $now = date('Y-m-d H:i:s');
            $bookingCheck = $conn->prepare("SELECT * FROM Bookings WHERE UserID = :userID AND BookingEnd > :now");
            $bookingCheck->execute(['userID' => $userID, 'now' => $now]);
            if ($bookingCheck->rowCount() > 0) {
                echo "<script>
                    alert('You already have an active booking. Please cancel it before booking a new bay.');
                    window.location.href = 'Index.php';
                </script>";
                exit;
            }
        }

        echo "<script>
            alert('Bay is available. Redirecting to booking...');
            window.location.href = 'Booking.php?spaceID=$spaceID&bayID=$bayID&available=1';
        </script>";
        exit;
    }

    // ❌ Bay is not available
    if ($role === 'Facility Manager') {
        echo "<script>
            if (confirm('This bay is unavailable. Do you want to proceed with a priority booking?')) {
                window.location.href = 'Booking.php?spaceID=$spaceID&bayID=$bayID&priority=1';
            } else {
                window.location.href = 'Index.php';
            }
        </script>";
    } else {
        echo "<script>
            let choice = confirm('This bay is currently unavailable.\\n\\nWould you like to join the waiting list for this bay?\\nPress Cancel to search for another bay.');
            if (choice) {
                window.location.href = 'JoinWaitingList.php?spaceID=$spaceID&bayID=$bayID';
            } else {
                window.location.href = 'Index.php';
            }
        </script>";
    }

} catch (Exception $e) {
    echo "<script>
        alert('Error: " . addslashes($e->getMessage()) . "');
        window.location.href = 'Index.php';
    </script>";
}
?>
