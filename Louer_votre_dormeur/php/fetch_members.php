<?php
include_once("pdo.php");

$filter = isset($_GET['filter']) ? $_GET['filter'] : null; 
$order = isset($_GET['order']) ? $_GET['order'] : null; 

$sql = "SELECT * FROM Membres WHERE 1";

if ($filter) {
    $sql .= " AND " . $filter; // Ajout des conditions de filtre
}

if ($order) {
    $sql .= " ORDER BY " . $order;
}

$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>