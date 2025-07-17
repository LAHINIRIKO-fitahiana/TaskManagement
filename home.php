<?php
include 'includes/db_connect.php';

if (isset($_POST['create_account'])) {
    // Récupération des données du formulaire
    $im = $_POST['IM'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password']; // Hachage du mot de passe
    $role = $_POST['role'];
    $division = $_POST['division'];
    $status = "pending";

    // crypter le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Préparation de la requête SQL
    $sql = "INSERT INTO users (IM, username, email, password, role, division, status) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $im, $username, $email, $hashed_password, $role, $division, $status);

    // Exécution et vérification
    if ($stmt->execute()) {
        echo "<script>alert('Compte créé avec succès. En attente de validation!');</script>";
    } else {
        echo "<script>alert('Erreur lors de la création du compte : " . $stmt->error . "');</script>";
    }

    // Fermeture de la requête et de la connexion
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de collaboration des tâches</title>

    <!-- Bootstrap CSS (added missing Bootstrap link) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: rgb(0, 0, 0);
            margin-top: 80px; /* Adjusting margin due to fixed navbar */
        }

        /* Navbar */
        .home_navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background-color: #fff;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-family: 'Imprint MT Shadow', serif;
            margin: 0;
            text-align: center; /* Centering the h2 */
        }

        .signup-btn .btn {
            background-color: #007bff;
            border-radius: 10px;
            color: white;
            padding: 10px 20px;
        }

        .signup-btn .btn:hover {
            background-color: #0056b3;
        }

        .dropdown-menu {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            padding: 10px 0;
        }

        .dropdown-item {
            color: #181717;
            padding: 10px 20px;
            font-size: 14px;
            transition: background-color 0.3s, color 0.3s;
        }

        .dropdown-item:hover {
            background-color: #007bff;
            color: white;
        }

    /* Historique Section */
        #sections-container{
        background-image: url('https://th.bing.com/th/id/R.b6bcc3d83a627886b05a61ae9dd984fb?rik=UCekDHzaHDtELw&pid=ImgRaw&r=0');
        background-size: cover;
        background-position: center;
        background-attachment: fixed; /* Keeps the background image fixed */
        color: white;
        text-align: center;
        border-radius: 15px;
        box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
        margin-top: 100px;
        position: relative;
        padding: 80px 20px; /* Increased padding */
        height: 630px; /* Increased height */
    }

/* Style for the container that wraps the section-title */
#sections-container {
    display: flex; /* Use flexbox for centering */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    padding: 80px 20px; /* Increased padding */
    height: 550px; /* Increased height */
    position: relative; /* Necessary for absolute positioning of children */
    overflow: hidden; /* Hide any overflow */
}

/* Style for the title */
.section-title {
    display: flex; /* Use flexbox to center content inside the title */
    flex-direction: column; /* Align content in a column */
    justify-content: center; /* Center the content vertically */
    align-items: center; /* Center the content horizontally */
    text-align: center; /* Ensure text is aligned to the center */
    color: #fff;
    background-color: rgba(255, 255, 255, 0.508);
    border-radius: 20px;
    width: auto; /* Adjust width automatically based on content */
    padding: 40px;
    position: relative; /* Keep its relative positioning for internal adjustments */
    box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.2);
    margin: auto; /* Automatically center within parent container */
    letter-spacing: 3px; /* Adjust text spacing */
}

/* Ensure responsiveness for smaller screens */
@media screen and (max-width: 768px) {
    .section-title {
        padding: 20px; /* Reduce padding for smaller screens */
        font-size: 18px; /* Adjust font size for smaller devices */
    }
}


    /* Button adjustment */
    .btn-historique {
        font-size: 15px; /* Increased font size */
        padding: 18px 35px; /* Increased button padding */
        margin-bottom: 5px;
    }

    .navigation-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background-color: #05c431;
        color: white;
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s, background-color 0.3s;
    }

    
    #btnInscrire{
        float: right;
    }

    .navigation-btn.left {
        left: 20px;
    }

    .navigation-btn.right {
        right: 20px;
    }

    .hidden {
        display: none;
    }

        /* Structure Organisationnelle */
        .division-card {
            top: 10px;
            background-color: #f0f0f0;
            padding: 30px; /* Espace interne plus grand */
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            margin: 15px 0; /* Espace entre les cartes */
            width: 100%; /* Occupation complète de la largeur disponible */
            max-width: 500px; /* Augmente la largeur maximale à 500px */
        }

        .division-card h3 {
            font-size: 24px;
            color: #007bff;
        }

        .division-card p {
            font-size: 16px;
            color: #333;
        }

        .division-card i {
            margin-right: 10px;
            color: #007bff;
        }

        footer {
            background-color: #f8f9fa;
            padding: 30px 20px;
            text-align: center;
            border-top: 1px solid #ddd;
        }

        footer p {
            font-size: 16px;
            color: #555;
        }

        footer .social-icons a {
            color: #007bff;
            margin: 0 10px;
            font-size: 24px;
            transition: color 0.3s;
        }

        footer .social-icons a:hover {
            color: #0056b3;
        }


        /* Small screens responsive */
        @media (max-width: 768px) {
            .home_navbar h2 {
                font-size: 18px;
            }
        }

        .modal-body {
            background-color: #f9f9f9; /* Fond léger pour plus de contraste */
            color: #333; /* Couleur du texte */
            font-family: 'Arial', sans-serif; /* Police simple et moderne */
            padding: 20px; /* Ajout de padding pour aérer le contenu */
            border-radius: 8px; /* Coins arrondis pour un look plus doux */
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Légère ombre pour faire ressortir la modal */
        }

        .modal-body p {
            line-height: 1.6; /* Améliorer la lisibilité avec un espacement entre les lignes */
            font-size: 16px; /* Taille de texte lisible */
        }

        .modal-body strong {
            color: #007bff; /* Accentuer la date avec une couleur spécifique */
        }

        .modal-body em {
            font-style: italic; /* Améliorer la lisibilité de l'italique */
            color: #555; /* Couleur plus douce pour l'italique */
        }

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav>
        <div class="home_navbar">
            <div class="log d-flex align-items-center">
                <img src="https://www.dgbf.mg/wp-content/uploads/2024/08/LOGO-MEF-DGBF-300x300.jpg" alt="Logo" style="height: 75px; border-radius: 50%;">            
            </div>
            <h2 class="align-items-center justify-content-center">Service Régional du Budget<br>Ihorombe</h2>
            <div class="d-flex align-items-center mr-4">
                <button class="nav-item dropdown btn btn-secondary ml-3 btn-sm" style="border-radius: 10px; padding: 8px 12px;">
                    <a class="nav-link dropdown-toggle text-white p-0" href="#" id="dropdown04" data-toggle="dropdown">
                        <i class="fas fa-user fa-sm"></i> Se connecter
                    </a>
                    <div class="dropdown-menu">
                        <p class="text-center mb-2">Se connecter en tant que:</p>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="templetes/chef_service_login.php">Chef de Service</a>
                        <a class="dropdown-item" href="templetes/admin_login.php">Admin (Coordonnateur)</a>
                        <a class="dropdown-item" href="templetes/employe_login.php">Employés</a>
                    </div>
                </button>
                <button id="signupBtn" class="btn btn-primary ml-3 btn-sm" style="border-radius: 10px; padding: 8px 12px;">
                    <i class="fas fa-user-plus fa-sm"></i> S'inscrire
                </button>
            </div>            
        </div>
    </nav>

    <div id="sections-container" style="position: relative; overflow: hidden;">
        <section id="section-1" class="py-5 hidden">
            <div class="container">
                <div class="historique-content text-center section-title mb-4">
                    <h2 class="text-center mb-4" style="font-size: 32px; font-family: 'cambria math'; font-weight: 600; color: rgb(0, 8, 255)">Bienvenue sur notre plateforme!</h2>
                    <p class="text-center" style="font-size: 26px; font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; color: rgb(67, 61, 78); line-height: 1.6; max-width: 800px; margin: 0 auto;">
                        Cette application a été conçue pour optimiser la gestion des activités.
                    </p>
                </div>
            </div>
        </section>        
    
    <!-- Historique et Mission -->
    <section id="section-2" class="py-5 hidden">
        <div class="container">
            <div class="historique-content text-center">
                <h1 class="section-title mb-4">
                    <span class="top-text d-block" style="font-size: 60px; font-family: Bradley Hand ITC; font-weight: 600; color:green; text-transform: uppercase;">S . R . B</span><br>
                    <span class="center-text d-block">
                        <span style="font-size: 50px; font-weight: 700; color: blue;">S</span><span style="font-family: Lucida Handwriting; color: #04213f;">ervice&nbsp;</span>  <span style="font-size: 50px; font-weight: 700; color: blue;">R</span><span style="font-family: Lucida Handwriting; color: #04213f;">égional du&nbsp;</span> <span style="font-size: 50px; font-weight: 700; color: blue;">B</span><span style="color: #04213f; font-family: Lucida Handwriting;">udget</span>
                    </span><br>
                    <span class="bottom-text d-block" style="font-size: 36px;font-family:'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif; font-weight: 600; color: #323a38;">Ihorombe</span><br>
                    <span class="mb-1 mr-1 d-block" 
                            style="font-size: 26px; color: #04213f;font-weight: bold; font-family: Bradley Hand ITC; position: absolute; bottom: 0; right: 0; margin: 10px;">
                        <em>Akaiky anao, miasa ho anao!</em>
                    </span>
                </h1>
            </div>
        </div>
    </section>
      <!-- Boutons de navigation -->
    <button class="navigation-btn left" onclick="showPreviousSection()"><i class="fas fa-angle-left"></i></button>
    <button class="navigation-btn right" onclick="showNextSection()"><i class="fas fa-angle-right"></i></button>
    </div>
       
    <!-- Modal pour le formulaire d'inscription -->
    <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-align-center " id="signupModalLabel">Créer un compte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="">
                        <div class="mb-3">
                            <label for="IM" class="form-label">IM :</label>
                            <input type="text" class="form-control" name="IM" id="IM" required placeholder="Votre Immatricule">
                            </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d'utilisateur :</label>
                            <input type="text" class="form-control" name="username" id="username" required  placeholder="Votre nom complet">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email :</label>
                            <input type="email" class="form-control" name="email" id="email" required placeholder="example@gmail.com">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle :</label>
                            <select name="role" id="role" class="form-control" required >
                                <!-- <option value="chef_service">Chef de Service</option> -->
                                <option value="employé">Agent</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="division">Division</label>
                            <select class="form-control" id="division" name="division">
                                <option value="">Sélectionnez une division</option>
                                <option value="BAAF">BAAF</option>
                                <option value="DEBRFM">DEBRFM</option>
                                <option value="DIVPE">DIVPE</option>
                                <option value="FINANCES LOCALES et TUTELLE DES EPN">FINANCES LOCALES et TUTELLE DES EPN</option>
                                <option value="CIR">CIR</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe :</label>
                            <div class="input-group">
                                <!-- Champ de mot de passe avec icône d'œil -->
                                <input type="password" class="form-control" name="password" id="password" required placeholder="Entrez votre mot de passe">
                                <div class="input-group-append">
                                    <!-- <span class="input-group-text" id="toggle-password" style="cursor: pointer;">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </span> -->
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="btnInscrire" name="create_account" class="btn btn-success">
                            <i class="fas fa-user-plus mr-2"></i>&nbsp;Créer 
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Section Organisationnelle -->
    <section id="structure" class="container my-4">
        <!-- Bouton Historique Centré -->
        <div class="text-center mb-5">
            <button 
                class="btn-historique btn btn-primary btn-lg" 
                data-toggle="modal" 
                title="Historique" 
                data-target="#historiqueModal" 
                style="
                    border-radius: 50px; 
                    padding: 15px 30px; 
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); 
                    transition: transform 0.3s; 
                    background-color: #0056b3; 
                    font-size: 18px;">
                <i class="fas fa-history"></i> Historique
            </button>
        </div>
        <h2 class="text-center mb-4">Structure organisationnelle</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-user-tie"></i> Chef de Service</h3><hr>
                    <p>Mise en oeuvre de la politique de l'Etat  en matière budgétaire, représentation de la Direction Générale du Budget et des Finances (DGBF) au niveau de la région Ihorombe.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-money-bill-alt"></i> BAAF</h3>
                    <p style="font-family: 'Times New Roman', Times, serif;">
                        (<em>Bureau des Affaires Administratives et Financières</em>)
                    </p><hr>
                    <p>Gestion du personnel, gestion financiaire internes, gestion des matières.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-home"></i> DIVPE</h3>
                    <p style="font-family: 'Times New Roman', Times, serif;">
                        (<em>Division Patrimoine de l’État</em>)
                    </p><hr>
                    <p>Gestion des deplacements des agents de l'Etat, gestion des matières, gestion des vehicvules administratifs, gestion des logements et bâtiments administratifs.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-chart-bar"></i> Division des Finances Locales et Tutelle des EPN</h3><hr>
                    <p>Appui et formation sur l'élaboration des documents budgétaires des Collectivités Territoriales Décentralisées (CTD). Tutelle budgétaire des EPN.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-file-alt"></i> DEBRFM</h3>
                    <p style="font-family: 'Times New Roman', Times, serif;">
                        (<em>Division Exécution Budgétaire et de Remboursement des Frais Médicaux</em>)
                    </p><hr>
                    <p>Remboursement des frais médicaux des agents du MEF Ihorombe, et des agents retraités dans la région Ihorombe.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="division-card">
                    <h3><i class="fas fa-laptop"></i> CIR</h3>
                    <p style="font-family: 'Times New Roman', Times, serif;">
                        (<em>Centre Informatique Régional</em>)
                    </p><hr>
                    <p>Appui, assistance, et formation des utilisateurs sur les traitements informatiques des dépenses publique.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Centré -->
    <div class="modal fade" id="historiqueModal" tabindex="-1" role="dialog" aria-labelledby="historiqueModalLabel" aria-hidden="true">
        <div class="modal-dialog d-flex align-items-center justify-content-center" style="min-height: 100vh;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historiqueModalLabel">Historique du SRB</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Depuis sa création officielle le <strong>12 décembre 2008</strong>, le SRB a évolué pour s’adapter aux besoins des usagés.
                    Initialement nommé <em><b>Service Régional de l'Exécution Budgétaire</b></em>, il est devenu une Direction Régionale en 2015, puis a pris son appellation actuelle depuis 2019.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Service Régional du Budget - Ihorombe &nbsp; <a href="https://www.google.com/maps" target="_blank">
            <i class="fas fa-map-marker-alt"></i>  Andrfetsena - Ihosy</a>
        </p>
        <div class="social-icons">
            <a href="https://www.facebook.com/dgfagdgfag" target="_blank"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com/MEFmadagascar" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://www.linkedin.com/company/ministere-de-leconomie-et-des-finances/" target="_blank"><i class="fab fa-linkedin"></i></a>
        </div>
    </footer>

    <!-- Bootstrap JS and jQuery (required for dropdown and modal functionality) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $('nav .dropdown').hover(function() {
            var $this = $(this);
            $this.addClass('show');
            $this.find('> a').attr('aria-expanded', true);
            $this.find('.dropdown-menu').addClass('show');
        }, function() {
            var $this = $(this);
            $this.removeClass('show');
            $this.find('> a').attr('aria-expanded', false);
            $this.find('.dropdown-menu').removeClass('show');
        });
        //2ème section
        let currentSectionIndex = 0;
        const sections = document.querySelectorAll("#sections-container section");

        function showSection(index) {
            sections.forEach((section, i) => {
            section.classList.toggle("hidden", i !== index);
            });
            currentSectionIndex = index;
        }

        function showNextSection() {
            const nextIndex = (currentSectionIndex + 1) % sections.length;
            showSection(nextIndex);
        }

        function showPreviousSection() {
            const prevIndex = (currentSectionIndex - 1 + sections.length) % sections.length;
            showSection(prevIndex);
        }

        // Initial display
        showSection(0);
       
        // Lorsque l'utilisateur clique sur le bouton "S'inscrire"
        document.getElementById('signupBtn').addEventListener('click', function() {
            // Remplacer le contenu de la section par le formulaire d'inscription
            document.getElementById('sections-container').innerHTML = '';

            // Ouvrir la modal
            $('#signupModal').modal('show');
        });

        // Validation du mot de passe lors de la soumission du formulaire
        document.querySelector('form').addEventListener('submit', function(event) {
            var password = document.getElementById('password').value;
            if (password.length < 6) {
                alert("Le mot de passe doit contenir au moins 6 caractères.");
                event.preventDefault(); // Empêcher l'envoi du formulaire
            }
        });

        // Recharger la page après la fermeture de la modal
        $('#signupModal').on('hidden.bs.modal', function () {
            location.reload(); // Recharge la page après la fermeture de la modal
        });

         // Fonction pour afficher/masquer le mot de passe
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        togglePassword.addEventListener('click', function () {
            // Vérifie si le mot de passe est masqué ou visible
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;

            // Change l'icône de l'œil
            if (passwordInput.type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
    });
    </script>
</body>

</html>
