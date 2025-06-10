<?php
include('db.php');
 
function promoteFromWaitingList($bayID) {
    global $conn;
    
    // Get the next user on the waiting list
    $stmt = $conn->prepare("SELECT * FROM WaitingList WHERE BayID = ? ORDER BY RequestedAt ASC LIMIT 1");
    $stmt->execute([$bayID]);
    $nextUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nextUser) {
        $userID = $nextUser['UserID'];

        // Insert into Bookings table
        $stmt = $conn->prepare("INSERT INTO Bookings (UserID, BayID, BookingStart, BookingEnd, PriorityStatus) VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR), 0)");
        $stmt->execute([$userID, $bayID]);

        // Mark space as not available again
        $stmt = $conn->prepare("UPDATE Bays SET IsAvailable = 0 WHERE BayID = ?");
        $stmt->execute([$bayID]);

        // Remove from waiting list
        $stmt = $conn->prepare("DELETE FROM WaitingList WHERE UserID = ? AND BayID = ?");
        $stmt->execute([$userID, $bayID]);

        // Insert notification for the user
        $message = "You have been promoted from the waiting list and have been assigned Bay $bayID. You can cancel the booking under 'View My Bookings' if you no longer need it.";
        $stmt = $conn->prepare("INSERT INTO Notifications (UserID, Message) VALUES (?, ?)");
        $stmt->execute([$userID, $message]);

        // (Optional) Notify the user - for now, just print
        echo "User $userID has been promoted from the waiting list for Space $bayID.";
    }
}

 
// Example usage:
if (isset($_GET['bayID'])) {
    promoteFromWaitingList(intval($_GET['bayID']));
}
?>
