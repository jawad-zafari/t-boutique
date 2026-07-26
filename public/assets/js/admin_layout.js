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

    // 1. Lire l'état depuis le localStorage pour que la sidebar reste ouverte/fermée en changeant de page
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
        if (overlay) overlay.classList.remove("active");
    }

    // 2. Fonction de basculement manuel (Toggle) 
    function toggleSidebar(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation(); 
        }
        
        sidebar.classList.toggle("open");
        const isOpen = sidebar.classList.contains("open");
        
        // Mise à jour de l'accessibilité en temps réel
        sidebar.setAttribute('aria-hidden', !isOpen);
        toggleBtn.setAttribute('aria-expanded', isOpen);
        
        // Sauvegarde du choix de l'utilisateur dans le navigateur
        if (isOpen) {
            localStorage.setItem("adminSidebarState", "open");
        } else {
            localStorage.setItem("adminSidebarState", "closed");
        }
        
        // Gestion de l'overlay pour mobile
        if (window.innerWidth < 992 && overlay) {
            if (isOpen) {
                overlay.classList.add("active");
            } else {
                overlay.classList.remove("active");
            }
        }
    }

    toggleBtn.addEventListener("click", toggleSidebar);
    
    // 3. Fermeture automatique si l'utilisateur clique en dehors de la sidebar (Click-Outside)
    document.addEventListener("click", function(event) {
        if (sidebar.classList.contains("open")) {
            if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
                sidebar.classList.remove("open");
                
                sidebar.setAttribute('aria-hidden', 'true');
                toggleBtn.setAttribute('aria-expanded', 'false');
                localStorage.setItem("adminSidebarState", "closed");
                
                if (overlay) overlay.classList.remove("active");
            }
        }
    });

    // Fermeture si l'utilisateur clique sur l'overlay noir (sur Mobile)
    if (overlay) {
        overlay.addEventListener("click", function(e) {
            e.stopPropagation();
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
                
                // Attendre la fin de l'animation CSS (400ms) avant de charger la nouvelle page
                setTimeout(() => {
                    window.location.href = this.href;
                }, 400); 
            } else {
                localStorage.setItem("adminSidebarState", "closed");
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