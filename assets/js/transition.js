$(document).ready(function() {
    // Gérer le clic sur les liens
    $('a').click(function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        
        // Ajouter une classe pour masquer la page actuelle
        $('.page').removeClass('active');

        // Charger la nouvelle page via AJAX
        $('.content').load(href + ' .content', function() {
            // Ajouter une classe pour afficher la nouvelle page avec une transition
            $('.page').addClass('active');
        });
    });
});