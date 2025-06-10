<?php
session_start();
include('includes/header.php');
include('db.php');

// Ensure user is logged in
if (!isset($_SESSION['UserID'])) {
    echo "<p style='color:red; text-align:center;'>You must be logged in to view your bookings.</p>";
    exit;
}

$userID = $_SESSION['UserID'];

// Fetch user bookings
$stmt = $conn->prepare("SELECT * FROM Bookings WHERE UserID = ? ORDER BY BookingStart DESC");
$stmt->execute([$userID]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section style="max-width: 800px; margin: auto; padding: 20px;">
    <h2 style="text-align:center;">My Bookings</h2>

    <?php if (count($bookings) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; margin-top: 20px;">
            <tr>
                <th>Booking ID</th>
                <th>Bay ID</th>
                <th>Start</th>
                <th>End</th>
                <th>Action</th>
            </tr>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= $booking['BookingID'] ?></td>
                    <td><?= $booking['BayID'] ?></td>
                    <td><?= $booking['BookingStart'] ?></td>
                    <td><?= $booking['BookingEnd'] ?></td>
                    <td>
                        <a href="CancelBooking.php?bookingID=<?= $booking['BookingID'] ?>"
                           onclick="return confirm('Are you sure you want to cancel this booking?');"
                           style="color: red;">Cancel</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="text-align:center;">You have no bookings yet.</p>
    <?php endif; ?>
</section>
<?php include('includes/footer.php'); ?>