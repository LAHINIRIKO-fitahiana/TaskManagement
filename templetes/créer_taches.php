<?php
// Inclure le fichier de connexion
include '../includes/db_connect.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = "";  // Initialiser la variable pour éviter des erreurs
$success = "";  // Initialiser également

if (isset($_POST['create'])) {
    $titre = $_POST['title'];
    $description = $_POST['description'];
    $responsable = $_POST['user_IM'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    //$division =  ($responsable === "Tous") ? "Tous" : $_POST['division'];
    $status = 'validee'; // Le statut est fixé à 'validee' pour toutes les nouvelles tâches

    $sql = "INSERT INTO tasks (title, description, assigned_to, date_debut, date_fin, status)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssss", $titre, $description, $responsable, $date_debut, $date_fin, $status);
        
        if ($stmt->execute()) {
            $success = "Tâche créée avec succès et validée.";
        } else {
            $error = "Erreur lors de la création de la tâche : " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Erreur lors de la préparation de la requête.";
    }
}

// Requête SQL pour récupérer les tâches
$query_user = "SELECT IM, username 
                FROM users 
                WHERE status = 'active'";

$result_user = mysqli_query($conn, $query_user);


if ($result_user) {
    while ($row = mysqli_fetch_assoc($result_user)) {
        $users[] = $row; 
    }
}
?>
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

    h1 {
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
</style>

<div class="container mt-4">
    <div class="form-container p-4">
        <h1 class="text-center mb-4">Créer un activité</h1>
        <form method="post" action="">
        <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <p><?php echo $success; ?></p>
                        <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
                    </div>
                <?php endif; ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="taskTitle">Titre</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="taskResponsable">Responsable</label>
                        <select id="user_IM" name="user_IM" class="form-select form-control" required>
                            <option value="">Choisir un responsable...</option>
                            <!-- <option value="Tous">Tous</option> Nouvelle option pour assigner à tous -->
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['IM'] ?>"><?= $user['username'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label for="taskDeadline">Date de début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="taskCompletionDate">Date fin</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                    </div>
                    <div class="form-group">
                        <label for="division">Division</label>
                        <input type="text" class="form-control" id="division" name="division" disabled>
                    </div>
                </div>
            </div>
            <div class="text-right mt-4">
                <button type="submit" name="create" class="btn btn-success ml-2 btn-lg">
                <i class="fas fa-paper-plane"></i>
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function resetForm() {
        // Réinitialiser le formulaire
        document.querySelector('form').reset();
    }
    //recuperé la division de responsable selectionné
    document.getElementById('user_IM').addEventListener('change', function() {
    const userIM = this.value;
    const divisionInput = document.getElementById('division');

    if (userIM === "Tous") {
        divisionInput.value = "Tous";
    } else if (userIM) {
        fetch(`fetch_division.php?user_IM=${userIM}`)
            .then(response => response.text())
            .then(data => {
                if (data && data !== "Division introuvable") {
                    divisionInput.value = data;
                } else {
                    divisionInput.value = "Aucune division trouvée";
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                divisionInput.value = "Erreur de récupération";
            });
    } else {
        divisionInput.value = "";
    }
});
</script>
