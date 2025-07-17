<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion avec mysqli
include '../includes/db_connect.php'; // Assurez-vous que $conn est une instance mysqli

// Vérifier si la connexion existe
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$message = ""; // Initialiser le message

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validation de l'email
    $email = trim($_POST["email"]);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Erreur : L'adresse email n'est pas valide.";
    } else {
        // Générer le token de réinitialisation
        try {
            $token = bin2hex(random_bytes(16));
            $token_hash = hash("sha256", $token);
            $expiry = date("Y-m-d H:i:s", time() + 60 * 30); // Expiration dans 30 minutes

            // Préparer la requête SQL avec des placeholders positionnels
            $sql = "UPDATE users 
                    SET reset_token_hash = ?, 
                        reset_token_expires_at = ?
                    WHERE email = ?";

            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Erreur de préparation de la requête : " . $conn->error);
            }

            // Lier les paramètres
            $stmt->bind_param("sss", $token_hash, $expiry, $email);

            // Exécuter la requête
            if ($stmt->execute()) {
                if ($stmt->affected_rows === 0) {
                    $message = "Si l'adresse email est enregistrée, vous recevrez un email pour réinitialiser votre mot de passe.";
                } else {
                    $message = "Un token de réinitialisation a été créé avec succès et un email vous a été envoyé.";
                    // Vous pouvez ajouter ici le code pour envoyer l'email avec le token
                }
            } else {
                $message = "Erreur d'exécution de la requête : " . $stmt->error;
            }

            // Fermer le statement
            $stmt->close();
        } catch (Exception $e) {
            $message = "Erreur lors de la génération du token : " . $e->getMessage();
        }
    }
}

// Fermer la connexion
$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du Mot de Passe</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/login.css">
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
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 5px 0 0 5px;
        }

        .input-group input {
            border-radius: 0 5px 5px 0;
        }

        .icon-btn {
            margin-right: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../home.php" class="btn btn-secondary mr-2" style="float: left;">
            <i class="fas fa-arrow-left icon-btn"></i>
        </a>
        <br><br><br>

        <h3 class="text-center my-4">Réinitialisation du Mot de Passe</h3>

        <!-- Affichage des messages de notification -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-info text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="login_forms">
            <form method="POST" action="">
                <div class="form-group">
                    <p class="text-center text-muted">Veuillez entrer votre adresse e-mail afin de pouvoir réinitialiser votre mot de passe.</p>
                <hr>
                </div>

                <!-- Champ Email -->
                <div class="form-group">
                    <label for="email" class="font-weight-semibold">Email :</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Entrez votre adresse email" required>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="row">
                    <div class="col-12 col-md-6 mb-2"></div>
                    <div class="col-12 col-md-6 mb-2">
                        <button class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-key icon-btn"></i> Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // Assurez-vous que le chemin vers l'autoloader de Composer est correct
    require '../vendor/autoload.php';

    // Vérifiez si le formulaire a été soumis
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Vérifiez si le champ email est défini et valide
        if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $email = $_POST['email'];

            // Création d'une instance PHPMailer
            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->SMTPAuth = true;

                // Configuration du serveur SMTP
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->Username = 'hsafidisoagilbert@gmail.com';
                $mail->Password = 'hbwo pprb uzrt mrof';

                $mail->setFrom('hsafidisoagilbert@gmail.com', 'srb_Ihorombe');
                $mail->addAddress($email);

                // Sujet et corps de l'email
                $mail->isHTML(true);
                $mail->Subject = 'Réinitialisation du mot de passe';
                $mail->Body = '<h3>Bonjour,</h3>
                            <p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous :</p>
                            <p><a href="http://localhost/gestion_taches/templetes/reset_password?token=' . $token . '">http://localhost/gestion_taches/templetes/reset_password?token=' . $token . '</a></p>
                            <p>Si vous n\'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
                            <p>Cordialement,</p>
                            <p>L\'équipe de gestion des activités.</p>';

                // Envoi de l'email
                if ($mail->send()) {
                    echo 'Message envoyé avec succès.';
                }
            } catch (Exception $e) {
                echo "Erreur lors de l'envoi du message : {$mail->ErrorInfo}";
            }
        } else {
            echo 'Erreur : Adresse email invalide ou non fournie.';
        }
    }
?>
