<?php 
require_once("db.php");

// Ta fonction corrigée
function getNiveauxComplets(PDO $pdo) {
    // 1. On récupère les niveaux (ce que tu as déjà fait)
    $stmt = $pdo->query("SELECT * FROM niveaux");
    $niveaux = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. On parcourt par RÉFÉRENCE pour ajouter les classes
    foreach ($niveaux as &$niveau) {
        $stmt= $pdo->prepare("SELECT * FROM classes WHERE idniveaux = :id");
        $stmt->execute(['id' => $niveau['idniveaux']]);
        // On crée la nouvelle clé 'classes' dans l'original
        $niveau['classes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($niveau); // Sécurité
    
    return $niveaux;
}


    function enregistrerClasse(PDO $pdo, $nomClasse, $idNiveau) {
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (nomclasse, idniveaux) VALUES (:nomclasse, :idniveaux)");
            return $stmt->execute(['nomclasse' => $nomClasse, 'idniveaux' => $idNiveau]);
        } catch (PDOException $e) {
            // Gérer l'erreur (par exemple, journaliser l'erreur)
            error_log("Erreur lors de l'enregistrement de la classe : " . $e->getMessage());
            return false;
        }
    }

    function enregistrerNiveau(PDO $pdo, $nomNiveau) {
        try {
            $stmt = $pdo->prepare("INSERT INTO niveaux (nom) VALUES (:nom)");
            return $stmt->execute(['nom' => $nomNiveau]);
        } catch (PDOException $e) {
            // Gérer l'erreur (par exemple, journaliser l'erreur)
            error_log("Erreur lors de l'enregistrement du niveau : " . $e->getMessage());
            return false;
        }
        }

function genererMatricule(PDO $pdo) {
    $sql = "SELECT COUNT(idetudiant) as total FROM etudiants";
    $stmt = $pdo->query($sql);
    $res = $stmt->fetch();
    $nb = $res['total'] + 1;
    // Format : MAT - ANNEE - NUMERO (ex: MAT-2026-001)
    return "MAT-" . date('Y') . "-" . str_pad($nb, 3, '0', STR_PAD_LEFT);
}

function inscrireEtudiant(PDO $pdo, $matricule, $nom, $prenom, $idclasse) {
    try {
        $sql = "INSERT INTO etudiants (matricule, nom, prenom, idclasse) VALUES (?, ?, ?, ?)";
        return $pdo->prepare($sql)->execute([$matricule, $nom, $prenom, $idclasse]);
    } catch (PDOException $e) {
        error_log("Erreur inscription : " . $e->getMessage());
        return false;
    }
}
/**
 * Supprime un étudiant et toutes ses notes associées
 */
function supprimerEtudiantComplet($pdo, $idetudiant) {
    try {
        // Début de la transaction
        $pdo->beginTransaction();

        // 1. Supprimer d'abord les notes (évaluations) de l'étudiant
        $sqlNotes = "DELETE FROM evaluations WHERE idetudiant = :id";
        $stmtNotes = $pdo->prepare($sqlNotes);
        $stmtNotes->execute([':id' => $idetudiant]);

        // 2. Supprimer l'étudiant
        $sqlEtudiant = "DELETE FROM etudiants WHERE idetudiant = :id";
        $stmtEtudiant = $pdo->prepare($sqlEtudiant);
        $stmtEtudiant->execute([':id' => $idetudiant]);

        // Valider les changements
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        // En cas d'erreur, on annule tout
        $pdo->rollBack();
        return false;
    }
}

function getEtudiantsAvecMoyenne(PDO $pdo, $idClasse) {
    try {
        $stmt = $pdo->prepare("
            SELECT e.idetudiant, e.matricule, e.nom, e.prenom, 
                   COALESCE(AVG(ev.note), 0) AS moyenne
            FROM etudiants e
            LEFT JOIN evaluations ev ON e.idetudiant = ev.idetudiant 
                 AND ev.type IN ('Devoir', 'Examen') -- CORRECTION : On filtre ICI
            WHERE e.idclasse = :idclasse
            GROUP BY e.idetudiant, e.nom, e.prenom, e.matricule
        ");
        $stmt->execute(['idclasse' => $idClasse]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur SQL : " . $e->getMessage());
        return [];
    }
}
function getMajorClasse(PDO $pdo, $idclasse) {
    try {
        $sql = "SELECT e.idetudiant, e.matricule, e.nom, e.prenom, 
                       AVG(ev.note) as moyenne
                FROM etudiants e
                JOIN evaluations ev ON e.idetudiant = ev.idetudiant
                WHERE e.idclasse = :idclasse
                GROUP BY e.idetudiant, e.matricule, e.nom, e.prenom
                ORDER BY moyenne DESC 
                LIMIT 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['idclasse' => $idclasse]);
        
        // Retourne l'étudiant (tableau associatif) ou false s'il n'y a pas de notes
        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Journalisation de l'erreur dans le fichier de log du serveur
        error_log("Erreur lors de la récupération du major de la classe : " . $e->getMessage());
        return null;
    }
}

function getEtudiantsElite(PDO $pdo, $idclasse) {
    try {
        $sql = "SELECT e.matricule, e.nom, e.prenom, AVG(ev.note) as moy_eleve
                FROM etudiants e
                JOIN evaluations ev ON e.idetudiant = ev.idetudiant
                WHERE e.idclasse = :idclasse1
                GROUP BY e.idetudiant, e.matricule, e.nom, e.prenom
                HAVING moy_eleve > (
                    SELECT AVG(ev2.note) 
                    FROM evaluations ev2 
                    JOIN etudiants e2 ON ev2.idetudiant = e2.idetudiant 
                    WHERE e2.idclasse = :idclasse2
                )";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'idclasse1' => $idclasse,
            'idclasse2' => $idclasse
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Erreur lors du calcul des étudiants d'élite : " . $e->getMessage());
        return [];
    }
}
function getMajorNiveau($pdo, $idniveau) {
    $sql = "SELECT e.nom, e.prenom, c.nomclasse, AVG(ev.note) as moyenne
            FROM etudiants e
            JOIN classes c ON e.idclasse = c.idclasse
            JOIN evaluations ev ON e.idetudiant = ev.idetudiant
            WHERE c.idniveaux = :id  -- On filtre par le niveau
            AND ev.type IN ('Devoir', 'Examen')
            GROUP BY e.idetudiant
            ORDER BY moyenne DESC
            LIMIT 1"; // On ne prend que le premier (le meilleur)
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idniveau]);   
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getMoyenneGeneraleClasse($pdo, $idclasse) {
    try {
        $sql = "SELECT AVG(ev.note) as moyenne_classe
                FROM evaluations ev
                JOIN etudiants et ON ev.idetudiant = et.idetudiant
                WHERE et.idclasse = :id
                AND ev.type IN ('Devoir', 'Examen')"; // Respect de la règle projet
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $idclasse]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['moyenne_classe'] ? round($result['moyenne_classe'], 2) : "0.00";
    } catch (PDOException $e) {
        return "0.00";
    }
}

function enregistrerEvaluation($pdo, $idetudiant, $idmodule, $type, $note) {
    try {
        $sql = "INSERT INTO evaluations (idetudiant, idmodule, type, note, date_eval) 
                VALUES (?, ?, ?, ?, CURDATE())";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $idetudiant, 
            $idmodule, 
            $type, 
            $note
        ]);
    } catch (PDOException $e) {
        // Optionnel : error_log($e->getMessage());
        return false;
    }
}
function getModulesByClasse(PDO $pdo, $idclasse) {
    try {
        $sql = "SELECT m.idmodule, m.nommodule, m.codemodule 
                FROM modules m
                JOIN classe_module cm ON m.idmodule = cm.idmodule
                WHERE cm.idclasse = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idclasse]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des modules : " . $e->getMessage());
        return [];
    }
}
function getAllModules(PDO $pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM modules ORDER BY nommodule");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de tous les modules : " . $e->getMessage());
        return [];
    }
}
function lierModuleAClasse(PDO $pdo, $idclasse, $idmodule) {
    try {
        $sql = "INSERT IGNORE INTO classe_module (idclasse, idmodule) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$idclasse, $idmodule]);
    } catch (PDOException $e) {
        error_log("Erreur SQL liaison : " . $e->getMessage());
        return false;
    }
}
function enregistrerNoteEtudiant(PDO $pdo, $idetudiant, $idmodule, $type, $note) {
    try {
        $sql = "INSERT INTO evaluations (idetudiant, idmodule, type, note, date_eval) 
                VALUES (:idetudiant, :idmodule, :type, :note, CURDATE())";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'idetudiant' => $idetudiant,
            'idmodule'   => $idmodule,
            'type'       => $type,
            'note'       => $note
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors de l'enregistrement de la note : " . $e->getMessage());
        return false;
    }
}
function supprimerNoteParCriteres($pdo, $matricule, $codeModule) {
    try {
        // On lie les tables pour identifier la note via le matricule et le code module
        $sql = "DELETE ev FROM evaluations ev
                JOIN etudiants e ON ev.idetudiant = e.idetudiant
                JOIN modules m ON ev.idmodule = m.idmodule
                WHERE e.matricule = :matricule 
                AND m.codemodule = :code";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'matricule' => $matricule,
            'code'      => $codeModule
        ]);
    } catch (PDOException $e) {
        error_log("Erreur suppression évaluation : " . $e->getMessage());
        return false;
    }
}
function getNotesDetaillees($pdo, $idetudiant) {
    $sql = "SELECT m.nommodule, m.codemodule, ev.note, ev.type 
            FROM evaluations ev
            JOIN modules m ON ev.idmodule = m.idmodule
            WHERE ev.idetudiant = ?
            ORDER BY m.nommodule";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idetudiant]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
function modifierNoteParCriteres($pdo, $matricule, $codeModule, $nouvelleNote, $nouveauType) {
    try {
        $sql = "UPDATE evaluations ev
                JOIN etudiants e ON ev.idetudiant = e.idetudiant
                JOIN modules m ON ev.idmodule = m.idmodule
                SET ev.note = :note, ev.type = :type
                WHERE e.matricule = :matricule AND m.codemodule = :code";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            'note' => $nouvelleNote,
            'type' => $nouveauType,
            'matricule' => $matricule,
            'code' => $codeModule
        ]);
    } catch (PDOException $e) {
        return false;
    }
} 
/**
 * Récupère tous les étudiants de la base avec le nom de leur classe
 */
function getAllEtudiantsGlobal($pdo) {
    try {
        $sql = "SELECT e.*, 
                       e.nom AS nometudiant, 
                       c.nomclasse, 
                       n.nom
                FROM etudiants e 
                LEFT JOIN classes c ON e.idclasse = c.idclasse 
                LEFT JOIN niveaux n ON c.idniveaux = n.idniveaux
                ORDER BY n.nom ASC, e.nom ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
/**
 * Récupère les statistiques globales des étudiants par statut
 */
function obtenirStatsEtudiants($pdo) {
    // Cette requête calcule d'abord la moyenne de chaque étudiant,
    // puis compte combien sont dans chaque catégorie.
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN moyenne_etudiant >= 10 THEN 1 ELSE 0 END) as admis,
                SUM(CASE WHEN moyenne_etudiant >= 5 AND moyenne_etudiant < 10 THEN 1 ELSE 0 END) as ajournes,
                SUM(CASE WHEN moyenne_etudiant < 5 OR moyenne_etudiant IS NULL THEN 1 ELSE 0 END) as exclus
            FROM (
                SELECT e.idetudiant, AVG(ev.note) as moyenne_etudiant
                FROM etudiants e
                LEFT JOIN evaluations ev ON e.idetudiant = ev.idetudiant
                GROUP BY e.idetudiant
            ) as calcul_moyennes";
            
    $res = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    
    // Sécurité si la base est vide
    return [
        'total' => $res['total'] ?? 0,
        'admis' => $res['admis'] ?? 0,
        'ajournes' => $res['ajournes'] ?? 0,
        'exclus' => $res['exclus'] ?? 0
    ];
}
/**
 * Récupère le nombre d'étudiants par niveau
 */
/**
 * Récupère le nombre d'étudiants ET le nombre de classes par niveau
 */
function obtenirEtudiantsParNiveau($pdo) {
    // Ajout de : COUNT(DISTINCT c.idclasse) as nb_classes
    $sql = "SELECT n.nom as niveau, 
                   COUNT(DISTINCT c.idclasse) as nb_classes, 
                   COUNT(e.idetudiant) as nb_etudiants
            FROM niveaux n
            LEFT JOIN classes c ON n.idniveaux = c.idniveaux
            LEFT JOIN etudiants e ON c.idclasse = e.idclasse
            GROUP BY n.idniveaux";
    return $pdo->query($sql)->fetchAll();
}
?>