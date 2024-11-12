<?php

include_once("secure/mdp.php");

include_once("include/pdo.php");

include_once("include/session.php");

include_once("include/class.php");

/*function temp_destroy(){

    echo"<form action='' method='post'><button type='submit' name='user_action' value='destroy'>Déconnexion</button></form>";

    if(isset($_POST['user_action']) && $_POST['user_action'] == 'destroy'){

        session_destroy();

        header('Location: index.php');

    }

}*/



function list_game($user_id) {

    global $pdo;

    try {

        // Requête SQL avec jointure sur la table users pour récupérer les noms des joueurs

        $sql = "SELECT games.game, games.Player1, games.Player2, 

                       u1.user_name AS player1_name, u2.user_name AS player2_name

                FROM games

                LEFT JOIN users u1 ON games.Player1 = u1.id_user

                LEFT JOIN users u2 ON games.Player2 = u2.id_user

                WHERE (games.Player1 = :user_id OR games.Player2 = :user_id) AND games.finish = 0";



        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        

        $games = $stmt->fetchAll(PDO::FETCH_ASSOC);

        

        if ($games) {
            echo "<div class='game-list'>";
            echo "<h3>Parties en cours :</h3>";
            foreach ($games as $game) {

                echo '<form method="GET" action="game.php">

                        <label>'.$game['game'].': '.$game['player1_name'].' VS '.$game['player2_name'].'</label>

                        <button type="submit" name="game" value="'.$game['game'].'">Reprendre la partie</button>

                      </form>';

            }
        echo "</div>";
        } else {
            echo "<div class='game-list'>";
            echo "<p>Aucune partie en cours.</p>";
            echo "</div>";
        }

    } catch (PDOException $e) {

        echo "<script>err ='Erreur lors de la récupération des parties : " . $e->getMessage() . "';console.log(err)</script>";

    }

}



?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/list-game.css">
    <link rel="stylesheet" href="css/dialog.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <title>Fleet Revenge | le meilleur jeu du monde</title>

</head>

<body>

    <?php

    include_once("include/login.php");//login.php a utiliser que quand tu as appuyer le bouton se connecter

    include_once("include/signin.php");//s'affiche que si appuyer sur bouton créer un compte

    include_once("include/create_game.php")// s'affiche que si on appuye sur nouvelle partie

     ?>
    <div class="wrapper">
        <div class="bords">
            <img id="bord-haut" src="img/bords-vertical.png" alt="Bordure en haut de l'écran">
            <img id="bord-gauche" src="img/bords-horizontal.png" alt="Bordure à gauche de l'écran">
            <img id="bord-droit" src="img/bords-horizontal.png" alt="Bordure à droite de l'écran">
            <img id="bord-bas" src="img/bords-vertical.png" alt="Bordure en bas de l'écran">
        </div>
        <div class="home-container">
            <div class="logo-container">
                <hr>
                <div class="logo">
                <img src="img/logo.svg" alt="Logo du jeu Fleet Revenge">
                </div>
                <div class="connexion-container">
                <?php
                if ($_SESSION['connexion'] == 'Déconnexion') {
                        echo '
                        <div class="profile-icon">
                                <img src="img/profile.svg" alt="Icon de profil">
                        </div>
                        <form method="post"><button id="connect" name="user_action" value="destroy" type="submit">Déconnexion</button></form>
                        <script>console.log("connected")</script>
                        ';
                    }
                ?>
            </div>
        </div>
    <?php
    if ($_SESSION['connexion'] == 'Déconnexion') {
    // Afficher le bouton Nouvelle Partie uniquement si l'utilisateur est connecté
        echo '
        <button id ="new_game" onclick=\'document.getElementById("create_game").showModal()\'>Nouvelle Partie</button>';
        list_game($_SESSION['id_user']);
    }
    else{
        echo'
        <div class="login-container">
        <button id="connect" onclick="document.getElementById(\''. $_SESSION['connexion'] .'\').showModal();">'.$_SESSION['connexion'] .'</button>
        </div>';
    }

?>

<!--

<button onclick="document.getElementById('<?php //echo $_SESSION['connexion'] ?>').showModal();"><?php //echo $_SESSION['connexion'] ?></button>

$_SESSION['connexion'];

 document.getElementById(\"create_game\").showModal()

  -->  

</body>

</html>