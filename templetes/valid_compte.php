<?php
include '../includes/db_connect.php'; // Chemin vers le fichier de connexion

// Assurez-vous que la connexion est établie avant de continuer
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifiez les requêtes en attente
$accountRequestsSql = "SELECT * FROM users WHERE status = 'pending'";
$accountRequestsResult = $conn->query($accountRequestsSql);

if (isset($_POST['validate_user'])) {
    $userId = trim($_POST['user_id']);
    $action = $_POST['validate_user'];
    $stmt = null;

    if ($action == 'approve') {
        $sql = "UPDATE users SET status = 'active' WHERE IM = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $message = "Compte validé avec succès!";
        $messageType = "success";
    } else {
        $sql = "DELETE FROM users WHERE IM = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userId);
        $message = "Compte rejeté!";
        $messageType = "error";
    }

    if ($stmt && $stmt->execute()) {
        echo "<script>
                document.getElementById('modalMessage').innerText = '$message';
                $('#messageModal').modal('show');
                setTimeout(function() {
                    location.reload();
                }, 2000);
              </script>";
    } else {
        $message = "Erreur lors de la mise à jour de l'utilisateur : " . $conn->error;
        echo "<script>
                document.getElementById('modalMessage').innerText = '$message';
                $('#messageModal').modal('show');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des Compte utilisateurs</title>

    <!-- Inclure Bootstrap 4 CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Container Styling */
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
        }

        /* Title Styling */
        h2 {
            font-family: "Imprint MT Shadow";
            text-align: center;
            margin-bottom: 20px;
        }

        /* Table Styling */
        .table {
            color: #000000;
            font-size: 16px;
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        .table thead {
            background-color: #92c4b5;
            color: #ffffff;
        }

        .table thead th {
            padding: 12px;
            text-align: center;
            border-bottom: 2px solid #0056b3;
            cursor: pointer;
        }

        .table tbody tr {
            transition: background-color 0.3s;
        }

        .table tbody tr:hover {
            background-color: #e2e6ea;
        }

        /* Button Styling */
        .btn-success,
        .btn-danger {
            padding: 6px 12px;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            margin: 0 5px;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }

        /* Centering action buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px; /* Space between buttons */
        }

        /* Pagination Styling */
        .pagination {
            display: flex;
            justify-content: flex-end; /* Alignement à droite */
            margin-top: 20px;
        }

        .pagination button {
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 0 5px; /* Espacement entre les boutons */
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .pagination button:hover {
            background-color: #007bff;
            transform: translateY(-2px); /* Légère élévation au survol */
        }

        /* General mobile responsiveness */
        @media (max-width: 768px) {
            .table {
                font-size: 12px;
            }

            h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<h2 class="text-center mb-4">Gestion des comptes utilisateurs</h2>

<table class="table table-bordered table-hover table-striped" id="user-table">
    <thead class="table-primary">
        <tr>
            <th>Nom d'utilisateur</th>
            <th>Rôle</th>
            <th class="action-column">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $hasRequests = false;  // Initialize the flag to track if there are any users
        while ($row = $accountRequestsResult->fetch_assoc()) {
            $hasRequests = true;  // Set the flag to true if there are requests
        ?>
            <tr class="hover-row">
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['role']); ?></td>
                <td class="action-buttons">
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['IM']); ?>">
                        <button type="submit" name="validate_user" title='valider' value="approve" class="btn btn-success">
                            <i class="fa-sharp-duotone fa-solid fa-user-check"></i>
                        </button>
                    </form>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['IM']); ?>">
                        <button type="submit" name="validate_user" value="reject" title='rejeter' class="btn btn-danger">
                            <i class="fas fa-times"></i>
                        </button>
                    </form>
                </td>
            </tr>
        <?php } ?>

        <?php if (!$hasRequests): ?>
            <tr>
                <td colspan="3" class="text-center">Aucun nouveau compte disponible.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Pagination -->
<div class="pagination" id="pagination"></div>

<!-- Modal de succès -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel">Succès</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalMessage">
        <!-- Le message de succès sera inséré ici -->
        Compte validé avec succès!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Inclure Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Afficher le modal de succès après une action réussie
    function showSuccessMessage(message) {
        document.getElementById('modalMessage').innerText = message; // Mettre à jour le message
        $('#messageModal').modal('show'); // Afficher le modal
    }

    function updatePagination() {
        const rowsPerPage = 5; // Ajustez selon vos besoins
        const table = document.querySelector('#user-table');
        const totalRows = table.querySelectorAll('tbody tr').length;
        const numPages = Math.ceil(totalRows / rowsPerPage);

        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        for (let i = 1; i <= numPages; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.addEventListener('click', () => {
                showPage(i);
            });
            paginationContainer.appendChild(button);
        }

        showPage(1);
    }

    function showPage(pageNumber) {
        const rowsPerPage = 5; // Ajustez selon vos besoins
        const table = document.querySelector('#user-table');
        const tableBody = table.querySelector('tbody');
        const rows = tableBody.querySelectorAll('tr');

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const visible = pageNumber === Math.ceil((i + 1) / rowsPerPage);
            row.style.display = visible ? '' : 'none';
        }
    }

    updatePagination();
</script>

</body>
</html>
