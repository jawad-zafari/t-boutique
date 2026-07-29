/**
 * Logique unifiée de la barre latérale (Sidebar) - Vanilla JS
 * Mémorisation de l'état (ouvert/fermé) via LocalStorage et redirection fluide.
 * 100% Accessible (A11y) pour les lecteurs d'écran.
 */

function initAdminSidebar() {
    // Évite d'initialiser deux fois les événements (Garde de sécurité)
    if (window.adminSidebarInitialized) return; 
    window.adminSidebarInitialized = true; 

    const sidebar = document.getElementById("adminSidebar");
    const toggleBtn = document.getElementById("sidebarToggleBtn");
    const overlay = document.getElementById("mobileOverlay");
    
    if (!sidebar || !toggleBtn) return;

    // ACCESSIBILITÉ : Indiquer aux lecteurs d'écran quel élément est contrôlé par ce bouton
    toggleBtn.setAttribute('aria-controls', 'adminSidebar');

    // 1. Lire l'état depuis le localStorage pour que la sidebar reste cohérente
    const savedState = localStorage.getItem("adminSidebarState");
    
    if (savedState === "open") {
        sidebar.classList.add("open");
        sidebar.setAttribute('aria-hidden', 'false');
        toggleBtn.setAttribute('aria-expanded', 'true');
        
        // Afficher l'overlay sombre sur mobile si la sidebar est ouverte
        if (window.innerWidth < 992 && overlay) {
            overlay.classList.add("active");
        }
    } else {
        sidebar.classList.remove("open");
        sidebar.setAttribute('aria-hidden', 'true');
        toggleBtn.setAttribute('aria-expanded', 'false');
    }

    // 2. Événement du bouton (Toggle)
    toggleBtn.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle("open");
        
        if (isOpen) {
            localStorage.setItem("adminSidebarState", "open");
            sidebar.setAttribute('aria-hidden', 'false');
            toggleBtn.setAttribute('aria-expanded', 'true');
            if (window.innerWidth < 992 && overlay) {
                overlay.classList.add("active");
            }
        } else {
            localStorage.setItem("adminSidebarState", "closed");
            sidebar.setAttribute('aria-hidden', 'true');
            toggleBtn.setAttribute('aria-expanded', 'false');
            if (overlay) {
                overlay.classList.remove("active");
            }
        }
    });

    // 3. Fermer la sidebar en cliquant sur l'overlay (Mobile)
    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.remove("open");
            sidebar.setAttribute('aria-hidden', 'true');
            toggleBtn.setAttribute('aria-expanded', 'false');
            localStorage.setItem("adminSidebarState", "closed");
            overlay.classList.remove("active");
        });
    }

    // 4. Fermeture automatique et fluide de la sidebar sur Mobile lors du clic sur un lien
    const navLinks = document.querySelectorAll('.sidebar-nav ul li a, .sidebar-footer .btn-footer');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Si on est sur mobile et que la sidebar est ouverte
            if (sidebar.classList.contains("open") && window.innerWidth < 992) {
                e.preventDefault(); // Suspendre temporairement la redirection
                
                sidebar.classList.remove("open");
                sidebar.setAttribute('aria-hidden', 'true');
                toggleBtn.setAttribute('aria-expanded', 'false');
                localStorage.setItem("adminSidebarState", "closed");
                if (overlay) overlay.classList.remove("active");
                
                // Attendre la fin de l'animation CSS (300ms) avant de charger la nouvelle page
                setTimeout(() => {
                    window.location.href = this.href;
                }, 300); 
            }
        });
    });
}

// Initialisation sûre au chargement complet du DOM
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAdminSidebar);
} else {
    initAdminSidebar();
}