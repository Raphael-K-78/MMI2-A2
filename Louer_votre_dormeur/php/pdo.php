<?php
$securePath = 'secure/mdp.php';
if (!file_exists($securePath)) {
    $securePath = '../secure/mdp.php';
}
include_once($securePath);

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo"<script>console.log('connected in db');</script>";
} 
catch (PDOException $e) {
    // break;
    echo "<script>console.log(\"Connexion échouée : " . $e->getMessage()."\");</script>";    
}

?>
