<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion de Collaboration des Taches</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    body {
      display: flex;
      font-family: Arial, sans-serif;
      background-color: #f4f7fa; /* Couleur de fond douce */
    }

    .sidebar {
            width: 250px;
            background-color: #0d5e48; /* Couleur sombre pour la barre latérale */
            height: 100vh;
            position: fixed;
            left: 0; /* Position fixe à gauche */
            top: 0;
            transition: transform 0.3s ease; /* Transition douce pour le masquage */
            transform: translateX(0); /* Valeur par défaut pour afficher la sidebar */
            z-index: 1000; /* Pour s'assurer que la sidebar est au-dessus */
        }

        .sidebar.hidden {
            transform: translateX(-100%); /* Masquer la sidebar en déplaçant à gauche */
        }

        .sidebar .nav-link {
            color: #ffffff; /* Couleur des liens */
            padding: 10px 20px; /* Espacement */
            display: flex;
            align-items: center;
            transition: background-color 0.3s; /* Transition pour le survol */
            border-radius: 5px; /* Coins arrondis */
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Couleur de fond au survol */
            color: white;
        }

        .nav-link i {
            margin-right: 8px; /* Ajustement de la distance pour les icônes */
        }

        .main-content {
            margin-left: 250px; /* Espace pour la barre latérale */
            transition: margin-left 0.3s; /* Transition douce pour le contenu principal */
            flex-grow: 1; /* Permet au contenu principal de s'étendre */
            padding: 20px; /* Ajout d'espacement autour du contenu principal */
        }

        .main-content.expanded {
            margin-left: 0; /* Pas d'espace lorsque la barre latérale est cachée */
        }

        .navbar {
            background-color: #ffffff; /* Couleur de fond de la navbar */
            box-shadow: 0 3px 5px rgba(0, 0, 0, 0.1); /* Ombre pour la navbar */
            border-radius: 12px;
            z-index: 1000; /* S'assurer que la navbar est au-dessus des autres éléments */
        }
        .navbar-nav{
          font-weight: bold;
          font-family: Imprint MT Shandow;
        }

        .navbar .nav-link {
            color: #333; /* Couleur des liens de la navbar */
            margin-left: 15px; /* Ajout d'espacement entre les liens */
        }

        .navbar .nav-link:hover {
            color: #007bff; /* Couleur de lien au survol */
        }

        .search-bar {
          width: 300px; /* Largeur fixe pour la barre de recherche */
        }

        .search-bar input {
          border-radius: 10px; /* Coins arrondis */
        }

        .search-bar button {
          border-radius: 10px; /* Coins arrondis */
        }

        .badge-danger {
            background-color: #dc3545; /* Couleur rouge pour les notifications */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                display: none; /* Cacher la barre latérale sur les petits écrans */
            }

            .main-content {
                margin-left: 0; /* Pas de marge pour le contenu principal */
            }
          }
    
    .small-box {
  position: relative;
  background: #f7f7f7;
  border-radius: 10px;
  padding: 15px;
  margin-bottom: 15px;
  transition: transform 0.3s ease-in-out;
}

.small-box:hover {
  transform: scale(1.05);
}

.small-box .icon {
  position: absolute;
  top: 10px;
  right: 10px;
  opacity: 0.2;
}

.small-box-footer {
  display: block;
  padding: 8px;
  background: rgba(0, 0, 0, 0.1);
  color: white;
  text-align: center;
  font-weight: bold;
  text-transform: uppercase;
  transition: background 0.3s;
}

.small-box-footer:hover {
  background: rgba(0, 0, 0, 0.2);
}

.shadow-sm {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.inner h3 {
  font-size: 2rem;
}

.inner p {
  font-size: 1rem;
}

  </style>
</head>
<body>

<!-- Sidebar -->
  <div class="sidebar d-none d-lg-block" id="sidebar">
    <div class="d-flex justify-content-between align-items-center p-3">
      <h3 class="text-white">SRB</h3>
    </div>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link active" href="{% url 'tasks:acceuil' %}">
          <i class="fas fa-tachometer-alt"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#" id="tasksDropdown" data-toggle="collapse" data-target="#taskList" aria-expanded="false" aria-controls="taskList">
          <i class="fas fa-tasks" style="font-size: 1.2em; margin-right: 8px;"></i>
          <span style="flex-grow: 1;">Tâches</span>
          <i class="fas fa-angle-down float-right" style="transition: transform 0.3s;"></i>
      </a>      
        <div class="collapse" id="taskList">
            <ul class="nav flex-column ml-3">
                <li class="nav-item">
                    <a class="nav-link" href="{% url 'tasks:propose_taches' %}">
                        <i class="fas fa-plus"></i>
                        Proposer une Tâche
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{% url 'tasks:tache_propose' %}">
                        <i class="fas fa-list"></i>
                        Tâches à Proposer
                    </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{% url 'tasks:taches_faire' %}">
                      <i class="fas fa-tasks"></i>
                      Tâches à faire
                  </a>
                </li>              
            </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{% url 'tasks:rapport' %}">
          <i class="fas fa-file-alt"></i>
          <span>Rapports</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="{% url 'tasks:user_list' %}">
          <i class="fas fa-users"></i>
          <span>Liste des utilisateurs</span>
        </a>
      </li>
    </ul>
  </div>
  

<!-- Main Content -->
<div class="main-content" id="mainContent">
    
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light ">
    
            
    <button class="btn btn-dark mr-3" id="toggleSidebar">
      <i class="fas fa-angle-left"></i> <!-- Icone pour masquer -->
    </button>
    
    <!-- Toggler/collapsibe Button for mobile -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <div class="navbar-nav mr-auto" >
        <h3 class="text-center ml-4" >Chef de division</h3>
      </div>

      <!-- Search bar with icon -->
      <form class="form-inline my-2 my-lg-0 search-bar">
        <div class="input-group">
          <input class="form-control" type="search" placeholder="Rechercher" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="submit">
              <i class="fas fa-search"></i> <!-- Search icon -->
            </button>
          </div>
        </div>
      </form>

      <!-- Notification Icon with Badge -->
      <ul class="navbar-nav ml-3">
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Notifications">
                <i class="fas fa-bell"></i> 
                <span class="badge badge-danger">3</span> <!-- Badge showing notification count -->
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <div class="dropdown-header text-center">
                    <strong>Notifications</strong>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-info-circle mr-2"></i> Notification 1
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-info-circle mr-2"></i> Notification 2
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-info-circle mr-2"></i> Notification 3
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-center" href="#">Voir toutes les notifications</a>
            </div>
        </li>
      </ul>

      <!-- User profile -->
      <div class="dropdown ml-3">
        <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownUser" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Mon Compte">
            <i class="fas fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUser">
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">
                <i class="fas fa-user-circle mr-2"></i> Mon Profil
            </a>
            <a class="dropdown-item" href="#">
                <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
            </a>
        </div>
      </div>
    </div>
  </nav>


  <!-- Main content goes here -->
  <!-- <div class="container mt-4">
    <h2>Bienvenue dans MonApp</h2>
    <p>Contenu principal ici.</p>
  </div> -->

 </div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script>
  const toggleSidebarButton = document.getElementById('toggleSidebar');
  const sidebar = document.getElementById('sidebar');
  const mainContent = document.getElementById('mainContent');

  toggleSidebarButton.addEventListener('click', () => {
    sidebar.classList.toggle('hidden');
    mainContent.classList.toggle('expanded');
    
    // Change icon based on the sidebar state
    if (sidebar.classList.contains('hidden')) {
      toggleSidebarButton.innerHTML = '<i class="fas fa-angle-right"></i>'; // Show icon
    } else {
      toggleSidebarButton.innerHTML = '<i class="fas fa-angle-left"></i>'; // Hide icon
    }
  });


  // Histogramme pour la progression des tâches
  const ctx1 = document.getElementById('tasksHistogram').getContext('2d');
  const tasksHistogram = new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: ['Tâches à faire', 'Tâches en cours', 'Tâches terminées', 'Tâches en retard'],
      datasets: [{
        label: 'Nombre de tâches',
        data: [30, 15, 45, 10], // Données d'exemple, à remplacer par vos données réelles
        backgroundColor: [
          'rgba(54, 162, 235, 0.7)', // Bleu
          'rgba(255, 206, 86, 0.7)', // Jaune
          'rgba(75, 192, 192, 0.7)', // Vert
          'rgba(255, 99, 132, 0.7)'  // Rouge
        ],
        borderColor: [
          'rgba(54, 162, 235, 1)',
          'rgba(255, 206, 86, 1)',
          'rgba(75, 192, 192, 1)',
          'rgba(255, 99, 132, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Diagramme en camembert pour la répartition des tâches
  const ctx2 = document.getElementById('tasksPieChart').getContext('2d');
  const tasksPieChart = new Chart(ctx2, {
    type: 'pie',
    data: {
      labels: ['Tâches à faire', 'Tâches en cours', 'Tâches terminées', 'Tâches en retard'],
      datasets: [{
        label: 'Répartition des tâches',
        data: [30, 15, 45, 10], // Données d'exemple, à remplacer par vos données réelles
        backgroundColor: [
          'rgba(54, 162, 235, 0.7)', // Bleu
          'rgba(255, 206, 86, 0.7)', // Jaune
          'rgba(75, 192, 192, 0.7)', // Vert
          'rgba(255, 99, 132, 0.7)'  // Rouge
        ]
      }]
    },
    options: {
      responsive: true
    }
  });

</script>

</body>
</html>
