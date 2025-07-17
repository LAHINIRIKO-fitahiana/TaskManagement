<?php
// Mettre à jour la date de fin d'une tâche
include '../includes/db_connect.php';

if (isset($_POST['taskId']) && isset($_POST['newEndDate'])) {
    $taskId = $_POST['taskId'];
    $newEndDate = $_POST['newEndDate'];

    $sql = "UPDATE tasks SET date_fin = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $newEndDate, $taskId);

    if ($stmt->execute()) {
        echo "La date de fin a été mise à jour.";
    } else {
        echo "Erreur lors de la mise à jour.";
    }
    $stmt->close();
    $conn->close();
}
?>
