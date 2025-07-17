<?php
session_start(); // Démarre la session

// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

$email= $_POST["email"];
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", token);


$sql="SELECT *FROM users WHERE reset_token_has = ? ";

$stmt = $mysqli-> prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->excute();

$result = $stmt->get_result();
$users = $result->fetch_assoc();

if ($user===null) {
    die("token not found");
}

if ($strtotime($user["reset_token_expires_at"]) <= time()){
    die("token has expired");
}
die("tpken is valid and hasn't expired");



$sql = "UPDATE users 
    set password_hash = ?, 
    set reset_token_hash = null, 
    reset_token_expires_at = null
    WHERE IM = ?";

 $stmt = $mysqli-> prepare($sql);
 $stmt->bind_param("ss", $password_hash, $user["IM"]);
 $stmt->excute();

 echo("Réinitialisation avec succes! Vous pouvez vous connecter pour rejoindre votre activités");
?>