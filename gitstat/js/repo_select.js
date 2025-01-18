
const accessToken = localStorage.getItem("access_token");
         $('#user_data').select2({
        placeholder: 'Rechercher un utilisateur GitHub...',
        minimumInputLength: 1, // Requête commence après 1 caractère
        ajax: {
            url: 'requete/search_user.php', // Endpoint de recherche d'utilisateurs
            dataType: 'json',
            delay: 250, // Ajoute un délai pour éviter trop de requêtes
            data: function (params) {
                return {
                    name : params.term, // Terme recherché
                    per_page: 10 // Limite à 10 résultats
                };
            },
            processResults: function (data) {
            return {
                results: data.items.map(user => ({
                    id: user.login, // Nom de l'utilisateur comme ID
                    text: user.login // Nom affiché dans la liste
                }))
            };
        },
        language: {
            noResults: function () {
                return "Aucun utilisateur trouvé"; // Message en cas de résultats vides
            },
            searching: function () {
                return "Recherche en cours..."; // Message affiché lors de la recherche
            },
            inputTooShort: function (args) {
                return "Tapez " + args.minimum + " caractères ou plus"; // Alerte si moins de caractères
            },
            errorLoading: function () {
                return "Les résultats n'ont pas pu être chargés."; // Message d'erreur
            }
        },
        }
    });

    // Lorsqu'un utilisateur est sélectionné
$('#user_data').on('select2:select', function (e) {
    const selectedUser = e.params.data.id;
    document.getElementById('data').style.display = 'none';
    // Réinitialise le deuxième champ Select2
    $('#repos_data')
        .empty() // Supprime les anciennes options
        .prop('disabled', true) // Désactive le champ temporairement
        .select2({
            placeholder: 'Chargement des dépôts...'
        });

    // Appelle l'API GitHub pour récupérer les dépôts publics
    fetch(`https://api.github.com/users/${selectedUser}/repos`, {
        headers: accessToken ? { Authorization: `token ${accessToken}` } : {}
    })
        .then(response => response.json())
        .then(repos => {
            // Réactive le deuxième champ Select2
            $('#repos_data').prop('disabled', false);

            // Initialise le deuxième champ Select2 avec les dépôts
            $('#repos_data').select2({
                placeholder: `Sélectionnez un dépôt de ${selectedUser}...`,
                data: [
                    { id: '', text: '' }, // Élément vide pour le placeholder
                    ...repos.map(repo => ({
                        id: repo.name,
                        text: repo.name
                    }))
                ],
                allowClear: true,
            });
            
            // Réinitialiser la sélection pour s'assurer qu'aucune valeur n'est choisie
            $('#repos_data').val(null).trigger('change');

            // Affiche un message s'il n'y a pas de dépôts
            if (repos.length === 0) {
                $('#repos_data').select2({
                    placeholder: `Aucun dépôt trouvé pour ${selectedUser}.`,
                    data: []
                });
            }
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des dépôts :', error);
            alert('Impossible de récupérer les dépôts de cet utilisateur.');
        });
});