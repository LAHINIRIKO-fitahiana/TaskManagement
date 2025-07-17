<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Connexion à la base de données
require '../includes/db_connect.php'; // Assurez-vous d'inclure votre fichier de connexion

// Get list of proposed tasks by chef de division
$proposedTasksSql = "SELECT * FROM tasks WHERE status = 'proposee'";

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
?>

<style>
    h1 {
        font-family: "Imprint MT Shadow";
        text-align: left;
        margin-bottom: 20px;
    }
        
        h4 {
                font-family: "Cambria Math";
                text-align: center;
                margin-bottom: 20px;
            }
</style>

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

                    // Requête pour compter les tâches par état, filtrées par date_fin
                    $sql_en_cours = "SELECT etat, COUNT(*) as count 
                        FROM tasks
                        WHERE etat IN ('Non démarrée', 'En cours', 'Non terminée', 'Terminée') 
                            AND status = 'validee'
                            AND date_fin BETWEEN '$currentMonthStart' AND '$currentMonthEnd'
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

                    // Requête pour compter les comptes en attente de validation
                    $pendingAccountsSql = "SELECT COUNT(*) as count FROM users WHERE status = 'pending'";
                    $pendingAccountsResult = $conn->query($pendingAccountsSql);
                    $pendingAccountsCount = $pendingAccountsResult->fetch_assoc()['count'];

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
        <h4>Histogramme de l'état des activités</h4>
        <div class="chart-container">
            <canvas id="tasksHistogram" height="250"></canvas>
        </div>
    </div>
    <div class="col-lg-6">
        <h4>Diagramme en camembert de la répartition des activités</h4>
        <div class="chart-container">
            <canvas id="tasksPieChart" height="250"></canvas>
        </div>
    </div>
</div>
<?php 
// Connexion à la base de données
include '../includes/db_connect.php';

// Obtenir le mois et l'année actuels
$currentMonth = date('m');
$currentYear = date('Y');

// Requête pour récupérer le nombre de tâches par statut, filtrées par mois actuel
$queryEtat = "SELECT etat, COUNT(*) as count 
              FROM tasks 
              WHERE status = 'validee' 
              AND MONTH(date_fin) = ? 
              AND YEAR(date_fin) = ? 
              GROUP BY etat";
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

// Requête pour compter les tâches assignées spécifiquement aux utilisateurs
$queryDivision = "SELECT u.division, COUNT(DISTINCT t.id) AS count
                  FROM tasks t
                  JOIN users u ON t.assigned_to = u.IM
                  WHERE t.status = 'validee' 
                  AND MONTH(t.date_fin) = ? 
                  AND YEAR(t.date_fin) = ? 
                  GROUP BY u.division";

$stmtDivision = $conn->prepare($queryDivision);
$stmtDivision->bind_param('ii', $currentMonth, $currentYear);
$stmtDivision->execute();
$resultDivision = $stmtDivision->get_result();

// Initialisation des données par division
$divisionData = [
    'DEBRFM' => 0,
    'DIVPE' => 0,
    'BAAF' => 0,
    'CIR' => 0,
    'FINANCES LOCALES et TUTELLE DES EPN' => 0
];

// Récupérer les données de la base
while ($row = $resultDivision->fetch_assoc()) {
    switch ($row['division']) {
        case 'DEBRFM':
            $divisionData['DEBRFM'] += $row['count'];
            break;
        case 'DIVPE':
            $divisionData['DIVPE'] += $row['count'];
            break;
        case 'BAAF':
            $divisionData['BAAF'] += $row['count'];
            break;
        case 'CIR':
            $divisionData['CIR'] += $row['count'];
            break;
        case 'FINANCES LOCALES et TUTELLE DES EPN':
            $divisionData['FINANCES LOCALES et TUTELLE DES EPN'] += $row['count'];
            break;
    }
}

// Compter les tâches assignées à 'Tous' séparément
$queryTous = "SELECT COUNT(DISTINCT id) AS count
              FROM tasks
              WHERE status = 'validee' 
              AND assigned_to = 'Tous' 
              AND MONTH(date_fin) = ? 
              AND YEAR(date_fin) = ?";

$stmtTous = $conn->prepare($queryTous);
$stmtTous->bind_param('ii', $currentMonth, $currentYear);
$stmtTous->execute();
$resultTous = $stmtTous->get_result();
$rowTous = $resultTous->fetch_assoc();
$tousCount = $rowTous['count'] ?? 0;

// Ajouter les tâches de 'Tous' à toutes les divisions
if ($tousCount > 0) {
    foreach ($divisionData as $division => $count) {
        $divisionData[$division] += $tousCount;
    }
}

$stmtDivision->close();
$stmtTous->close();
$conn->close();
?>

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Données récupérées depuis PHP, encodées en JSON
    var tasksEtatData = <?php echo json_encode($etatData); ?>;
    var tasksDivisionData = <?php echo json_encode($divisionData); ?>;

    // Configuration de l'histogramme des tâches par état
    var ctxHistogram = document.getElementById('tasksHistogram').getContext('2d');
    var tasksHistogram = new Chart(ctxHistogram, {
        type: 'bar',
        data: {
            labels: ['Non démarrée', 'En cours', 'Terminée', 'Non terminée'],
            datasets: [{
                label: 'Nombre de tâches',
                data: [
                    tasksEtatData.to_do,
                    tasksEtatData.in_progress,
                    tasksEtatData.completed,
                    tasksEtatData.not_completed
                ],
                backgroundColor: [
                    'rgba(75, 142, 192, 0.6)', 
                    'rgba(255, 159, 64, 0.6)',  
                    'rgba(75, 192, 192, 0.6)',  
                    'rgba(255, 99, 132, 0.6)'   
                ],
                borderColor: [
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 99, 132, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Configuration du diagramme en camembert des tâches par division
    var ctxPieChart = document.getElementById('tasksPieChart').getContext('2d');
    var tasksPieChart = new Chart(ctxPieChart, {
        type: 'pie',
        data: {
            labels: ['DEBRFM', 'DIVPE', 'BAAF', 'CIR', 'FINANCES LOCALES et TUTELLE DES EPN'],
            datasets: [{
                data: [
                    tasksDivisionData.DEBRFM,
                    tasksDivisionData.DIVPE,
                    tasksDivisionData.BAAF,
                    tasksDivisionData.CIR,
                    tasksDivisionData['FINANCES LOCALES et TUTELLE DES EPN']
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 100, 235, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)'
                ],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            onClick: function(e, legendItem) {
                var activeSegment = tasksPieChart.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);
                if (activeSegment.length > 0) {
                    var clickedDivision = tasksPieChart.data.labels[activeSegment[0].index];
                    updateHistogram(clickedDivision);
                }
            }
        }
    });
</script>

