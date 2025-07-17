<?php
$servername = "localhost";
$username = "root";  // Utilisateur par défaut pour WAMP
$password = "";  // Mot de passe par défaut
$dbname = "gestion_taches";

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}
?>
