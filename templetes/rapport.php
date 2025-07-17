<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inclure la connexion à la base de données
include '../includes/db_connect.php';

$tasks = []; 

// Date actuelle
$currentDate = date('Y-m-d');

// Calculer le premier jour du mois actuel
$startOfMonth = date('Y-m-01');

// Calculer le dernier jour du mois actuel
$endOfMonth = date('Y-m-t');

// Calculer la date 10 jours après le dernier jour du mois
$endWithExtraDays = date('Y-m-d', strtotime($endOfMonth . ' +10 days'));

// Requête SQL pour récupérer les tâches
$query_tasks = "SELECT id, title 
                FROM tasks 
                WHERE status = 'validee' AND etat = 'terminée'
                AND date_fin BETWEEN '$startOfMonth' AND '$endWithExtraDays' 
                AND date_fin IS NOT NULL";

$result_tasks = mysqli_query($conn, $query_tasks);


if ($result_tasks) {
    while ($row = mysqli_fetch_assoc($result_tasks)) {
        $tasks[] = $row; 
    }
}

// Vérifier si une session existe déjà pour les tâches
if (!isset($_SESSION['rapport_data'])) {
    $_SESSION['rapport_data'] = []; // Initialiser un tableau vide pour les tâches
}

// Traitement du formulaire
if (isset($_POST['add_task'])) {
    // Vérification si les données ont été soumises pour une tâche
    if (isset($_POST['task_id'], $_POST['produit_tache'])) {
        $task_id = $_POST['task_id'];
        $produit_tache = mysqli_real_escape_string($conn, $_POST['produit_tache']);
        $probleme_rencontre = isset($_POST['probleme_rencontre']) ? mysqli_real_escape_string($conn, $_POST['probleme_rencontre']) : '';
        $solution_proposee = isset($_POST['solution_proposee']) ? mysqli_real_escape_string($conn, $_POST['solution_proposee']) : '';

        // Récupérer les détails de la tâche
        $query_task_details = "SELECT * FROM tasks WHERE id = '$task_id'";
        $result_task_details = mysqli_query($conn, $query_task_details);
        $task = mysqli_fetch_assoc($result_task_details);

        // Ajouter la tâche au tableau de rapports si les données sont valides
        if ($task) {
            $_SESSION['rapport_data'][] = [
                'title' => $task['title'],
                'produit_tache' => $produit_tache,
                'etat' => $task['etat'],
             //   'division' => $task['division'],
                'date_debut' => $task['date_debut'],
                'date_fin' => $task['date_fin'],
                'probleme_rencontre' => $probleme_rencontre,
                'solution_proposee' => $solution_proposee
            ];
        }
    }
}

// Récupérer les tâches ajoutées en session
$rapport_data = $_SESSION['rapport_data']; 

// Effacer les données après l'exportation PDF
if (isset($_POST['export_pdf'])) {
    // Effacer les tâches après l'exportation
    unset($_SESSION['rapport_data']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Tâche</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        font-family: 'Arial', sans-serif; /* Utiliser une police sans-serif moderne */
        background-color: #f8f9fa; /* Couleur de fond légère */
    }

    .form-container {
        background-color: #ffffff; /* Fond blanc pour le formulaire */
        border: 1px solid #ced4da; /* Bordure légère */
        border-radius: 10px; /* Coins arrondis */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    h1 {
        font-family: "Imprint MT Shadow";
    }

    .form-control {
        border: 1px solid #007bff; /* Bordure des champs */
        border-radius: 5px; /* Coins arrondis */
    }

    .form-control:focus {
        border-color: #0056b3; /* Couleur de la bordure au focus */
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Ombre au focus */
    }

    .btn-primary {
        background-color: #007bff; /* Couleur du bouton */
        border-color: #007bff; /* Couleur de la bordure du bouton */
    }

    .btn-primary:hover {
        background-color: #0056b3; /* Couleur du bouton au survol */
        border-color: #0056b3; /* Couleur de la bordure du bouton au survol */
    }

    .btn-danger {
        background-color: #dc3545; /* Couleur du bouton Annuler */
        border-color: #dc3545; /* Couleur de la bordure du bouton Annuler */
    }

    .btn-danger:hover {
        background-color: #c82333; /* Couleur du bouton Annuler au survol */
        border-color: #bd2130; /* Couleur de la bordure du bouton Annuler au survol */
    }

    #rapportTable {
    width: 100%;
    table-layout: auto;
    border-collapse: collapse;
    margin-top: 20px;
}

#rapportTable th, #rapportTable td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
}

#rapportTable th {
    background-color: #f2f2f2;
    font-weight: bold;
}

#rapportTable td, #rapportTable th {
    min-width: 100px;
}


</style>
</head>
<body>
<div class="container mt-4">
    <div class="form-container p-4">
        <h1 class="text-center mb-4">Rapport des activités</h1>

    <!-- Formulaire pour ajouter une tâche -->
    <form method="post" action="">
        <div class="row mb-3">
            <label for="task_id" class="col-sm-2 col-form-label">Tâche</label>
            <div class="col-sm-10">
                <select id="task_id" name="task_id" class="form-select" required>
                    <option value="">Choisir une tâche...</option>
                    <?php foreach ($tasks as $task): ?>
                        <option value="<?= $task['id'] ?>"><?= $task['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="row mb-3">
            <label for="produit_tache" class="col-sm-2 col-form-label">Produit par tâches</label>
            <div class="col-sm-10">
                <textarea id="produit_tache" name="produit_tache" class="form-control" rows="3" required></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="probleme_rencontre" class="col-sm-2 col-form-label">Problème rencontré</label>
            <div class="col-sm-10">
                <textarea id="probleme_rencontre" name="probleme_rencontre" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <div class="row mb-3">
            <label for="solution_proposee" class="col-sm-2 col-form-label">Solution proposée</label>
            <div class="col-sm-10">
                <textarea id="solution_proposee" name="solution_proposee" class="form-control" rows="3"></textarea>
            </div>
        </div>

        <!-- Bouton pour ajouter une tâche au tableau -->
        <div class="text-end">
            <button type="submit" name="add_task" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter la tâche
            </button>
        </div>
    </form>

</div>
</div>
<div class="container mt-4">
    <div class="form-container p-4">
    <!-- Afficher les tâches ajoutées dans le tableau -->
    <?php if (!empty($rapport_data)): ?>
        <h2 class="mt-5 " >Résumé des activités</h2>
        <table class="table table-bordered" id="rapportTable">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Produit par activité</th>
                    <th>État</th>
                    
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Problème rencontré</th>
                    <th>Solution proposée</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rapport_data as $rapport): ?>
                    <tr>
                        <td><?= $rapport['title'] ?></td>
                        <td><?= $rapport['produit_tache'] ?></td>
                        <td><?= $rapport['etat'] ?></td>
                       
                        <td><?= $rapport['date_debut'] ?></td>
                        <td><?= $rapport['date_fin'] ?></td>
                        <td><?= $rapport['probleme_rencontre'] ?></td>
                        <td><?= $rapport['solution_proposee'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Bouton pour générer le PDF -->
        <form method="post" action="">
            <div class="text-start">
                <button type="submit" name="export_pdf" class="btn btn-success mr-3" onclick="generatePDF()"><i class="fas fa-print"></i>
                Exporter en PDF
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>
</div><script>
    // Fonction pour générer le PDF avec les rapports ajoutés
    function generatePDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');  // Mode paysage

        const title = "Rapport de Tâches";

        // Ajouter la date actuelle en haut à gauche
        const currentDate = new Date().toLocaleDateString('fr-FR'); // Formater la date en français (jj/mm/aaaa)
        doc.setFontSize(10); // Taille de police réduite pour la date
        doc.text(`Date : ${currentDate}`, 40, 10); // Afficher la date en haut à gauche (14 mm du bord gauche, 10 mm du haut)

        // Centrer le titre sur la page
        const pageWidth = doc.internal.pageSize.width;
        const titleWidth = doc.getStringUnitWidth(title) * doc.internal.scaleFactor;
        const titleX = (pageWidth - titleWidth) / 2;

        const spacingAboveTitle = 20; // Espacement au-dessus du titre
        const spacingBetweenTitleAndTable = 10; // Espacement entre le titre et le tableau

        // Ajouter un espacement avant le titre
        doc.text(title, titleX, spacingAboveTitle);  // Placer le titre après l'espacement

        // Ajouter le tableau simplifié sans couleurs de fond ni bordures complexes
        doc.autoTable({
            html: '#rapportTable',
            startY: spacingAboveTitle + spacingBetweenTitleAndTable,  // Commencer le tableau sous le titre avec l'espacement défini
            margin: { top: spacingAboveTitle + spacingBetweenTitleAndTable },  // Marge pour laisser de l'espace sous le titre
            styles: {
                fontSize: 12,  // Réduire la taille de la police
                cellPadding: 2,  // Espacement interne réduit
                valign: 'middle',  // Alignement vertical des cellules
                halign: 'center'  // Alignement horizontal des cellules
            },
            headStyles: {
                fillColor: [255, 255, 255],  // Retirer la couleur de fond des en-têtes
                textColor: [0, 0, 0],  // Couleur du texte noire
                fontStyle: 'bold',  // Texte en gras
                lineWidth: 0.1,  // Bordure fine pour les en-têtes
            },
            bodyStyles: {
                fillColor: [255, 255, 255],  // Fond des lignes (blanc)
                lineWidth: 0.1  // Bordure fine pour le corps du tableau
            },
            tableWidth: 'auto'  // Largeur automatique des colonnes
        });

        // Enregistrer le PDF
        doc.save("rapports_de_taches.pdf");
    }
</script>
</body>
</html>
