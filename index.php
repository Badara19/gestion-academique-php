<?php
$dossierPublic = "http://localhost/PHP/miniProjetV2/Public/";
require_once "Traitements/db.php";
require_once "Traitements/requete.php"; // Toujours charger les fonctions avant la logique

include "Include/header.php";
include "Include/navbar.php";
include "Include/sidebar.php"; 

$page = $_GET['page'] ?? 'home';

if ($page === 'niveaux') {
    $niveaux = getNiveauxComplets($pdo); 
}else if ($page === 'classes_details') {
    $idclasse = $_GET['id'] ?? null;
    if ($idclasse) {
        $modules = getModulesByClasse($pdo, $idclasse);
        $etudiants = getEtudiantsAvecMoyenne($pdo, $idclasse);
        $major = getMajorClasse($pdo, $idclasse);
        $elites = getEtudiantsElite($pdo, $idclasse);
    }else {
        // Si on essaie d'accéder à la liste sans ID, on redirige vers les niveaux
        header("Location: index.php?page=niveaux");
        exit();
    }
}
$stats = obtenirStatsEtudiants($pdo);
$statsNiveaux = obtenirEtudiantsParNiveau($pdo);

// Optionnel : Sécurité pour éviter l'erreur si la base est vide
if (!$stats) {
    $stats = ['total' => 0, 'admis' => 0, 'ajournes' => 0, 'exclus' => 0];
}

$file = "Pages/$page.php";
if (file_exists($file)) {
    include_once $file;
} else {
    include_once "Pages/error404.php";
}

include "Include/footer.php";
?>