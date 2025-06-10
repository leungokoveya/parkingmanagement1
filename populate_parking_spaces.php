<?php
include('../db.php'); // Assumes this sets up $conn as your PDO connection

// Configuration: [SiteID => [SpaceType => Count]]
$siteSpaces = [
    1 => ['Electric' => 21, 'Disabled' => 16, 'Visitor' => 12, 'Standard' => 78],
    2 => ['Electric' => 14, 'Disabled' => 10, 'Visitor' => 8,  'Standard' => 71],
    3 => ['Electric' => 12, 'Disabled' => 8,  'Visitor' => 8,  'Standard' => 20],
];

try {
    $conn->beginTransaction();

    foreach ($siteSpaces as $siteID => $spaceTypes) {
        foreach ($spaceTypes as $type => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $stmt = $conn->prepare("INSERT INTO ParkingSpaces (SiteID, SpaceType, BayNumber) VALUES (?, ?, ?)");
                $stmt->execute([$siteID, $type, $i]);
            }
        }
    }

    $conn->commit();
    echo "✅ All 278 ParkingSpaces inserted successfully.";

} catch (PDOException $e) {
    $conn->rollBack();
    echo "❌ Error inserting parking spaces: " . $e->getMessage();
}
?>
