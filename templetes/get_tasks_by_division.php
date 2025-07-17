<?php
include '../includes/db_connect.php';

if (isset($_POST['division'])) {
    $division = $_POST['division'];

    // Récupérer les tâches par état pour la division spécifiée
    $query = "SELECT etat, COUNT(*) as count 
              FROM tasks t
              JOIN users u ON u.IM = t.assigned_to
              WHERE t.status = 'validee'
              AND u.division = ?
              GROUP BY etat";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $division);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialisation des données
    $etatData = [
        'to_do' => 0,
        'in_progress' => 0,
        'completed' => 0,
        'not_completed' => 0
    ];

    // Récupérer les données de la base
    while ($row = $result->fetch_assoc()) {
        switch ($row['etat']) {
            case 'Non démarrée':
                $etatData['to_do'] = $row['count'];
                break;
            case 'En cours':
                $etatData['in_progress'] = $row['count'];
                break;
            case 'Terminée':
                $etatData['completed'] = $row['count'];
                break;
            case 'Non terminée':
                $etatData['not_completed'] = $row['count'];
                break;
        }
    }

    $stmt->close();
    $conn->close();

    // Retourner les données en format JSON
    echo json_encode($etatData);
}
?>
