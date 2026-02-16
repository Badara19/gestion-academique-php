<!-- ====== STYLES ====== -->
<style>
body { background: #f5f7fb; transition: background .3s, color .3s; }

.stat-card {
    border: none;
    border-radius: 18px;
    transition: all 0.25s ease;
    background: #ffffff;
}
.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.08);
}

.icon-shape {
    width: 52px; height: 52px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 14px; font-size: 20px; color: white;
}

.gradient-primary { background: linear-gradient(135deg,#4e73df,#224abe); }
.gradient-success { background: linear-gradient(135deg,#1cc88a,#13855c); }
.gradient-warning { background: linear-gradient(135deg,#f6c23e,#dda20a); }
.gradient-danger  { background: linear-gradient(135deg,#e74a3b,#be2617); }

.stat-label { font-size: .8rem; text-transform: uppercase; color: #858796; }
.stat-value { font-size: 1.8rem; font-weight: 700; }

.card-modern { border-radius: 20px; border: none; }

.progress-custom {
    height: 10px;
    border-radius: 20px;
    background-color: #eaecf4;
}

.table-modern thead th {
    font-size: .75rem;
    text-transform: uppercase;
    color: #6c757d;
    border-bottom: 2px solid #e3e6f0;
}

/* DARK MODE */
.dark-mode { background: #121212 !important; color: #e4e6eb !important; }
.dark-mode .card { background: #1e1e2f !important; color: white; }
.dark-mode .table { color: #ddd; }
.dark-mode .table thead { background: #2a2a3d; }
</style>

<!-- ====== DARK MODE BUTTON ====== -->
<div class="d-flex justify-content-end mb-3">
    <button id="darkModeToggle" class="btn btn-sm btn-dark">🌙 Mode sombre</button>
</div>

<!-- ====== STAT CARDS ====== -->
<div class="row g-4 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Total Étudiants</div>
                    <div class="stat-value counter"><?= $stats['total'] ?></div>
                </div>
                <div class="icon-shape gradient-primary"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Admis</div>
                    <div class="stat-value text-success counter"><?= $stats['admis'] ?></div>
                </div>
                <div class="icon-shape gradient-success"><i class="fas fa-user-check"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Ajournés</div>
                    <div class="stat-value text-warning counter"><?= $stats['ajournes'] ?></div>
                </div>
                <div class="icon-shape gradient-warning"><i class="fas fa-user-clock"></i></div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Exclus</div>
                    <div class="stat-value text-danger counter"><?= $stats['exclus'] ?></div>
                </div>
                <div class="icon-shape gradient-danger"><i class="fas fa-user-times"></i></div>
            </div>
        </div>
    </div>

</div>

<!-- ====== FILTER ====== -->
<div class="mb-3">
    <input type="text" id="filterInput" class="form-control" placeholder="🔍 Filtrer par niveau...">
</div>

<!-- ====== TABLE ====== -->
<div class="card card-modern shadow-sm">
    <div class="card-header bg-white border-0 pt-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-chart-pie text-secondary me-2"></i>Répartition par Niveau</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
<table class="table align-middle table-modern mb-0">
    <thead>
        <tr>
            <th>Niveau</th>
            <th class="text-center">Classes</th> <th class="text-center">Effectif</th>
            <th style="width:40%">Taux</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($statsNiveaux as $sn): 
        $percent = ($stats['total'] > 0) ? ($sn['nb_etudiants'] / $stats['total']) * 100 : 0;
        if($percent > 60) $color = "bg-success";
        elseif($percent > 30) $color = "bg-warning";
        else $color = "bg-danger";
    ?>
        <tr>
            <td class="fw-semibold"><?= htmlspecialchars($sn['niveau']) ?></td>
            
            <td class="text-center">
                <span class="badge rounded-pill bg-light text-dark border fw-normal">
                    <i class="fas fa-chalkboard text-primary me-1"></i> 
                    <?= $sn['nb_classes'] ?>
                </span>
            </td>

            <td class="text-center fw-bold"><?= $sn['nb_etudiants'] ?></td>
            <td>
                <div class="d-flex align-items-center gap-3">
                    <div class="progress progress-custom w-100">
                        <div class="progress-bar <?= $color ?>" style="width: <?= $percent ?>%"></div>
                    </div>
                    <span class="fw-bold small"><?= round($percent) ?>%</span>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
        </div>
    </div>
</div>

<!-- ====== CHART ====== -->
<div class="card card-modern shadow-sm mt-4">
    <div class="card-header bg-white border-0 pt-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-chart-bar text-secondary me-2"></i>Effectif par Niveau</h5>
    </div>
    <div class="card-body">
        <canvas id="niveauChart" height="100"></canvas>
    </div>
</div>

<!-- ====== SCRIPTS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Counter animation
document.querySelectorAll('.counter').forEach(el => {
    let target = +el.innerText, count = 0, speed = target / 40;
    function update() {
        count += speed;
        if(count < target){ el.innerText = Math.floor(count); requestAnimationFrame(update); }
        else { el.innerText = target; }
    }
    update();
});

// Dark mode
const toggleBtn = document.getElementById("darkModeToggle");
toggleBtn.addEventListener("click", () => {
    document.body.classList.toggle("dark-mode");
    toggleBtn.innerText = document.body.classList.contains("dark-mode") ? "☀️ Mode clair" : "🌙 Mode sombre";
    toggleBtn.classList.toggle("btn-light");
    toggleBtn.classList.toggle("btn-dark");
});

// Filter
document.getElementById("filterInput").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll("table tbody tr").forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(value) ? "" : "none";
    });
});

// Chart
const niveaux = <?= json_encode(array_column($statsNiveaux, 'niveau')) ?>;
const effectifs = <?= json_encode(array_column($statsNiveaux, 'nb_etudiants')) ?>;

new Chart(document.getElementById('niveauChart'), {
    type: 'bar',
    data: {
        labels: niveaux,
        datasets: [{
            data: effectifs,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>
