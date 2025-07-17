<?php
// Connexion à la base de données (Assurez-vous de remplacer les paramètres de connexion)
include '../includes/db_connect.php'; // Connexion à la base de données

if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}
$rejeter = "";  // Initialiser la variable pour éviter des erreurs
$valider = "";  // Initialiser également
$r= "";  // Initialiser la variable pour éviter des erreurs
$v = ""; 

// Récupération des tâches proposées
$sql = "SELECT tasks.*, 
    CASE 
        WHEN tasks.assigned_to = 'Tous' THEN 'Tous' 
        ELSE users.division 
    END AS division
    FROM tasks
    LEFT JOIN users ON tasks.assigned_to = users.IM
    WHERE tasks.status = 'proposee'
    ORDER BY tasks.date_fin DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Récupérer toutes les tâches sous forme de tableau associatif
    $taches = $result->fetch_all(MYSQLI_ASSOC);
} else {
    $taches = [];
}
$accountRequestsSql = "SELECT tasks.*, 
                              CASE 
                                  WHEN tasks.assigned_to = 'Tous' THEN 'Tous' 
                                  ELSE users.division 
                              END AS division
                       FROM tasks
                       LEFT JOIN users ON tasks.assigned_to = users.IM
                       WHERE tasks.status = 'proposee'
                       ORDER BY tasks.date_fin DESC";
$accountRequestsResult = $conn->query($accountRequestsSql);

if (isset($_POST['validate_tasks'])) {
    // Utiliser trim pour s'assurer que user_id ne contient que des chiffres
    $userId = $_POST['user_id'];
    $action = $_POST['validate_tasks'];

    // Assurez-vous que IM est de type approprié dans la base de données
    if ($action == 'valider') {
        $sql_v = "UPDATE tasks SET status = 'validee' WHERE id = '$userId'";
        $valider = $conn->query($sql_v);
        echo "<script>alert('la tâche est validé avec succès.');</script>";
        echo "<script>window.location.href = '?page=taches_a_valider';</script>"; // Redirection avec pagination et filtre
    } else {
        $sql_d = "DELETE FROM tasks WHERE id = '$userId'";
        $rejeter = $conn->query($sql_d);
        echo "<script>alert(' la tâche est annulé.');</script>";
        echo "<script>window.location.href = '?page=taches_a_valider';</script>"; // Redirection avec pagination et filtre
       
    }

    // Exécution de la requête SQL
    /*if ($conn->query($sql) === TRUE) {
        // Affichez un message de succès et rafraîchissez la page après 2 secondes
        //echo "<p> Tachê validé avec succée</p>";
        
        //exit; // S'assurer qu'aucune autre sortie n'est faite après le message
    } else {
        // Gérer l'erreur
        $errorMessage = "Erreur lors de la mise à jour de tâches : " . $conn->error;
        // Vous pouvez afficher ou traiter l'erreur comme vous le souhaitez
        echo $errorMessage; // Pour l'exemple, affichons l'erreur
    }*/

}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des activités Proposées</title>
    <!-- Lien vers Font Awesome pour l'icône d'édition -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Styles améliorés pour le tableau */
        .container {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
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
            padding: 8px;
            text-align: left;
            border-bottom: 2px solid #0056b3;
            width: 10%;
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
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .table td:first-child {
            font-weight: bold;
        }

        .pagination {
            display: flex;
            justify-content: right;
            margin-top: 20px;
        }

        .pagination button {
            background-color: #0056b3;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .pagination button:hover {
            background-color: #007bff;
            transform: translateY(-2px);
        }

        /* Styles généraux pour la responsivité */
        @media (max-width: 1024px) {
            h2 {
                color: #007bff;
                font-size: 22px;
            }
            .table {
                font-size: 14px;
            }
            .table td,
            .table th {
                padding: 6px;
                width: auto;
            }
        }

        @media (max-width: 768px) {
            h2 {
                color: #007bff;
                font-size: 22px;
            }
            .table {
                font-size: 12px;
            }
            .table thead {
                display: none;
            }
            .table td {
                display: block;
                text-align: left;
            }
            .table tr {
                margin-bottom: 15px;
                border: 1px solid #dee2e6;
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 20px;
            }
            .table {
                font-size: 10px;
            }
            .table td {
                padding: 4px;
            }
        }
    </style>
</head>
<body>
    
        <h2>Liste des activités proposés</h2>
        <table class="table table-bordered table-hover table-striped" id="proposedTasksTable">
            <thead class="table-primary">
                <tr>
                    <th onclick="sortTable(0)">Titre</th>
                    <th onclick="sortTable(1)">Description</th>
                    <th onclick="sortTable(2)">Date de début</th>
                    <th onclick="sortTable(3)">Date de fin</th>
                    <th onclick="sortTable(4)">Division</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
        $hasRequests = false;  // Initialize the flag to track if there are any users
        while ($row = $accountRequestsResult->fetch_assoc()) {
            $hasRequests = true;  // Set the flag to true if there are requests
        ?>
                        <tr class="hover-row">
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_debut']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_fin']); ?></td>
                            <td><?php echo htmlspecialchars($row['division']); ?></td>
                            <td style="text-align: center;">
                                <!-- Icône de validation -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="validate_tasks" title="Valider" value="valider" class="btn btn-success">
                                        <i class="fas fa-check-circle" style=" font-size: 24px; transition: transform 0.3s, color 0.3s;">
                                        </i>
                                    </button>
                                </form>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                    <button type="submit" name="validate_tasks" value="rejeter" title="Rejeter" class="btn btn-danger">
                                        <i class="fas fa-times-circle" style="font-size: 24px; transition: transform 0.3s, color 0.3s;">
                                        </i>
                                    </button>
                                </form>
                               
                            </td>

                        </tr>
                <?php } ?>
                
                <?php if (!$hasRequests): ?>
                <tr>
                    <td colspan="6" class="text-center">Aucun nouveau activité proposé.</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
        <div class="pagination" id="pagination"></div>
    

    <script>
        function sortTable(columnIndex) {
            var table, rows, switching, i, x, y, shouldSwitch, direction, switchCount = 0;
            table = document.getElementById("proposedTasksTable");
            switching = true;
            direction = "asc"; 

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[columnIndex];
                    y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

                    if (direction === "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (direction === "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }

                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchCount++;
                } else {
                    if (switchCount === 0 && direction === "asc") {
                        direction = "desc";
                        switching = true;
                    }
                }
            }
        }

        function updatePagination() {
            const rowsPerPage = 8;
            const table = document.querySelector('table');
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

        function showPage(page) {
            const rowsPerPage = 8;
            const table = document.querySelector('table');
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach((row, index) => {
                row.style.display = 'none';
                if (index >= (page - 1) * rowsPerPage && index < page * rowsPerPage) {
                    row.style.display = '';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            updatePagination();
        });
    </script>
</body>
</html>