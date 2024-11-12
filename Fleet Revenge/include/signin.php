<dialog class="popup" id="signin">
    <div class="popup-content">
    <button class="close" onclick="document.getElementById('signin').close();">&times;</button><h2>Inscription</h2>
        <form action="#" method="POST" id="signup-form">
            <input type='hidden' name='user_action' value='signin'/>
            <input type="text" name="pseudo" placeholder="pseudo" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit" onclick="document.getElementById('signin').close();">S'inscrire</button>
        </form>
    </div>
</dialog>
