<?php
session_start();
 if(isset($_GET['token'])){
    $token = $_GET['token'];
    // echo json_encode(['token'=>$token]);
    $ch = curl_init();//init
    curl_setopt($ch, CURLOPT_URL, "https://api.github.com/user");  // Requête pour obtenir les infos utilisateur   
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // Ne pas afficher directement la réponse
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",  // En-tête avec le jeton d'accès
        "Accept: application/json",
        "User-Agent: PHP-cURL"  // Obligatoire pour GitHub
]);

    $rep = curl_exec($ch);//exec
    $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);//récupérer le code HTTP
    curl_close($ch);//fermer la session curl

    if($http_code == 401 || $http_code == 422){
        echo json_encode(['token'=>'false',$rep]);
    }
    else{
        echo json_encode(['token'=>'true']);
        $_SESSION["token"] = $token;
    }


 }
 else{
    echo json_encode(["token"=>'false']);
 }
?>