<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure le fichier de connexion
include '../includes/db_connect.php';

$tasks = [];  // Tableau pour stocker les tâches existantes

// Récupérer toutes les tâches ayant le statut 'validée' depuis la base de données
$query_tasks = "SELECT id, title FROM tasks WHERE status = 'validee'";
$result_tasks = mysqli_query($conn, $query_tasks);

// Vérifier si la requête a retourné des résultats
if ($result_tasks) {
    while ($row = mysqli_fetch_assoc($result_tasks)) {
        $tasks[] = $row; // Stocker chaque tâche dans le tableau $tasks
    }
}

// Récupérer les données du formulaire si le formulaire est soumis
if (isset($_POST['create'])) {
    // Vérifier si les indices existent dans $_POST avant de les utiliser
    $task_id = isset($_POST['task_id']) ? mysqli_real_escape_string($conn, $_POST['task_id']) : '';
    $task_title = isset($_POST['task_title']) ? mysqli_real_escape_string($conn, $_POST['task_title']) : '';
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : '';
    $task_etat = isset($_POST['task_etat']) ? mysqli_real_escape_string($conn, $_POST['task_etat']) : '';
    $assigned_to = isset($_POST['assigned_to']) ? mysqli_real_escape_string($conn, $_POST['assigned_to']) : '';
    $division = isset($_POST['division']) ? mysqli_real_escape_string($conn, $_POST['division']) : '';
    $date_debut = isset($_POST['date_debut']) ? mysqli_real_escape_string($conn, $_POST['date_debut']) : '';
    $date_fin = isset($_POST['date_fin']) ? mysqli_real_escape_string($conn, $_POST['date_fin']) : '';
    $completion_date = isset($_POST['completion_date']) ? mysqli_real_escape_string($conn, $_POST['completion_date']) : NULL;
    $probleme_rencontre = isset($_POST['probleme_rencontre']) ? mysqli_real_escape_string($conn, $_POST['probleme_rencontre']) : NULL;
    $solution_proposee = isset($_POST['solution_proposee']) ? mysqli_real_escape_string($conn, $_POST['solution_proposee']) : '';

    // Préparer la requête d'insertion
    $sql = "INSERT INTO rapport (rapport_id,task_id, task_title, description, task_etat, assigned_to, division, date_debut, date_fin, completion_date, probleme_rencontre, solution_proposee) 
            VALUES (NULL,'$task_id', '$task_title', '$description', '$task_etat', '$assigned_to', '$division', '$date_debut', '$date_fin', '$completion_date', '$probleme_rencontre', '$solution_proposee')";

    // Exécuter la requête
    if (mysqli_query($conn, $sql)) {
        $success = "Le rapport a été ajouté avec succès.";
    } else {
        $error = "Erreur lors de l'ajout du rapport : " . mysqli_error($conn);
    }
}
?>

<!-- HTML Formulaire -->

<style>
    /* Style pour la page */
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

    h1 {
        font-family: "Imprint MT Shadow";
    }

    .form-control {
        border: 1px solid #007bff;
        border-radius: 5px;
    }

    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }

    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
</style>

<div class="container mt-4">
    <div class="form-container p-4">
        <h1 class="text-center mb-4">Rapport de Tâche</h1>
        <form method="post" action="">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo $success; ?></p>
                <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger">
                <p><?php echo $error; ?></p>
                <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
            </div>
        <?php endif; ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <!-- Sélectionner une tâche existante -->
                    <div class="form-group">
                        <label for="taskId">Sélectionner une Tâche</label>
                        <select class="form-control" id="taskId" name="task_id" required onchange="fetchTaskDetails()">
                            <option value="">Choisissez une tâche</option>
                            <?php
                            // Afficher uniquement les tâches validées dans le champ de sélection
                            foreach ($tasks as $task) {
                                echo "<option value='" . $task['id'] . "'>" . $task['title'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <?php
                        // Vérifier si une session est déjà démarrée avant d'appeler session_start
                        if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                        }

                        // Vérifier si la session contient 'employeur' au lieu de 'username'
                        if (isset($_SESSION['employeur'])) {
                        $username = $_SESSION['employeur'];
                        } else {
                        $username = 'Nom non défini';
                        }
                    ?>
                    <!-- Responsable (utilisateur assigné) -->
                    <div class="form-group">
                        <label for="assignedTo">Responsable</label>
                        <input type="text" class="form-control" id="assignedTo" name="assigned_to" value="<?php echo htmlspecialchars($username); ?>" readonly>
                    </div>



                    <!-- Date de fin réelle -->
                    <div class="form-group">
                        <label for="completionDate">Date de fin réelle</label>
                        <input type="date" class="form-control" id="completionDate" name="completion_date" placeholder="Sélectionnez la date de fin réelle">
                    </div>

                </div>
                <div class="col-md-6 mb-3">

                    <!-- Problème rencontré -->
                    <div class="form-group">
                        <label for="problemeRencontre">Problème Rencontré</label>
                        <textarea class="form-control" id="problemeRencontre" name="probleme_rencontre" rows="3" placeholder="Décrivez le problème rencontré lors de l'exécution de la tâche"></textarea>
                    </div>

                    <!-- Solution proposée -->
                    <div class="form-group">
                        <label for="solutionProposee">Solution Proposée</label>
                        <textarea class="form-control" id="solutionProposee" name="solution_proposee" rows="3" placeholder="Proposez une solution pour résoudre le problème rencontré" required></textarea>
                    </div>
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" name="create" class="btn btn-primary ml-2 btn-lg">
                    <i class="fas fa-paper-plane"></i> Soumettre le Rapport
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function fetchTaskDetails() {
        var taskId = document.getElementById('taskId').value;
        
        if (taskId) {
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_task_details.php?task_id=' + taskId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var taskDetails = JSON.parse(xhr.responseText);
                    document.getElementById('taskTitle').value = taskDetails.title;
                    document.getElementById('taskEtat').value = taskDetails.status;
                    // Autres champs à remplir avec les détails de la tâche si nécessaire
                }
            };
            xhr.send();
        }
    }
</script>

