document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const code = urlParams.get('code');
    if (code) {
        fetch('requete/callback.php?code=' + code, {
            method: 'GET',
        })
        .then(response => response.json()) // Convertir la réponse en JSON
        .then(data => {
            if (data.access_token) {
                console.log("Jeton d'accès récupéré :", data.access_token);
                // Sauvegarder le jeton dans localStorage
                localStorage.setItem('access_token', data.access_token);
                // Rafraîchir la page pour récupérer les infos de l'utilisateur
                window.location.replace(window.location.href.split('?')[0]);
            } else if (data.error) {
                console.error("Erreur :", data.error);
            }
        
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
    }
    const accessToken = localStorage.getItem("access_token");
    console.log(accessToken)
    if (accessToken) {
        //vérifie le token
        fetch("requete/verif_token.php?token="+accessToken)
        .then(response =>response.json())
        .then(data =>{
            // console.table(data);
            if(data.token == "false"){
                console.log("L'utilisateur n'est pas connecté.");
                document.getElementById('welcome').style.display = 'block';
                document.getElementById('userInfo').style.display = 'none';
                console.log("token Invalid");
                localStorage.clear();
            }
            else{
                console.log("token Valid")
                document.getElementById('userInfo').style.display = 'block';

            }
        });
    }
    else {
        // Afficher la section de bienvenue si l'utilisateur n'est pas connecté
        console.log("L'utilisateur n'est pas connecté.");
        document.getElementById('welcome').style.display = 'block';
        document.getElementById('userInfo').style.display = 'none';
    }
});