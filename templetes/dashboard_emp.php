<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_im = $_SESSION['employeur'];

// Connexion à la base de données
require '../includes/db_connect.php'; // Assurez-vous d'inclure votre fichier de connexion

// Get list of proposed tasks by chef de division
$proposedTasksSql = "SELECT * FROM tasks WHERE status = 'proposee'";
//$proposedTasks = getTaskData($proposedTasksSql);

// Function to validate or reject a user account creation request
if (isset($_POST['validate_user'])) {
    $IM = $conn->real_escape_string($_POST['IM']);
    $action = $conn->real_escape_string($_POST['validate_user']); // Échappement de l'action pour éviter les erreurs

    if ($action == 'approve') {
        $sql = "UPDATE users SET status = 'active' WHERE IM = '$IM'";
    } elseif ($action == 'reject') {
        $sql = "DELETE FROM users WHERE IM = '$IM'";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php?view=accounts");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'utilisateur : " . $conn->error;
    }
}

// Function to compile or delete tasks proposed by chef de division
if (isset($_POST['compile_task'])) {
    $taskId = intval($_POST['task_id']);
    $action = $conn->real_escape_string($_POST['action']);

    if ($action == 'compile') {
        $sql = "UPDATE tasks SET status = 'compiled' WHERE id = $taskId";
    } else {
        $sql = "DELETE FROM tasks WHERE id = $taskId";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php");
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la tâche : " . $conn->error;
    }
}

// Get list of user account creation requests
$accountRequestsSql = "SELECT * FROM users WHERE status = 'pending'";
$accountRequestsResult = $conn->query($accountRequestsSql);

if (!$accountRequestsResult) {
    die("Erreur de la requête SQL : " . $conn->error);
}

// --- Récupérer les informations de l'utilisateur connecté ---
$userInfoSql = "SELECT * FROM users WHERE IM = ?";
$stmt = $conn->prepare($userInfoSql);
$stmt->bind_param("s", $user_im);
$stmt->execute();
$userInfoResult = $stmt->get_result();

if ($userInfoResult->num_rows == 0) {
    die("Erreur : utilisateur introuvable.");
}

$user = $userInfoResult->fetch_assoc();
$user_division = $user['division']; // Récupérer la division de l'utilisateur connecté

// --- Récupérer les utilisateurs actifs de la même division ---
$RequestsSql = "SELECT * FROM users WHERE status = 'active' AND division = ?";
$stmt = $conn->prepare($RequestsSql);
$stmt->bind_param("s", $user_division);
$stmt->execute();
$RequestsResult = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'aceuil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        h1 {
            font-family: "Imprint MT Shadow";
            text-align: left;
            margin-bottom: 20px;
        }
        
        h4 {
                font-family: "Cambria Math";
                text-align: left;
                margin-bottom: 20px;
            }

            .table {
                color: #000000;
                font-size: 16px;
                border-collapse: collapse;
                width: 100%;
            }

            /* .table thead {
                background-color: #92c4b5;
                color: #ffffff;
                transition: background-color 0.3s;
            } */

            .table thead th {
                position: relative;
                cursor: pointer;
                padding: 12px;
                text-align: center;
                border-bottom: 2px solid #0056b3;
            }

            .table thead th:hover {
                background-color: #0056b3;
            }

            .table tbody tr {
                transition: background-color 0.3s;
            }

            .table tbody tr:hover {
                background-color: #e2e6ea;
            }
            /* Limiter la taille des cellules et afficher les "..." */
            td.Username{
                max-width: 120px;  /* Limiter la largeur de la cellule */
                white-space: nowrap;  /* Empêcher le texte de passer à la ligne */
                overflow: hidden;  /* Masquer tout texte excédentaire */
                text-overflow: ellipsis;  /* Afficher "..." à la fin si le texte est trop long */
            }

            .table-striped tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa;
            }

            .table td {
                padding: 12px;
                border-bottom: 1px solid #dee2e6;
                vertical-align: middle;
                text-align: left;
            }

            .modal-dialog {
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 100vh; /* Assure que la modal reste centrée, même si le contenu est court */
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
    </style>
</head>
<body>
<section class="content">
    <div class="container mt-4">
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>Page d'accueil</h1>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                <?php
                    // Obtenir le mois et l'année actuels
                    $currentMonthStart = date('Y-m-01');  // Premier jour du mois actuel
                    $currentMonthEnd = date('Y-m-t');      // Dernier jour du mois actuel

                    $sql_en_cours = "SELECT etat, COUNT(*) as count 
                        FROM tasks,users
                        WHERE tasks.assigned_to = users.IM 
                        AND tasks.assigned_to = '$user_im' 
                        AND tasks.etat IN ('Non démarrée', 'En cours', 'Non terminée', 'Terminée') 
                        AND tasks.status = 'validee'
                        AND tasks.date_fin BETWEEN '$currentMonthStart' AND '$currentMonthEnd'
                        GROUP BY etat
                    ";
                    $result_en_cours = $conn->query($sql_en_cours);

                    // Initialiser un tableau pour stocker les comptes par état
                    $etat_counts = [
                        'Non démarrée' => 0,
                        'En cours' => 0,
                        'Non terminée' => 0,
                        'Terminée' => 0,
                    ];

                    // Remplir le tableau avec les résultats de la requête
                    if ($result_en_cours->num_rows > 0) {
                        while ($row_en_cours = $result_en_cours->fetch_assoc()) {
                            $etat_counts[$row_en_cours['etat']] = $row_en_cours['count'];
                        }
                    }
                    // Fermer la connexion
                    $conn->close();
                    ?>

                        <!-- Tâches non démarrées -->
                        <div class="col-lg-3 col-md-6 col-12">
                        <div class="small-box bg-info shadow-sm">
                            <div class="inner text-center">
                                <h3><?php echo $etat_counts['Non démarrée']; ?></h3>
                                <p class="text-white">Activités Non démarrée</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-clock fa-3x"></i> <!-- Icône mise à jour -->
                            </div>
                            <a href="?page=etat_non_demarrée" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>


                    <!-- Tâches en cours -->
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="small-box bg-warning shadow-sm">
                            <div class="inner text-center">
                                <h3><?php echo $etat_counts['En cours']; ?></h3>
                                <p class="text-white">Activités en cours</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-spinner fa-3x"></i>
                            </div>
                            <a href="?page=etat_en_cours" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <!-- Tâches terminées -->
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="small-box bg-success shadow-sm">
                            <div class="inner text-center">
                                <h3><?php echo $etat_counts['Terminée']; ?></h3>
                                <p class="text-white">Activités terminées</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-check-circle fa-3x"></i>
                            </div>
                            <a href="?page=etat_terminée" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <!-- Tâches non terminées -->
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="small-box bg-danger shadow-sm">
                            <div class="inner text-center">
                                <h3><?php echo $etat_counts['Non terminée']; ?></h3>
                                <p class="text-white">Activités Non terminée</p>
                            </div>
                            <div class="icon">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                            </div>
                            <a href="?page=etat_non_terminée" class="small-box-footer text-white">Plus d'info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-lg-6">
                    <h4><em>Courbe de l'état des activités</em></h4>
                    <div class="chart-container">
                        <canvas id="tasksCurveLine" width="400" height="280"></canvas>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4 class="justidy-content-center text-align-center align-items-center"><em>
                        Liste des employés sous une division</em></h4>
                    <div class="chart-container">
                        <table class="table table-striped" id="userTable">
                            <thead>
                                <tr>
                                    <th>IM</th>
                                    <th>Noms</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $RequestsResult->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['IM']); ?></td>
                                    <td class="Username"><?php echo htmlspecialchars($row['username']); ?></td>
                                    <td class="d-flex justify-content-center align-items-center">
                                        <!-- Bouton "Voir détail" -->
                                        <button type="button" class="btn btn-info mr-2 show-details-btn" 
                                            data-IM="<?php echo htmlspecialchars($row['IM']); ?>"
                                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                            data-division="<?php echo htmlspecialchars($row['division']); ?>">
                                            <i class="fa fa-eye mr-2"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div class="pagination" id="pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- pour afficher les detaille -->
<div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userDetailModalLabel">Informations d'un employé</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><strong>IM :</strong> <span id="modalUserIM"></span></p>
                <p><strong>Nom :</strong> <span id="modalUserName"></span></p>
                <p><strong>Division :</strong> <span id="modalDivision"></span></p>
            </div>
        </div>
    </div>
</div>

<!-- pour l'histogramme -->
<?php
    // Inclure le fichier de connexion à la base de données
    include '../includes/db_connect.php';

    // Démarrer la session si elle n'est pas déjà démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_im = $_SESSION['employeur'];

    // Assurez-vous que la connexion est établie
    if (!$conn) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // Obtenir le mois et l'année actuels
    $currentMonth = date('m'); // Mois actuel (01 à 12)
    $currentYear = date('Y');  // Année actuelle

    // Requête pour récupérer le nombre de tâches par statut, filtrées par mois actuel
    $queryEtat = "SELECT etat, COUNT(*) as count 
         FROM tasks ,users
        WHERE tasks.assigned_to = users.IM 
        AND tasks.assigned_to = '$user_im' 
        AND tasks.status = 'validee' 
        AND MONTH(date_fin) = ? 
        AND YEAR(date_fin) = ? 
        GROUP BY etat
    ";
    $stmtEtat = $conn->prepare($queryEtat);
    $stmtEtat->bind_param('ii', $currentMonth, $currentYear);
    $stmtEtat->execute();
    $resultEtat = $stmtEtat->get_result();

    // Initialisation des données par défaut
    $etatData = [
        'to_do' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'not_completed' => 0
    ];

    // Récupérer les données de la base
    while ($row = $resultEtat->fetch_assoc()) {
        switch ($row['etat']) {
            case 'Non démarrée':
                $etatData['to_do'] = $row['count'];
                break;
            case 'En cours':
                $etatData['in_progress'] = $row['count'];
                break;
            case 'Terminée':
                $etatData['completed'] = $row['count'];
                break;
            case 'Non terminée':
                $etatData['not_completed'] = $row['count'];
                break;
        }
    }
    $stmtEtat->close();

    // Fermer la connexion à la base de données
    $conn->close();
?>

</section>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('tasksCurveLine').getContext('2d');

        // Configuration des données pour le graphique en courbe
        const data = {
            labels: ['Non démarrée', 'En cours', 'Non terminée', 'Terminée'],
            datasets: [{
                label: 'Nombre d\'activités',
                data: [
                    <?php echo $etatData['to_do']; ?>,
                    <?php echo $etatData['in_progress']; ?>,
                    <?php echo $etatData['not_completed']; ?>,
                    <?php echo $etatData['completed']; ?>
                ],
                borderColor: '#007bff', // Couleur de la courbe
                backgroundColor: 'rgba(0, 123, 255, 0.2)', // Couleur d'ombre sous la courbe
                fill: true, // Remplir sous la courbe
                tension: 0.4, // Lissage de la courbe
                pointBackgroundColor: '#007bff', // Couleur des points
                pointBorderColor: '#fff' // Couleur de la bordure des points
            }]
        };

        // Configuration du graphique
        new Chart(ctx, {
            type: 'line', // Type de graphique en courbe
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true, // Affiche la légende
                        position: 'top'
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Statut des activités',
                            padding: {
                                top: 20 
                            },
                            font: {
                                weight: 'bold', // Définit le titre en gras
                                size: 16 // Taille du texte (facultatif)
                            }
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nombre d\'activités'
                        },
                        beginAtZero: true // L'axe Y commence à 0
                    }
                }
            }
        });
    });

// pagination
function updatePagination() {
    const rowsPerPage = 3;
    const table = document.querySelector('#userTable');
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

// information des utilisateurs
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.show-details-btn').forEach(button => {
        button.addEventListener('click', () => {
            const IM = button.getAttribute('data-IM');
            const username = button.getAttribute('data-username');
            const division = button.getAttribute('data-division');
            
            // Injecter les données dans la modale
            document.getElementById('modalUserIM').textContent = IM;
            document.getElementById('modalUserName').textContent = username;
            document.getElementById('modalDivision').textContent = division;
            
            // Afficher la modale
            $('#userDetailModal').modal('show');
        });
    });
});
</script>
</body>
</html>