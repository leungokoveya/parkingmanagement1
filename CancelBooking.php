<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    die("You must be logged in to cancel a booking.");
}

$userID = $_SESSION['UserID'];
$bookingID = $_GET['bookingID'] ?? null;

if (!$bookingID || !is_numeric($bookingID)) {
    die("Invalid booking ID.");
}

// Fetch the booking to confirm it belongs to the user
$stmt = $conn->prepare("SELECT * FROM Bookings WHERE BookingID = ? AND UserID = ?");
$stmt->execute([$bookingID, $userID]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found or you don't have permission to cancel it.");
}

$bayID = $booking['BayID'];

// Delete the booking
$stmt = $conn->prepare("DELETE FROM Bookings WHERE BookingID = ?");
$stmt->execute([$bookingID]);

// Update the space as available (make sure it's marked as available for booking again)
$updateSpace = $conn->prepare("UPDATE Bays SET IsAvailable = 1 WHERE BayID = ?");
$updateSpace->execute([$bayID]);

// ✅ Promote next user from the waiting list (ensure this runs after the space is marked as available)
include('CheckWaitingList.php');
promoteFromWaitingList($bayID);

// Provide user feedback and redirect
echo "<script>alert('Booking cancelled successfully.'); window.location.href='MyBookings.php';</script>";
?>
