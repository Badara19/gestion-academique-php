<style>
    .hover-card { 
        transition: all 0.2s ease-in-out; 
        border-left: 4px solid #4361ee !important; 
        /* On retire l'overflow hidden pour laisser dépasser le dropdown */
        overflow: visible !important; 
    }
    
    .accordion-button:not(.collapsed) { background-color: #f0f3ff; color: #4361ee; }
    .class-link { text-decoration: none; color: inherit; transition: color 0.2s; }
    .class-link:hover { color: #4361ee; }
    
    /* Force le dropdown à passer devant tout le reste */
    .dropdown-menu { z-index: 1060 !important; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4 ">
    <h2 class="fw-bold text-dark"><i class="fas fa-layer-group me-2 text-primary"></i>Structure Académique</h2>
    <div class="btn-group">
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNiveau">
            <i class="fas fa-plus-circle me-2"></i>Nouveau Niveau
        </button>
        <button class="btn btn-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalClasse">
            <i class="fas fa-plus me-2"></i>Nouvelle Classe
        </button>
    </div>
</div>

<div class="accordion accordion-flush" id="accordionNiveaux">
    <?php foreach ($niveaux as $niveau): ?>
        <?php 
            $classes = $niveau['classes'] ?? []; 
            $nbClasses = count($classes);
            $majorNiveau = getMajorNiveau($pdo, $niveau['idniveaux']);
        ?>
        <div class="accordion-item border mb-3 rounded-3 shadow-sm">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed fw-bold py-3" type="button" 
                        data-bs-toggle="collapse" data-bs-target="#level<?= $niveau['idniveaux'] ?>">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <span>
                            <i class="fas fa-graduation-cap me-3 text-secondary"></i>
                            <?= htmlspecialchars($niveau['nom']) ?>
                                <?php if ($majorNiveau): ?>
                                    <small class="ms-3 badge bg-warning text-dark fw-normal">
                                        <i class="fas fa-trophy me-1"></i> 
                                        Major:  <?= htmlspecialchars($majorNiveau['prenom']) ?> <?= htmlspecialchars($majorNiveau['nom']) ?> 
                                        (<?= htmlspecialchars($majorNiveau['nomclasse']) ?>) 
                                        - <?= number_format($majorNiveau['moyenne'], 2) ?>
                                    </small>
                                <?php endif; ?>
                        </span>
                        
                        <span class="badge rounded-pill <?= $nbClasses > 0 ? 'bg-info text-dark' : 'bg-danger text-white' ?> border me-3">
                            <?= $nbClasses ?> <?= $nbClasses > 1 ? 'Classes' : 'Classe' ?>
                        </span>
                    </div>
                </button>
            </h2>

            <div id="level<?= $niveau['idniveaux'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionNiveaux">
                <div class="accordion-body bg-light-subtle" style="overflow: visible;">
                    <div class="row g-3">
                        <?php if ($nbClasses > 0): ?>
                            <?php foreach ($classes as $classe): ?>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm p-3 hover-card" style="overflow: visible;">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <a href="index.php?page=classes_details&id=<?= $classe['idclasse'] ?>" class="class-link flex-grow-1">
                                                <div class="fw-bold">
                                                    <i class="fas fa-door-open me-2 text-secondary"></i>
                                                    <?= htmlspecialchars($classe['nomclasse']) ?>
                                                </div>
                                            </a>
                                            
                                            <div class="dropdown">
                                                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                                    <li>
                                                        <a class="dropdown-item py-2" href="#" data-bs-toggle="modal" data-bs-target="#editClasse<?= $classe['idclasse'] ?>">
                                                            <i class="fas fa-edit me-2 text-warning"></i>Modifier
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="Traitements/action.php" method="POST" onsubmit="return confirm('Supprimer cette classe définitivement ?')">
                                                            <input type="hidden" name="idclasse" value="<?= $classe['idclasse'] ?>">
                                                            <button type="submit" name="supprimer_classe" class="dropdown-item py-2 text-danger">
                                                                <i class="fas fa-trash-alt me-2"></i>Supprimer
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="editClasse<?= $classe['idclasse'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="Traitements/action.php" method="POST" class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title fw-bold">Modifier la classe</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="idclasse" value="<?= $classe['idclasse'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Nom de la classe</label>
                                                    <input type="text" name="nomclasse" class="form-control" value="<?= htmlspecialchars($classe['nomclasse']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">Rattacher à un autre niveau</label>
                                                    <select name="idniveaux" class="form-select">
                                                        <?php foreach ($niveaux as $n): ?>
                                                            <option value="<?= $n['idniveaux'] ?>" <?= ($n['idniveaux'] == $niveau['idniveaux']) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($n['nom']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" name="modifier_classe" class="btn btn-warning">Mettre à jour</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center text-muted py-3">
                                <small><i class="fas fa-info-circle me-1"></i> Aucune classe dans ce niveau.</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?php foreach ($niveaux as $niveau): ?>
    <?php if (isset($niveau['classes'])): ?>
        <?php foreach ($niveau['classes'] as $classe): ?>
            <div class="modal fade" id="editClasse<?= $classe['idclasse'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-dark border-0">
                            <h5 class="modal-title fw-bold">Modifier <?= htmlspecialchars($classe['nomclasse']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <input type="hidden" name="idclasse" value="<?= $classe['idclasse'] ?>">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">NOM DE LA CLASSE</label>
                                <input type="text" name="nomclasse" class="form-control" value="<?= htmlspecialchars($classe['nomclasse']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small">NIVEAU</label>
                                <select name="idniveaux" class="form-select" required>
                                    <?php foreach ($niveaux as $n): ?>
                                        <option value="<?= $n['idniveaux'] ?>" <?= ($n['idniveaux'] == $niveau['idniveaux']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($n['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" name="modifier_classe" class="btn btn-warning fw-bold px-4">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>

<div class="modal fade" id="modalNiveau" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nouveau Niveau</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" name="nom" class="form-control" placeholder="Nom du niveau" required>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="enregistrer" class="btn btn-primary">Créer</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalClasse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Nouvelle Classe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <input type="text" name="nomclasse" class="form-control" placeholder="Nom de la classe" required>
                </div>
                <select name="idniveaux" class="form-select" required>
                    <option value="">-- Choisir un niveau --</option>
                    <?php foreach ($niveaux as $n): ?>
                        <option value="<?= $n['idniveaux'] ?>"><?= htmlspecialchars($n['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" name="enregistrer" class="btn btn-dark">Créer</button>
            </div>
        </form>
    </div>
</div> 