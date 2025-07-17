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
        <a href="home.php" class="btn btn-secondary mr-2" style="float: left;">
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
