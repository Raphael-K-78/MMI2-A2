<dialog class="pop-up" id="Connexion">
        <div class="popup-content">
            <button class="close"  onclick="document.getElementById('Connexion').close();">&times;</button>
            <h2>Connexion</h2>
            <form action="#" method="POST">
                <input type="text" name="pseudo" placeholder="pseudo" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>
            <p>Vous n'avez pas de compte ?
    <button onclick="document.getElementById('Connexion').close();document.getElementById('signin').showModal()" id='create-account'>Créer un compte</button>
        </div>
</dialog>
