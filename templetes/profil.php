<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Utilisateur</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* Center card and add shadow */
        .profile-card {
            max-width: 400px;
            margin: auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        /* Style for profile image */
        .profile-img-container {
            position: relative;
            display: inline-block;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #0d5e48; /* Modified border color */
            background-color: #fff;
            border-radius: 50%;
            transition: opacity 0.3s;
        }

        /* Edit icon overlay on profile image */
        .edit-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s;
            color: white;
            font-size: 24px;
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 50%;
            padding: 10px;
        }

        /* Show edit icon on hover */
        .profile-img-container:hover .profile-img {
            opacity: 0.3;
        }

        .profile-img-container:hover .edit-icon {
            opacity: 1;
        }

        /* Custom styles */
        .card-header {
            text-align: center;
            background-color: #0d5e48; /* Modified header color */
        }

        .user-info {
            text-align: left;
            margin-top: 20px;
        }

        /* Button custom style */
        #edit-profile-btn {
            width: 50%;
            border-radius: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card profile-card">
        <div class="card-header text-white">
            <h4>Profil de l'Utilisateur</h4>
        </div>
        <div class="card-body text-center">
            <!-- Photo de profil avec option de changement -->
            <div class="profile-img-container">
                <img src="https://via.placeholder.com/150" id="profile-img" class="profile-img" alt="Photo de profil">
                <label for="upload-img" class="edit-icon" title="Changer la photo de profil">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="upload-img" accept="image/*" style="display: none;">
            </div>
            
            <!-- Informations utilisateur -->
            <h5 class="mt-3" id="user-fullname">Elsevline Jocaya</h5>
            <p class="text-muted" id="user-role">Coordonnateur</p>

            <div class="user-info mt-4">
                <p><strong>IM:</strong> <span id="user-im">12345678</span></p>
                <p><i class="fas fa-envelope mr-2"></i> <span id="user-email">elsevline@example.com</span></p>
                <p><i class="fas fa-user-tag mr-2"></i> <span id="user-role">Coordonnateur</span></p>
                <p><i class="fas fa-building mr-2"></i><span id="company-name">Service Régional du Budget Ihorombe (SRB)</span></p>
                <p><i class="fas fa-map-marker-alt mr-2"></i><span id="company-location">Analamanga Ihosy, Madagascar</span></p>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap and jQuery JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Changer la photo de profil
    document.getElementById("upload-img").addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profile-img").src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // Action bouton Modifier le profil
    document.getElementById("edit-profile-btn").addEventListener("click", function() {
        alert("Fonction de modification du profil à implémenter.");
    });
</script>
</body>
</html>
