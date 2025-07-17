// Fonction de filtrage des tâches par division
function filterTasksByDivision() {
    const selectedDivision = document.getElementById('divisionSelect').value;
    window.location.href = '?page=taches_faire&division=' + selectedDivision;
}

// Fonction pour afficher les données dans la modal lors de l'ouverture
$('#taskModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);
    modal.find('#taskTitle').val(button.data('title'));
    modal.find('#taskDescription').val(button.data('description'));
    modal.find('#taskDeadline').val(button.data('date_debut'));
    modal.find('#taskCompletionDate').val(button.data('date_fin'));
    modal.find('#etat').val(button.data('etat'));
    modal.find('#division').val(button.data('division'));
    modal.find('#taskId').val(button.data('id'));
});


// Fonction de pagination
function updatePagination() {
    const rowsPerPage = 6; // Ajustez selon vos besoins
    const table = document.querySelector('#taskTable');
    const totalRows = table.querySelectorAll('tbody tr').length;
    const numPages = Math.ceil(totalRows / rowsPerPage);

    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';

    // Créer les boutons de pagination
    for (let i = 1; i <= numPages; i++) {
        const button = document.createElement('button');
        button.textContent = i;
        button.addEventListener('click', () => {
            showPage(i);
        });
        paginationContainer.appendChild(button);
    }

    // Afficher la première page
    showPage(1);
}

// Fonction pour afficher une page spécifique dans la table
function showPage(pageNumber) {
    const rowsPerPage = 6; // Ajustez selon vos besoins
    const table = document.querySelector('#taskTable');
    const tableBody = table.querySelector('tbody');
    const rows = tableBody.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const visible = pageNumber === Math.ceil((i + 1) / rowsPerPage);
        row.style.display = visible ? '' : 'none';
    }
}

// Initialiser la pagination
updatePagination();
