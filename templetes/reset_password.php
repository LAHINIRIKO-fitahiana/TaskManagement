<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

// Vérifier si le token est présent dans l'URL
if (!isset($_GET["token"])) {
    die("Token non fourni.");
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

// Requête pour vérifier si le token existe
$sql = "SELECT * FROM users WHERE reset_token_hash = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Erreur de préparation de la requête : ' . $conn->error);
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token invalide.");
}

// Vérifier si le token a expiré
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Le token a expiré.");
}

// Token valide
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: rgba(240, 248, 255, 0.77);
            font-family: 'Arial', sans-serif;
        }

        .container {
            max-width: 500px;
            margin-top: 50px;
        }

        .login_forms {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-family: "Imprint MT Shadow";
            font-size: 2rem;
            color: #007bff;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group label {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn {
            border-radius: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .alert {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
        }

        .icon-btn {
            margin-right: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 5px 0 0 5px;
        }

        .input-group input {
            border-radius: 0 5px 5px 0;
        }

        .reset-container {
            max-width: 500px;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <a href="../home.php" class="btn btn-secondary mb-4">
        <i class="fas fa-arrow-left icon-btn"></i>
    </a>

    <h3>Réinitialisation du Mot de Passe</h3>

    <div class="login_forms reset-container">
        <p class="text-center text-muted">Pour sécuriser votre compte, veuillez entrer un nouveau mot de passe.</p>
        <hr>

        <form action="" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Nouveau mot de passe :</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Entrez votre nouveau mot de passe" required>
            </div>

            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirmez le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirmez votre mot de passe" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                <i class="fas fa-redo-alt"></i> Réinitialiser le mot de passe
            </button>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Vérification du token depuis le formulaire
    $token = $_POST["token"];
    $token_hash = hash("sha256", $token);

    // Récupération des mots de passe
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Vérifier si les deux mots de passe correspondent
    if ($password !== $confirm_password) {
        echo '<div class="alert alert-danger">Les mots de passe ne correspondent pas.</div>';
        exit;
    }

    // Hacher le mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Requête pour mettre à jour le mot de passe et le statut du compte
    $sql = "UPDATE users 
            SET password = ?, status = 'pending', reset_token_hash = NULL, reset_token_expires_at = NULL 
            WHERE reset_token_hash = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Erreur de préparation de la requête : ' . $conn->error);
    }

    $stmt->bind_param("ss", $password_hash, $token_hash);

    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            echo '<div class="alert alert-danger">Erreur : Impossible de mettre à jour le mot de passe. Le token est invalide.</div>';
        } else {
            echo '<div class="alert alert-success">Mot de passe réinitialisé avec succès. Votre compte est en statut "pending".</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Erreur d\'exécution de la requête : ' . $stmt->error . '</div>';
    }

    $stmt->close();
    $conn->close();
}
?>
