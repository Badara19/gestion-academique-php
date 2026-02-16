<?php
require_once("db.php");
require_once("requete.php");

if(isset($_POST['enregistrer'])) {
    
    // CAS A : C'est une CLASSE (car le champ 'nomclasse' existe)
    if (isset($_POST['nomclasse'])) {
        $nom = trim(htmlspecialchars($_POST['nomclasse']));
        $idNiv = $_POST['idniveaux'];

        if(!empty($nom) && !empty($idNiv)) {
            if(enregistrerClasse($pdo, $nom, $idNiv)) {
                header("Location: ../index.php?page=niveaux");
                exit();
            }
        }
    }
    
    // CAS B : C'est un NIVEAU (car le champ 'nom' existe)
    if (isset($_POST['nom'])) {
        $nomNiveau = trim(htmlspecialchars($_POST['nom']));
        
        if(!empty($nomNiveau)) {
            if(enregistrerNiveau($pdo, $nomNiveau)) {
                header("Location: ../index.php?page=niveaux");
                exit();
            }
        }
    }
}


    if(isset($_POST['inscrire_etudiant'])) {
        $nom = trim(htmlspecialchars($_POST['nom']));
        $prenom = trim(htmlspecialchars($_POST['prenom']));
        $idclasse = $_POST['idclasse'];

        // 1. On génère le matricule unique
        $matricule = genererMatricule($pdo); // Cette fonction doit être dans ton requete.php

    // 2. On insère
    if(inscrireEtudiant($pdo, $matricule, $nom, $prenom, $idclasse)) {
        header("Location: ../index.php?page=classes_details&id=$idclasse");
        exit();
    }
    }
    if (isset($_POST['ajouter_note'])) {
        // 1. Récupération des données du formulaire
        $idetudiant = intval($_POST['idetudiant']);
        $idmodule   = intval($_POST['idmodule']); // L'ID du module choisi dans le select
        $type       = $_POST['type_eval']; // 'devoir', 'examen' ou 'tp'
        $note       = floatval($_POST['note']); // La note saisie
        $idclasse   = $_POST['idclasse']; // Pour la redirection vers la bonne page

        // 2. Sécurité : Vérification de la plage de note (0 à 20)
        if (!is_numeric($note) || $note < 0 || $note > 20) {
            header("Location: ../index.php?page=classes_details&id=$idclasse&error=note_hors_limite");
            exit();
        }

        // 3. Insertion en base de données
        // Note : on utilise la fonction enregistrerEvaluation que nous avons définie
        if (enregistrerEvaluation($pdo, $idetudiant, $idmodule, $type, $note)) {
            // Redirection succès
            header("Location: ../index.php?page=classes_details&id=$idclasse&success=note");
            } else {
            // Redirection erreur SQL
            header("Location: ../index.php?page=classes_details&id=$idclasse&error=echec_note");
        }
        exit();
    }
// --- AFFECTATION D'UN MODULE À UNE CLASSE (POINT C.1) ---
if (isset($_POST['lier_module'])) {
    $idclasse = intval($_POST['idclasse']);
    $idmodule = intval($_POST['idmodule']);

    if ($idclasse > 0 && $idmodule > 0) {
        // ON APPELLE LA FONCTION AU LIEU D'ÉCRIRE LE SQL ICI
        if (lierModuleAClasse($pdo, $idclasse, $idmodule)) {
            header("Location: ../index.php?page=classes_details&id=$idclasse&success=module_lie");
        } else {
            header("Location: ../index.php?page=classes_details&id=$idclasse&error=liaison_sql");
        }
    } else {
        header("Location: ../index.php?page=classes_details&id=$idclasse&error=donnees_invalides");
    }
    exit();
}
// --- SUPPRIMER UNE EVALUATION (POINT C.2) ---
if (isset($_GET['action']) && $_GET['action'] == 'supprimer_note') {
    $matricule  = $_GET['matricule'];
    $codeModule = $_GET['code'];
    $idclasse   = $_GET['idclasse'];

    if (supprimerNoteParCriteres($pdo, $matricule, $codeModule)) {
        header("Location: ../index.php?page=classes_details&id=$idclasse&success=note_supprimee");
    } else {
        header("Location: ../index.php?page=classes_details&id=$idclasse&error=echec_suppression");
    }
    exit();
}
// --- MODIFIER UNE EVALUATION (POINT C.2) ---
if (isset($_POST['modifier_note'])) {
    $matricule = $_POST['matricule'];
    $code = $_POST['code'];
    $nouvelleNote = floatval($_POST['nouvelle_note']);
    $nouveauType = $_POST['nouveau_type']; // <--- Nouveau
    $idclasse = $_POST['idclasse'];

    if ($nouvelleNote >= 0 && $nouvelleNote <= 20) {
        // On passe les 5 arguments à la fonction
        if (modifierNoteParCriteres($pdo, $matricule, $code, $nouvelleNote, $nouveauType)) {
            header("Location: ../index.php?page=classes_details&id=$idclasse&success=note_modifiee");
        } else {
            header("Location: ../index.php?page=classes_details&id=$idclasse&error=echec_modif");
        }
    } else {
        header("Location: ../index.php?page=classes_details&id=$idclasse&error=note_hors_limite");
    }
    exit();
}
if (isset($_POST['ajouter_catalogue_module'])) {
    $code = $_POST['codemodule'];
    $nom = $_POST['nommodule'];

    // Ta requête SQL pour insérer dans la table des modules
    $query = "INSERT INTO modules (codemodule, nommodule) VALUES (:code, :nom)";
    $stmt = $pdo->prepare($query);
    
    if ($stmt->execute(['code' => $code, 'nom' => $nom])) {
        header("Location: ../index.php?page=modules&success=module_ajoute");
    } else {
        header("Location: ../index.php?page=modules&error=echec_ajout");
    }
    exit();
}
// --- AJOUT D'UN MODULE ---
if (isset($_POST['ajouter_catalogue_module'])) {
    $code = trim($_POST['codemodule']);
    $nom = trim($_POST['nommodule']);

    if (!empty($code) && !empty($nom)) {
        $sql = "INSERT INTO modules (codemodule, nommodule) VALUES (:code, :nom)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute(['code' => $code, 'nom' => $nom])) {
            header("Location: ../index.php?page=modules&success=module_ajoute");
        } else {
            header("Location: ../index.php?page=modules&error=echec_ajout");
        }
    } else {
        header("Location: ../index.php?page=modules&error=champs_vides");
    }
    exit();
}

// --- SUPPRESSION D'UN MODULE ---
if (isset($_GET['action']) && $_GET['action'] == 'supprimer_catalogue_module') {
    $idmodule = $_GET['idmodule'];

    try {
        // On tente de supprimer
        $sql = "DELETE FROM modules WHERE idmodule = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idmodule]);
        
        header("Location: ../index.php?page=modules&success=module_supprime");
    } catch (PDOException $e) {
        // Si erreur (ex: module lié à des notes), on capture l'erreur
        header("Location: ../index.php?page=modules&error=module_lie");
    }
    exit();
}
// --- MODIFICATION D'UN MODULE ---
if (isset($_POST['modifier_catalogue_module'])) {
    $idmodule = $_POST['idmodule'];
    $code = trim($_POST['codemodule']);
    $nom = trim($_POST['nommodule']);

    if (!empty($code) && !empty($nom)) {
        try {
            $sql = "UPDATE modules SET codemodule = :code, nommodule = :nom WHERE idmodule = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'code' => $code,
                'nom' => $nom,
                'id' => $idmodule
            ]);
            
            header("Location: ../index.php?page=modules&success=module_modifie");
        } catch (PDOException $e) {
            header("Location: ../index.php?page=modules&error=echec_modif");
        }
    } else {
        header("Location: ../index.php?page=modules&error=champs_vides");
    }
    exit();
}
// --- Dans Traitements/action.php ---

if (isset($_POST['modifier_etudiant_global'])) {
    
    // 1. On récupère l'ID (caché) pour savoir QUI modifier
    // On récupère le Nom et le Prénom (seules données modifiables)
    $id = $_POST['idetudiant']; 
    $nom = strtoupper(trim(htmlspecialchars($_POST['nom']))); 
    $prenom = ucwords(strtolower(trim(htmlspecialchars($_POST['prenom']))));

    // Note : On ne récupère PAS le matricule ici car on a décidé 
    // de ne jamais le modifier dans la base de données.

    try {
        // 2. La requête SQL cible uniquement NOM et PRENOM
        $sql = "UPDATE etudiants SET nom = :nom, prenom = :prenom WHERE idetudiant = :id";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':id' => $id
        ]);

        // 3. Retour vers l'annuaire avec confirmation
        header("Location: ../index.php?page=etudiants_annuaire&status=success");
        exit();

    } catch (PDOException $e) {
        // En cas d'erreur (ex: problème de base de données)
        header("Location: ../index.php?page=etudiants_annuaire&status=error");
        exit();
    }
}
// --- MODIFIER UNE CLASSE ---
if (isset($_POST['modifier_classe'])) {
    $idclasse = htmlspecialchars($_POST['idclasse']);
    $nomclasse = htmlspecialchars(trim($_POST['nomclasse']));
    $idniveaux = htmlspecialchars($_POST['idniveaux']);

    $sql = "UPDATE classes SET nomclasse = :nom, idniveaux = :id_niv WHERE idclasse = :id_c";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nomclasse,
        ':id_niv' => $idniveaux,
        ':id_c' => $idclasse
    ]);
    header("Location: ../index.php?page=niveaux&status=updated"); // Ajuste ta page de retour
    exit();
}

// --- SUPPRIMER UNE CLASSE ---
if (isset($_POST['supprimer_classe'])) {
    $idclasse = $_POST['idclasse'];

    // Attention : Vérifie si la classe est vide d'étudiants avant ou gère les contraintes SQL
    $sql = "DELETE FROM classes WHERE idclasse = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $idclasse]);
    
    header("Location: ../index.php?page=niveaux&status=deleted");
    exit();
}
// --- ACTION : SUPPRIMER UN ÉTUDIANT ---
if (isset($_GET['action']) && $_GET['action'] == 'supprimer_etudiant') {
    $idetudiant = intval($_GET['idetudiant']);
    $idclasse = intval($_GET['idclasse']);

    if ($idetudiant > 0) {
        // On appelle la fonction créée dans requete.php
        if (supprimerEtudiantComplet($pdo, $idetudiant)) {
            header("Location: ../index.php?page=classes_details&id=$idclasse&success=etudiant_supprime");
        } else {
            header("Location: ../index.php?page=classes_details&id=$idclasse&error=echec_suppression");
        }
    } else {
        header("Location: ../index.php?page=classes_details&id=$idclasse&error=id_invalide");
    }
    exit();
}
?>
