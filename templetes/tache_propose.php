
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Connexion à la base de données
require '../includes/db_connect.php'; // Assurez-vous d'inclure votre fichier de connexion

// Récupération des tâches proposées
$query = "SELECT title, description, date_debut, date_fin, id, division FROM tasks"; // Ajustez la requête selon votre base de données
$result = mysqli_query($conn, $query);
$taches = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Vérifiez si des tâches sont présentes
if (!$taches) {
    $taches = []; // S'assurer que $taches est un tableau vide si aucune tâche n'est trouvée
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des activités Proposées</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Lien vers Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <script src="path/to/jquery.min.js"></script> <!-- Lien vers jQuery -->
    <script src="path/to/bootstrap.bundle.min.js"></script> <!-- Lien vers Bootstrap JS -->
    <script>
        function sortTable(columnIndex) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("proposedTasksTable");
            switching = true;
            dir = "asc"; 

            while (switching) {
                switching = false;
                rows = table.rows;

                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[columnIndex];
                    y = rows[i + 1].getElementsByTagName("TD")[columnIndex];

                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true; 
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true; 
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++; 
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
<style>
    /* Styles améliorés pour le tableau */
    .container {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        padding: 30px;
        margin-top: 50px;
    }

    .hover-row:hover {
        background-color: #e9ecef; /* Couleur de fond au survol */
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
    }

    .table thead th {
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #0056b3;
    }

    .table tbody tr {
        transition: background-color 0.3s;
    }

    .table tbody tr:hover {
        background-color: #e2e6ea;
    }

    .pagination {
        display: flex;
        justify-content: right; /* Centrer les boutons */
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

    /* Styles généraux */
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
<div class="container">
    <h2 class="text-center mb-4">Liste des activités Proposées</h2>
    <table class="table table-bordered table-hover table-striped" id="proposedTasksTable">
        <thead class="table-primary">
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Titre</th>
                <th onclick="sortTable(2)">Description</th>
                <th onclick="sortTable(3)">Date Limite</th>
                <th onclick="sortTable(4)">Date d'Achèvement</th>
                <th onclick="sortTable(5)">Division</th>
                <th onclick="sortTable(6)">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($taches)): ?>
                <?php foreach ($taches as $tache): ?>
                <tr class="hover-row">
                    <td><?php echo $tache['id']; ?></td>
                    <td><?php echo htmlspecialchars($tache['title']); ?></td>
                    <td><?php echo htmlspecialchars($tache['description']); ?></td>
                    <td><?php echo htmlspecialchars($tache['date_debut']); ?></td>
                    <td><?php echo htmlspecialchars($tache['date_fin']); ?></td>
                    <td><?php echo htmlspecialchars($tache['division']); ?></td>
                    <td class="text-center align-middle"> <!-- Centre horizontalement et verticalement -->
                    <button class="btn btn-outline-primary btn-sm d-flex justify-content-center align-items-center mx-auto" 
        style="width: 40px; height: 40px;" 
        onclick="console.log('ID de tâche : <?php echo $tache['id']; ?>'); window.location.href='admin_dashboard.php?page=modif_propose_taches&id=<?php echo $tache['id']; ?>'">
    <i class="fas fa-edit"></i>
</button>

</td>




                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">Aucune activité proposée disponible.</td>
            </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">
                    <div class="text-right mt-4">
                        <a href="propose_taches.php" class="btn btn-primary ml-4">
                            <i class="fas fa-plane"></i> Proposer une activités
                        </a>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
   <div class="pagination" id="pagination"></div>

</div>
</body>
</html>


