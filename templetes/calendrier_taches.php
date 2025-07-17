<?php
// Inclure le fichier de connexion à la base de données
include '../includes/db_connect.php';

// Obtenir le mois et l'année actuels
$currentMonthStart = date('Y-m-01');  // Premier jour du mois actuel
$currentMonthEnd = date('Y-m-t');      // Dernier jour du mois actuel

// Requête SQL pour récupérer toutes les tâches validées avec la division associée depuis la table users
$sql = "SELECT t.*, u.division
        FROM tasks t
        JOIN users u ON t.assigned_to = u.IM
        WHERE t.status = 'validee' 
        AND t.etat IN ('En cours', 'Non demarrée', 'Non terminée','terminée') 
        AND t.date_fin BETWEEN '$currentMonthStart' AND '$currentMonthEnd'";

$result = $conn->query($sql);

$tasks = [];
$colors = ['#dfb645aa', '#df454594', '#5733FF', '#FFC300', '#DAF7A6', '#FF33A6', '#33FFF0', '#A633FF', '#FFB733', '#33B5FF'];
$currentDate = date('Y-m-d');

if ($result->num_rows > 0) {
    $index = 0; // Index pour les couleurs
    while ($task = $result->fetch_assoc()) {
        $date_fin = isset($task['date_fin']) ? $task['date_fin'] : $task['date_debut'];
        $isExpired = ($date_fin <= $currentDate) ? 'true' : 'false';

        // Assigner une couleur unique à la tâche
        $color = $colors[$index % count($colors)];
        $index++;

        $tasks[] = [
            'id' => $task['id'],
            'title' => $task['title'],
            'start' => $task['date_debut'],
            'end' => $date_fin,
            'description' => $task['description'],
            'division' => $task['division'], // Division récupérée depuis users
            'isExpired' => $isExpired,
            'color' => $color // Couleur unique
        ];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planification des activités</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <style>
        .fc-event {
            color: white !important; /* Couleur du texte */
            font-weight: bold;
            border-radius: 5px; /* Coins arrondis */
            padding: 5px; /* Espacement intérieur */
        }

        .fc-event:hover {
            opacity: 0.8; /* Effet au survol */
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Calendrier des activités Validées</h2>
        <div id="calendar" class="mt-4"></div>

        <!-- Modale de prolongation ou annulation de la tâche -->
        <div class="modal" id="extendTaskModal" tabindex="-1" aria-labelledby="extendTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="extendTaskModalLabel">Prolonger ou Annuler un activité</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="newEndDate">Nouvelle Date de Fin :</label>
                        <input type="date" id="newEndDate" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveNewEndDate">Prolonger</button>
                        <button type="button" class="btn btn-danger" id="deleteTask">Annuler l'activités</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <!-- Bootstrap Bundle JS (inclut Popper) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'fr',
                initialView: 'dayGridMonth',
            
                events: [
                    <?php
                    foreach ($tasks as $task) {
                        echo "{
                            id: '" . $task['id'] . "',
                            title: '" . addslashes($task['title']) . "',
                            start: '" . $task['start'] . "',
                            end: '" . $task['end'] . "',
                            description: '" . addslashes($task['description']) . "',
                            division: '" . addslashes($task['division']) . "',
                            isExpired: '" . $task['isExpired'] . "',
                            color: '" . $task['color'] . "' // Couleur unique
                        },";
                    }
                    ?>
                ],
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                eventClick: function (info) {
                    if (info.event.extendedProps.isExpired === 'true') {
                    } else {
                        alert(
                            "Informations de la tâche :\n\n" +
                            "Titre: " + info.event.title +
                            "\nDescription: " + info.event.extendedProps.description +
                            "\nDivision: " + info.event.extendedProps.division +
                            "\nDate de début: " + info.event.start.toLocaleDateString() +
                            "\nDate de fin: " + info.event.end.toLocaleDateString()
                        );
                    }
                }
            });

            calendar.render();
        });
    </script>
</body>
</html>
