<dialog class="pop-up" id="create_game" open>
        <div class="popup-content">
            <hgroup>
            <h2>Choix des Vaisseaux</h2>
            <p><?php echo $_GET['game']; ?></p></hgroup>
            <form method="post">
                <fieldset>
                    <legend>Vos vaisseaux</legend>
    <label><input type="checkbox" name="ship[]" value=1 onclick="limitCheckbox()"> Enterprise</label><br>
    <label><input type="checkbox" name="ship[]" value=2 onclick="limitCheckbox()"> Blackbird</label><br>
    <label><input type="checkbox" name="ship[]" value=3 onclick="limitCheckbox()"> Panthera</label><br>
    <label><input type="checkbox" name="ship[]" value=4 onclick="limitCheckbox()"> Kaiten</label><br>
    <label><input type="checkbox" name="ship[]" value=5 onclick="limitCheckbox()"> Soukhoï</label><br>
                </fieldset>
                <button id='submit_create' type="submit" disabled>Créer</button>
            </form>
        </div>
</dialog>
<script src="js/checkbox.js"></script>