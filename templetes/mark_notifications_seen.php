<?php
include '../includes/db_connect.php';

// Mettre à jour les tâches proposées comme vues
$updateProposedSql = "UPDATE tasks SET notification_seen_proposed = 1 WHERE status = 'proposee'";
$conn->query($updateProposedSql);

// Mettre à jour les tâches terminées comme vues
$updateCompletedSql = "UPDATE tasks SET notification_seen_completed = 1 WHERE status = 'validee' AND etat = 'Terminée'";
$conn->query($updateCompletedSql);

echo "Notifications marked as seen";
$conn->close();
?>
