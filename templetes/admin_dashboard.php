<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

$error = ''; // Variable pour les messages d'erreur
$success = ''; // Variable pour les messages de succès

// Vérifier si l'utilisateur est connecté en tant que chef de division
if (!isset($_SESSION['coordonateur'])) {
    header("Location: admin_login.php");
    exit;
}
$adminUsername = $_SESSION['coordonateur'];
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
    <link rel="stylesheet" href="admin_dashboard.css">
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
                <button class="btn btn-link nav-link active w-100" onclick="window.location.href='?page=valid_compte'">
                    <i class="fas fa-clipboard-check"></i>
                    <span>Valider Compte</span>
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
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=propose_taches'">
                                <i  class="fas fa-sticky-note" style="margin-right: 8px;"></i>
                                Proposer un activité
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=taches_faire'">
                                <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                                Activités Validées
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-link nav-link w-100" onclick="window.location.href='?page=plan_taches'">
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
                    <h3>Coordonateur</h3>
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


        <!-- Div pour afficher les résultats de recherche -->
        <div id="searchResults" class="mt-3"></div>

        <ul class="navbar-nav ml-3">
    <?php
    // Comptage des notifications non vues
    $pendingAccountsSql = "SELECT COUNT(*) as count FROM users WHERE status = 'pending' AND notification_seen_pending = 0";
    $pendingAccountsResult = $conn->query($pendingAccountsSql);
    $pendingAccountsCount = $pendingAccountsResult->fetch_assoc()['count'];

    $valideeAccountsCountSql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'validee' AND etat = 'Non démarrée' AND notification_seen_validated = 0";
    $valideeAccountsCountResult = $conn->query($valideeAccountsCountSql);
    $valideeAccountsCount = $valideeAccountsCountResult->fetch_assoc()['count'];

    $finAccountsSql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'validee' AND etat = 'terminée' AND notification_seen_completed = 0";
    $finAccountsResult = $conn->query($finAccountsSql);
    $finAccountsCount = $finAccountsResult->fetch_assoc()['count'];

    $sum = $pendingAccountsCount + $valideeAccountsCount + $finAccountsCount;
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
            <a class="dropdown-item" href="?page=valid_compte">
                <i class="fas fa-user-clock mr-2" ></i> Compte en attente de validation : <span><?php echo $pendingAccountsCount; ?></span>
            </a>
            <a class="dropdown-item" href="?page=taches_faire">
                <i class="fas fa-check-circle mr-2"></i> Nouvelle(s) activité(s) validée(s) : <span><?php echo $valideeAccountsCount; ?></span>
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
        <a href="#" class="dropdown-item" data-toggle="modal" data-target="#createAdminModal">
            <i class="fas fa-user-plus mr-2"></i>&nbsp;&nbsp;Ajouter un autre admin
        </a>
        <a class="dropdown-item" href="logout.php">
            <i class="fas fa-sign-out-alt mr-2"></i>&nbsp;&nbsp;Se déconnecter
        </a>
    </div>
</div>

<script>
    // Lorsque l'utilisateur clique sur l'icône de notification
    document.getElementById('navbarDropdown').addEventListener('click', function () {
        fetch('mark_all_notifications_seen.php')
            .then(response => response.text())
            .then(data => {
                console.log(data);
                document.querySelector('.badge-danger').innerText = 0;
            })
            .catch(error => console.error('Error:', error));
    });

    // Gestion du survol des menus déroulants
    document.querySelectorAll('nav .dropdown').forEach(function(dropdown) {
        dropdown.addEventListener('mouseenter', function () {
            this.classList.add('show');
            this.querySelector('.dropdown-menu').classList.add('show');
        });
        dropdown.addEventListener('mouseleave', function () {
            this.classList.remove('show');
            this.querySelector('.dropdown-menu').classList.remove('show');
        });
    });

    // Affichage du modal pour ajouter un admin
    document.addEventListener('DOMContentLoaded', function () {
        const createAdminModal = document.getElementById('createAdminModal');
        if (createAdminModal) {
            const openModalButton = document.querySelector('[data-target="#createAdminModal"]');
            if (openModalButton) {
                openModalButton.addEventListener('click', function () {
                    new bootstrap.Modal(createAdminModal).show();
                });
            }

            createAdminModal.addEventListener('hidden.bs.modal', function () {
                location.reload();
            });
        }
    });
</script>

</div>
</nav>

<!-- redirection -->
<div class="container mt-4">

    <div class="content">
        <?php
        // Vérifier si le paramètre 'page' est défini dans l'URL
        if (isset($_GET['page'])) {
            $page = $_GET['page'];  // Récupérer la page demandée

            switch ($page) {
                case 'dashboard_cordo':
                    include 'dashboard_cordo.php'; // Contenu du dashboard pour le coordonnateur
                    break;
                case 'valid_compte':
                    include 'valid_compte.php'; // Contenu pour la validation des comptes
                    break;
                case 'propose_taches':
                    include 'propose_taches.php'; // Contenu pour les tâches proposées
                    break;
                case 'taches_faire':
                    include 'taches_faire.php'; // Contenu pour les tâches à faire
                    break;
                case 'modif_statut':
                    include 'modif_statut.php'; // Contenu pour les mise a jour des tâches
                    break;
                case 'plan_taches':
                    include 'plan_taches.php'; // Contenu pour planifier les tâches
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
                case 'rapport':
                    include 'rapport.php'; // Contenu pour les rapports
                    break;
                case 'user_list':
                    include 'user_list.php'; // Contenu pour la liste des utilisateurs
                    break;
                case 'search_task':
                    include 'search_task.php'; // Contenu pour la liste des utilisateurs
                    break;    
                case 'modif_statut': // Vérification pour la page de modification de statut
                    // Validation de l'ID si nécessaire
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        include 'modif_statut.php'; // Inclure le fichier pour la modification du statut
                    } else {
                        header('Location: admin_dashboard.php?page=taches_faire');
                        exit();
                    }
                    break;
                default:
                    include 'dashboard_cordo.php'; // Page par défaut
            }
        } else {
            include 'dashboard_cordo.php'; // Page par défaut si aucune page n'est spécifiée
        }
        ?>
    </div>
</div>
<?php
// Inclure la connexion à la base de données
include '../includes/db_connect.php';

// Initialiser le message à vide
$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et valider les données du formulaire
    $im = isset($_POST['IM']) ? trim($_POST['IM']) : '';
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $division = isset($_POST['division']) ? trim($_POST['division']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Vérifier que les champs requis ne sont pas vides
    if (empty($im) || empty($username) || empty($email) || empty($role) || empty($password)) {
        $message = "<div class='alert alert-danger'>Tous les champs sont obligatoires.</div>";
    } else {
        // Échapper les données pour éviter les injections SQL
        $im = mysqli_real_escape_string($conn, $im);
        $username = mysqli_real_escape_string($conn, $username);
        $email = mysqli_real_escape_string($conn, $email);
        $role = mysqli_real_escape_string($conn, $role);
        $division = mysqli_real_escape_string($conn, $division);
        $password = mysqli_real_escape_string($conn, $password);

        // Fixer les valeurs par défaut pour le rôle et le statut
        $status = 'active';

        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Vérifier si l'IM existe déjà dans la base de données
        $check_sql = "SELECT IM FROM users WHERE IM = '$im'";
        $result = $conn->query($check_sql);

        if ($result->num_rows > 0) {
            $message = "<div class='alert alert-danger'>Un utilisateur avec cet IM existe déjà.</div>";
        } else {
            // Insérer les données dans la table users
            $sql = "INSERT INTO users (IM, username, email, password, role, division, status) 
                    VALUES ('$im', '$username', '$email', '$hashed_password', '$role', '$division', '$status')";

            // Exécuter la requête et afficher un message en fonction du résultat
            if ($conn->query($sql) === TRUE) {
                $message = "<div class='alert alert-success'>Compte Admin créé avec succès !</div>";
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de la création du compte : " . $conn->error . "</div>";
            }
        }
    }
}
?>

        <!-- Modal pour ajouter un admin -->
<div class="modal fade" id="createAdminModal" tabindex="-1" role="dialog" aria-labelledby="createAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="createAdminModalLabel">
                    <i class="fas fa-user-shield mr-2"></i>Ajouter un autre compte Admin
                </h5>
                <button type="button" class="close text-white" title="Fermer" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <?php if (isset($message)) echo $message; ?>
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="IM" class="font-weight-bold"><i class="fas fa-id-card"></i>&nbsp;IM</label>
                                <input type="text" class="form-control" id="IM" name="IM" placeholder="Saisir l'IM" required>
                            </div>
                            <div class="form-group">
                                <label for="username" class="font-weight-bold"><i class="fas fa-user"></i>&nbsp;Nom d'utilisateur</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="font-weight-bold"><i class="fas fa-envelope"></i>&nbsp;Adresse Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" required>
                            </div>
                            <div class="form-group">
                                <label for="role" class="font-weight-bold">Rôle :</label>
                                <select name="role" id="role" class="form-control" required >
                                    <option value="">Sélectionnez un rôle</option>
                                    <option value="chef_service">Chef de Service</option>
                                    <option value="coordonateur">Coordonateur</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password" class="font-weight-bold"><i class="fas fa-lock"></i>&nbsp;Mot de Passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                            </div>
                            <div class="form-group">
                                <label for="division" class="font-weight-bold"><i class="fas fa-building"></i>&nbsp;Division</label>
                                <select class="form-control" id="division" name="division">
                                    <option value="">Sélectionnez une division</option>
                                    <option value="BAAF">BAAF</option>
                                    <option value="DEBRFM">DEBRFM</option>
                                    <option value="DIVPE">DIVPE</option>
                                    <option value="FINANCES LOCALES et TUTELLE DES EPN">FINANCES LOCALES et TUTELLE DES EPN</option>
                                    <option value="CIR">CIR</option>
                                </select>
                            </div>
                            <div class="text-right">
                                <button type="submit" title="créer" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>Créer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                $query = "SELECT id, title, description, username, division, date_debut, date_fin FROM tasks ,users
                WHERE users.IM = tasks.assigned_to
                AND date_fin BETWEEN '$currentDate' 
                AND '$limitDate' 
                AND date_fin IS NOT NULL
                AND tasks.status = 'validee' 
                AND (etat = 'Non démarrée' OR etat = 'En cours')";

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
                                        "<b style='font-size: 20px; color: #2c3e50;'>Titre:" + task.title + "</b><br><br>" + 
                                        "<h4 style='font-size: 16px; font-style: italic; color: #34495e;'>Cette tâche doit être exécutée avant la date limite :</h4>" + 
                                        "<span style='color: #d9534f; font-weight: bold; font-size: 18px;'>" + task.date_fin + "</span><br>" +
                                        "<p style='font-size: 16px; color: black;'>Divison concernée : " + task.division + "</p>" +
                                        "<p style='color: rgb(0, 8, 255)'> Responsable:<b> " + task.username + "</b></p><br>" +
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
