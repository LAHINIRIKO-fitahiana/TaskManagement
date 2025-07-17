<?php
include '../includes/db_connect.php';

if (isset($_GET['user_IM'])) {
    $user_IM = mysqli_real_escape_string($conn, $_GET['user_IM']);
    
    // Récupérer les détails de la tâche à partir de la base de données
    $query = "SELECT * FROM user WHERE IM = '$user_IM' AND division=?";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        echo json_encode($user);
    } else {
        echo json_encode([]);
    }
}
?>
