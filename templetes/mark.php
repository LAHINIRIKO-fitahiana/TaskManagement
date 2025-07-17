<?php
include '../includes/db_connect.php';

// Mettre à jour les notifications comme vues
$updateSql = "UPDATE tasks SET notification_seen = 1 WHERE status = 'validee' AND etat='Non démarrée'";
if ($conn->query($updateSql) === TRUE) {
    echo "Notifications marked as seen";
} else {
    echo "Error updating notifications: " . $conn->error;
}
$conn->close();
?>
