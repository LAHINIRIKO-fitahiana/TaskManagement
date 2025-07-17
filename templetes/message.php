<?php
include '../includes/db_connect.php'; // Connexion à la base de données

// Vérifier si le paramètre 'query' est défini
if (isset($_GET['query'])) {
    $query = $_GET['query'];

    // Assurez-vous que la connexion à la base de données est réussie
    if (!$conn) {
        die("Erreur de connexion : " . $conn->connect_error);
    }

    // Requête pour rechercher les utilisateurs par leur nom d'utilisateur
    $stmt = $conn->prepare("SELECT IM, username FROM users WHERE username LIKE ? LIMIT 10");
    $searchTerm = "%" . $query . "%"; // Ajoute les caractères pour une recherche partielle
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    // Si des utilisateurs sont trouvés, affichez-les dans un menu déroulant
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<a class='dropdown-item' href='#' onclick='selectUser(\"" . $row['IM'] . "\", \"" . $row['username'] . "\")'>" . $row['username'] . "</a>";
        }
    } else {
        echo "<p class='dropdown-item'>Aucun utilisateur trouvé</p>";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Messages</title>
    <!-- Lien vers Bootstrap 4 -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        .chat-box {
            height: 400px;
            overflow-y: auto;
            background-color: #f1f1f1;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .chat-message {
            margin-bottom: 10px;
        }
        .message-input {
            resize: none;
            width: 100%;
            height: 80px;
        }
        .response-message {
            margin-top: 10px;
            font-size: 16px;
            text-align: center;
        }
        .search-container {
            margin-bottom: 20px;
        }
        #searchResults .dropdown-item {
            cursor: pointer;
            padding: 10px;
        }

        #searchResults .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        #searchResults .dropdown-item {
            cursor: pointer;
            padding: 10px;
        }

        #searchResults .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        #searchResults {
            max-height: 200px;
            overflow-y: auto;
            border-radius: 8px;
        }
        #selectedUsers .badge {
            margin-right: 5px;
            margin-bottom: 5px;
            background-color: #007bff;
        }

        #searchResults .dropdown-item {
            cursor: pointer;
            padding: 10px;
        }

        #searchResults .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        #searchResults {
            max-height: 200px;
            overflow-y: auto;
            border-radius: 8px;
        }

    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Chat Messages</h2>

<!-- Section de recherche d'utilisateur -->
<div class="search-container">
    <input type="text" id="searchUser" class="form-control" placeholder="Rechercher un contact" onkeyup="searchUser()">
    <!-- Affichage des utilisateurs sélectionnés -->
    <div id="selectedUsers" class="d-flex flex-wrap mt-2"></div>
    <!-- Menu déroulant pour afficher les résultats de recherche -->
    <div class="dropdown-menu" id="searchResults" style="display: none; width: 100%;"></div>
</div>



    <!-- Section pour afficher les messages -->
    <div class="chat-box" id="chatBox">
        <!-- Les messages seront chargés ici via AJAX -->
    </div>

    <!-- Formulaire pour envoyer un message -->
    <form id="sendMessageForm">
        <div class="form-group">
            <textarea id="messageInput" class="form-control message-input" placeholder="Tapez votre message..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Envoyer</button>
    </form>

    <!-- Message de confirmation ou d'erreur -->
    <div id="responseMessage" class="response-message"></div>

</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
function searchUser() {
    var query = document.getElementById('searchUser').value;

    // Si la recherche est vide, on cache les résultats
    if (query.length === 0) {
        document.getElementById('searchResults').style.display = 'none'; // Cacher le dropdown
        return;
    }

    // Afficher le dropdown
    document.getElementById('searchResults').style.display = 'block';

    // Envoi de la requête AJAX pour rechercher l'utilisateur
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'searchUser.php?query=' + query, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            // Afficher les résultats dans la dropdown
            document.getElementById('searchResults').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
}

function selectUser(IM, username) {
    // Vous pouvez utiliser userId pour effectuer d'autres actions, comme afficher des informations supplémentaires sur l'utilisateur
    console.log("Utilisateur sélectionné : " + username);

    // Réinitialiser le champ de recherche et vider les résultats
    document.getElementById('searchUser').value = '';
    document.getElementById('searchResults').innerHTML = ''; // Effacer les résultats
    document.getElementById('searchResults').style.display = 'none'; // Cacher le dropdown
}


</script>
</body>
</html>
