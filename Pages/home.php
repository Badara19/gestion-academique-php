

<div class="row">
    <div class="col-12 mb-4">
        <div class="p-5 bg-white shadow-sm rounded-4 border-start border-primary border-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold text-dark">Bienvenue, Admin !</h1>
                    <p class="fs-5 text-muted">Système de Gestion Académique de l'Institut d'Enseignement Supérieur. Prêt à gérer vos 5 niveaux d'excellence ? </p>
                    <div class="d-flex gap-2">
                    </div>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fas fa-university fa-8x text-light opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm hover-up">
            <div class="card-body text-center p-4">
                <div class="icon-shape bg-soft-primary text-primary rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background-color: #eef2ff;">
                    <i class="fas fa-layer-group fa-lg"></i>
                </div>
                <h5 class="fw-bold">Niveaux & Classes</h5>
                <p class="text-muted small">Organisation académique de la Licence 1 au Master 2.</p>
                <a href="?page=niveaux" class="btn btn-link btn-sm text-decoration-none p-0">Gérer la structure →</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm hover-up">
            <div class="card-body text-center p-4">
                <div class="icon-shape bg-soft-success text-success rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background-color: #ecfdf5;">
                    <i class="fas fa-user-graduate fa-lg"></i>
                </div>
                <h5 class="fw-bold">Réussite Étudiante</h5>
                <p class="text-muted small">Calcul des moyennes  et identification des étudiants admis.</p>
                <a href="?page=evaluations" class="btn btn-link btn-sm text-decoration-none p-0">Voir les analyses →</a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-0 shadow-sm hover-up">
            <div class="card-body text-center p-4">
                <div class="icon-shape bg-soft-warning text-warning rounded-circle mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; background-color: #fffbeb;">
                    <i class="fas fa-file-pdf fa-lg"></i>
                </div>
                <h5 class="fw-bold">Édition & Reporting</h5>
                <p class="text-muted small">Générez des bulletins officiels via FPDF/TCPDF</p>
                <a href="?page=dashboard" class="btn btn-link btn-sm text-decoration-none p-0">Tableau de bord →</a>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-up {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

