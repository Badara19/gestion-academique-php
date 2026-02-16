<?php
// Rectification du chemin selon ton dossier réel
require('../fpdf186/fpdf.php'); 
include_once 'db.php'; 
require_once 'requete.php';


if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Récupération des données Étudiant
    $stmtE = $pdo->prepare("SELECT e.*, c.nomclasse, c.idclasse, n.nom as nom_niveau 
                             FROM etudiants e 
                             JOIN classes c ON e.idclasse = c.idclasse 
                             JOIN niveaux n ON c.idniveaux = n.idniveaux 
                             WHERE e.idetudiant = ?");
    $stmtE->execute([$id]);
    $etud = $stmtE->fetch();

    if(!$etud) die("Étudiant introuvable.");

    // 2. Récupération des notes (Rectifié avec nommodule et exclusion TP)
    $stmtN = $pdo->prepare("SELECT m.nommodule, e.note, e.type 
                              FROM evaluations e 
                              JOIN modules m ON e.idmodule = m.idmodule 
                              WHERE e.idetudiant = ? AND e.type IN ('Devoir', 'Examen')
                              ORDER BY m.nommodule ASC");
    $stmtN->execute([$id]);
    $notes = $stmtN->fetchAll();

    // 3. Configuration PDF (Style Moderne)
    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();
    
    // --- DESIGN EN-TÊTE ---
    $pdf->SetFillColor(41, 128, 185); // Bleu pro
    $pdf->Rect(0, 0, 210, 40, 'F'); 
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 15, utf8_decode('RELEVÉ DE NOTES'), 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 5, utf8_decode('Session 2025-2026'), 0, 1, 'C');
    $pdf->Ln(20);

    // --- BLOC INFOS ÉTUDIANT ---
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(35, 8, utf8_decode('Nom & Prénom:'), 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(75, 8, utf8_decode($etud['nom'].' '.$etud['prenom']), 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 8, 'Matricule:', 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, $etud['matricule'], 0, 1);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(35, 8, 'Niveau:', 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(75, 8, utf8_decode($etud['nom_niveau']), 0);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(30, 8, 'Classe:', 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, utf8_decode($etud['nomclasse']), 0, 1);
    $pdf->Ln(10);

    // --- TABLEAU DES NOTES ---
    $pdf->SetFillColor(236, 240, 241); // Gris très clair
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(100, 10, ' MODULE', 1, 0, 'L', true);
    $pdf->Cell(40, 10, 'TYPE', 1, 0, 'C', true);
    $pdf->Cell(50, 10, 'NOTE / 20', 1, 1, 'C', true);

    $pdf->SetFont('Arial', '', 10);
    $total = 0; $count = 0;

    foreach($notes as $n) {
        $pdf->Cell(100, 10, ' '.utf8_decode($n['nommodule']), 1);
        $pdf->Cell(40, 10, $n['type'], 1, 0, 'C');
        
        if($n['note'] < 10) $pdf->SetTextColor(200, 0, 0);
        $pdf->Cell(50, 10, number_format($n['note'], 2), 1, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        
        $total += $n['note'];
        $count++;
    }

    // --- ANALYSE ET MOYENNES ---
    $pdf->Ln(5);
    $moyenneEtudiant = ($count > 0) ? round($total / $count, 2) : 0;
    $moyenneClasse = getMoyenneGeneraleClasse($pdo, $etud['idclasse']);

    // Affichage Moyenne Classe (Plus discret)
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(127, 140, 141);
    $pdf->Cell(140, 8, utf8_decode('Moyenne de la classe : '), 0, 0, 'R');
    $pdf->Cell(50, 8, $moyenneClasse . ' / 20', 0, 1, 'C');

    // Affichage Moyenne Étudiant (Mise en avant)
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(140, 12, utf8_decode('MOYENNE GÉNÉRALE : '), 0, 0, 'R');
    
    $bg = ($moyenneEtudiant >= 10) ? [46, 204, 113] : [231, 76, 60];
    $pdf->SetFillColor($bg[0], $bg[1], $bg[2]);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(50, 12, number_format($moyenneEtudiant, 2) . ' / 20', 0, 1, 'C', true);

    // --- PIED DE PAGE ---
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetY(-50);
    $pdf->SetFont('Arial', 'I', 9);
    $pdf->Cell(0, 10, utf8_decode("Généré le " . date('d/m/Y à H:i')), 0, 1, 'C');

    $pdf->Output('I', 'Bulletin_'.$etud['matricule'].'.pdf');
}