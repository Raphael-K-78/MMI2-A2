<?php
include_once("pdo.php");

if (isset($_GET['dialog']) && is_numeric($_GET['dialog'])) {
    $id = intval($_GET['dialog']);
    $stmt = $pdo->prepare("SELECT * FROM Membres WHERE ID = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $membre = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($membre) {
        $avis = (intval($membre['MatchWin']) - intval($membre['MatchLoose'])) ;
        echo json_encode([
            'success' => true,
            'nom' => $membre['Nom'],
            'prenom' => $membre['Prenom'],
            'age' => $membre['Age'],
            'sexe'=> $membre['Sexe'],
            'avis'=> $avis,
            'id' => $membre['ID'],
            'status' => $membre['Status'],
            'phone' => $membre['phone']
        ]);
    }
}
?>