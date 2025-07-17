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

if ($role==='employé') {   
    // Récupération des filtres avec valeurs par défaut
    $divisionFilter = $_GET['division'] ?? '';
    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';

    // Construire la requête SQL dynamique
    $sql = "SELECT * 
    FROM tasks t
    JOIN users u ON (t.assigned_to = 'Tous' OR t.assigned_to = u.IM)
    WHERE t.assigned_to = '$user_im'
    AND t.status = 'validee'";

    $params = [];
    $types = "";
    if (!empty($month)) {
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $sql .= " AND MONTH(date_fin) = ?";
            $params[] = $month;
            $types .= "i"; // i pour integer
        }
    }

    if (!empty($year)) {
        if (is_numeric($year) && $year > 1900) {
            $sql .= " AND YEAR(date_fin) = ?";
            $params[] = $year;
            $types .= "i"; // i pour integer
        }
    }
    $sql .= " ORDER BY date_fin DESC";
} else {
    // Récupération des filtres avec valeurs par défaut
    $divisionFilter = $_GET['division'] ?? '';
    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';

    // Construire la requête SQL dynamique
    $sql = "SELECT * FROM tasks,users 
    WHERE tasks.assigned_to = users.IM
    AND tasks.status = 'validee'";
    $params = [];
    $types = "";
    if (!empty($month)) {
        if (is_numeric($month) && $month >= 1 && $month <= 12) {
            $sql .= " AND MONTH(date_fin) = ?";
            $params[] = $month;
            $types .= "i"; // i pour integer
        }
    }

    if (!empty($year)) {
        if (is_numeric($year) && $year > 1900) {
            $sql .= " AND YEAR(date_fin) = ?";
            $params[] = $year;
            $types .= "i"; // i pour integer
        }
    }

    if (!empty($divisionFilter) && $divisionFilter !== 'all') {
        $sql .= " AND division = ?";
        $params[] = $divisionFilter;
        $types .= "s"; // s pour string
    }
    $sql .= " ORDER BY date_fin DESC";
}

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Récupérer toutes les tâches
$taches = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taches[] = $row;
    }
}

// Vérification si la date de fin est dépassée d'un jour
foreach ($taches as $tache) {
    $date_fin = new DateTime($tache['date_fin']);
    $current_date = new DateTime();
    $interval = $date_fin->diff($current_date);

    // Si la date de fin est dépassée de plus d'un jour, et que l'état est "Non démarrée" ou "En cours"
    if ($interval->days > 1 && ($tache['etat'] === 'Non démarrée' || $tache['etat'] === 'En cours')) {
        $id = $tache['id'];
        $etat = 'Non terminée';

        // Préparer la requête pour mettre à jour l'état de la tâche
        $update_sql = "UPDATE tasks SET etat = ? WHERE id = ? ";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $etat, $id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Cette activité a dépassé sa date de limite et elle sera comme un activité \"Non terminée\".');</script>";
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l\'état de l\'activité.');</script>";
        }
    }
}

// Vérification de la soumission du formulaire pour la mise à jour de l'état de la tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $etat = $_POST['etat']; // Le nouvel état de la tâche

    // Vérifier si l'ID est valide
    if (!empty($id) && is_numeric($id) && !empty($etat)) {
        // Préparer la requête pour mettre à jour l'état de la tâche
        $sql = "UPDATE tasks SET etat = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Lier les paramètres et exécuter la requête
        $stmt->bind_param("si", $etat, $id);
        if ($stmt->execute()) {
            echo "<script>alert('L\'état de la tâche a été mis à jour avec succès.');</script>";
            echo "<script>window.location.href = '?page=taches_faire';</script>"; // Redirection avec pagination et filtre
        } else {
            echo "<script>alert('Erreur lors de la mise à jour de l\'état de la tâche.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Erreur: ID ou état invalide.');</script>";
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
    <link rel="stylesheet" href="taches_faire.css">
</head>
<body>
<div class="form-container p-4">
    <h2 class="text-center mb-4">Liste des Tâches</h2>

    <!-- Section des filtres -->
    <div class="row mb-3">
        <!-- Filtre division -->
        <?php
        
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

        if ($role==='employé') {
            ?>
                <div class="col-md-4 mb-2">
                    <select id="divisionSelect" class="form-select btn-secondary text-primary" onchange="filterTasksByDivision()" disabled>
                        <option value="">Quel division?</option>
                        <option value="all" <?php echo $divisionFilter == 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="BAAF" <?php echo $divisionFilter == 'BAAF' ? 'selected' : ''; ?>>BAAF</option>
                        <option value="DEBRFM" <?php echo $divisionFilter == 'DEBRFM' ? 'selected' : ''; ?>>DEBRFM</option>
                        <option value="DIVPE" <?php echo $divisionFilter == 'DIVPE' ? 'selected' : ''; ?>>DIVPE</option>
                        <option value="FINANCES LOCALES et TUTELLE DES EPN" <?php echo $divisionFilter == 'FINANCES LOCALES et TUTELLE DES EPN' ? 'selected' : ''; ?>>FINANCES LOCALES et TUTELLE DES EPN</option>
                        <option value="CIR" <?php echo $divisionFilter == 'CIR' ? 'selected' : ''; ?>>CIR</option>
                    </select>
                </div>

                <!-- Filtre mois -->
                <div class="col-md-4 mb-2">
                            <?php
                    $moisFrancais = [
                        '01' => 'Janvier','02' => 'Février','03' => 'Mars','04' => 'Avril','05' => 'Mai','06' => 'Juin','07' => 'Juillet','08' => 'Août',
                        '09' => 'Septembre','10' => 'Octobre','11' => 'Novembre','12' => 'Décembre',
                    ];
                    ?>
                    <label for="monthFilter" class="form-label">Mois :</label>
                    <select id="monthFilter" class="form-select" onchange="applyFilters()">
                        <option value="">Tous les mois</option>
                        <?php foreach ($moisFrancais as $key => $mois): ?>
                            <option value="<?php echo $key; ?>"><?php echo $mois; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Filtre année -->
                <div class="col-md-4 mb-2">
                    <label for="yearFilter" class="form-label">Année :</label>
                    <select id="yearFilter" class="form-select" onchange="applyFilters()">
                        <option value="">Toutes les années</option>
                        <?php 
                        for ($year = 2024; $year <= date('Y') + 5; $year++): 
                        ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <?php
        } else {
            ?>
            <div class="col-md-4 mb-2">
                <select id="divisionSelect" class="form-select" onchange="filterTasksByDivision()">
                    <option value="">Quel division?</option>
                    <option value="all" <?php echo $divisionFilter == 'all' ? 'selected' : ''; ?>>Tous</option>
                    <option value="BAAF" <?php echo $divisionFilter == 'BAAF' ? 'selected' : ''; ?>>BAAF</option>
                    <option value="DEBRFM" <?php echo $divisionFilter == 'DEBRFM' ? 'selected' : ''; ?>>DEBRFM</option>
                    <option value="DIVPE" <?php echo $divisionFilter == 'DIVPE' ? 'selected' : ''; ?>>DIVPE</option>
                    <option value="FINANCES LOCALES et TUTELLE DES EPN" <?php echo $divisionFilter == 'FINANCES LOCALES et TUTELLE DES EPN' ? 'selected' : ''; ?>>FINANCES LOCALES et TUTELLE DES EPN</option>
                    <option value="tutelle" <?php echo $divisionFilter == 'tutelle' ? 'selected' : ''; ?>>TUTELLE DES EPN</option>
                </select>
            </div>

            <!-- Filtre mois -->
            <div class="col-md-4 mb-2">
                        <?php
                $moisFrancais = [
                    '01' => 'Janvier','02' => 'Février','03' => 'Mars','04' => 'Avril','05' => 'Mai','06' => 'Juin','07' => 'Juillet','08' => 'Août',
                    '09' => 'Septembre','10' => 'Octobre','11' => 'Novembre','12' => 'Décembre',
                ];
                ?>
                <label for="monthFilter" class="form-label">Mois :</label>
                <select id="monthFilter" class="form-select" onchange="applyFilters()">
                    <option value="">Tous les mois</option>
                    <?php foreach ($moisFrancais as $key => $mois): ?>
                        <option value="<?php echo $key; ?>"><?php echo $mois; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Filtre année -->
            <div class="col-md-4 mb-2">
                <label for="yearFilter" class="form-label">Année :</label>
                <select id="yearFilter" class="form-select" onchange="applyFilters()">
                    <option value="">Toutes les années</option>
                    <?php 
                    for ($year = 2024; $year <= date('Y') + 5; $year++): 
                    ?>
                        <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
            <?php
        }
        ?>
    
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
                <td colspan="7" class="text-center">Aucun activité à faire</td>
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
                <h5 class="modal-title" id="taskModalLabel">Mettre à jour l'État de la activité</h5>
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
                     <!--<div class="form-group">
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