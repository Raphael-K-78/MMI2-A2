<?php
session_start();

function verif_auth($id, $pwd) {
    global $pdo;
    
    // Préparer la requête pour vérifier le nom d'utilisateur et le mot de passe
    $sql = "SELECT user_name, user_password, id_user FROM users WHERE user_name = :user_name";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_name', $id, PDO::PARAM_STR);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur existe et si le mot de passe est correct
    if ($user && $user['user_password'] === $pwd) {
        $_SESSION['connexion'] = 'Déconnexion';
        $_SESSION['id_user'] = $user['id_user'];
        return true; // Succès
    }
    return false; // Échec d'authentification
}

function create_account($user_name, $password) {
    global $pdo;
    
    try {
        $query = "INSERT INTO users (user_name, user_password) VALUES (:user_name, :password)";
        $stmt = $pdo->prepare($query);
        
        // Liez les paramètres et exécutez la requête
        $stmt->bindParam(':user_name', $user_name, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();

        echo "<script>alert('Compte créé avec succès !');</script>";

    } catch (PDOException $e) {
        echo "<script>alert('Erreur lors de la création du compte : " . $e->getMessage() . "');</script>";
    }
}


// Initialiser la session de connexion si elle n'existe pas
if (empty($_SESSION['connexion'])) {
    $_SESSION['connexion'] = 'Connexion';
}

// Si l'utilisateur est en mode "Connexion"
    if (isset($_POST['user_action']) && $_POST['user_action'] == 'signin') {
        create_account($_POST['pseudo'], $_POST['password']);
    }

    elseif (isset($_POST['pseudo']) && isset($_POST['password'])) {
        if (verif_auth($_POST['pseudo'], $_POST['password'])) {
            $_SESSION['connexion'] = 'Déconnexion';
            // echo "<script>console.log('".$_SESSION['connexion']."')</script>";
            echo "<script>alert('Vous êtes connecté !');</script>";
        }
        else {
            echo "<script>console.log('".$_SESSION['connexion']."')</script>";
            echo "<script>alert('Identifiant et/ou mot de passe non valide.');</script>";
        }
    }
    elseif(isset($_POST['user_action']) && $_POST['user_action'] == 'destroy'){
        session_destroy();
        header('Location: index.php');
    }
?>