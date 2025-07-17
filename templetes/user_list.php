<?php
include '../includes/db_connect.php'; // Chemin vers le fichier de connexion

// Assurez-vous que la connexion est établie avant de continuer
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Vérifier si une requête POST de suppression a été envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    // Utiliser une requête préparée pour éviter les injections SQL
    $stmt = $conn->prepare("DELETE FROM users WHERE IM = ?");
    $stmt->bind_param("s", $userId);

    if ($stmt->execute()) {
        echo "<script>alert('Utilisateur supprimé avec succès.');</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression : " . $conn->error . "');</script>";
    }

    $stmt->close();
}

// Récupérer les utilisateurs actifs
$accountRequestsSql = "SELECT * FROM users WHERE status = 'active'";
$accountRequestsResult = $conn->query($accountRequestsSql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Liste des Utilisateurs</title>
    <style>
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
        }

        h2 {
            font-family: "Imprint MT Shadow";
            text-align: center;
            margin-bottom: 20px;
        }

        .table {
            color: #000000;
            font-size: 16px;
            border-collapse: collapse;
            width: 100%;
        }

        .table thead {
            background-color: #92c4b5;
            color: #ffffff;
            transition: background-color 0.3s;
        }

        .table thead th {
            position: relative;
            cursor: pointer;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #0056b3;
        }

        .table thead th:hover {
            background-color: #0056b3;
        }

        .table tbody tr {
            transition: background-color 0.3s;
        }

        .table tbody tr:hover {
            background-color: #e2e6ea;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .table {
                font-size: 12px;
            }

            h2 {
                font-size: 24px;
            }
        }

        /* Styles pour la pagination */
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
    </style>
</head>
<body>
        <h2>Liste des employés</h2>
        <table class="table table-striped" id="userTable">
            <thead>
                <tr>
                    <th>IM</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $accountRequestsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['IM']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['IM']); ?>">
                            <button type="submit" name="delete_user" title="Supprimer" class="btn-delete">
                                <i class="fas fa-trash-alt"></i> <!-- Icône de corbeille -->
                            </button>

                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</body>


    <div class="pagination" id="pagination"></div>
<script>
    function updatePagination() {
        const rowsPerPage = 5;
        const table = document.querySelector('#userTable');
        const totalRows = table.querySelectorAll('tbody tr').length;
        const numPages = Math.ceil(totalRows / rowsPerPage);

        const paginationContainer = document.getElementById('pagination');
        paginationContainer.innerHTML = '';

        let currentPage = 1;
        let visibleStart = 1;
        let visibleEnd = Math.min(3, numPages); // Affiche seulement deux boutons

        // Bouton gauche
        const leftScrollButton = document.createElement('button');
        leftScrollButton.innerHTML = '<i class="fas fa-angle-left"></i>';
        leftScrollButton.disabled = true;
        leftScrollButton.addEventListener('click', () => {
            if (visibleStart > 1) {
                visibleStart -= 2;
                visibleEnd -= 2;
                renderPaginationButtons();
            }
        });
        paginationContainer.appendChild(leftScrollButton);

        // Bouton droit
        const rightScrollButton = document.createElement('button');
        rightScrollButton.innerHTML = '<i class="fas fa-angle-right"></i>';
        rightScrollButton.disabled = numPages <= 3;
        rightScrollButton.addEventListener('click', () => {
            if (visibleEnd < numPages) {
                visibleStart += 3;
                visibleEnd += 3;
                renderPaginationButtons();
            }
        });
        paginationContainer.appendChild(rightScrollButton);

        // Fonction pour afficher les boutons visibles
        function renderPaginationButtons() {
            // Supprime les anciens boutons
            document.querySelectorAll('.page-button').forEach(btn => btn.remove());

            // Ajoute les boutons visibles
            for (let i = visibleStart; i <= visibleEnd; i++) {
                const button = document.createElement('button');
                button.textContent = i;
                button.classList.add('page-button');
                if (i === currentPage) {
                    button.classList.add('active');
                }
                button.addEventListener('click', () => {
                    currentPage = i;
                    showPage(currentPage);
                    renderPaginationButtons();
                });
                paginationContainer.insertBefore(button, rightScrollButton);
            }

            // Active/désactive les boutons de défilement
            leftScrollButton.disabled = visibleStart <= 1;
            rightScrollButton.disabled = visibleEnd >= numPages;
        }

        function showPage(pageNumber) {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.display = (index >= (pageNumber - 1) * rowsPerPage && index < pageNumber * rowsPerPage) ? '' : 'none';
            });
        }

        // Affiche la première page et initialise les boutons
        showPage(1);
        renderPaginationButtons();
    }

    updatePagination();


</script>
</html>
