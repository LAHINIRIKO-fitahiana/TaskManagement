<?php
// Inclure la connexion à la base de données
include '../includes/db_connect.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $im = mysqli_real_escape_string($conn, $_POST['IM']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $division = mysqli_real_escape_string($conn, $_POST['division']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Fixer les valeurs par défaut pour le rôle et le statut
    $role = 'coordonateur';
    $status = 'active';

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insérer les données dans la table users
    $sql = "INSERT INTO users (IM, username, email, password, role, division, status) 
    VALUES ('$im', '$username', '$email', '$hashed_password', '$role', '$division', '$status')";

    // Exécuter la requête et afficher un message
    if ($conn->query($sql) === TRUE) {
        $message = "<div class='alert alert-success'>Compte Admin créé avec succès !</div>";
    } else {
        $message = "<div class='alert alert-danger'>Erreur lors de la création du compte : " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Compte Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #4facfe, #00f2fe);
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            border: 1px solid #007bff;
            border-radius: 5px;
        }

        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        h2 {
            font-weight: bold;
            color: #fff;
        }

        .icon-bg {
            background-color: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 50px;
            height: 50px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .icon-bg i {
            color: #007bff;
            font-size: 24px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <div class="icon-bg">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="mt-3">Ajouter un Compte Admin</h2>
            </div>
            <div class="card p-4">
                <?php if (isset($message)) echo $message; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="IM" class="form-label"><i class="fas fa-id-card"></i>&nbsp;IM</label>
                        <input type="text" class="form-control" id="IM" name="IM" placeholder="Saisir l'IM" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label"><i class="fas fa-user"></i>&nbsp;Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="fas fa-envelope"></i>&nbsp;Adresse Email </label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Adresse Email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label"><i class="fas fa-lock"></i>&nbsp;Mot de Passe </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Mot de passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="division" class="form-label"><i class="fas fa-building"></i>&nbsp;Division</label>
                        <select class="form-control" id="division" name="division" required>
                                <option value="">Sélectionnez une division</option>
                                <option value="tous">Tous</option>
                                <option value="BAAF">BAAF</option>
                                <option value="DEBRFM">DEBRFM</option>
                                <option value="DIVPE">DIVPE</option>
                                <option value="FINANCES LOCALES et TUTELLE DES EPN">FINANCES LOCALES et TUTELLE DES EPN</option>
                                <option value="CIR">CIR</option>
                            </select>
                    </div>
                    <div class="text-end">
                        <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-sign-out-alt me-2"></i>Retour</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Créer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>
