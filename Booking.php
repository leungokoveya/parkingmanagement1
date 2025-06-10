<?php
session_start();
include('includes/header.php');
require_once 'db.php';

$spaceID = $_GET['spaceID'] ?? null;
$bayID = $_GET['bayID'] ?? null;
$isAvailable = isset($_GET['available']) && $_GET['available'] == 1;
$isPriority = isset($_GET['priority']) && $_GET['priority'] == 1;
$role = $_SESSION['Role'] ?? 'Guest';
$userID = $_SESSION['UserID'] ?? null;

// Check for login first
if (!$userID) {
    echo "<p style='color:red; text-align:center;'>You must log in to select a bay for booking. <a href='Login.php'>Click here to log in</a>.</p>";
    include('includes/footer.php');
    exit;
}

// Then check for valid space and bay
if (!$spaceID || !$bayID) {
    echo "<p style='color:red; text-align:center;'>Invalid access – bay or space information is missing.</p>";
    include('includes/footer.php');
    exit;
}

// Restrict booking for normal users with an active booking
if ($role !== 'Facility Manager') {
    $now = date('Y-m-d H:i:s');
    $stmt = $conn->prepare("SELECT * FROM Bookings WHERE UserID = :userID AND BookingEnd > :now");
    $stmt->execute(['userID' => $userID, 'now' => $now]);
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:red; text-align:center;'>You already have an active booking. Cancel it before booking another bay.</p>";
        include('includes/footer.php');
        exit;
    }
}
?>


<section style="padding: 30px; max-width: 600px; margin: auto;">
    <h2 style="text-align:center; font-size: 24px; margin-bottom: 20px;">
        Booking for Bay ID: <?= htmlspecialchars($bayID) ?>
    </h2>

    <?php if ($isPriority): ?>
        <p style="color: green; text-align: center;">Priority booking enabled for Facility Manager.</p>
    <?php elseif ($isAvailable): ?>
        <p style="color: green; text-align: center;">This bay is available for booking.</p>
    <?php endif; ?>

    <form method="POST" action="ConfirmBooking.php" style="display: flex; flex-direction: column; gap: 15px;">
        <input type="hidden" name="spaceID" value="<?= htmlspecialchars($spaceID) ?>">
        <input type="hidden" name="bayID" value="<?= htmlspecialchars($bayID) ?>">
        <?php if ($isPriority): ?>
            <input type="hidden" name="priority" value="1">
        <?php endif; ?>

        <label for="start">Booking Start Date & Time:</label>
        <input type="datetime-local" id="start" name="start" required>

        <label for="end">Booking End Date & Time:</label>
        <input type="datetime-local" id="end" name="end" required>

        <button type="submit" style="padding: 10px; background-color: #004080; color: white; border: none; border-radius: 5px;">
            Confirm Booking
        </button>
    </form>
</section>

<?php include('includes/footer.php'); ?>
