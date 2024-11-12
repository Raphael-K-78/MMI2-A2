<?php
if(empty($vaisseaux_user) && !empty($logs)|| empty($vaisseaux_ennemis) && !empty($logs)){
    echo '
    <dialog id="finish">
        <p>Fin de la partie</p>
        <button onclick=\'window.location.href = "game.php?game='.$_GET["game"].'";\'>Actualiser</button>
    </dialog>
    <script>
    document.getElementById("actualiser").showModal();
    document.getElementById("actualiser").addEventListener("keydown", function(event) {if (event.key === "Escape") {event.preventDefault();}});</script>';
    $sql = "UPDATE games SET finish = 1 WHERE game = :game";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':game' => $_GET['game']]);
}


if(isset($_POST['cible_mentalist'])&& isset($_POST['id_mentalist'])){
    $humain = getHumainById($_POST['cible_mentalist']);
    $mentalist = getHumainById($_POST['id_mentalist']);
    // print_r($humain);
    // echo $humain->getMetier();
    if($humain->getMetier() != 'Mecanicien' ||$humain->getMetier() != 'Manutentionnaire'){
        mentalist_action($humain);
    }
    if($humain->getMetier() == 'Mecanicien' ||$humain->getMetier() == 'Manutentionnaire'){
        $humain->agir();
        $mentalist->setXP();
        echo '<script>window.location.href = "game.php?game='.$_GET["game"].'";</script>';
        // print_r($_POST);
    //    echo $_POST[$humain->getMetier()];
    }
    };

if(isset($_POST['Pilote'])){
    $humain = getHumainById($_POST['Pilote']);
    pilote($humain);
}
elseif(isset($_POST['Artilleur'])){
    $humain = getHumainById($_POST['Artilleur']);
    artilleur($humain);
    
}

elseif(isset($_POST['Mecanicien'])){
    getHumainById($_POST['Mecanicien'])->agir();
    if(isset($_POST['id_mentalist'])){
        getHumainById($_POST['id_mentalist'])->setXP();
    }
    echo '<script>window.location.href = "game.php?game='.$_GET["game"].'";</script>';
}
elseif(isset($_POST['Manutentionnaire'])){
    getHumainById($_POST['Manutentionnaire'])->agir();
    if(isset($_POST['id_mentalist'])){
        getHumainById($_POST['id_mentalist'])->setXP();
    }
    echo '<script>window.location.href = "game.php?game='.$_GET["game"].'";</script>';

}
elseif(isset($_POST['Mentaliste'])){
    $humain = getHumainById($_POST['Mentaliste']);
    if($humain->influencer()){
        mentalist($humain);
    }
}
elseif(isset($_POST['x']) && isset($_POST['y'])){
    getHumainById($_POST['id_humain'])->agir($_POST['x'],$_POST['y']);
    if(isset($_POST['id_mentalist'])){
        getHumainById($_POST['id_mentalist'])->setXP();
    }
    echo '<script>window.location.href = "game.php?game='.$_GET["game"].'";</script>';

}
elseif(isset($_POST['cible-artilleur']) && isset($_POST['id_humain'])){
    $vaisseau = getVaisseauById($_POST['cible-artilleur']);
    getHumainById($_POST['id_humain'])->agir($vaisseau);
    if(isset($_POST['id_mentalist'])){
        getHumainById($_POST['id_mentalist'])->setXP();
    }
    echo '<script>window.location.href = "game.php?game='.$_GET["game"].'";</script>';
}


?>