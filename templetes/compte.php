<?php
include '../includes/db_connect.php'; // Chemin vers le fichier de connexion

// Assurez-vous que la connexion est établie avant de continuer
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}
$accountRequestsSql = "SELECT * FROM users WHERE status = 'pending'";
$accountRequestsResult = $conn->query($accountRequestsSql);

if (isset($_POST['validate_user'])) {
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action == 'approve') {
        $sql = "UPDATE users SET status = 'active' WHERE id = $userId";
    } else {
        $sql = "DELETE FROM users WHERE id = $userId";
    }
    $conn->query($sql);
    header("Location: admin_dashboard.php");
}
?>

<table>
    <tr>
        <th>Nom d'utilisateur</th>
        <th>Rôle</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $accountRequestsResult->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['role']; ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="validate_user" value="approve">Approuver</button>
                <button type="submit" name="validate_user" value="reject">Rejeter</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>
