    <?php
    // On s'assure de récupérer l'ID de la classe depuis l'URL
    $idclasse = $_GET['id'] ?? null;

    // Sécurité : si on accède à la page sans ID ou si les données ne sont pas chargées
    if (!$idclasse || !isset($etudiants)) {
        echo "<div class='alert alert-danger m-4'>Erreur : Impossible de charger les données de la classe.</div>";
        return;
    }
    ?>

    <div class="container-fluid px-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">
                    <i class="fas fa-users text-primary me-2"></i>Gestion des Étudiants
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="index.php?page=niveaux" class="text-decoration-none">Niveaux</a></li>
                        <li class="breadcrumb-item active">Liste des étudiants</li>
                    </ol>
                </nav>
            </div>
            <button type="submit" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalInscription" name="inscrire_etudiant">
                <i class="fas fa-user-plus me-2"></i>Inscrire un étudiant
            </button>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100 bg-primary text-white" style="background: linear-gradient(135deg, #4361ee, #4cc9f0);">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                        <h5 class="text-uppercase small fw-bold opacity-75">Major de la classe</h5>
                        <?php if ($major): ?>
                            <h2 class="mb-1"><?= htmlspecialchars($major['prenom'] . ' ' . $major['nom']) ?></h2>
                            <div class="display-6 fw-bold mb-2"><?= number_format($major['moyenne'], 2) ?>/20</div>
                            <p class="mb-0 small opacity-75">Matricule: <?= htmlspecialchars($major['matricule']) ?></p>
                        <?php else: ?>
                            <p class="mt-3">Aucune note enregistrée</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title mb-0 fw-bold text-info"><i class="fas fa-star me-2"></i>Groupe Élite</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <?php if (empty($elites)): ?>
                                <div class="col-12 text-muted italic">Aucun étudiant au-dessus de la moyenne.</div>
                            <?php else: ?>
                                <?php foreach (array_slice($elites, 0, 4) as $e): ?>
                                    <div class="col-3 border-end">
                                        <h6 class="mb-1 text-dark"><?= htmlspecialchars($e['nom']) ?></h6>
                                        <h6 class="mb-1 text-dark"><?= htmlspecialchars($e['prenom']) ?></h6>
                                        <h6 class="mb-1 text-dark"><?= htmlspecialchars($e['matricule']) ?></h6>
                                        <span class="badge bg-info-subtle text-info"><?= number_format($e['moy_eleve'], 2) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        <i class="fas fa-link me-2"></i>Affecter un module à cette classe
                    </div>
                    <div class="card-body">
                        <form action="Traitements/action.php" method="POST" class="row g-2">
                            <input type="hidden" name="idclasse" value="<?= $idclasse ?>">
                            <div class="col-md-9">
                                <select name="idmodule" class="form-select" required>
                                    <option value="" disabled selected>Choisir un module à enseigner ici...</option>
                                    <?php 
                                    $tousModules = getAllModules($pdo); 
                                    foreach ($tousModules as $m): 
                                    ?>
                                        <option value="<?= $m['idmodule'] ?>">
                                            <?= htmlspecialchars($m['nommodule']) ?> [<?= htmlspecialchars($m['codemodule']) ?>]
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="lier_module" class="btn btn-dark w-100">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="fas fa-check-circle me-2"></i> <strong>Succès !</strong> 
                        <?php
                            if($_GET['success'] == 'note_supprimee') echo "L'évaluation a été supprimée.";
                            elseif($_GET['success'] == 'note_modifiee') echo "La note a été mise à jour avec succès.";
                            else echo "Action effectuée.";
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Erreur :</strong> 
                        <?php 
                            if($_GET['error'] == 'note_hors_limite') echo "La note doit être comprise entre 0 et 20.";
                            elseif($_GET['error'] == 'echec_note') echo "Impossible d'enregistrer la note.";
                            elseif($_GET['error'] == 'echec_suppression') echo "Impossible de supprimer l'évaluation.";
                            elseif($_GET['error'] == 'echec_modif') echo "La modification a échoué.";
                            else echo "Une erreur est survenue.";
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                <?php $moyenneClasse = getMoyenneGeneraleClasse($pdo, $idclasse); ?>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-gradient p-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 100px;">
                                <i class="fas fa-chart-line fa-2x text-white"></i>
                            </div>
                            
                            <div class="ps-4">
                                <p class="text-uppercase fw-bold text-muted small mb-1" style="letter-spacing: 1px;">
                                    Performance Globale
                                </p>
                                <div class="d-flex align-items-baseline">
                                    <h2 class="mb-0 fw-black text-dark" style="font-size: 2rem;">
                                        <?= $moyenneClasse ?>
                                    </h2>
                                    <span class="text-muted ms-2 fw-bold">/ 20</span>
                                </div>
                                <p class="mb-0 small text-primary fw-medium">
                                    <i class="fas fa-info-circle me-1"></i> Moyenne de classe (Hors TP)
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
               <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="py-3">Matricule</th>
                        <th class="py-3">Nom</th>
                        <th class="py-3">Prénom</th>
                        <th class="py-3">Moyenne</th>
                        <th class="py-3">Statut</th>
                        <th class="py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($etudiants)): ?>
                        <tr><td colspan="6" class="py-5 text-muted">Aucun étudiant trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($etudiants as $e): ?>
                            <tr>
                                <td class="fw-bold text-secondary small"><?= htmlspecialchars($e['matricule']) ?></td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="fw-bold"><?= htmlspecialchars($e['nom']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <span class="fw-bold"><?= htmlspecialchars($e['prenom']) ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill <?= $e['moyenne'] >= 10 ? 'bg-success' : 'bg-danger' ?>  ">
                                        <?= number_format($e['moyenne'], 2) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        $isElite = false;
                                        if (!empty($elites)) {
                                            foreach($elites as $el) {
                                                if($el['matricule'] == $e['matricule']) { $isElite = true; break; }
                                            }
                                        }
                                        if($isElite) echo '<span class="badge bg-info text-dark"><i class="fas fa-bolt"></i> Élite</span>';
                                        else echo '<span class="badge bg-light text-muted border">Standard</span>';
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalNote<?= str_replace('-', '', $e['matricule']) ?>"> 
                                        <i class="fas fa-plus"></i> Note
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalDetails<?= str_replace('-', '', $e['matricule']) ?>"> 
                                        <i class="fas fa-eye"></i> Détails
                                    </button>
                                    <a href="Traitements/action.php?action=supprimer_etudiant&idetudiant=<?= $e['idetudiant'] ?>&idclasse=<?= $idclasse ?>" 
                                        class="btn btn-sm btn-outline-danger" 
                                        onclick="return confirm('Attention ! Cela supprimera l\'étudiant et toutes ses notes. Confirmer ?')">
                                            <i class="fas fa-trash"></i>supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* Optionnel : pour s'assurer que les bordures de la table ne doublent pas avec celles de la card */
    .table-bordered {
        border: 1px solid #dee2e6 !important;
    }
    /* Harmonisation des bordures intérieures */
    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6 !important;
    }
</style>
            </div>
        </div>
    </div>

    <?php if (!empty($etudiants)): ?>
        <?php foreach ($etudiants as $e): ?>
            <div class="modal fade" id="modalNote<?= str_replace('-', '', $e['matricule']) ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered">
                    <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow-lg">
                        <input type="hidden" name="idetudiant" value="<?= $e['idetudiant'] ?>">
                        <div class="modal-header bg-primary text-white border-0">
                            <h5 class="modal-title small fw-bold">Noter : <?= htmlspecialchars($e['nom']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="idclasse" value="<?= $idclasse ?>">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NOTE / 20</label>
                                <input type="number" name="note" class="form-control form-control-lg text-center fw-bold" 
                                    step="0.25" min="0" max="20" placeholder="EX: 14.5" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">MATIÈRE</label>
                                <select name="idmodule" class="form-select" required>
                                    <option value="" disabled selected>Choisir une matière...</option>
                                    <?php foreach ($modules as $mod): ?>
                                        <option value="<?= $mod['idmodule'] ?>">
                                            <?= htmlspecialchars($mod['nommodule']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">TYPE D'ÉVALUATION</label>
                                <select name="type_eval" class="form-select" required>
                                    <option value="devoir">Devoir</option>
                                    <option value="examen">Examen</option>
                                    <option value="tp">TP</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" name="ajouter_note" class="btn btn-primary w-100 fw-bold py-2">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="modal fade" id="modalDetails<?= str_replace('-', '', $e['matricule']) ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-info text-white border-0">
                            <h5 class="modal-title fw-bold">Évaluations de <?= htmlspecialchars($e['nom'] . ' ' . $e['prenom']) ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Matière (Code)</th>
                                            <th class="text-end pe-4">Type & Note / 20</th>
                                            <th class="text-end pe-4">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $notesEtudiant = getNotesDetaillees($pdo, $e['idetudiant']); 
                                        if (empty($notesEtudiant)):
                                        ?>
                                            <tr><td colspan="3" class="text-center py-4 text-muted">Aucune note enregistrée.</td></tr>
                                        <?php else: foreach ($notesEtudiant as $n): ?>
                                            <tr>
                                                <td class="ps-4 fw-bold">
                                                    <?= htmlspecialchars($n['nommodule']) ?> 
                                                    <br><small class="text-muted"><?= htmlspecialchars($n['codemodule']) ?></small>
                                                </td>
                                                
                                                <td class="pe-4">
                                                    <form action="Traitements/action.php" method="POST" class="d-flex gap-2 justify-content-end">
                                                        <input type="hidden" name="matricule" value="<?= $e['matricule'] ?>">
                                                        <input type="hidden" name="code" value="<?= $n['codemodule'] ?>">
                                                        <input type="hidden" name="idclasse" value="<?= $idclasse ?>">
                                                        
                                                        <select name="nouveau_type" class="form-select form-select-sm" style="width: auto;">
                                                            <option value="devoir" <?= $n['type'] == 'devoir' ? 'selected' : '' ?>>Devoir</option>
                                                            <option value="examen" <?= $n['type'] == 'examen' ? 'selected' : '' ?>>Examen</option>
                                                            <option value="tp" <?= $n['type'] == 'tp' ? 'selected' : '' ?>>TP</option>
                                                        </select>

                                                        <input type="number" name="nouvelle_note" 
                                                            class="form-control form-control-sm fw-bold <?= $n['note'] < 10 ? 'text-danger' : 'text-success' ?>" 
                                                            value="<?= $n['note'] ?>" step="0.25" min="0" max="20" style="width: 80px;">
                                                            
                                                        <button type="submit" name="modifier_note" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                </td>

                                                <td class="text-end pe-4">
                                                    <a href="Traitements/action.php?action=supprimer_note&matricule=<?= $e['matricule'] ?>&code=<?= $n['codemodule'] ?>&idclasse=<?= $idclasse ?>" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="return confirm('Supprimer cette évaluation ?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <div class="modal fade" id="modalInscription" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Nouvelle Inscription</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="idclasse" value="<?= $idclasse ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">NOM</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">PRÉNOM</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="inscrire_etudiant" class="btn btn-primary px-4">Valider l'inscription</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert-success, .alert-danger');
            alerts.forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 4000);
    </script>