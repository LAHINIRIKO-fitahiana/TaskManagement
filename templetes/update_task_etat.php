<?php
// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

// Vérifier si l'ID de la tâche et la nouvelle date de fin sont passés via la requête POST
if (isset($_POST['taskId']) && isset($_POST['newEndDate'])) {
    $taskId = $_POST['taskId'];
    $newEndDate = $_POST['newEndDate'];

    // Requête SQL pour mettre à jour la date de fin de la tâche
    $sql = "UPDATE tasks SET date_fin = ? WHERE id = ?";

    // Préparer la requête pour éviter les injections SQL
    if ($stmt = $conn->prepare($sql)) {
        // Lier les paramètres (nouvelle date de fin et ID de la tâche)
        $stmt->bind_param("si", $newEndDate, $taskId);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "La date de fin a été prolongée avec succès !";
        } else {
            echo "Erreur lors du prolongement de la tâche : " . $conn->error;
        }

        // Fermer la requête préparée
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
} else {
    echo "ID de tâche ou nouvelle date de fin non fournis.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
