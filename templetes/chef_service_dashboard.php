<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

$error = ''; // Variable pour les messages d'erreur
$success = ''; // Variable pour les messages de succès

// Vérifier si l'utilisateur est connecté en tant que chef de division
if (!isset($_SESSION['chef_service'])) {
    header("Location: chef_service_login.php");
    exit;
}
$adminUsername = $_SESSION['chef_service'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestions des collaboration des taches</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">


    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
            background-color: #f4f7fa; /* Couleur de fond douce */
        }

        

        .sidebar {
            width: 250px;
            background-color: #0d5e48; /* Couleur sombre pour la barre latérale */
            height: 100vh;
            position: fixed;
            left: 0; /* Position fixe à gauche */
            top: 0;
            transition: transform 0.3s ease; /* Transition douce pour le masquage */
            transform: translateX(0); /* Valeur par défaut pour afficher la sidebar */
            z-index: 1000; /* Pour s'assurer que la sidebar est au-dessus */
        }

        .sidebar.hidden {
            transform: translateX(-100%); /* Masquer la sidebar en déplaçant à gauche */
        }

        .sidebar .nav-link {
            color: #ffffff; /* Couleur des liens */
            padding: 10px 20px; /* Espacement */
            display: flex;
            align-items: center;
            transition: background-color 0.3s; /* Transition pour le survol */
            border-radius: 5px; /* Coins arrondis */
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Couleur de fond au survol */
            color: white;
        }

        .nav-link i {
            margin-right: 8px; /* Ajustement de la distance pour les icônes */
        }

        .main-content {
            margin-left: 250px; /* Espace pour la barre latérale */
            transition: margin-left 0.3s; /* Transition douce pour le contenu principal */
            flex-grow: 1; /* Permet au contenu principal de s'étendre */
            padding: 20px; /* Ajout d'espacement autour du contenu principal */
        }

        .main-content.expanded {
            margin-left: 0; /* Pas d'espace lorsque la barre latérale est cachée */
        }

        .navbar {
            background-color: #ffffff; /* Couleur de fond de la navbar */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ombre pour la navbar */
            z-index: 1000; /* S'assurer que la navbar est au-dessus des autres éléments */
        }

        .navbar .nav-link {
            color: #333; /* Couleur des liens de la navbar */
            margin-left: 15px; /* Ajout d'espacement entre les liens */
        }

        .navbar .nav-link:hover {
            color: #007bff; /* Couleur de lien au survol */
        }

        .search-bar {
        width: 300px; /* Largeur fixe pour la barre de recherche */
        }

        .search-bar input {
        border-radius: 12px; /* Coins arrondis */
        }

        .search-bar button {
        border-radius: 12px; /* Coins arrondis */
        }


        .badge-danger {
            background-color: #dc3545; /* Couleur rouge pour les notifications */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none; /* Cacher la barre latérale sur les petits écrans */
            }

            .main-content {
                margin-left: 0; /* Pas de marge pour le contenu principal */
            }
        }

        .small-box {
            position: relative;
            background: #f7f7f7;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ajout d'ombre */
        }

        .small-box:hover {
            transform: scale(1.05);
        }

        .small-box .icon {
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0.2;
        }

        .small-box-footer {
            display: block;
            padding: 8px;
            background: rgba(0, 0, 0, 0.1);
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            transition: background 0.3s;
        }

        .small-box-footer:hover {
            background: rgba(0, 0, 0, 0.2);
        }

        .shadow-sm {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .inner h3 {
            font-size: 2rem;
            margin: 0; /* Suppression de la marge pour une meilleure présentation */
        }

        .inner p {
            font-size: 1rem;
        }

        /* Nouveau style pour le bouton de recherche */
        .search-bar input {
            border-radius: 20px; /* Coins arrondis */
            border: 1px solid #0d5e48; /* Bordure correspondant à la couleur de la barre latérale */
        }

        .search-bar button {
            border-radius: 20px; /* Coins arrondis */
            background-color: #0d5e48; /* Couleur correspondant à la barre latérale */
            color: white; /* Couleur des icônes de bouton */
        }

        .search-bar button:hover {
            background-color: #007bff; /* Changer la couleur au survol */
        }
        h1{
            font-family: "Imprint MT Shadow";
        }
    </style>
</head>

<body>
        
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
            
        <div class="d-flex align-items-center justify-content-center p-3">
            <img src="../assets/images/logo.png" alt="Logo" style="height: 90px;">
        </div>
        <p style="text-align: center">S.R.B. Ihorombe</p>
        <hr>
        <ul class="nav flex-column">
            
            
            <li class="nav-item">
                <button class="btn btn-link nav-link active w-100" onclick="window.location.href='?page=dashboard'">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Tableau de bord</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="btn btn-link nav-link w-100" id="tasksDropdown" data-toggle="collapse" data-target="#taskList" aria-expanded="false" aria-controls="taskList">
                    <i class="fas fa-tasks" style="font-size: 1.2em; margin-right: 8px;"></i>
                    <span style="flex-grow: 1;">Gestion des activités</span>
                    <i class="fas fa-angle-down float-right" style="transition: transform 0.3s; margin-left: 12px;"></i>
                </button>

                <div class="collapse" id="taskList">
                    <ul class="nav flex-column ml-3">
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=créer_taches'">
                                <i  class="fas fa-sticky-note" style="margin-right: 8px;"></i>
                                Créer des activités
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=taches_a_valider'">
                                <i class="fas fa-clipboard-check" style="margin-right: 8px;"></i>
                                Activités à valider
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=taches_faire'">
                                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                                Activités Validées
                            </button>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=rapport'">
                    <i class="fas fa-file-alt"></i>
                    <span>Rapports</span>
                </button>
            </li>
            <li class="nav-item">
                <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=user_list'">
                    <i class="fas fa-users"></i>
                    <span>Liste des utilisateurs</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            
            <button class="btn btn-dark mr-3" id="toggleSidebar">
                <i class="fas fa-angle-left"></i> <!-- Icone pour masquer -->
            </button>
            
            <!-- Toggler/collapsibe Button for mobile -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto" style="font-family: imprint shadow;">
                    <h3>Chef de Service</h3>
                </ul>

                      <!-- Barre de recherche -->
                <form id="searchForm" class="form-inline my-2 my-lg-0 search-bar">
                    <div class="input-group">
                        <input 
                            id="searchInput" 
                            class="form-control" 
                            type="search" 
                            placeholder="Rechercher un activité" 
                            aria-label="Search" 
                            required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
        <script>
            document.getElementById('searchForm').addEventListener('submit', function (e) {
                e.preventDefault(); // Empêche le comportement par défaut du formulaire
                const query = document.getElementById('searchInput').value.trim();

                if (query !== '') {
                    // Redirige vers la page avec le paramètre de recherche
                    window.location.href = '?page=search_task&query=' + encodeURIComponent(query);
                }
            });
        </script>


<ul class="navbar-nav ml-3">
    <?php
    // Comptage des tâches non vues
    $pendingAccountsSql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'proposee' AND notification_seen_proposed = 0";
    $pendingAccountsResult = $conn->query($pendingAccountsSql);
    $pendingAccountsCount = $pendingAccountsResult->fetch_assoc()['count'];

    $finAccountsSql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'validee' AND etat = 'Terminée' AND notification_seen_completed = 0";
    $finAccountsResult = $conn->query($finAccountsSql);
    $finAccountsCount = $finAccountsResult->fetch_assoc()['count'];

    $sum = $pendingAccountsCount + $finAccountsCount;
    ?>
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications">
            <i class="fas fa-bell position-relative" style="font-size: 24px;">
                <span class="badge badge-danger position-absolute" style="top: -5px; right: -10px; font-size: 10px; padding: 3px 6px;">
                    <?php echo $sum; ?>
                </span>
            </i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <div class="dropdown-header text-center">
                <strong>Notifications</strong>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="?page=taches_a_valider">
                <i class="fas fa-user-clock mr-2"></i> Activités en attente de validation : <span><?php echo $pendingAccountsCount; ?></span>
            </a>
            <a class="dropdown-item" href="?page=etat_terminée">
                <i class="fas fa-check-circle mr-2"></i> Nouvelle(s) activité(s) terminée(s) : <span><?php echo $finAccountsCount; ?></span>
            </a>
        </div>
    </li>
</ul>

<!-- User profile -->
<div class="dropdown ml-3">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownUser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user"></i>&nbsp;&nbsp;
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUser">
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="logout.php">
            <i class="fas fa-sign-out-alt mr-2"></i>&nbsp;&nbsp;Se déconnecter
        </a>
    </div>
</div>

<script>
    // Lorsque l'utilisateur clique sur l'icône de notification
    document.getElementById('navbarDropdown').addEventListener('click', function () {
        fetch('mark_notifications_seen.php')
            .then(response => response.text())
            .then(data => {
                console.log(data);
                document.querySelector('.badge-danger').innerText = 0;
            })
            .catch(error => console.error('Error:', error));
    });

    // Gestion du survol des menus déroulants en JavaScript pur
    document.querySelectorAll('nav .dropdown').forEach(function (dropdown) {
        dropdown.addEventListener('mouseenter', function () {
            this.classList.add('show');
            this.querySelector('.dropdown-menu').classList.add('show');
        });
        dropdown.addEventListener('mouseleave', function () {
            this.classList.remove('show');
            this.querySelector('.dropdown-menu').classList.remove('show');
        });
    });
</script>

</nav>

        <!-- redirection -->
        <div class="container mt-4">
            <div class="content">
                <?php
                // Vérifiez si le paramètre 'page' est défini dans l'URL
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];

                    switch ($page) {
                        case 'dashboard':
                            include 'dashboard_chef.php'; // Contenu du dashboard
                            break;
                        case 'créer_taches':
                            include 'créer_taches.php'; // Contenu pour proposer des tâches
                            break;
                        case 'propose_taches':
                            include 'propose_taches.php'; // Contenu pour les tâches à proposer
                            break;
                        case 'taches_a_valider':
                            include 'taches_a_valider.php'; // Contenu pour les tâches à valider
                            break;
                        case 'taches_faire':
                            include 'taches_faire.php'; // Contenu pour les tâches à faire
                            break;
                        case 'modif_statut':
                            include 'modif_statut.php'; // Contenu pour les modification tâches
                            break;
                        case 'search_task':
                            include 'search_task.php'; // Contenu pour la liste des utilisateurs
                            break;
                        case 'rapport':
                            include 'rapport.php'; // Contenu pour les rapports
                            break;
                        case 'user_list':
                            include 'user_list.php'; // Contenu pour la liste des utilisateurs
                            break;
                        case 'etat_en_cours':
                            include 'etat_en_cours.php'; // Contenu pour les rapports
                            break; 
                        case 'etat_non_demarrée':
                            include 'etat_non_demarrée.php'; // Contenu pour les rapports
                            break;    
                        case 'etat_non_terminée':
                            include 'etat_non_terminée.php'; // Contenu pour les rapports
                            break;  
                        case 'etat_terminée':
                            include 'etat_terminée.php'; // Contenu pour les rapports
                            break;                 
                        default:
                            include 'dashboard_chef.php'; // Contenu par défaut
                    }
                } else {
                    include 'dashboard_chef.php'; // Contenu par défaut si aucune page n'est spécifiée
                }
                ?>
            </div>
        </div>
        <?php
                // Inclure la connexion à la base de données
                include '../includes/db_connect.php';

                // Vérifier si l'alerte a déjà été affichée
                if (!isset($_SESSION['alert_shown'])) {
                    $_SESSION['alert_shown'] = false; // Initialiser à false
                }

                // Récupérer la date actuelle et ajouter 2 jours
                $currentDate = date('Y-m-d');
                $limitDate = date('Y-m-d', strtotime('+2 days'));

                // Requête SQL pour récupérer les tâches dont la date limite est dans les 2 jours
                $query = "SELECT t.id, t.title, t.description, u.username, u.division, t.date_debut, t.date_fin
                        FROM tasks t
                        JOIN users u ON u.IM = t.assigned_to
                        WHERE t.date_fin BETWEEN '$currentDate' AND '$limitDate'
                        AND t.date_fin IS NOT NULL
                        AND t.status = 'validee'
                        AND (t.etat = 'Non démarrée' OR t.etat = 'En cours')";


                // Exécuter la requête
                $result = mysqli_query($conn, $query);

                // Tableau pour stocker les tâches à alerter
                $tasksToAlert = [];

                // Récupérer les tâches proches de la date limite
                while ($row = mysqli_fetch_assoc($result)) {
                    $tasksToAlert[] = $row;
                }

                // Fermer la connexion à la base de données
                mysqli_close($conn);
            ?>


</div>
    <!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Vérifier si des tâches doivent être alertées
    var tasksToAlert = <?php echo json_encode($tasksToAlert); ?>;

    <?php if (!$_SESSION['alert_shown'] && !empty($tasksToAlert)): ?>
        // Si des tâches sont proches de la date limite (dans les 2 jours)
        var alertMessage = "<ul style='list-style-type: none; padding-left: 0;'>"; // Liste sans puces

        // Boucle pour ajouter chaque tâche avec une présentation soignée
         tasksToAlert.forEach(function(task) {
            alertMessage += "<li style='font-size: 18px; margin-bottom: 20px;'>" +
                                            "<b style='font-size: 20px; color: #2c3e50;'>Titre: " + task.title + "</b><br><br>" +
                                            "<h4 style='font-size: 16px; font-style: italic; color: #34495e;'>Cette tâche doit être exécutée avant la date limite :</h4>" + 
                                            "<span style='color: #d9534f; font-weight: bold; font-size: 18px;'>" + task.date_fin + "</span><br>" +
                                            "<p style='font-size: 16px; color: black;'>Division concernée : " + task.division + "</p>" +
                                            "<p style='color: rgb(0, 8, 255)'>Responsable: <b> " + task.username + "</b></p><br>" +
                                            "<span style='font-size: 16px; color: #8e44ad;'>Veuillez prendre les mesures nécessaires pour respecter cette échéance.</span>";
            });


        alertMessage += "</ul>";

        // Affichage de l'alerte avec SweetAlert
        Swal.fire({
            title: '<strong style=" color: red;"><i class="fas fa-exclamation-triangle" style=" color: red;"></i> Tâches en retard</strong>',
            html: alertMessage, // Contenu HTML avec une liste formatée
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6', // Couleur du bouton "OK"
            background: '#fff3cd', // Fond de l'alerte
            padding: '2em', // Padding de l'alerte pour plus de confort
            showCloseButton: true, // Afficher un bouton pour fermer l'alerte
            customClass: {
                title: 'alert-title', // Classe personnalisée pour le titre
                popup: 'alert-popup', // Classe personnalisée pour la popup
                confirmButton: 'alert-button' // Classe personnalisée pour le bouton
            }
        }).then(function() {
            location.reload(); // Recharger la page après que l'utilisateur ferme l'alerte
        });

        <?php $_SESSION['alert_shown'] = true; // Marquer l'alerte comme affichée ?>
    <?php endif; ?>

// Lorsqu'on clique sur le bouton de notification
document.getElementById('navbarDropdown').addEventListener('click', function (e) {
    e.preventDefault();  // Empêche le rechargement de la page
    
    var dropdownMenu = this.nextElementSibling;  // Trouve le menu suivant dans le DOM
    var isOpen = dropdownMenu.classList.contains('show');

    // Si le menu est déjà ouvert, on le ferme, sinon on l'ouvre
    if (isOpen) {
        dropdownMenu.classList.remove('show');
        this.setAttribute('aria-expanded', 'false');
    } else {
        dropdownMenu.classList.add('show');
        this.setAttribute('aria-expanded', 'true');
    }
});

    // Script pour basculer la visibilité de la barre latérale
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("mainContent");
    const toggleSidebar = document.getElementById("toggleSidebar");

    toggleSidebar.addEventListener("click", function () {
        sidebar.classList.toggle("hidden");
        mainContent.classList.toggle("expanded");
        toggleSidebar.innerHTML = sidebar.classList.contains("hidden") ? '<i class="fas fa-angle-right"></i>' : '<i class="fas fa-angle-left"></i>';
    });

    // Script pour le dropdown
    document.getElementById('tasksDropdown').addEventListener('click', function () {
        const taskList = document.getElementById('taskList');
        taskList.classList.toggle('collapse');
    });


</script>
</body>

</html>
