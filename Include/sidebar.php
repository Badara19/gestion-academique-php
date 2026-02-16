<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    /* 1. Base de la Sidebar */
    .sb-sidenav-dark {
        background-color: #1a1c23 !important; 
    }

    /* 2. Liens de navigation */
    .sb-sidenav-dark .sb-sidenav-menu .nav-link {
        color: rgba(255, 255, 255, 0.7);
        transition: all 0.3s ease;
        border-left: 3px solid transparent; /* Prévient le saut de texte à l'activation */
    }

    /* 3. Survol (Hover) */
    .sb-sidenav-dark .sb-sidenav-menu .nav-link:hover {
        color: #fff;
        background-color: rgba(67, 97, 238, 0.1);
    }

    /* 4. État Actif */
    .sb-sidenav-dark .sb-sidenav-menu .nav-link.active {
        color: #fff;
        border-left: 3px solid #4361ee;
        background-color: rgba(67, 97, 238, 0.1);
    }

    /* 5. Icônes */
    .sb-sidenav-dark .sb-sidenav-menu .nav-link .sb-nav-link-icon {
        color: #4361ee; /* Ton bleu de projet */
    }

    /* 6. En-têtes de section */
    .sb-sidenav-dark .sb-sidenav-menu .sb-sidenav-menu-heading {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* 7. Footer */
    .sb-sidenav-footer {
        background-color: rgba(0, 0, 0, 0.2) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
</style>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Principal</div>
                    
                    <a class="nav-link" href="?page=home">
                        <div class="sb-nav-link-icon"><i class="fas fa-home"></i></div>
                        Accueil
                    </a>
                    
                    <a class="nav-link" href="?page=niveaux">
                        <div class="sb-nav-link-icon"><i class="fas fa-level-up-alt"></i></div>
                        Niveaux & Classes
                    </a>

                    <a class="nav-link" href="?page=etudiants_annuaire">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Gestion des Étudiants
                    </a>

                    <a class="nav-link" href="?page=modules">
                        <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                        Gestion Modules
                    </a>
                    
                    <a class="nav-link" href="?page=dashboard">
                        <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                        Tableau de Bord
                    </a>
                </div>
            </div>
            
            <div class="sb-sidenav-footer">
                <div class="small text-muted">Connecté en tant que :</div>
                <span class="fw-bold text-white"><i class="fas fa-user-circle me-1"></i> Admin</span>
            </div>
        </nav>
    </div>
    
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 mt-4">

<script>
    /**
     * Script de gestion automatique de l'état actif
     */
    window.addEventListener('DOMContentLoaded', event => {
        const currPath = window.location.search; 
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            // On vérifie si l'attribut href correspond aux paramètres de l'URL
            if(link.getAttribute('href') === currPath || (currPath === '' && link.getAttribute('href') === '?page=home')) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>