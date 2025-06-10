<?php
session_start();
include('includes/header.php'); // This already includes the navbar
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    die("<p style='color:red; text-align:center;'>You must be logged in to view notifications.</p>");
}

$userID = $_SESSION['UserID'];

// If a notification ID is provided, mark it as read
if (isset($_GET['notificationID'])) {
    $notificationID = $_GET['notificationID'];
    
    // Update the notification as read
    $stmt = $conn->prepare("UPDATE Notifications SET IsRead = 1 WHERE NotificationID = ?");
    $stmt->execute([$notificationID]);
}

// Fetch notifications for the user
$stmt = $conn->prepare("SELECT * FROM Notifications WHERE UserID = ? ORDER BY DateCreated DESC");
$stmt->execute([$userID]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count unread notifications
$stmt = $conn->prepare("SELECT COUNT(*) FROM Notifications WHERE UserID = ? AND IsRead = 0");
$stmt->execute([$userID]);
$unreadCount = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        .notification-list {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .notification-item {
            padding: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }
        .notification-item:hover {
            background-color: #f0f0f0;
        }
        .notification-item a {
            color: #333;
            text-decoration: none;
            font-weight: bold;
        }
        .notification-item a:hover {
            text-decoration: underline;
        }
        .notification-meta {
            font-size: 0.9em;
            color: #888;
        }
        .unread {
            font-weight: bold;
            color: #e74c3c;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
        }
        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Your Notifications</h1>

    <div class="notification-list">
        <?php if ($notifications): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-item <?php echo $notification['IsRead'] == 0 ? 'unread' : ''; ?>">
                    <a href="Notifications.php?notificationID=<?php echo $notification['NotificationID']; ?>">
                        <?php echo htmlspecialchars($notification['Message']); ?>
                    </a>
                    <div class="notification-meta">
                        <small>Received on: <?php echo $notification['DateCreated']; ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You have no notifications.</p>
        <?php endif; ?>
    </div>


</body>
</html>
