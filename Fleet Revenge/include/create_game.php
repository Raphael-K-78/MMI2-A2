<?php

function genererCodeUnique($longueur = 16) {
    // Liste de lettres majuscules et minuscules
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Générer une chaîne aléatoire de lettres
    $codeUnique = '';
    for ($i = 0; $i < $longueur - 10; $i++) {  // Réserver 10 caractères pour le timestamp
        $codeUnique .= $alphabet[rand(0, strlen($alphabet) - 1)];
    }

    // Ajouter un timestamp court pour assurer l'unicité (5 chiffres suffisent)
    $codeUnique .= substr(time(), -5);  // Les 5 derniers chiffres du timestamp Unix

    return $codeUnique;
}
$code_game = genererCodeUnique();

?>

<dialog class="pop-up" id="create_game">
        <div class="popup-content">
            <button class="close"  onclick="document.getElementById('create_game').close();">&times;</button>
            <hgroup>
            <h2>Nouvelle Partie</h2>
            <p><?php echo $code_game; ?></p></hgroup>
            <form action="" method="POST">
               <select name="player2">
<?php $sql = "SELECT id_user, user_name FROM users WHERE id_user != :id_user";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_user', $_SESSION['id_user'], PDO::PARAM_INT);
$stmt->execute();
// Boucle pour afficher chaque utilisateur dans une balise <option>
while ($player = $stmt->fetch(PDO::FETCH_ASSOC)) {
    printf("<option name='player' value='%d'>%s</option>", 
           htmlspecialchars($player['id_user']), 
           htmlspecialchars($player['user_name'])
    );
}?>
                </select> 
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
<script src="js/checkbox.js"></script>
</div>
</dialog>

<?php

//création de la game ainsi que des 2 vaisseaux
// Vérification si le formulaire est soumis
if (isset($_POST['ship']) && isset($_POST['player2'])) {
    $ships_id = [];
    // Récupération des données envoyées
    $player2_id = $_POST['player2'];
    $selected_ships = $_POST['ship']; // Un tableau des vaisseaux sélectionnés
    // Démarrer la transaction pour assurer que les deux vaisseaux sont créés en même temps

    // Créer une nouvelle partie
    $sql_game = "INSERT INTO games (Player1, Player2, finish, game) VALUES (:player1, :player2, 0, :game)";
    $stmt_game = $pdo->prepare($sql_game);
    $stmt_game->bindParam(':player1', $_SESSION['id_user'], PDO::PARAM_INT);
    $stmt_game->bindParam(':player2', $player2_id, PDO::PARAM_INT);
    $stmt_game->bindParam(':game', $code_game);
    $stmt_game->execute();
    $id_game = $pdo->lastInsertId();
    $pdo->beginTransaction();

    if (is_array($selected_ships)) {
        foreach ($selected_ships as $ship_id) {
            switch ($ship_id) {
                case "1": 
                    $ship_name = "Enterprise"; 
                    $name ='NX-'. $_SESSION['id_user'];
                    $pos = json_encode(['x' => 4, 'y' => 0]);  // position sur la map
                    $vitess = 1.5; // stat vitess
                    $puissance = 1; // stat puissance
                    $solidite = 2.5; // stat solidité
                    break;
                case "2": 
                    $ship_name = "Blackbird";
                    $pos = json_encode(['x' => 3, 'y' => 0]);  // position sur la map
                    $vitess = 1; // stat vitess
                    $name ='USS-'. $_SESSION['id_user'];
                    $puissance = 1; // stat puissance
                    $solidite = 1; // stat solidité
                    break;
                case "3": 
                    $ship_name = "Panthera"; 
                    $pos = json_encode(['x' => 2, 'y' => 0]);  // position sur la map
                    $vitess = 1; // stat vitess
                    $name ='NA-'. $_SESSION['id_user'];
                    $puissance = 1; // stat puissance
                    $solidite = 1; // stat solidité
                    break;
                case "4": 
                    $ship_name = "Kaiten"; 
                    $name ='NCC-'. $_SESSION['id_user'];
                    $pos = json_encode(['x' => 1, 'y' => 0]);  // position sur la map
                    $vitess = 1; // stat vitess
                    $puissance = 1; // stat puissance
                    $solidite = 1; // stat solidité
                    break;
                case "5": 
                    $name ='NT-'. $_SESSION['id_user'];
                    $ship_name = "Soukhoi"; 
                    $pos = json_encode(['x' => 0, 'y' => 0]);  // position sur la map
                    $vitess = 1; // stat vitess
                    $puissance = 1; // stat puissance
                    $solidite = 1; // stat solidité
                    break;
            }

            // Insertion des vaisseaux dans la table vaisseaux
            $sql_ship = "INSERT INTO vaisseaux (nom, status, position, pv, vitesse, puissance, solidite, classe, id_user, id_game) 
                         VALUES (:nom, 1, :position, 100, :vitesse, :puissance, :solidite, :classe, :id_user, :id_game)";
            $stmt_ship = $pdo->prepare($sql_ship);
            $stmt_ship->bindParam(':nom', $name, PDO::PARAM_STR);
            $stmt_ship->bindParam(':position', $pos, PDO::PARAM_STR); // position comme JSON
            $stmt_ship->bindParam(':classe', $ship_name, PDO::PARAM_STR); // classe du vaisseau
            $stmt_ship->bindParam(':id_user', $_SESSION['id_user'], PDO::PARAM_INT);
            $stmt_ship->bindParam(':id_game', $id_game, PDO::PARAM_INT);
            $stmt_ship->bindParam(':vitesse', $vitess, PDO::PARAM_INT);
            $stmt_ship->bindParam(':solidite', $solidite, PDO::PARAM_INT);
            $stmt_ship->bindParam(':puissance', $puissance, PDO::PARAM_INT);
            $stmt_ship->execute();
            array_push($ships_id, $pdo->lastInsertId());
        }
    }

    // Finaliser la transaction
    $pdo->commit();
    ajouterHumain("Barry","Smith",$ships_id[0],'Pilote',$_SESSION['id_user'],$id_game);
ajouterHumain("Hector","Salazar",$ships_id[0],'Artilleur',$_SESSION['id_user'],$id_game);
ajouterHumain("Leroy","Gibbs",$ships_id[0],'Mecanicien',$_SESSION['id_user'],$id_game);
ajouterHumain("Florent","Nadiedjoa",$ships_id[0],'Manutentionnaire',$_SESSION['id_user'],$id_game);
ajouterHumain("Raphaël","Thuret",$ships_id[0],'Mentaliste',$_SESSION['id_user'],$id_game);
ajouterHumain("Martin","Myster",$ships_id[1],'Pilote',$_SESSION['id_user'],$id_game);
ajouterHumain("Nolan","Sorento",$ships_id[1],'Artilleur',$_SESSION['id_user'],$id_game);
ajouterHumain("Anna","Fang",$ships_id[1],'Mecanicien',$_SESSION['id_user'],$id_game);
ajouterHumain("Finn","Skywalker",$ships_id[1],'Manutentionnaire',$_SESSION['id_user'],$id_game);


}
?>
