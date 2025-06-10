<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('db.php'); // Needed for database queries

// Default unread count
$unreadCount = 0;

// Only fetch if user is logged in
if (isset($_SESSION['UserID'])) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Notifications WHERE UserID = ? AND IsRead = 0");
    $stmt->execute([$_SESSION['UserID']]);
    $unreadCount = $stmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>RoppaCorp Carpark Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-white text-gray-800">
    <!-- Navbar -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="images/logo.png" alt="RoppaCorp Logo" class="h-10 w-10">
                <span class="text-xl font-bold text-blue-800 uppercase">RoppaCorp</span>
            </div>
            <nav class="flex gap-4 items-center">
                <a href="Index.php" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
                <a href="Booking.php" class="text-gray-700 hover:text-blue-600 font-medium">Booking</a>
                <a href="AboutUs.php" class="text-gray-700 hover:text-blue-600 font-medium">About Us</a>
                <a href="ContactUs.php" class="text-gray-700 hover:text-blue-600 font-medium">Contact Us</a>

                <div class="relative">
                    <a href="Notifications.php" class="text-gray-700 hover:text-blue-600 font-medium">
                        Notifications
                        <?php if ($unreadCount > 0): ?>
                            <span class="absolute -top-2 -right-3 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5">
                                <?php echo $unreadCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>

                <a href="MyBookings.php" class="text-gray-700 hover:text-blue-600 font-medium">View My Bookings</a>

                <?php if (isset($_SESSION['UserID'])): ?>
                    <!-- If logged in -->
                    <a href="Logout.php" class="px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-50 font-semibold">Logout</a>
                <?php else: ?>
                    <!-- If not logged in -->
                    <a href="Login.php" class="px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50 font-semibold">Sign in</a>
                    <a href="Register.php" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
