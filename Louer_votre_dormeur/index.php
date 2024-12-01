<?php
// include_once('../secure/mdp.php');
include_once("php/pdo.php");

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- lib js -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://kit.fontawesome.com/4c96bf3b71.js" crossorigin="anonymous"></script>    
    <script src="https://code.jquery.com/ui/1.14.0/jquery-ui.min.js" integrity="sha256-Fb0zP4jE3JHqu+IBB9YktLcSjI1Zc6J2b6gTjB0LpoM=" crossorigin="anonymous"></script>

    <!-- lib css -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="css/mobile.css" rel='stylesheet'/>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Louer votre Dormeur</title>
</head>
<body>
    <dialog class="pop-up" id="member_desc">
            <div class="popup-content">
                <button class="close"  onclick="document.getElementById('member_desc').close();">&times;</button>
                <div id='background_img'>
                    <p><span id="nom"> </span>&nbsp;<span id="prenom"></span></p>
                </div>
                <div>
                    <div>
                        <p><span id="status"></span>, &nbsp;<span id="sexe"></span></p>
                        <p><span id="age">&nbsp;</span>ans</p>
                        <div class="star">
                        </div>
                    </div>
                    <a id='tel_num'><button id='contact' >contact</button></a>
                </div>
            </div>
    </dialog>
    <header id="header">
        <div id="slider_text" role="alert">
            <p>Le site "Louer votre dormeur" est un projet humoristique et satirique réalisé dans le cadre d’un projet académique. Il a été conçu dans un esprit de divertissement et d’apprentissage, et ne doit en aucun cas être pris au sérieux. Il est crucial de rappeler que la location ou l’exploitation de personnes est formellement illégale et constitue une violation grave des droits humains. L’esclavage, sous toutes ses formes, est une pratique abominable qui doit être interdite à jamais et combattue sans relâche. Ce projet ne vise qu’à illustrer une démarche créative et pédagogique, sans aucune intention de promouvoir ou légitimer des pratiques immorales ou illégales.</p>
            <p>Le site "Louer votre dormeur" est un projet humoristique et satirique réalisé dans le cadre d’un projet académique. Il a été conçu dans un esprit de divertissement et d’apprentissage, et ne doit en aucun cas être pris au sérieux. Il est crucial de rappeler que la location ou l’exploitation de personnes est formellement illégale et constitue une violation grave des droits humains. L’esclavage, sous toutes ses formes, est une pratique abominable qui doit être interdite à jamais et combattue sans relâche. Ce projet ne vise qu’à illustrer une démarche créative et pédagogique, sans aucune intention de promouvoir ou légitimer des pratiques immorales ou illégales.</p>
        </div>
        <hgroup>
            <h1>Louer votre dormeur</h1>
            <p>Fatigué de dormir seul ? Vos nuits manquent de chaleur humaine ? 🛌✨ Avec Louer votre Dormeur, transformez vos nuits en expérience cosy ! Que vous ayez besoin d’un pro des câlins, d’un maître en ronronnements rassurants ou simplement d’un compagnon anti-froid, nous avons le dormeur qu’il vous faut. 😴🔥 Pourquoi dormir seul quand vous pouvez louer un peu de réconfort ? (Recommandé par 100% des oreillers délaissés.)</p>
            <button class="cta"><i class="fa-solid fa-moon"></i>Réservez votre dormeur !<i class="fa-solid fa-moon"></i></button>
        </hgroup>
    </header>
    <nav>
        <a href="#header"><i class="fa-solid fa-house"></i></a>
        <a href="#search"><i class="fa-solid fa-fingerprint"></i></a>
        <a href="#find"><i class="fa-solid fa-list generate"></i></a>
    </nav>
    <main>
        <section id='search'>
            <div>
                <?php
                $stmt = $pdo->prepare("SELECT ID, Nom, Prenom, Sexe FROM Membres ORDER BY Sexe, Nom, Prenom");//requete sql pour récupérer tous les noms
                $stmt->execute();//executer
                $membres = $stmt->fetchAll(PDO::FETCH_ASSOC);//récupération
                $groupes = ['Homme' => [], 'Femme' => [], 'LGBTQIA+' => []];// séparer les membres selon leur sexe
                foreach ($membres as $membre) {
                    if (isset($groupes[$membre['Sexe']])) {//vérifier que le sexe éxiste bien
                        $groupes[$membre['Sexe']][] = $membre;//ajouter à membre à la liste des membres selon leur sexe
                    }
                }
                ?>
                    <h2>Rechercher par le nom</h2>
                    <p>Vous avez déjà fais appel à nos service ? vous souhaitez avoir accès au contact d'un de nos dormeur ? Rechercher son nom directement</p>
                <select name="membres" id='membre'>
                    <option></option>
                    <?php
                    foreach ($groupes as $sexe => $membres_du_sexe) {
                        if (!empty($membres_du_sexe)) {
                            echo "<optgroup label='" . htmlspecialchars($sexe) . "'>";
                            foreach ($membres_du_sexe as $membre) {
                                $nom_prenom = htmlspecialchars($membre['Nom'] . ' ' . $membre['Prenom']);
                                echo "<option value='" . htmlspecialchars($membre['ID']) . "'>" . $nom_prenom . "</option>";
                            }
                            echo "</optgroup>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div>
                    <h2>Rechercher dans la liste</h2>
                    <p>Vous n'avez jamais fais appel à nous ? vous souhaitez contactez un dormeur mais vous ne savez pas qui choisir ? utiliser notre moteur de tri pour trouver le dormeur qu'il vous faut !</p>
                    <button class="cta generate"><i class="fa-solid fa-moon"></i>Voir la liste<i class="fa-solid fa-moon"></i></button>
            </div>
        </section>
        <section id='find'>
        <div id="filter">
        <h3>filtres:</h3>
    <div class="filter-group">
        <div class="filter-item">
            <label>
                Tranche d'âge
            </label>
            <div class="filter-inputs">
                <label>
                    <input type="checkbox" name="Age" value="18-25"> 18-25 ans
                </label>
                <label>
                    <input type="checkbox" name="Age" value="26-35"> 26-35 ans
                </label>
                <label>
                    <input type="checkbox" name="Age" value="36-50"> 36-50 ans
                </label>
                <label>
                    <input type="checkbox" name="Age" value="51-150"> 51 ans et plus
                </label>
            </div>
        </div>
    </div>

    <div class="filter-group">
        <div class="filter-item">
            <label>
                Sexe
            </label>
            <div class="filter-inputs">
                <label>
                    <input type="checkbox" name="Sexe" value="Homme"> Homme
                </label>
                <label>
                    <input type="checkbox" name="Sexe" value="Femme"> Femme
                </label>
                <label>
                    <input type="checkbox" name="Sexe" value="LGBTQIA+"> LGBTQIA+
                </label>
            </div>
        </div>
    </div>

    <div class="filter-group">
        <div class="filter-item">
            <label>
                Status
            </label>
            <div class="filter-inputs">
                <label>
                    <input type="checkbox" name="Status" value="Professionnel"> Professionnel
                </label>
                <label>
                    <input type="checkbox" name="Status" value="Amateur"> Amateur
                </label>
            </div>
        </div>
    </div>
</div>


        <div id="data">
        <div id="none">
    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
    Aucune donnée trouvée, modifiez les filtres.
    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
    </div>
        </div>
        </section>
    </main>
    
    <script src="js/script.js"></script>
</body>
</html>