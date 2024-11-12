<?php
// CLASSES HUMAIN

class Humain {
    protected $nom;
    protected $prenom;
    protected $xp;
    protected $id_vaisseau;
    protected $id;
    protected $vaisseau;
    protected $id_user;

    public function getMetier() {
        return get_class($this);
    }
    public function getNom() {
        return $this->nom;
    }
    public function getPrenom() {
        return $this->prenom;
    }

    public function Setvaisseau($vaisseau){
        $this->vaisseau = $vaisseau;
    }
    public function getVaisseau(){
        return $this->id_vaisseau;
    }

    public function getvaisseauobj(){
        return $this->vaisseau;
    }
        

    public function Getid(){
        return $this->id;
    }
    public function Getuser(){
        return $this->id_user;
    }
    
    public function __construct($nom, $prenom,$id,$id_vaisseau,$id_user) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->id = $id;
        $this->xp = 0;
        $this->id_vaisseau = $id_vaisseau;
        $this->id_user = $id_user;


    }

    public function calculerBonus(){
        return $this->xp/250;

    }
    public function setXP() {
        $xpGain = rand(1, 5);
        $this->xp += $xpGain;
        global $id_game;
        global $pdo;
        $stmt = $pdo->prepare("UPDATE humains SET xp = :xp WHERE id_humain = :id");
        $stmt->bindParam(':xp', $this->xp, PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        $action =  "{$this->nom} {$this->prenom} à gagné {$xpGain} XP!";
        $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);        
    }

    public function getXP() {
        return $this->xp;
    }


    public function sePresenter() {
        echo "Bonjour, je suis {$this->prenom} {$this->nom}, un(e) " . get_class($this) . ".<br>";
    }
}

// CLASSES OPÉRATEUR

class Operateur extends Humain {
    public function __construct($nom, $prenom,$id,$id_vaisseau,$id_user) {
        parent::__construct($nom, $prenom,$id,$id_vaisseau,$id_user);
    }

}

class Pilote extends Operateur {
    public function agir($x, $y) {
        global $pdo;
        $pos = "UPDATE vaisseaux SET position = JSON_OBJECT('x', :x, 'y', :y) WHERE id_vaisseau = :id_vaisseau";
        $stmt = $pdo->prepare($pos);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, PDO::PARAM_INT);
        $stmt->bindParam(':id_vaisseau', $this->id_vaisseau, PDO::PARAM_INT);
        $stmt->execute();
        global $id_game;
        $action =  "{$this->nom} {$this->prenom} à déplacer le vaisseau à X:{$x}, Y:{$y}";
        $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);
        $this->setXP();

    }
}

class Mecanicien extends Operateur {
    public function agir() {//créer le log
        global $pdo;
        $pv = intval(rand(1,20)*(1+$this->calculerBonus())+$this->vaisseau->getPV());
        // echo $this->vaisseau->getPV();
        // echo $pv;
        $this->vaisseau->setpv( $pv);
        if($pv>100){
            $this->vaisseau->setpv(100);
        }

        // echo $this->vaisseau->getPV();
        $sql_pv = "UPDATE vaisseaux SET pv = :pv  WHERE id_vaisseau = :id_vaisseau";
        $stmt = $pdo->prepare($sql_pv);
        $stmt->bindParam(':pv', $pv, PDO::PARAM_INT);
        $stmt->bindParam(':id_vaisseau', $this->id_vaisseau, PDO::PARAM_INT);
        $stmt->execute();
        $action =  "{$this->nom} {$this->prenom} a réparer le vaisseau {$this->vaisseau->getnom()}";
        global $id_game;
        $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);
        $this->setXP();
    }
}

class Manutentionnaire extends Operateur {
    public function agir() {
        $action =  "{$this->nom} {$this->prenom} a Nettoyé le vaisseau";
        global $id_game;
        $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);
        $this->setXP();
    }
}

class Artilleur extends Operateur {
    public function agir($vaisseau) {
        //Vérifier que l'artilleur possède un vaisseau associé
        if ($this->vaisseau instanceof Vaisseau && $vaisseau instanceof Vaisseau && $vaisseau->getId() !== $this->vaisseau->getId()) {
            // Calcul de la distance entre le vaisseau de l'artilleur et la cible
            $distance = sqrt(pow($vaisseau->getpos()['x'] - $this->vaisseau->getpos()['x'], 2) + 
                             pow($vaisseau->getpos()['y'] - $this->vaisseau->getpos()['y'], 2));
            
            // Si la cible est à portée
            if ($distance <= 4) {
                $degat = intval(rand(1, 20) * (1+$this->calculerBonus()) * (1+$this->vaisseau->getPuissance()));
                // echo $degat;
                $vaisseau->degat($degat,$this);
                $action = "{$this->prenom} {$this->nom} tire sur le vaisseau {$vaisseau->getnom()} et inflige {$degat} points de dégâts.";
            } 
                else {
                $action = "Le vaisseau cible est trop éloigné pour être touché.";
                }
            global $id_game;
                $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);
                
                $this->setXP();

        }
}
}

class Mentaliste extends Humain {
    private $mana;

    public function __construct($nom, $prenom,$id,$mana,$id_vaisseau,$id_user) {
        parent::__construct($nom, $prenom,$id,$id_vaisseau,$id_user);
        $this->mana = $mana;
    }

    public function influencer() {
        if ($this->mana >= 20) {            
                return true;
        } else {
            $this->mana += 50;
            global $pdo;
            $pos = "UPDATE humains SET mana = :mana WHERE id_humain = :id";
            $stmt = $pdo->prepare($pos);
            $stmt->bindParam(':mana', $this->mana, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->execute();
    
            $action = "{$this->prenom} {$this->nom} n'a pas assez d'énergie il se repose";
            global $id_game;
            $logs[] = new Log($_SESSION['id_user'],$id_game,$this->id,$action);
            $this->setXP();
            return false;
        }
    }

    public function seReposer() {//old
        $this->mana += 50;
    }
}

// CLASSES VAISSEAUX

class Vaisseau {
    protected $nom;
    protected $status;
    protected $position;
    protected $pv;
    protected $proprete;
    // protected $equipage; old
    protected $vitesse;
    protected $puissance;
    protected $solidite;
    protected $id;
    protected $id_user;

    public function getIdUser() {
        return $this->id_user;
    }

    public function getPos(){
        return $this->position;
    }
    
    public function setpv($pv){
        $this->pv = $pv;
    }

    public function getPuissance(){
        return $this->puissance;
    }
    public function getvitess(){
        return $this->vitesse;
    }
    public function getsolidite(){
        return $this->solidite;
    }
    public function getnom(){
        return $this->nom;
    }
    public function getuser(){
        return $this->id_user;
    }

    public function getId(){
        return $this->id;
    }
    public function getPV(){
        return $this->pv;
    }
    public function getMetier() {
        return get_class($this);
    }
    public function __construct($nom, $position, $vitesse, $puissance, $solidite,$id,$pv = 100,$id_user) {
        $this->nom = $nom;
        $this->status = true;
        $this->position = $position;
        $this->pv = $pv;
        $this->proprete = 100;
        $this->puissance = $puissance;
        $this->vitesse = $vitesse;
        $this->solidite = $solidite;
        $this->id = $id;
        $this->id_user = $id_user;
    }



    public function degat($pv,$humain) {//modif rajout log  et faire fonctionner le system de dégat
        $this->pv += -intval($pv/$this->solidite);
        global $pdo;
        if ($this->pv > 0) {
            $this->status = true;
            $action = "{$this->nom} à maintenant {$this->pv} pv";

        } else {
            $this->pv = 0;
            $action = "{$this->nom} a été détruit";
            $this->status = false;
        }

        $sql = "UPDATE vaisseaux SET pv = :pv, status = :status WHERE id_vaisseau = :id_vaisseau";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':pv', $this->pv, PDO::PARAM_INT);
        $stmt->bindParam(':status', $this->status, PDO::PARAM_BOOL); 
        $stmt->bindParam(':id_vaisseau', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        global $id_game;
            $logs[] = new Log($_SESSION['id_user'],$id_game,$humain->getId(),$action);

    }


    public function esquive($humain) {
        $random = $this->vitesse * (rand(0, 100) / 100);
        $result = $random >= 1;
        
        if ($result) {
            $action = "{$this->nom} a esquivé l'attaque.";
            global $id_game;
            $logs[] = new Log($_SESSION['id_user'],$id_game,$humain->id,$action);
        }

        return $result;
    }

    public function attaque($cible) {//old
        if (!$cible->esquive($this) && $cible->status) {
            $degat = -intval($this->puissance * (rand(1, 20)));
            if ($degat == 0) {
                $degat++;
            }
            echo "{$this->nom} attaque {$cible->nom}, il a perdu {$degat} PV.<br>";
            $cible->degat($degat);
        }
    }
}

class Blackbird extends Vaisseau {
    public function __construct($nom, $position,$id,$pv,$id_user) {
        parent::__construct($nom, $position, 2.5, 1.5, 1,$id,$pv,$id_user);
    }
}

class Enterprise extends Vaisseau {
    public function __construct($nom, $position,$id,$pv,$id_user) {
        parent::__construct($nom, $position, 1, 1.5, 2.5,$id,$pv,$id_user);
    }
}

class Panthera extends Vaisseau {
    public function __construct($nom, $position,$id,$pv,$id_user) {
        parent::__construct($nom, $position, 2, 2, 1,$id,$pv,$id_user);
    }
}

class Kaiten extends Vaisseau {
    public function __construct($nom, $position,$id,$pv,$id_user) {
        parent::__construct($nom, $position, 1, 2, 2,$id,$pv,$id_user);
    }
}

class Soukhoi extends Vaisseau {
    public function __construct($nom, $position,$id,$pv,$id_user) {
        parent::__construct($nom, $position, 2, 1, 2,$id,$pv,$id_user);
    }
}

//class factory

class VaisseauFactory {
    public static function creervaisseau($class, $nom, $position,$id,$pv,$id_user) {
        switch ($class) {
            case 'Soukhoi':
                return new Soukhoi($nom, $position,$id,$pv,$id_user);
            case 'Kaiten':
                return new Kaiten($nom, $position,$id,$pv,$id_user);
            case 'Panthera':
                return new Panthera($nom, $position,$id,$pv,$id_user);
            case 'Enterprise':
                return new Enterprise($nom, $position,$id,$pv,$id_user);
            case 'Blackbird':
                return new Blackbird($nom, $position,$id,$pv,$id_user);
        }
    }
    public static function getVaisseauById($id_vaisseau) {
        global $pdo;  // Assurez-vous que la variable PDO est accessible ici

        // Récupérer le vaisseau depuis la base de données en fonction de son ID
        $sql = "SELECT * FROM vaisseaux WHERE id_vaisseau = :id_vaisseau";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_vaisseau', $id_vaisseau, PDO::PARAM_INT);
        $stmt->execute();
        $vaisseau_data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si le vaisseau existe, créer un objet Vaisseau et le retourner
        if ($vaisseau_data) {
            return VaisseauFactory::creervaisseau(
                $vaisseau_data['classe'],
                $vaisseau_data['nom'],
                json_decode($vaisseau_data['position'], true),
                $vaisseau_data['id_vaisseau'],
                $vaisseau_data['pv'],
                $vaisseau_data['id_user'],
            );
        } else {
            return null;  // Retourne null si le vaisseau n'a pas été trouvé
        }
    }
}

class HumanFactory {
    public static function creerHumain($type, $nom, $prenom,$id,$mana,$id_vaisseau,$id_user) {
        switch ($type) {
            case 'Pilote':
                return new Pilote($nom, $prenom,$id,$id_vaisseau,$id_user);
            case 'Artilleur':
                return new Artilleur($nom, $prenom,$id,$id_vaisseau,$id_user);
            case 'Mecanicien':
                return new Mecanicien($nom, $prenom,$id,$id_vaisseau,$id_user);
            case 'Manutentionnaire':
                return new Manutentionnaire($nom, $prenom,$id,$id_vaisseau,$id_user);
            case 'Mentaliste':
                return new Mentaliste($nom, $prenom,$id,$mana,$id_vaisseau,$id_user);
        }
    }
}

class Log{
    protected $id_user;
    protected $id_game;
    protected $datetime;
    protected $id_humain;
    protected $action;
    protected $id;// si pas dans db vide sinon id_log

    public function Getid(){
        return $this->id;
    }

    protected function newlog(){
            global $pdo;
            $sql = "INSERT INTO log (id_user, id_game, datetime, id_humain, action) 
                    VALUES (:id_user, :id_game, :datetime, :id_humain, :action)";
        
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id_user', $this->id_user, PDO::PARAM_INT);
            $stmt->bindParam(':id_game', $this->id_game, PDO::PARAM_INT);
            $stmt->bindParam(':datetime', $this->datetime);
            $stmt->bindParam(':id_humain', $this->id_humain, PDO::PARAM_INT);
            $stmt->bindParam(':action', $this->action, PDO::PARAM_STR);
    
            $stmt->execute();
            return $pdo->lastInsertId();

    }

    public function gethumain(){
        return $this->id_humain;
    }
    public function getdatetime(){
        return $this->datetime;
    }
    public function getaction(){
        return $this->action;
    }

    public function __construct($id_user,$id_game,$id_humain,$action,$id = NULL,$datetime = NULL){
        $this->id_user = $id_user;
        $this->id_game = $id_game;
        $this->datetime = $datetime ?? $datetime = date('Y-m-d H:i:s', time());
        $this->id_humain = $id_humain;
        $this->action = $action;
        if(empty($id)){
           $this->id = $this->newlog(); 
        }

    }
}


function ajouterHumain($nom, $prenom, $vaisseau, $classe, $id_user, $id_game) {
    global $pdo; // Accéder à la variable globale $pdo

    // Préparation de la requête d'insertion
    $sql = "INSERT INTO humains (nom, prenom, id_vaisseau, classe, id_user, id_game) 
            VALUES (:nom, :prenom, :vaisseau, :classe, :id_user, :id_game)";

    $stmt = $pdo->prepare($sql);

    // Liaison des paramètres
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':vaisseau', $vaisseau);
    $stmt->bindParam(':classe', $classe);
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $stmt->bindParam(':id_game', $id_game, PDO::PARAM_INT);
    $stmt->execute();
}

function gametoid_game($gameName) {
    global $pdo;  // Utilisation de la variable PDO globale

    // Préparer la requête SQL pour récupérer l'id_game basé sur le nom du jeu
    $sql = "SELECT id_game FROM games WHERE game = :game";
    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':game', $gameName, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['id_game'];
    }
}

function getHumainById($id_humain) {
    global $humains;
    foreach ($humains as $humain) {
        // Vérifier si l'ID de l'humain correspond à l'ID passé en paramètre
        if ($humain->getId() == $id_humain) {
            return $humain;  // Retourne l'objet humain si trouvé
        }
    }
    return null;
}

function getVaisseauById($id_vaisseau) {
    global $vaisseaux;
    foreach ($vaisseaux as $vaisseau) {
        // Vérifier si l'ID de l'humain correspond à l'ID passé en paramètre
        if ($vaisseau->getId() == $id_vaisseau) {
            return $vaisseau;  // Retourne l'objet humain si trouvé
            break;
        }
    }
    return null;
}
?>