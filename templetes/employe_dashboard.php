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

$error = ''; // Variable pour les messages d'erreur
$success = ''; // Variable pour les messages de succès

// Vérifier si l'utilisateur est connecté en tant que chef de division
if (!isset($_SESSION['employeur'])) {
    header("Location: employeur_login.php");
    exit;
}
$adminUsername = $_SESSION['employeur'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Collaboration des taches</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

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
        /* Assurez-vous que les boutons et menus sont cliquables */
button, .dropdown-menu {
    pointer-events: auto;
}

/* Gérer les transitions pour le menu */
.dropdown-menu {
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

.dropdown-menu.show {
    display: block;
    opacity: 1;
}

    </style>
</head>

<body>

<!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="d-flex align-items-center justify-content-center p-3">
            <img src="../assets/images/logo.png" alt="Logo" style="height: 90px;">
        </div>
        <h6 style="text-align: center;">S.R.B. Ihorombe</h6>
        <hr>
        <ul class="nav flex-column">
            <li class="nav-item">
                <button class="btn btn-link nav-link active w-100" onclick="window.location.href='?page=dashboard_emp'">
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
                        <!-- Lien vers les tâches à faire -->
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=taches_faire'">
                                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                                Activités à faire
                            </button>
                        </li>

                        <!-- Lien vers le calendrier des tâches -->
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=calendrier_taches'">
                                <i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>
                                Calendrier des activités
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
        </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content" id="mainContent">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            
            <button class="btn btn-dark mr-3" id="toggleSidebar">
                <i class="fas fa-angle-left"></i> <!-- Icone pour masquer -->
            </button>

            <script>
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
            
            <!-- Toggler/collapsibe Button for mobile -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto" style="font-family: imprint shadow;">
                    <h3>Employeurs</h3>
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
                        const division = ''; // Ici, vous pouvez définir la division si nécessaire

                        if (query !== '') {
                            // Redirige vers la page de recherche avec les paramètres de recherche
                            window.location.href = '?page=search_task&query=' + encodeURIComponent(query) + '&division=' + encodeURIComponent(division);
                        }
                    });
                </script>

<ul class="navbar-nav ml-3">
    <?php
    // Sécurisation de la variable utilisateur
    $user_im = mysqli_real_escape_string($conn, $user_im);

    // Requête pour compter les tâches non vues
    $pendingAccountsSql = "SELECT COUNT(*) as count 
        FROM tasks 
        INNER JOIN users ON tasks.assigned_to = users.IM 
        WHERE tasks.assigned_to = '$user_im' 
        AND tasks.status = 'validee' 
        AND tasks.etat = 'Non démarrée' 
        AND tasks.notification_seen = 0";

    $pendingAccountsResult = $conn->query($pendingAccountsSql);
    $pendingAccountsCount = $pendingAccountsResult->fetch_assoc()['count'];
    ?>
    <!-- Bouton Notification -->
    <li class="nav-item dropdown">
        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false" title="Notifications">
            <i class="fas fa-bell position-relative" style="font-size: 24px;">
                <span class="badge badge-danger position-absolute" style="top: -5px; right: -10px; font-size: 10px; padding: 3px 6px;">
                    <?php echo $pendingAccountsCount; ?>
                </span>
            </i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <div class="dropdown-header text-center">
                <strong>Notifications</strong>
            </div>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" id="drowpdownNewsEtat" href="?page=etat_non_demarrée">
                <i class="fas fa-check-circle mr-2"></i> Nouvelle(s) activité(s) : <span><?php echo $pendingAccountsCount; ?></span>
            </a>
        </div>
    </li>
</ul>

<script>
    // Lorsqu'on clique sur le bouton de notification
    document.getElementById('drowpdownNewsEtat').addEventListener('click', function (e) {
        e.preventDefault(); // Empêche le rechargement de la page

        // Envoi de la requête AJAX pour marquer les notifications comme vues
        fetch('mark_all_notifications_seen.php')
            .then(response => response.text())
            .then(data => {
                console.log(data);
                document.querySelector('.badge-danger').innerText = 0;
            })
            .catch(error => console.error('Error:', error));
    });
</script>

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
    // Gestion du survol des menus déroulants avec JavaScript pur
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
                        case 'dashboard_emp':
                            include 'dashboard_emp.php'; // Contenu du dashboard
                            break;
                        case 'taches_faire':
                            include 'taches_faire.php'; // Contenu pour les tâches à faire
                            break;
                        case 'calendrier_taches':
                            include 'calendrier_taches.php'; // Contenu pour le calendrier des tâches
                            break;
                        case 'search_task':
                            include 'search_task.php'; // Contenu pour la liste des utilisateurs
                            break;
                        case 'rapport':
                            include 'rapport.php'; // Contenu pour les rapports
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
                            include 'dashboard_emp.php'; // Contenu par défaut
                    }
                } else {
                    include 'dashboard_emp.php'; // Contenu par défaut si aucune page n'est spécifiée
                }
                ?>
            </div>
             <!-- pour l'alerte -->
             <?php
            // Inclure la connexion à la base de données
            include '../includes/db_connect.php';

            // Démarrer la session si elle n'est pas déjà démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Vérification de l'identifiant de l'utilisateur
            if (!isset($_SESSION['employeur'])) {
                die("Erreur : L'utilisateur n'est pas connecté.");
            }

            $user_im = $_SESSION['employeur'];

            // Assurez-vous que la connexion est établie
            if (!$conn) {
                die("Erreur de connexion : " . $conn->connect_error);
            }

            // Initialiser l'heure de la dernière alerte
            if (!isset($_SESSION['last_alert_time'])) {
                $_SESSION['last_alert_time'] = 0;
            }

            // Vérifier si le délai de 2 heures est écoulé
            $currentTimestamp = time();
            $timeDifference = $currentTimestamp - $_SESSION['last_alert_time'];

            if ($timeDifference >= 7200) { // 7200 secondes = 2 heures
                $_SESSION['last_alert_time'] = $currentTimestamp;

                // Récupérer la date actuelle et ajouter 2 jours
                $currentDate = date('Y-m-d');
                $limitDate = date('Y-m-d', strtotime('+2 days'));

                // Requête SQL pour récupérer les tâches dont la date limite est proche
                $query = "SELECT id, title, description, username, division, date_debut, date_fin 
                        FROM tasks
                        JOIN users ON tasks.assigned_to = users.IM
                        WHERE tasks.assigned_to = '$user_im'
                        AND date_fin BETWEEN '$currentDate' AND '$limitDate'
                        AND date_fin IS NOT NULL
                        AND tasks.status = 'validee'
                        AND (etat = 'Non démarrée' OR etat = 'En cours')";

                // Exécuter la requête
                $result = mysqli_query($conn, $query);

                // Tableau pour stocker les tâches à alerter
                $tasksToAlert = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $tasksToAlert[] = $row;
                }
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
<!-- Inclure le CDN de SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Récupérer les tâches à alerter depuis le PHP
    var tasksToAlert = <?php echo json_encode($tasksToAlert); ?>;

    // Vérifier s'il y a des tâches à afficher
    <?php if (!empty($tasksToAlert)): ?>
        var alertMessage = "<ul style='list-style-type: none; padding-left: 0;'>";

        // Boucle pour formater chaque tâche
        tasksToAlert.forEach(function(task) {
    alertMessage += "<li style='font-size: 18px; margin-bottom: 20px;'>" +
                    "<b style='font-size: 20px; color: #2c3e50;'>Titre : " + task.title + "</b><br><br>" +
                    "<p style='color: rgb(0, 8, 255); font-size: 18px;'>👤  <b>" + task.username + "</b></p>" +
                    "<h4 style='font-size: 16px; font-style: italic; color: #34495e;'>⏳ Cette activité doit être exécutée avant :</h4>" +
                    "<span style='color: #d9534f; font-weight: bold; font-size: 18px;'>" + task.date_fin + "</span><br>" +
                    "<span style='font-size: 16px; color: #8e44ad;'><em> Veuillez prendre les mesures nécessaires pour respecter cette échéance.</em></span>" +
                    "</li>";
});


        alertMessage += "</ul>";

        // Afficher l'alerte avec SweetAlert2
        Swal.fire({
            title: '<strong style="color: red;"><i class="fas fa-exclamation-triangle"></i> Activité(s) en retard</strong>',
            html: alertMessage,
            icon: 'warning',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6',
            background: '#fff3cd'
        });
    <?php endif; ?>
</script>

</body>

</html>
