<?php
include(__DIR__ . '/../secure/mdp.php');
session_start();
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $url = 'https://github.com/login/oauth/access_token';//lien pour créer un token
    $data = [
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $code,
        'redirect_uri' => $redirect_uri,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        echo json_encode(['error' => 'Erreur cURL: ' . curl_error($ch)]);
        exit;
    }
    
    curl_close($ch);
    // echo $response;
    $response_data = json_decode($response, true);
    if (isset($response_data['access_token'])) {
        echo json_encode(['access_token' => $response_data['access_token']]);//retourner le token
        $_SESSION['token'] = $response_data['access_token'];//stocker le token dans session
    }
    else {
        echo json_encode(['error' => 'Le jeton d\'accès n\'a pas pu être récupéré.', 'details' => $response_data]);//erreur
    }
}
?>
