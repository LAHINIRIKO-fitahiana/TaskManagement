<?php
// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

// Vérifier si l'ID de la tâche est passé via la requête POST
if (isset($_POST['taskId'])) {
    $taskId = $_POST['taskId'];

    // Requête SQL pour mettre à jour l'état de la tâche à "Non terminée"
    $sql = "UPDATE tasks SET etat = 'Non terminée' WHERE id = ?";

    // Préparer la requête pour éviter les injections SQL
    if ($stmt = $conn->prepare($sql)) {
        // Lier le paramètre (taskId)
        $stmt->bind_param("i", $taskId);

        // Exécuter la requête
        if ($stmt->execute()) {
            echo "Tâche annulée avec succès !";
        } else {
            echo "Erreur lors de l'annulation de la tâche : " . $conn->error;
        }

        // Fermer la requête préparée
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête : " . $conn->error;
    }
} else {
    echo "ID de tâche non fourni.";
}

// Fermer la connexion à la base de données
$conn->close();
?>
