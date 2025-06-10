<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    echo json_encode(["error" => "Unauthorized access. Please log in."]);
    exit;
}
include 'db.php';

if (isset($_GET['siteID'])) {
    $siteID = $_GET['siteID'];

    try {
        $stmt = $conn->prepare("
            SELECT SpaceID, SpaceType 
            FROM Spaces 
            WHERE SiteID = :siteID
        ");
        $stmt->bindParam(':siteID', $siteID, PDO::PARAM_INT);
        $stmt->execute();
        $spaces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($spaces);
    } catch (PDOException $e) {
        echo json_encode(["error" => "Query error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Site ID not provided"]);
}
?>
