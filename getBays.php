<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    echo json_encode(["error" => "Unauthorized access. Please log in."]);
    exit;
}
include 'db.php';

if (isset($_GET['spaceID']) && is_numeric($_GET['spaceID'])) {
    $spaceID = $_GET['spaceID'];

    try {
        $stmt = $conn->prepare("SELECT BayID, BayNumber, IsAvailable FROM Bays WHERE SpaceID = :spaceID");
        $stmt->bindParam(':spaceID', $spaceID, PDO::PARAM_INT);
        $stmt->execute();
        $bays = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Check if bays are found
        if ($bays) {
            echo json_encode($bays);
        } else {
            echo json_encode(["error" => "No bays found for this space."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["error" => "Query error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Space ID not provided or invalid."]);
}
?>
