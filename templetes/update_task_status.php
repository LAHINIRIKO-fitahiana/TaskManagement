<?php
include '../includes/db_connect.php';

if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifiez et affichez des messages d'erreur spécifiques pour chaque paramètre
if (empty($_POST['id'])) {
   
}
if (empty($_POST['etat'])) {
    
}
if (empty($_POST['division'])) {
   
}

// Si tous les paramètres sont présents, continuez avec la mise à jour
$taskId = $_POST['id'];
$newStatus = $_POST['etat'];
$division = $_POST['division'];

$sql = "UPDATE tasks SET etat = ? WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erreur de préparation de la requête : " . $conn->error);
}

$stmt->bind_param("si", $newStatus, $taskId);

if ($stmt->execute()) {
    header("Location: ?page=taches_faire&division=" . urlencode($division) . "&message=success");
    exit();
} else {
    header("Location: ?page=taches_faire&division=" . urlencode($division) . "&message=error");
    exit();
}

$stmt->close();
$conn->close();
?>
