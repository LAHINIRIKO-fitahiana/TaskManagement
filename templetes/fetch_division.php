<?php
include '../includes/db_connect.php';

header('Content-Type: application/json');

// Récupération des données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['division'])) {
    $division = $data['division'];

    // Sélectionner les tâches de la division
    $query = "SELECT id, title FROM tasks WHERE division = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $division);
        $stmt->execute();
        $result = $stmt->get_result();

        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }

        echo json_encode($tasks);
        $stmt->close();
    } else {
        echo json_encode(["error" => "Erreur de requête"]);
    }

    $conn->close();
} else {
    echo json_encode(["error" => "Aucune division fournie"]);
}
?>
