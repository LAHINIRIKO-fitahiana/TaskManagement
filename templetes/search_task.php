<?php
// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';// Démarrer la session si elle n'est pas déjà démarrée

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_im ; //c , $_SESSION['coordonateur'], =$_SESSION['chef_service']
$role=$_SESSION['roleUser'];

// Assurez-vous que la connexion est établie
if (!$conn) {
    die("Erreur de connexion : " . $conn->connect_error);
}

switch ($_SESSION['roleUser']) {
    case 'employé':
        $user_im= $_SESSION['employeur'];
        break;
    case 'coordonateur':
        $user_im= $_SESSION['coordonateur'];
        break;
    case 'chef_service':
        $user_im= $_SESSION['chef_service'];
        break;
    default:
        $user_im= $_SESSION['employeur'];
        break;
}

  
    // Vérifier si une requête de recherche a été envoyée
    if (isset($_GET['query']) && !empty($_GET['query'])) {
        $query = $_GET['query']; // La requête de recherche brute
        $division = isset($_GET['division']) ? $_GET['division'] : ''; // Récupérer la division si elle est passée

        // Protéger les entrées pour éviter les injections SQL
        $query = mysqli_real_escape_string($conn, "%" . $query . "%");
        $division = mysqli_real_escape_string($conn, $division);

        if ($role==='employé') { 
            // Préparer une requête SQL pour rechercher dans la base de données
            $sql = "SELECT t.*, u.division
                FROM tasks t
                JOIN users u ON t.assigned_to = u.IM
                WHERE (t.title LIKE '$query' OR t.description LIKE '$query' OR t.date_debut LIKE '$query' OR t.date_fin LIKE '$query' OR t.etat LIKE '$query')
                " . ($division ? "AND u.division = '$division'" : "") . "
                " . ($role === 'employé' ? "AND t.assigned_to = '$user_im'" : "") . "
                ORDER BY t.date_debut ASC";
      
            // Exécuter la requête
            $result = $conn->query($sql);
        }else {
            // Préparer une requête SQL pour rechercher dans la base de données
            $sql = "SELECT t.*, u.division
                    FROM tasks t
                    JOIN users u ON t.assigned_to = u.IM
                    WHERE (t.title LIKE '$query' OR t.description LIKE '$query' OR t.date_debut LIKE '$query' OR t.date_fin LIKE '$query' OR t.etat LIKE '$query')
                    " . ($division ? "AND u.division = '$division'" : "") . "
                    ORDER BY t.date_debut ASC";
            
            // Exécuter la requête
            $result = $conn->query($sql);
        }        

        // Vérifier si des résultats sont trouvés
        if ($result->num_rows > 0) {
            // Afficher la requête de recherche de l'utilisateur
            echo "<p><strong>Résultats de la recherche pour : </strong>" . htmlspecialchars($query) . "</p>";
            
            // Début du tableau
            echo "<table class='table table-bordered'>";
            echo "<thead><tr>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Division</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                </tr></thead>";
            echo "<tbody>";

            // Parcourir les résultats et afficher dans le tableau
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['division']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_debut']) . "</td>";
                echo "<td>" . htmlspecialchars($row['date_fin']) . "</td>";
                echo "</tr>";
            }

            // Fin du tableau
            echo "</tbody>";
            echo "</table>";
        } else {
            echo "Aucune information trouvée pour ce mot-clé.";
        }

    } else {
        echo "Veuillez entrer un mot-clé pour rechercher.";
    }

// Fermer la connexion à la base de données
$conn->close();
?>
