<?php
include '../includes/db_connect.php'; // Connexion à la base de données

// Vérifiez si l'ID de la tâche est passé en paramètre dans l'URL
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];

    // Préparer la requête pour récupérer les informations de la tâche à partir de la base de données
    $sql = "SELECT * FROM tasks WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $taskId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $tache = $result->fetch_assoc();
        } else {
            echo "Tâche non trouvée.";
            exit;
        }

        $stmt->close();
    } else {
        echo "Erreur lors de la récupération de la tâche.";
        exit;
    }
} else {
    echo "ID de tâche manquant.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Statut de la Tâche</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <style>
    body {
                font-family: 'Arial', sans-serif; /* Utiliser une police sans-serif moderne */
                background-color: #f8f9fa; /* Couleur de fond légère */
            }

            .form-container {
                background-color: #ffffff; /* Fond blanc pour le formulaire */
                border: 1px solid #ced4da; /* Bordure légère */
                border-radius: 10px; /* Coins arrondis */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Ombre légère */
            }

            h2 {
            font-family: "Imprint MT Shadow";
            }

            .form-control {
                border: 1px solid #007bff; /* Bordure des champs */
                border-radius: 5px; /* Coins arrondis */
            }

            .form-control:focus {
                border-color: #0056b3; /* Couleur de la bordure au focus */
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Ombre au focus */
            }

            .btn-primary {
                background-color: #007bff; /* Couleur du bouton */
                border-color: #007bff; /* Couleur de la bordure du bouton */
            }

            .btn-primary:hover {
                background-color: #0056b3; /* Couleur du bouton au survol */
                border-color: #0056b3; /* Couleur de la bordure du bouton au survol */
            }

            .btn-danger {
                background-color: #dc3545; /* Couleur du bouton Annuler */
                border-color: #dc3545; /* Couleur de la bordure du bouton Annuler */
            }

            .btn-danger:hover {
                background-color: #c82333; /* Couleur du bouton Annuler au survol */
                border-color: #bd2130; /* Couleur de la bordure du bouton Annuler au survol */
            }

            /* Styles pour le conteneur */


    /* Améliorations du champ de sélection */
    .division-selection {
        margin-bottom: 15px;
        text-align: left; /* Centrer le contenu */
    }

    /* Améliorations du champ de sélection */
    .form-select {
        width: 210px; /* Largeur ajustée */
        padding: 10px; /* Espacement interne */
        border: 1px solid black; /* Bordure */
        border-radius: 8px; /* Coins arrondis */
        font-size: 16px; /* Taille de police */
        font-family: 'Times New Roman', Times, serif; /* Ajout de la police */
        background-color: #ffffff; /* Couleur de fond */
        color: #333333; /* Couleur gris foncé pour le texte */
        transition: border-color 0.3s; /* Transition de couleur de bordure */
        appearance: none; /* Supprimer l'apparence par défaut */
        background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"%3E%3Cpath fill="%230056b3" d="M10 12l-6-6h12z"/%3E%3C/svg%3E'); /* Flèche vers le bas */
        background-repeat: no-repeat;
        background-position: right 10px center; /* Positionner la flèche */
        background-size: 12px; /* Taille de la flèche */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    .form-select:hover {
        border-color: #007bff; /* Changement de couleur au survol */
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3); /* Ombre au survol */
    }

    .form-select:focus {
        outline: none; /* Enlève le contour par défaut */
        border-color: #007bff; /* Couleur de bordure au focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Ombre lors du focus */
    }

    /* Styles pour le titre */
    h2 {
        font-family: "Imprint MT Shadow";
        text-align: center;
        margin-bottom: 20px;
    }

    /* Styles pour le tableau */
    .table {
        color: #000000;
        font-size: 16px;
        border-collapse: collapse;
        width: 100%;
    }

    .table thead {
        background-color: #92c4b5;
        color: #ffffff;
    }

    .table thead th {
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #0056b3;
    }

    /* Styles pour les lignes du tableau */
    .table tbody tr {
        transition: background-color 0.3s;
    }

    .table tbody tr:hover {
        background-color: #e2e6ea; /* Couleur de fond au survol */
    }

    /* Styles pour la pagination */
    .pagination {
        display: flex;
        justify-content: flex-end; /* Alignement à droite */
        margin-top: 20px;
    }

    .pagination button {
        background-color: #0056b3;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        margin: 0 5px; /* Espacement entre les boutons */
        cursor: pointer;
        transition: background-color 0.3s, transform 0.2s;
    }

    .pagination button:hover {
        background-color: #007bff;
        transform: translateY(-2px); /* Légère élévation au survol */
    }

    /* Styles généraux pour les appareils mobiles */
    @media (max-width: 768px) {
        .table {
            font-size: 12px;
        }

        h2 {
            font-size: 24px;
        }

        .form-select {
            width: 100%; /* Prendre toute la largeur sur mobile */
        }
    }
    </style>
</head>
<body>
<div class="form-container mt-4">
    <h2 class="text-center mb-4">Modifier le Statut de l'activités</h2>

    <form id="taskForm" method="POST" action="modif_statut.php">
        <div class="form-group">
            <label for="taskTitle">Titre</label>
            <input type="text" class="form-control" id="taskTitle" value="<?php echo htmlspecialchars($tache['title']); ?>" disabled>
        </div>
        <div class="form-group">
            <label for="taskDescription">Description</label>
            <textarea class="form-control" id="taskDescription" rows="5" disabled><?php echo htmlspecialchars($tache['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="taskDeadline">Date de début</label>
            <input type="date" class="form-control" id="taskDeadline" value="<?php echo htmlspecialchars($tache['date_debut']); ?>" disabled>
        </div>
        <div class="form-group">
            <label for="taskCompletionDate">Date de fin</label>
            <input type="date" class="form-control" id="taskCompletionDate" value="<?php echo htmlspecialchars($tache['date_fin']); ?>" disabled>
        </div>
        <div class="form-group">
            <label for="statut">Etat</label>
            <select class="form-control" id="statut" name="etat" required>
                <option value="en_cours" <?php echo $tache['etat'] == 'en_cours' ? 'selected' : ''; ?>>En cours</option>
                <option value="termine" <?php echo $tache['etat'] == 'termine' ? 'selected' : ''; ?>>Terminé</option>
            </select>
        </div>
        <input type="hidden" name="taskId" value="<?php echo $taskId; ?>">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="?page=taches_faire" class="btn btn-secondary">Retour à la liste</a>
    </form>
</div>

<!-- Scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Traitement du formulaire après la soumission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['taskId'], $_POST['etat'])) {
    $taskId = $_POST['id'];
    $etat = $_POST['etat'];

    // Mise à jour de l'état de la tâche
    include '../includes/db_connect.php';

    $sqlUpdate = "UPDATE tasks SET etat = ? WHERE id = ?";
    if ($stmtUpdate = $conn->prepare($sqlUpdate)) {
        $stmtUpdate->bind_param("si", $etat, $taskId);
        
        // Exécuter la requête
        if ($stmtUpdate->execute()) {
            // Redirection dynamique
            header("Location: taches_faire.php?division=$divisionFilter");
            exit();
        } else {
            echo "Erreur lors de la mise à jour.";
        }
        $stmtUpdate->close();
    } else {
        echo "Erreur de préparation de la requête.";
    }

    $conn->close();
}
?>