<?php
// Vérification si le formulaire est soumis
if (isset($_POST['ship'])) {
    $ships_id = [];
    // Récupération des données envoyées
    $selected_ships = $_POST['ship']; // Un tableau des vaisseaux sélectionnés
    // Connexion à la base de données (pdo)
    // Démarrer la transaction pour assurer que les deux vaisseaux sont créés en même temps
    $pdo->beginTransaction();

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
    $pdo->commit();
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
?>