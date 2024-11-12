<?php



include_once("include/pdo.php");

include_once("include/session.php");

include_once("include/class.php");





// Vérification si le formulaire est soumis

if (isset($_POST['ship'])) {

    $ships_id = [];

    // Récupération des données envoyées

    $selected_ships = $_POST['ship']; // Un tableau des vaisseaux sélectionnés

    // Démarrer la transaction pour assurer que les deux vaisseaux sont créés en même temps

    // Vérifier si $selected_ships est un tableau

    if (is_array($selected_ships)) {

        // Créer les vaisseaux pour le joueur 1 en fonction des cases cochées

        foreach ($selected_ships as $ship_id) {

            // Déterminer le nom du vaisseau en fonction de la valeur du checkbox

            switch ($ship_id) {

                case "1": 

                    $ship_name = "Enterprise"; 

                    $name ='NX-'. $_SESSION['id_user'];

                    $pos = json_encode(['x' => 4, 'y' => 4]);  // position sur la map

                    $vitess = 1.5; // stat vitess

                    $puissance = 1; // stat puissance

                    $solidite = 2.5; // stat solidité

                    break;

                case "2": 

                    $ship_name = "Blackbird";

                    $pos = json_encode(['x' => 3, 'y' => 4]);  // position sur la map

                    $vitess = 1; // stat vitess

                    $name ='USS-'. $_SESSION['id_user'];

                    $puissance = 1; // stat puissance

                    $solidite = 1; // stat solidité

                    break;

                case "3": 

                    $ship_name = "Panthera"; 

                    $pos = json_encode(['x' => 2, 'y' => 4]);  // position sur la map

                    $vitess = 1; // stat vitess

                    $name ='NA-'. $_SESSION['id_user'];

                    $puissance = 1; // stat puissance

                    $solidite = 1; // stat solidité

                    break;

                case "4": 

                    $ship_name = "Kaiten"; 

                    $name ='NCC-'. $_SESSION['id_user'];

                    $pos = json_encode(['x' => 1, 'y' => 4]);  // position sur la map

                    $vitess = 1; // stat vitess

                    $puissance = 1; // stat puissance

                    $solidite = 1; // stat solidité

                    break;

                case "5": 

                    $name ='NT-'. $_SESSION['id_user'];

                    $ship_name = "Soukhoi";

                    $pos = json_encode(['x' => 0, 'y' => 4]);  // position sur la map

                    $vitess = 1; // stat vitess

                    $puissance = 1; // stat puissance

                    $solidite = 1; // stat solidité

                    break;

            }



            // Insertion des vaisseaux dans la table vaisseaux

            $sql_ship = "INSERT INTO vaisseaux (nom, status, position, pv, vitesse, puissance, solidite, classe, id_user, id_game) 

                         VALUES (:nom, 1, :position, 100, :vitesse, :puissance, :solidite, :classe, :id_user, :id_game)";

            $stmt_ship = $pdo->prepare($sql_ship);

            if(empty($id_game)){

                $id_game = gametoid_game($_GET['game']);

            }

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

    // echo $_SESSION['id_user'];

ajouterHumain("James T","Kirk",$ships_id[0],'Pilote',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Boba","Fett",$ships_id[0],'Artilleur',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Emmett","Brown",$ships_id[0],'Mecanicien',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Jean-luc","Picard",$ships_id[0],'Manutentionnaire',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Nyota","Uhura",$ships_id[0],'Mentaliste',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Jadzia","Dax",$ships_id[1],'Pilote',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Han","Solo",$ships_id[1],'Artilleur',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Leonard","McCoy",$ships_id[1],'Mecanicien',$_SESSION['id_user'],gametoid_game($_GET['game']));

ajouterHumain("Rip","Hunter",$ships_id[1],'Manutentionnaire',$_SESSION['id_user'],gametoid_game($_GET['game']));



}

function actualiser() {



    echo '

    <dialog id="actualiser">

        <p>Ce n\'est pas votre tour ! Veuillez attendre que l\'autre joueur joue.</p>

        <button onclick=\'window.location.href = "game.php?game='.$_GET["game"].'";\'>Actualiser</button>

    </dialog>

    <script>

    document.getElementById("actualiser").showModal();

    document.getElementById("actualiser").addEventListener("keydown", function(event) {if (event.key === "Escape") {event.preventDefault();}});</script>';

}





//boite de dialog pour artilleur

function artilleur($humain){

    global $pdo; // Accéder à l'objet PDO pour exécuter des requêtes SQL



    // Récupérer tous les vaisseaux sauf celui du tireur

    $sql = "SELECT v.* FROM vaisseaux v JOIN games g ON v.id_game = g.id_game WHERE v.id_vaisseau !=:id_Vaisseau AND g.game = :game AND v.status=1";

    $stmt = $pdo->prepare($sql);

    $id_vaisseau = $humain->getvaisseauobj()->getId();

    $stmt->bindParam(':id_Vaisseau', $id_vaisseau);

    $stmt->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

    $stmt->execute();

    $vaisseaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo'<dialog id="action"><h3>Choisir une cible</h3><form method="POST" action=""><select name="cible-artilleur" id="vaisseau-cible" required>';

    foreach ($vaisseaux as $vaisseau) {

        echo '<option value="' . htmlspecialchars($vaisseau['id_vaisseau']) . '">' . htmlspecialchars($vaisseau['nom']) . '</option>';

    }

    echo '</select>';

    echo '<input type="hidden" name="id_humain" value="' . htmlspecialchars($humain->Getid()) . '"><button type="submit">Tirer</button></form></dialog><script>document.getElementById("action").showModal();</script>';

}



//boite de dialog pour pilote

function pilote($humain){

    echo'<dialog id="action"><h3>Choisir une destination</h3><form method="POST" action=""><fieldset><legend>Coordonnées</legend><label>X: <input name="x" type="number" value="0"min=0 max=4 ></label><label>Y:<input name="y" type="number" value=0 min=0 max=4 ></label>';

    echo '<input type="hidden" name="id_humain" value="' . htmlspecialchars($humain->Getid()) . '"><button type="submit">Se déplacer</button></form></dialog><script>document.getElementById("action").showModal();</script>';

}



function mentalist($humain){

    global $pdo;

    // Récupérer tous les humains sauf les mentalistes

    $sql = "SELECT h.id_humain,g.game, h.nom, h.prenom, h.classe, v.id_vaisseau, v.status FROM humains h JOIN games g ON h.id_game = g.id_game JOIN vaisseaux v ON v.id_vaisseau = h.id_vaisseau WHERE h.classe != 'mentaliste' AND g.game = :game AND v.status=1";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

    $stmt->execute();

    $humains = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo'<dialog id="action"><h3>Choisir une cible</h3><form method="POST" action="">';

    echo '<input type="hidden" name="id_mentalist" value="' . htmlspecialchars($_POST["Mentaliste"]) . '">';

    echo '<select name="cible_mentalist" id="cible" required>';

    foreach ($humains as $humain) {

        echo '<option value="' . htmlspecialchars($humain['id_humain']) . '">' . htmlspecialchars($humain['nom']) ." ". htmlspecialchars($humain['prenom']) . '</option>';

    }

    echo '</select><button type="submit">Influencer</button></form></dialog><script>document.getElementById("action").showModal();</script>';

  

}



function mentalist_action($humain){



    global $pdo;

      // Vérifier le rôle de l'humain (pilote ou artilleur)

    $Metier = $humain->getMetier();

    // Si l'humain est un pilote

    if ($Metier == 'Pilote') {

        echo '<dialog id="action">

        <h3>Choisir une destination</h3>

        <form method="POST" action="">

        <input type="hidden" name="id_mentalist" value="' . htmlspecialchars($_POST["id_mentalist"]) . '">

        <input type="hidden" name="id_humain" value="' . htmlspecialchars($humain->getId()) . '">     

        <fieldset>

                <legend>Coordonnées</legend>

                <label>X: <input name="x" type="number" min="0" max=4 value="0" required /></label>

                <label>Y: <input name="y" type="number" min="0" max=4 value="0" required /></label>

            </fieldset>

            <button type="submit">Se déplacer</button>

        </form></dialog><script>document.getElementById("action").showModal();</script>';

    }

    // Si l'humain est un artilleur

    elseif ($Metier == 'Artilleur') {

        // Récupérer tous les vaisseaux sauf celui de l'artilleur

        $sql_vaisseaux = "SELECT v.* FROM vaisseaux v JOIN games g ON v.id_game = g.id_game WHERE id_vaisseau !=:id_Vaisseau AND g.game = :game AND v.status=1";

        $stmt_vaisseaux = $pdo->prepare($sql_vaisseaux);

        $stmt_vaisseaux->bindParam(':id_Vaisseau', $humain->getVaisseau(), PDO::PARAM_INT);

        $stmt_vaisseaux->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

        $stmt_vaisseaux->execute();

        $vaisseaux = $stmt_vaisseaux->fetchAll(PDO::FETCH_ASSOC);



        echo '

        <dialog id="action">

            <h3>Choisir une cible</h3>

            <form method="POST" action="">

                    <input type="hidden" name="id_humain" value="' . htmlspecialchars($humain->getId()) . '">     

            <input type="hidden" name="id_mentalist" value="' . htmlspecialchars($_POST["id_mentalist"]) . '">

                <select name="cible-artilleur" id="vaisseau-cible" required>';

        

        // Remplir la liste déroulante avec les vaisseaux disponibles

        foreach ($vaisseaux as $vaisseau) {

            echo '<option value="' . htmlspecialchars($vaisseau['id_vaisseau']) . '">' . htmlspecialchars($vaisseau['nom']) . '</option>';

        }



        echo '

                </select>

                <input type="hidden" name="id_humain" value="' . htmlspecialchars($humain->Getid()) . '">

                <button type="submit">Tirer</button>

            </form>

        </dialog><script>document.getElementById("action").showModal();</script>';

    }

}



?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/game.css">
    <link rel="stylesheet" href="css/dialog.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <title>Fleet Revenge | <?php echo htmlspecialchars($_GET['game']); ?></title>

</head>

<body>

    <div class="wrapper">
        <div class="bords">
            <img id="bord-haut" src="img/bords-vertical.png" alt="">
            <img id="bord-gauche" src="img/bords-horizontal.png" alt="">
            <img id="bord-droit" src="img/bords-horizontal.png" alt="">
            <img id="bord-bas" src="img/bords-vertical.png" alt="">
        </div>
    </div>

<?php



// Vérifie si l'utilisateur est bien connecté et s'il participe à la partie

if (empty($_GET['game']) || empty($_SESSION['id_user'])) {

    header('Location: index.php');

} else {

    $sql = "SELECT * FROM games WHERE (Player1 = :user OR Player2 = :user) AND game = :game AND finish !=1";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':user', $_SESSION['id_user'], PDO::PARAM_STR);

    $stmt->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

    $stmt->execute();

    $game = $stmt->fetch(PDO::FETCH_ASSOC);



    if (empty($game)) {

        header('Location: index.php');

    } else {

        // ID de la partie et de l'utilisateur actuel

        $id_game = gametoid_game($_GET['game']);

        $id_user = $_SESSION['id_user'];

        // echo $id_user;

        // echo $game['Player1'];

        $isPlayer1 = ($id_user == $game['Player1']); // Détermine si l'utilisateur est Player1



        // Récupère le dernier log de la partie actuelle

        $sql_last_log = "SELECT * FROM log WHERE id_game = :id_game ORDER BY datetime DESC LIMIT 1";

        $stmt_last_log = $pdo->prepare($sql_last_log);

        $stmt_last_log->bindParam(':id_game', $id_game, PDO::PARAM_INT);

        $stmt_last_log->execute();

        $last_log = $stmt_last_log->fetch(PDO::FETCH_ASSOC);

        

        // Récupérer tous les vaisseaux de la partie

        $sql_vaisseaux = "SELECT v.*, g.game FROM vaisseaux v JOIN games g ON v.id_game = g.id_game WHERE g.game = :game AND status=1";

        $stmt_vaisseaux = $pdo->prepare($sql_vaisseaux);

        $stmt_vaisseaux->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

        $stmt_vaisseaux->execute();

        $vaisseaux_data = $stmt_vaisseaux->fetchAll(PDO::FETCH_ASSOC);



        // Créer les instances de vaisseaux

        $vaisseaux = [];

        foreach ($vaisseaux_data as $data) {

            $vaisseau = VaisseauFactory::creervaisseau(

                $data['classe'],$data['nom'],json_decode($data['position'], true),$data['id_vaisseau'],$data['pv'],$data['id_user']);

            $vaisseaux[] = $vaisseau;

        }

        // print_r($vaisseaux);

        // Séparer les vaisseaux par joueur (celui de l'utilisateur et ceux des ennemis)

        $vaisseaux_user = [];

        $vaisseaux_ennemis = [];

        foreach ($vaisseaux as $vaisseau){

            $user = $vaisseau->getIdUser();

            // echo $user;

            if ($user == $id_user) {

                // print_r($vaisseau);

                $vaisseaux_user[] = $vaisseau; // Vaisseaux de l'utilisateur

            } else {

                $vaisseaux_ennemis[] = $vaisseau; // Vaisseaux ennemis

            }

        }



        // Récupérer les humains

        $sql_humains = "SELECT h.*, g.game, v.status, v.id_vaisseau FROM humains h JOIN games g ON g.id_game = h.id_game JOIN vaisseaux v ON v.id_vaisseau = h.id_vaisseau WHERE game = :game AND v.status=1";

        $stmt_humains = $pdo->prepare($sql_humains);

        $stmt_humains->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

        $stmt_humains->execute();

        $humains_data = $stmt_humains->fetchAll(PDO::FETCH_ASSOC);



        // Créer les instances d'humains et associer les vaisseaux

        $humains = [];

        foreach ($humains_data as $data) {

            $humain = HumanFactory::creerHumain(

                $data['classe'],

                $data['nom'],

                $data['prenom'],

                $data['id_humain'],

                $data['mana'],

                $data['id_vaisseau'],

                $data['id_user']

            );



            foreach ($vaisseaux as $vaisseau) {

                if ($vaisseau->getId() == $data['id_vaisseau']) {

                    $humain->Setvaisseau($vaisseau);

                    break;

                }

            }

            $humains[] = $humain;

        }



        // Récupérer tous les logs de la partie

        $sql_logs = "SELECT l.*, g.* FROM log l JOIN games g ON l.id_game = g.id_game WHERE g.game = :game";

        $stmt_logs = $pdo->prepare($sql_logs);

        $stmt_logs->bindParam(':game', $_GET['game'], PDO::PARAM_STR);

        $stmt_logs->execute();

        $logs_data = $stmt_logs->fetchAll(PDO::FETCH_ASSOC);



        // Créer les instances de vaisseaux

        $logs = [];

        foreach ($logs_data as $data) {

            $log = New log(

                $data['id_user'],$data['id_game'],$data['id_humain'],$data['action'],$data['id_log']);

            $logs[] = $log;

        }

        



        if(empty($last_log) && !$isPlayer1 && empty($vaisseaux_user)){

            include_once("include/create_vaisseau.php");

        } 





        // ici commence les trucs

        // Afficher les vaisseaux et les humains
        echo "<div class='bottom_container'>";
        echo "<div class='container-user'>";
        echo "<h2>Vaisseaux alliés</h2>";
        echo "<div class='vaisseau-user-container'>";

        foreach ($vaisseaux_user as $vaisseau) {
        // Points de vie du vaisseau
        $pv = $vaisseau->getPV();
        // echo $pv;
        $pv_max = 100; // Points de vie maximum
        $pv_percentage = intval(($pv / $pv_max) * 100); // Calcul de la largeur de la barre de vie
        
        // Choisir la couleur en fonction du pourcentage de vie
        $meter_class = 'green'; // Par défaut, si la vie est au-dessus de 50%
        if ($pv_percentage < 50 && $pv_percentage >= 20) {
            $meter_class = 'orange';
        } elseif ($pv_percentage < 20) {
            $meter_class = 'red';
        }

         // Afficher le nom du vaisseau et sa barre de vie
            echo "<div class='vaisseau-container'>";
            echo "<div class='vaisseaux-metier-container'>";
            echo "<h3>" . htmlspecialchars($vaisseau->getNom()) . " ({$vaisseau->getMetier()})</h3>";
            echo "<div class='meter-container'>";  // Conteneur de la barre de vie
            echo "<div class='meter'><span class='$meter_class' style='width: {$pv_percentage}%;'></span></div>";
            echo "{$pv} PV</div>";
            echo "</div>";

        // Affichage des humains associés à ce vaisseau
            echo "<div class='humain-container'>";
            echo "<ul>";
            foreach ($humains as $humain) {

                if ($humain->getvaisseauobj()->getId() == $vaisseau->getId()) {

                    echo "<li><form method='post'><button type='submit' name='{$humain->getMetier()}' value='{$humain->Getid()}'>" . htmlspecialchars($humain->getNom()) . " " . htmlspecialchars($humain->getPrenom()) ." ({$humain->getMetier()})</button></form></li>";

                }
            }
            echo "</ul>";
            echo "</div>";
            echo "</div>";
        }
            echo "</div>";
            echo " </div>";


        echo "<div class='vaisseau-enemy-container'>";
        echo "<div class='container-enemy'>";
         echo "<h2>Vaisseaux de l'ennemi</h2>";

        foreach ($vaisseaux_ennemis as $vaisseau) {

            echo "<h3>" . htmlspecialchars($vaisseau->getNom()) . " ({$vaisseau->getMetier()}) {$vaisseau->getPV()}</h3>";

        }

        echo "<table class='table-container'>";
    
        echo "<tr><th></th>";
        foreach (range('0', '4') as $chiffre) {
            echo "<th>{$chiffre}</th>";
        }
        echo "</tr>";
    
        for ($i = 0; $i < 5; $i++) {
            echo "<tr><th>{$i}</th>"; 
            for ($j = 0; $j < 5; $j++) {
                $grille = (($i + $j) % 2 == 0) ? 'color1' : 'color2';
                $nom_vaisseaux = '';
                foreach ($vaisseaux_user as $vaisseau) {
                    $position = $vaisseau->getPos(); 
                    if ($position['x'] == $i && $position['y'] == $j) {
                        $nom_vaisseaux = htmlspecialchars($vaisseau->getNom());
                    }
                }

                foreach ($vaisseaux_ennemis as $vaisseau) {
                    $position = $vaisseau->getPos();
                    if ($position['x'] == $i && $position['y'] == $j) {
                        $nom_vaisseaux = htmlspecialchars($vaisseau->getNom());
                    }
                }
                echo "<td class='{$grille}'>{$nom_vaisseaux}</td>";
            }
            echo "</tr>";
        }
    
        echo "</table>";
        echo " </div>";
        echo " </div>";
        echo " </div>";

        // Afficher les logs
    echo "<div class='logs-list'>";
    echo "<h2>Logs de la Partie " . htmlspecialchars($_GET['game']) . "</h2>";


    foreach ($logs as $log) {

        echo '<div class="log-item">';
        echo "<p><strong>Action:</strong> " . htmlspecialchars($log->getAction()) . "</p>";
        echo "<p><strong>ID Humain:</strong> " . htmlspecialchars($log->getHumain()) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($log->getDatetime()) . "</p>";
        echo '</div>';
    }
    echo "</div>";
    }

// Vérifie si c'est le tour de l'autre joueur

if (empty($last_log) && $isPlayer1|| (!empty($last_log) && $last_log['id_user'] == $id_user)) {

    actualiser();

}

else{

    include_once('include/action.php');

}

}

?>

</body>

</html>



</body>

</html>

