    <div class="container-fluid px-4 mt-4">
        <div class="card border-0 shadow-sm mb-4 bg-dark text-white rounded-3">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold mb-0 text-white">Annuaire des Étudiants</h3>
                    <p class="mb-0 opacity-75">Liste globale des élèves par niveau et classe</p>
                </div>
                <i class="fas fa-address-book fa-3x opacity-25"></i>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="searchEtudiant" class="form-control border-0" placeholder="Rechercher par nom, matricule, classe ou niveau...">
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tableEtudiants">
                        <thead class="table-light">
                            <tr class="text-uppercase small fw-bold">
                                <th class="ps-4">Etudiant</th>
                                <th>Matricule</th>
                                <th>Niveau & Classe</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $allEtudiants = getAllEtudiantsGlobal($pdo); 
                            if (empty($allEtudiants)):
                            ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted">Aucun étudiant trouvé.</td></tr>
                            <?php else: foreach ($allEtudiants as $etudiant): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($etudiant['nometudiant']) ?></div>
                                        <div class="small text-muted text-uppercase" style="font-size:0.7rem;"><?= htmlspecialchars($etudiant['prenom']) ?></div>
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded text-primary fw-bold border">
                                            <?= htmlspecialchars($etudiant['matricule']) ?>
                                        </code>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-info-subtle text-info border-info mb-1" style="width: fit-content; font-size: 0.7rem;">
                                                <?= htmlspecialchars($etudiant['nom'] ?? 'Niveau N/A') ?>
                                            </span>
                                            <span class="small text-secondary fw-bold">
                                                <i class="fas fa-graduation-cap me-1"></i><?= htmlspecialchars($etudiant['nomclasse'] ?? 'Non affecté') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4">
    <div class="d-flex justify-content-end align-items-center">
        <a href="index.php?page=classes_details&id=<?= $etudiant['idclasse'] ?>" 
           class="btn btn-sm btn-outline-primary shadow-sm me-2" 
           title="Voir les détails et notes">
            <i class="fas fa-chart-line"></i>
        </a>

        <a href="Traitements/generer_bulletin.php?id=<?= $etudiant['idetudiant'] ?>" 
           class="btn btn-sm btn-danger shadow-sm me-2" 
           target="_blank" 
           title="Générer le Bulletin">
            <i class="fas fa-file-pdf"></i>
        </a>

        <button class="btn btn-sm btn-info text-white shadow-sm" 
                data-bs-toggle="modal" 
                data-bs-target="#editModal<?= $etudiant['idetudiant'] ?>" 
                title="Modifier l'étudiant">
            <i class="fas fa-user-edit"></i>
        </button>
    </div>
</td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($allEtudiants)): foreach ($allEtudiants as $etudiant): ?>
        <div class="modal fade" id="editModal<?= $etudiant['idetudiant'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-info text-white border-0">
                        <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Modifier l'étudiant</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="idetudiant" value="<?= $etudiant['idetudiant'] ?>">
                        
                        <div class="mb-4 bg-light p-3 rounded-3 border-start border-info border-4 text-center">
                            <label class="form-label small fw-bold text-muted d-block">MATRICULE</label>
                            <span class="h5 fw-bold text-dark"><?= htmlspecialchars($etudiant['matricule']) ?></span>
                            <input type="hidden" name="matricule" value="<?= htmlspecialchars($etudiant['matricule']) ?>">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NOM</label>
                                <input type="text" name="nom" class="form-control" value="<?=htmlspecialchars($etudiant['nometudiant']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">PRÉNOM</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier_etudiant_global" class="btn btn-info text-white px-4 fw-bold">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; endif; ?>

    <script>
    // Système de recherche universel (Nom, Matricule, Classe, Niveau)
    document.getElementById('searchEtudiant').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#tableEtudiants tbody tr');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
    </script>