<?php
include '../includes/db_connect.php';

if (isset($_GET['task_id'])) {
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    
    // Récupérer les détails de la tâche à partir de la base de données
    $query = "SELECT * FROM tasks WHERE id = '$task_id'";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $task = mysqli_fetch_assoc($result);
        echo json_encode($task);
    } else {
        echo json_encode([]);
    }
}
?>
