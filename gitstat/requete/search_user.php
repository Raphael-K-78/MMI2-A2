<?php
session_start();
if(isset($_GET['name'])){
    $name = $_GET['name'];
    $page = isset($_GET["per_page"]) ? $_GET["per_page"] : 10;
    $token = $_SESSION['token'];
    $url = "https://api.github.com/search/users?q=" . urlencode($name) . "&per_page=10";


    $ch = curl_init();//init
    curl_setopt($ch, CURLOPT_URL, $url);  // Requête pour obtenir les infos utilisateur   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // Ne pas afficher directement la réponse
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",  // En-tête avec le jeton d'accès
        "Accept: application/json",
        "User-Agent: PHP-cURL"  // Obligatoire pour GitHub
]);

    $rep = curl_exec($ch);//exec
    // $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);//récupérer le code HTTP
    curl_close($ch);//fermer la session curl
    echo $rep;
}
?>