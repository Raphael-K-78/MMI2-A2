<?php
include_once('secure/mdp.php');
try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo"<script>console.log('connected in db');</script>";
} catch (PDOException $e) {
    echo "<script>console.log(\"Connexion échouée : " . $e->getMessage()."\");</script>";    
}

?>
