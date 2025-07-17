<?php
include '../includes/db_connect.php';

// Date actuelle
$currentDate = date('Y-m-d');

// Calculer le premier jour du mois actuel
$startOfMonth = date('Y-m-01');

// Calculer le dernier jour du mois actuel
$endOfMonth = date('Y-m-t');

// Calculer la date 10 jours après le dernier jour du mois
$endWithExtraDays = date('Y-m-d', strtotime($endOfMonth . ' +10 days'));

// Vérifier si la division a été envoyée via la requête GET
if (isset($_POST['division'])) {
    $division = mysqli_real_escape_string($conn, $_POST['division']);
    
    // Requête pour récupérer les tâches associées à la division
    $query = "SELECT t.id, t.title FROM tasks t
              JOIN users u ON t.user_id = u.id
              WHERE u.division = '$division'";
    $result = mysqli_query($conn, $query);
    
    $tasks = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tasks[] = $row;
        }
    }
    
    // Retourner les tâches sous forme JSON
    echo json_encode($tasks);
}
?>