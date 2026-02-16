<div class="container-fluid px-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark"><i class="fas fa-book text-primary me-2"></i>Catalogue des Modules</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
            <i class="fas fa-plus me-2"></i>Nouveau Module
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Nom du Module</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $modules = getAllModules($pdo); // On réutilise ta fonction !
                    foreach ($modules as $m): ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($m['codemodule']) ?></td>
                        <td><?= htmlspecialchars($m['nommodule']) ?></td>
                        <td class="text-end">
                           <a href="Traitements/action.php?action=supprimer_catalogue_module&idmodule=<?= $m['idmodule'] ?>" 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('Voulez-vous vraiment supprimer ce module du catalogue ?')">
                                    <i class="fas fa-trash"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModule<?= $m['idmodule'] ?>">
    <i class="fas fa-edit"></i>
</button>

<div class="modal fade" id="editModule<?= $m['idmodule'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold">Modifier le module</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <input type="hidden" name="idmodule" value="<?= $m['idmodule'] ?>">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">CODE DU MODULE</label>
                    <input type="text" name="codemodule" class="form-control" value="<?= htmlspecialchars($m['codemodule']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOM DU MODULE</label>
                    <input type="text" name="nommodule" class="form-control" value="<?= htmlspecialchars($m['nommodule']) ?>" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="modifier_catalogue_module" class="btn btn-info text-white px-4">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>  
    </div>
</div>
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="Traitements/action.php" method="POST" class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Ajouter un nouveau module</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">CODE DU MODULE</label>
                    <input type="text" name="codemodule" class="form-control" placeholder="Ex: PHP-001" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">NOM DU MODULE</label>
                    <input type="text" name="nommodule" class="form-control" placeholder="Ex: Programmation Web PHP" required>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" name="ajouter_catalogue_module" class="btn btn-primary px-4">Enregistrer</button>
            </div>
        </form>
    </div>
</div>  