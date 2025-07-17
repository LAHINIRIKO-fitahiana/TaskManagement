<?php
include '../includes/db_connect.php'; // Connexion à la base de données
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_im ; //c , $_SESSION['coordonateur'], =$_SESSION['chef_service']
$role=$_SESSION['roleUser'];

// Assurez-vous que la connexion est établie
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

switch ($_SESSION['roleUser']) {
    case 'employé':
        $user_im= $_SESSION['employeur'];
        break;
    case 'coordonateur':
        $user_im= $_SESSION['coordonateur'];
        break;
    case 'chef_service':
        $user_im= $_SESSION['chef_service'];
        break;
    default:
        $user_im= $_SESSION['employeur'];
        break;
}

// Calcul des dates limites pour le filtre
$currentDate = date('Y-m-01'); // Début du mois actuel
$limitDate = date('Y-m-t'); // Dernier jour du mois actuel

if ($role==='employé') {
    $sql = "SELECT * FROM tasks,users 
        WHERE tasks.assigned_to = users.IM 
        AND tasks.assigned_to = '$user_im' 
        AND tasks.status = 'validee' 
        AND tasks.etat = 'En cours'
        AND tasks.date_fin BETWEEN ? AND ?
        ORDER BY date_fin DESC";
    
} else {
    $sql = "SELECT * FROM tasks,users 
        WHERE tasks.assigned_to = users.IM
        AND tasks.status = 'validee'
        AND tasks.etat = 'En cours'
        AND tasks.date_fin BETWEEN ? AND ?
        ORDER BY date_fin DESC";
}

$params = [$currentDate, $limitDate];
$types = "ss";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);

// Lier les paramètres dynamiquement
$stmt->bind_param($types, ...$params);

// Exécuter la requête
$stmt->execute();
$result = $stmt->get_result();

// Récupérer toutes les tâches trouvées
$taches = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taches[] = $row;
    }
}

// Vérifier si le formulaire a été soumis pour une mise à jour de tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $etat = $_POST['etat']; // Le nouvel état de la tâche

    // Vérifier si l'ID est valide
    if (!empty($id)) {
        // Préparer la requête pour mettre à jour l'état de la tâche
        $sql = "UPDATE tasks SET etat = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres et exécuter la requête
        $stmt->bind_param("si", $etat, $id);
        if ($stmt->execute()) {
            echo "<script>alert('L\'état de la tâche a été mis à jour avec succès.');</script>";
            echo "<script>window.location.href = '?page=etat_terminée';</script>"; // Redirection avec pagination et filtre
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l\'état de la tâche.');</script>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Tâches à Faire</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="modif_statut.css">
    <style>
/* Styles pour le conteneur */
.container {
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 50px;
}

/* Améliorations du champ de sélection */
.division-selection {
    margin-bottom: 15px;
    text-align: left; /* Centrer le contenu */
}

/* Améliorations du champ de sélection */
.form-select {
    width: 210px; /* Largeur ajustée */
    padding: 10px; /* Espacement interne */
    border: 1px solid #0056b3; /* Bordure */
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

.filter-container {
    display: flex;
    gap: 20px;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
}

.filter-container label {
    font-weight: bold;
    margin-bottom: 5px;
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

/* Limiter la taille des cellules et afficher les "..." */
td.title, td.description{
    max-width: 120px;  /* Limiter la largeur de la cellule */
    white-space: nowrap;  /* Empêcher le texte de passer à la ligne */
    overflow: hidden;  /* Masquer tout texte excédentaire */
    text-overflow: ellipsis;  /* Afficher "..." à la fin si le texte est trop long */
}

/* Optionnel: ajouter un style pour le bouton "Voir détail" */
.btn-info {
    font-size: 14px;
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
<div class="form-container p-4">
    <h2 class="text-center mb-4">Liste des activités en cours </h2>
    
    <!-- table des activités -->
    <table class="table table-bordered table-hover table-striped" id="taskTable">
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <p><?php echo $success; ?></p>
                <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <p><?php echo $error; ?></p>
                <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
            </div>
        <?php endif; ?>
        <thead class="table-primary">
            <tr>
                <th>Titre</th>
                <th>Description</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Etat</th>
                <th>Division</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($taches)): ?>
                <?php foreach ($taches as $tache): ?>
                    <tr class="hover-row" data-id="<?php echo $tache['id']; ?>">
                        <td class="title"><?php echo htmlspecialchars($tache['title']); ?></td>
                        <td class="description"><?php echo htmlspecialchars($tache['description']); ?></td>
                        <td><?php echo htmlspecialchars($tache['date_debut']); ?></td>
                        <td><?php echo htmlspecialchars($tache['date_fin']); ?></td>
                        <td><?php echo htmlspecialchars($tache['etat']); ?></td>
                        <td class="description"><?php echo htmlspecialchars($tache['division']); ?></td>
                        <td class="d-flex justify-content-center align-items-center">
                            <button type="button" class="btn btn-success mr-2" data-toggle="modal" title="Mettre à jour" data-target="#taskModal" 
                                    data-id="<?php echo $tache['id']; ?>" 
                                    data-title="<?php echo htmlspecialchars($tache['title']); ?>" 
                                    data-description="<?php echo htmlspecialchars($tache['description']); ?>"
                                    data-date_debut="<?php echo htmlspecialchars($tache['date_debut']); ?>"
                                    data-date_fin="<?php echo htmlspecialchars($tache['date_fin']); ?>"
                                    data-etat="<?php echo htmlspecialchars($tache['etat']); ?>"
                                    data-username="<?php echo htmlspecialchars($tache['username']); ?>"
                                    data-division="<?php echo htmlspecialchars($tache['division']); ?>">
                                <i class="fa fa-edit mr-2"></i>
                            </button>
                            <!-- Bouton "Voir détail" -->
                            <button 
                                type="button" 
                                class="btn btn-info mr-2" 
                                title="Voir les détails" 
                                onclick="showTaskDetails(<?php echo $tache['id']; ?>)"
                                data-title="<?php echo htmlspecialchars($tache['title']); ?>"
                                data-description="<?php echo htmlspecialchars($tache['description']); ?>"
                                data-date_debut="<?php echo htmlspecialchars($tache['date_debut']); ?>"
                                data-date_fin="<?php echo htmlspecialchars($tache['date_fin']); ?>"
                                data-etat="<?php echo htmlspecialchars($tache['etat']); ?>"
                                data-username="<?php echo htmlspecialchars($tache['username']); ?>"
                                data-division="<?php echo htmlspecialchars($tache['division']); ?>">
                                <i class="fa fa-eye mr-2"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Aucune tâche de cette division</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

<!-- Pagination -->
<div class="pagination" id="pagination"></div>

</div>


<!-- Modale pour modifier le statut de la tâche -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskModalLabel">Mettre à jour l'État de la tâche</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form  method="post">
                    <input type="hidden" name="id" id="id" value="">

                    <div class="form-group">
                        <label for="taskTitle">Titre</label>
                        <input type="text" class="form-control" id="taskTitle" disabled>
                    </div>
                    <!-- <div class="form-group">
                        <label for="taskDescription">Description</label>
                        <textarea class="form-control" id="taskDescription" rows="2" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="taskDeadline">Date de début</label>
                        <input type="date" class="form-control" id="taskDeadline" disabled>
                    </div>
                    <div class="form-group">
                        <label for="taskCompletionDate">Date de fin</label>
                        <input type="date" class="form-control" id="taskCompletionDate" disabled>
                    </div>
                    <div class="form-group">
                        <label for="taskResponsable">Responsable</label>
                        <input type="text" class="form-control" id="taskResponsable" disabled>
                    </div>
                    <div class="form-group">
                        <label for="division">Division</label>
                        <input type="text" class="form-control" name="division" id="division" readonly>
                    </div> -->
                    <div class="form-group">
                        <label for="etat">Etat</label>
                        <select class="form-control" name="etat" required>
                            <option value="Non démarrée">Non démarrée</option>
                            <option value="En cours">En cours</option>
                            <option value="terminée">Terminé</option>
                            <option value="Non terminée">Non Terminé</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Annuler</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- pour afficher les detaille -->
<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title  justitfy-content-center align-items-center" id="taskDetailModalLabel">Détails de l'activité</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>Titre :</strong> <span id="modalTaskTitle"></span></p>
                <p><strong>Description :</strong> <span id="modalTaskDescription"></span></p>
                <p><strong>Date de début :</strong> <span id="modalTaskStartDate"></span></p>
                <p><strong>Date de fin :</strong> <span id="modalTaskEndDate"></span></p>
                <p><strong>État :</strong> <span id="modalTaskState"></span></p>
                <p><strong>Résponsable :</strong> <span id="modalTaskResp"></span></p>
                <p><strong>Division :</strong> <span id="modalTaskDivision"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- Scripts Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>

<script>
    function filterTasksByDivision() {
    const selectedDivision = document.getElementById('divisionSelect').value;
    const month = document.getElementById('monthFilter').value;
    const year = document.getElementById('yearFilter').value;
    window.location.href = `?page=taches_faire&division=${selectedDivision}&month=${month}&year=${year}`;
    }

    function applyFilters() {
        const month = document.getElementById('monthFilter').value;
        const year = document.getElementById('yearFilter').value;
        const selectedDivision = document.getElementById('divisionSelect').value;
        window.location.href = `?page=taches_faire&division=${selectedDivision}&month=${month}&year=${year}`;
    }

    // Fonction pour afficher les données dans la modal lors de l'ouverture
    $('#taskModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Bouton qui a déclenché la modale
        var modal = $(this);

        // Remplir les champs de la modale
        modal.find('#taskTitle').val(button.data('title'));
        modal.find('#taskDescription').val(button.data('description'));
        modal.find('#taskDeadline').val(button.data('date_debut'));
        modal.find('#taskCompletionDate').val(button.data('date_fin'));
        modal.find('#division').val(button.data('division'));
        modal.find('#taskResponsable').val(button.data('username'));
        modal.find('select[name="etat"]').val(button.data('etat'));
        
        // Remplir le champ caché ID
        modal.find('#id').val(button.data('id')); // Remplir le champ caché ID avec l'attribut data-id

        // Vérification pour le débogage
        console.log("ID de la tâche dans la modale : " + button.data('id'));
    });

    function updatePagination() {
    const rowsPerPage = 5;
    const table = document.querySelector('#taskTable');
    const totalRows = table.querySelectorAll('tbody tr').length;
    const numPages = Math.ceil(totalRows / rowsPerPage);

    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';

    let currentPage = 1;
    let visibleStart = 1;
    let visibleEnd = Math.min(3, numPages);

    // Scroll buttons
    const leftScrollButton = document.createElement('button');
    leftScrollButton.innerHTML = '<i class="fas fa-angle-left"></i>';
    leftScrollButton.disabled = true;
    leftScrollButton.addEventListener('click', () => {
        if (visibleStart > 1) {
            visibleStart -= 2;
            visibleEnd -= 2;
            renderPaginationButtons();
        }
    });
    paginationContainer.appendChild(leftScrollButton);

    const rightScrollButton = document.createElement('button');
    rightScrollButton.innerHTML = '<i class="fas fa-angle-right"></i>';
    rightScrollButton.disabled = numPages <= 3;
    rightScrollButton.addEventListener('click', () => {
        if (visibleEnd < numPages) {
            visibleStart += 3;
            visibleEnd += 3;
            renderPaginationButtons();
        }
    });
    paginationContainer.appendChild(rightScrollButton);

    // Render pagination buttons
    function renderPaginationButtons() {
        document.querySelectorAll('.page-button').forEach(btn => btn.remove());

        for (let i = visibleStart; i <= visibleEnd; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.classList.add('page-button');
            if (i === currentPage) {
                button.classList.add('active');
            }
            button.addEventListener('click', () => {
                currentPage = i;
                showPage(currentPage);
                renderPaginationButtons();
            });
            paginationContainer.insertBefore(button, rightScrollButton);
        }

        leftScrollButton.disabled = visibleStart <= 1;
        rightScrollButton.disabled = visibleEnd >= numPages;
    }

    // Show rows for the current page
    function showPage(pageNumber) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row, index) => {
            row.style.display = (index >= (pageNumber - 1) * rowsPerPage && index < pageNumber * rowsPerPage) ? '' : 'none';
        });
    }

    showPage(1);
    renderPaginationButtons();
}

updatePagination();

    // Fonction pour afficher les détails de la tâche dans une modale
function showTaskDetails(taskId) {
    // Récupérer les données à partir des attributs du bouton cliqué
    const button = document.querySelector(`button[onclick="showTaskDetails(${taskId})"]`);
    
    // Vérifier si le bouton existe (sécurité supplémentaire)
    if (!button) {
        console.error(`Bouton pour la tâche avec ID ${taskId} introuvable.`);
        return;
    }
    
    const title = button.getAttribute('data-title');
    const description = button.getAttribute('data-description');
    const startDate = button.getAttribute('data-date_debut');
    const endDate = button.getAttribute('data-date_fin');
    const state = button.getAttribute('data-etat');
    const username = button.getAttribute('data-username');
    const division = button.getAttribute('data-division');
    
    // Injecter les données dans les éléments de la modale
    document.getElementById('modalTaskTitle').textContent = title;
    document.getElementById('modalTaskDescription').textContent = description;
    document.getElementById('modalTaskStartDate').textContent = startDate;
    document.getElementById('modalTaskEndDate').textContent = endDate;
    document.getElementById('modalTaskState').textContent = state;
    document.getElementById('modalTaskResp').textContent = username;
    document.getElementById('modalTaskDivision').textContent = division;

    // Afficher la modale
    $('#taskDetailModal').modal('show');
}

</script>

</body>
</html>