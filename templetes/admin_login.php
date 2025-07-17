<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_im = $_POST['IM'];
    $password = $_POST['password'];

    // Débogage : afficher les valeurs des variables
    var_dump($user_im);
    var_dump($password);
    

    // Sélectionner l'utilisateur admin
    $sql = "SELECT * FROM users 
            WHERE IM = '$user_im'  
            AND (role = 'coordonateur' OR role = 'chef_service')";
    $result = $conn->query($sql);

    // Débogage : vérifier si la requête a échoué
    if ($result === false) {
        die("Erreur SQL: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        foreach($user as $utilisateur){
            $status = $user['status'];
            $role = $user['role'];
                    // Vérifier si le compte est actif
        if (password_verify($password, $user['password'])) {
            $_SESSION['coordonateur'] =  $user_im;  // Enregistrer le nom d'utilisateur en session
            $_SESSION['roleUser'] = $role; 
            header("Location: admin_dashboard.php");  // Rediriger vers le tableau de bord admin
            exit;
        } else {
            $error = "Nom d'utilisateur ou mot de passe invalide.";
        }
    }
    } else {
        $error = "Nom d'utilisateur ou mot de passe invalide.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'cdn.php' ?>
    <title>Connexion Admin</title>
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/login.css">
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
                        <img src="../assets/images/d.png" alt="">
                        <div class="swiper_text">
                            <p>
                                Validation des créations des comptes
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <img src="../assets/images/c.png" alt="">
                        <div class="swiper_text">
                            <p>
                                Compiler les tâches proposés
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
                <p>Connectez-vous pour gérer vos activités</p>
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
                    <i class="fas fa-sign-in-alt me-2"></i> connexion</button>
                </div>
                <div class="text-center">
                    <a href="mod_password.php" role="">
                        Mots de passe oublié?
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="../assets/jss/swiper.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
