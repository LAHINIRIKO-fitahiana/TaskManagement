<?php
include '../includes/db_connect.php';

// Marquer les comptes en attente comme vus
$conn->query("UPDATE users SET notification_seen_pending = 1 WHERE status = 'pending'");

// Marquer les tâches validées comme vues
$conn->query("UPDATE tasks SET notification_seen_validated = 1 WHERE status = 'validee' AND etat = 'Non démarrée'");

// Marquer les tâches terminées comme vues
$conn->query("UPDATE tasks SET notification_seen_completed = 1 WHERE status = 'validee' AND etat = 'Terminée'");

echo "All notifications marked as seen";
$conn->close();
?>
