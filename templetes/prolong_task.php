<?php
// Connexion à la base de données
include '../includes/db_connect.php';

// Récupérer les paramètres de la requête
if (isset($_POST['taskId']) && isset($_POST['newDateFin'])) {
    $taskId = $_POST['taskId'];
    $newDateFin = $_POST['newDateFin'];

    // Mettre à jour la tâche dans la base de données
    $query = "UPDATE tasks SET date_fin = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $newDateFin, $taskId);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }
}
?>
