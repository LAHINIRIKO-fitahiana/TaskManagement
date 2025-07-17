<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_im = $_POST['IM'];
    $password = $_POST['password'];

    // Afficher pour débogage
    // echo "Username: $username, Password: $password"; // Décommenter pour déboguer

    // Sélectionner l'utilisateur chef de service avec le statut 'active'
    $sql = "SELECT * FROM users WHERE IM = ? AND role = 'chef_service'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_im);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si la requête a échoué
    if ($result === false) {
        die("Erreur SQL: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier si le compte est actif
        if ($user['status'] != 'active') {
            $error = "Votre compte n'est pas encore activé. Veuillez attendre l'approbation de l'admin.";
        } else {
            foreach($user as $utilisateur){
                $status = $user['status'];
                $role = $user['role'];
                        // Vérifier si le compte est actif
    
            if (password_verify($password, $user['password'])) {
                $_SESSION['chef_service'] = $user_im;  // Enregistrer le nom d'utilisateur en session
                $_SESSION['roleUser'] = $role; 
                header("Location: chef_service_dashboard.php");  // Rediriger vers le tableau de bord chef de service
                exit;
            } else {
                $error = "Matricule ou mot de passe invalide. (Mot de passe incorrect)";
            }
        }
    }
    } else {
        $error = "Matricule ou mot de passe invalide. (Utilisateur introuvable)";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'cdn.php' ?>
    <title>Connexion Chef de Service</title>
    <!-- CDN Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Optionnel : CDN Font Awesome pour les icônes -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    <div class="login_all">
        <div class="login_swiper">
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="../assets/images/chef1.png" alt="">
                        <div class="swiper_text">
                            <p>
                            Gestion des tâches validées 
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                    <img src="../assets/images/chef2.png" alt="">
                        <div class="swiper_text">
                            <p>
                                
                                Validation des tâches
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="login_forms">
            <form method="post" action="">
                <a href="../home.php" class="btn btn-secondary" style="margin-top:1px;" title="Retour"><i class="fas fa-arrow-left me-2"></i></a><br><br><br>
                <div class="forms">
                    <h2>Bienvenue!</h2>
                    <p>Connectez-vous pour gérer vos tâches</p>
                </div>
                <?php if ($error): ?>
                <div class="forms error">
                    <p><?php echo $error; ?></p>
                    <i class="fa-regular fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
                </div>
                <?php endif; ?>
                <div class="forms">
                <label>IM :</label>
                <input type="text" placeholder="Entrez votre IM" name="IM" required>
                    <span><i class="fa-sharp fa-solid fa-id-badge"></i></span>
                    </div>
                <div class="forms">
                <label>Mot de passe :</label>
                <input type="password" placeholder="Entrez votre mot de passe" name="password" required>
                    <span><i class="fas fa-lock"></i></span>
                </div>
                <div class="forms">
                    <button type="submit">
                    <i class="fas fa-sign-in-alt me-2" title="Se connecter"></i> Connexion</button>
                </div>
                <div class="text-center">
                    <a href="mod_password.php" role="">
                        Mots de passe oublié?
                    </a> <br><br>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/jss/swiper.js"></script>
</body>

</html>

