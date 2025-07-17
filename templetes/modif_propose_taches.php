<?php
// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Démarrer le tampon de sortie
ob_start();

// Connexion à la base de données
require '../includes/db_connect.php';

// Récupérer l'ID de la tâche à modifier depuis l'URL
$id = $_GET['id'] ?? '';

if (empty($id)) {
    echo "ID de la tâche non spécifié.";
    exit;
}

// Récupérer la tâche depuis la base de données
$query = "SELECT * FROM tasks WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$tache = $result->fetch_assoc();

if (!$tache) {
    echo "Tâche non trouvée.";
    exit;
}

// Afficher l'ID de la tâche pour le débogage
// echo "ID de la tâche : " . htmlspecialchars($tache['id']);

// Mettre à jour la tâche si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['Title'];
    $description = $_POST['description'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $division = $_POST['division'];

    $update_query = "UPDATE tasks SET title = ?, description = ?, date_debut = ?, date_fin = ?, division = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssi", $titre, $description, $date_debut, $date_fin, $division, $id);

    if ($update_stmt->execute()) {
        header("Location: tache_propose.php"); // Redirige après mise à jour
        exit;
    } else {
        echo "Erreur lors de la mise à jour de la tâche.";
    }
}

// Terminer le tampon de sortie et envoyer tout le contenu
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la Tâche</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
    }
    .form-container {
        background-color: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        color: #007bff;
        font-weight: 600;
    }
    .form-control {
        border: 1px solid #007bff;
        border-radius: 5px;
    }
    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    .btn-danger:hover {
        background-color: #c82333;
    }
</style>

<div class="container mt-4">
    <div class="form-container p-4">
        <h2 class="text-center mb-4">Modifier la tâche proposée</h2>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="taskTitle">Titre</label>
                        <input type="text" class="form-control" id="Title" name="Title" value="<?php echo htmlspecialchars($tache['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($tache['description']); ?></textarea>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="taskDeadline">Début de la date</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo $tache['date_debut']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="taskCompletionDate">Date d'Achèvement</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo $tache['date_fin']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="userIM">IM Utilisateur</label>
                        <input type="text" class="form-control" id="IM" value="<?php echo $_SESSION['IM'] ?? ''; ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="division">Division</label>
                        <select class="form-control" id="division" name="division" required>
                            <option value="">Sélectionnez une division</option>
                            <option value="division Budgétaire" <?php if ($tache['division'] == 'division Budgétaire') echo 'selected'; ?>>Planification Budgétaire</option>
                            <option value="division patrimoine" <?php if ($tache['division'] == 'division patrimoine') echo 'selected'; ?>>Responsable patrimoine</option>
                            <option value="division finance locale" <?php if ($tache['division'] == 'division finance locale') echo 'selected'; ?>>Finance locale</option>
                            <option value="CIR" <?php if ($tache['division'] == 'CIR') echo 'selected'; ?>>Centre Informatique Régionale</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="text-right mt-4">
                <a href="tache_propose.php" class="btn btn-danger ml-4">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" class="btn btn-success ml-2">
                    ✔️ Appliquer
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
