<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion avec mysqli
include '../includes/db_connect.php'; // Assurez-vous que $conn est une instance mysqli

// Vérifier si la connexion existe
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$email = $_POST["email"];

// Validation de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Erreur : L'adresse email n'est pas valide.");
}

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

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
if (!$stmt->execute()) {
    die("Erreur d'exécution de la requête : " . $stmt->error);
}

// Vérifier si une ligne a été affectée
if ($stmt->affected_rows === 0) {
    echo "Si l'adresse email que vous avez saisie est enregistrée dans notre système, vous recevrez un email contenant un lien pour réinitialiser votre mot de passe.";
} else {
    echo "Un token de réinitialisation a été créé avec succès et un email vous a été envoyé.";
}

// Fermer le statement
$stmt->close();
?>
