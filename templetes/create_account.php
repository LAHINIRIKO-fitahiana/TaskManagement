<?php
session_start();
include '../includes/db_connect.php';

$error = "";  // Initialiser la variable pour les erreurs
$success = "";  // Initialiser également pour les succès

if (isset($_POST['create_account'])) {
    $IM = $_POST['IM'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hacher le mot de passe
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Préparer la requête d'insertion avec des requêtes préparées
    $sql = "INSERT INTO users (IM, username, email, password, role, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssss", $IM, $username, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            $success = "Compte créé avec succès. En attente de validation.";
        } else {
            $error = "Erreur lors de la création du compte : " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error = "Erreur lors de la préparation de la requête.";
    }
}
?>
<div class="container-sm py-3">
    <div class="card shadow-lg p-4">
        <h2 class="text-center mb-4 text-primary">Créer un compte</h2>
        <form method="post" action="">
            <div class="text-center mb-4">
                <h3>Bienvenue !</h3>
                <p>Commencez à gérer vos activités plus rapidement et efficacement.</p>
            </div>

            <!-- Messages de succès ou d'erreur -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Champ IM -->
            <div class="mb-3">
                <label for="IM" class="form-label">IM :</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Entrez votre IM" name="IM" id="IM" required>
                    <span class="input-group-text"><i class="fa-sharp fa-solid fa-id-badge"></i></span>
                </div>
            </div>

            <!-- Champ Nom d'utilisateur -->
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur :</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Entrez votre nom d'utilisateur" name="username" id="username" required>
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
            </div>

            <!-- Champ Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Entrez votre adresse email" name="email" id="email" required>
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                </div>
            </div>

            <!-- Champ Rôle -->
            <div class="mb-3">
                <label for="role" class="form-label">Rôle :</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="chef_service">Chef de Service</option>
                    <option value="employé">Agent</option>
                </select>
            </div>

            <!-- Champ Mot de passe -->
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <div class="input-group">
                    <input type="password" class="form-control" placeholder="Entrez votre mot de passe" name="password" id="password" required>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>

            <!-- Boutons -->
            <div class="row">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <button type="submit" name="create_account" class="btn btn-success w-100">
                        <i class="fas fa-user-plus me-2"></i>&nbsp;Créer
                    </button>
                </div>
                <div class="col-12 col-md-6">
                    <a href="home.html" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- CDN Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Validation supplémentaire
    document.querySelector('form').addEventListener('submit', function(event) {
        var password = document.getElementById('password').value;
        if (password.length < 6) {
            alert("Le mot de passe doit contenir au moins 6 caractères.");
            event.preventDefault();
        }
    });
</script>
