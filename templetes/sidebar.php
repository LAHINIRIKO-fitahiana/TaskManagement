<div class="sidebar_all">
    <i class="fa-solid fa-circle-xmark close-icon" onclick="this.parentElement.style.display='none';"></i>
    <div class="logos"></div>

    <div class="links">
        <?php
        // Inclure la connexion à la base de données
        include '../includes/db_connect.php';

        // Récupérer le nombre de comptes d'utilisateur en attente
        $pendingAccountsSql = "SELECT COUNT(*) as count FROM users WHERE status = 'pending'";
        $pendingAccountsResult = $conn->query($pendingAccountsSql);

        // Vérifier si la requête a réussi
        if ($pendingAccountsResult) {
            $pendingAccountsCount = $pendingAccountsResult->fetch_assoc()['count'];
        } else {
            $pendingAccountsCount = 0; // En cas d'erreur
        }

        // Récupérer le nombre de propositions de tâches en attente de compilation
        $pendingTasksSql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'proposee'";
        $pendingTasksResult = $conn->query($pendingTasksSql);

        // Vérifier si la requête a réussi
        if ($pendingTasksResult) {
            $pendingTasksCount = $pendingTasksResult->fetch_assoc()['count'];
        } else {
            $pendingTasksCount = 0; // En cas d'erreur
        }

        // Check if the logged-in user is an admin
        if (isset($_SESSION['coordinateur'])) {
            echo '<h3>UTILISATEURS</h3>';

            // Afficher les comptes en attente avec vérification
            if ($pendingAccountsCount > 0) {
                echo '<a href="admin_dashboard.php?view=accounts">Comptes en attente <span class="badge"><?php echo $pendingAccountsCount; ?></span></a>
';
            } else {
                echo '<a href="admin_dashboard.php?view=accounts">Aucun compte en attente</a>';
            }

            // Afficher les tâches en attente avec vérification
            if ($pendingTasksCount > 0) {
                echo '<a href="tasks_pending.php">Tâches en attente <span class="badge">' . $pendingTasksCount . '</span></a>';
            } else {
                echo '<a href="tasks_pending.php">Aucune tâche en attente</a>';
            }
        }
        ?>
    </div>
</div>

<button id="toggleButton">
    <i class="fa-solid fa-bars-staggered"></i>
</button>
<p>Menu</p>

<script>
    // Get the button and sidebar elements
    var toggleButton = document.getElementById("toggleButton");
    var sidebar = document.querySelector(".sidebar_all");

    // Add click event listener to the button
    toggleButton.addEventListener("click", function() {
        // Toggle the visibility of the sidebar
        if (sidebar.style.display === "none" || sidebar.style.display === "") {
            sidebar.style.display = "block";
        } else {
            sidebar.style.display = "none";
        }
    });
</script>
