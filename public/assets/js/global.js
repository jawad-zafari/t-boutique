/**
 * Scripts globaux du site (Header Menu, Footer Newsletter, et Gestion des Favoris AJAX)
 * Sécurisé (Routing Absolu et Anti-XSS, Protection CSRF & Redirection Intelligente)
 */
document.addEventListener("DOMContentLoaded", function() {

    // 1. Récupération sécurisée de l'URL de base
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // =========================================================================
    // FONCTION UTILITAIRE : Récupération dynamique du jeton CSRF global
    // =========================================================================
    function getGlobalCsrfToken() {
        const wrapper = document.querySelector('[data-csrf]');
        if (wrapper) return wrapper.getAttribute('data-csrf');
        
        const input = document.querySelector('input[name="csrf_token"]');
        if (input) return input.value;
        
        return '';
    }

    // 2. Gestion du menu principal (Header avec animations douces)
    const menuItems = document.querySelectorAll('.menu-level-1 > li');
    let menuTimers = {};

    menuItems.forEach((item, index) => {
        if (!item.hasAttribute('data-time')) {
            item.setAttribute('data-time', 'menu_' + index);
        }
        
        const timerId = item.getAttribute('data-time');

        item.addEventListener('mouseenter', function() {
            clearTimeout(menuTimers[timerId]);
            menuTimers[timerId] = setTimeout(() => {
                this.classList.add('active-menu');
            }, 300);
        });

        item.addEventListener('mouseleave', function() {
            clearTimeout(menuTimers[timerId]);
            menuTimers[timerId] = setTimeout(() => {
                this.classList.remove('active-menu');
            }, 400); 
        });
    });

    // 3. Gestion globale des boutons de retour (Remplacement du onclick natif)
    const backButtons = document.querySelectorAll('.js-back-button');
    backButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    });

    // =========================================================================
    // 4. GESTION DYNAMIQUE DES FAVORIS (AJAX + REDIRECTION LOGIN) - Anti XSS & CSRF
    // =========================================================================
    function showGlobalFavToast(message, type = 'success') {
        let toast = document.getElementById('globalFavToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'globalFavToastNotification';
            document.body.appendChild(toast);
        }
        toast.className = `toast-notification ${type === 'danger' ? 'toast-danger' : 'toast-success'}`;
        toast.style.backgroundColor = type === 'danger' ? '#e03131' : '#2b8a3e';
        
        // Neutralisation du XSS via textContent
        const span = document.createElement('span');
        span.textContent = message;
        
        toast.innerHTML = `<i class="fa-solid ${type === 'danger' ? 'fa-circle-exclamation' : 'fa-heart'}"></i> `;
        toast.appendChild(span);
        
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    document.addEventListener('click', async (e) => {
        const btnHeart = e.target.closest('.btn-favorite-toggle');
        if (btnHeart) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = btnHeart.getAttribute('data-id');
            const icon = btnHeart.querySelector('i');
            if (!productId) return;

            try {
                // SÉCURITÉ : Injection du jeton CSRF pour prouver que la requête est légitime
                const params = new URLSearchParams();
                params.append('csrf_token', getGlobalCsrfToken());

                const response = await fetch(`${baseUrl}Account/toggleFavorite/${productId}`, {
                    method: 'POST',
                    body: params
                });
                const result = await response.json();

                if (result.status === 'unauthorized') {
                    // REDIRECTION INTELLIGENTE : Sauvegarde de la page actuelle et redirection vers le login
                    const currentPath = window.location.pathname.replace(baseUrl, '') + window.location.search;
                    window.location.href = baseUrl + 'Login/index?back=' + encodeURIComponent(currentPath);
                } else if (result.status === 'error') {
                    showGlobalFavToast(result.message, 'danger');
                } else if (result.status === 'success') {
                    showGlobalFavToast(result.message, 'success');

                    // Mise à jour visuelle du bouton cœur
                    if (result.action === 'added') {
                        btnHeart.classList.add('active');
                        if (icon) icon.className = 'fa-solid fa-heart';
                    } else if (result.action === 'removed') {
                        btnHeart.classList.remove('active');
                        if (icon) icon.className = 'fa-regular fa-heart';
                        
                        // Si on est sur la page liste des favoris, masquer la carte fluidement
                        const favCard = document.getElementById(`fav-card-${productId}`);
                        if (favCard) {
                            favCard.style.transition = 'opacity 0.3s ease';
                            favCard.style.opacity = '0';
                            setTimeout(() => favCard.remove(), 300);
                        }
                    }

                    // MISE À JOUR DYNAMIQUE DU COMPTEUR DANS LE HEADER
                    const favBadge = document.getElementById('navFavCounterBadge');
                    if (favBadge) {
                        const count = parseInt(result.favCount || 0);
                        favBadge.innerText = count;
                        if (count > 0) {
                            favBadge.style.display = 'inline-flex';
                            favBadge.style.transform = 'scale(1.4)';
                            setTimeout(() => { favBadge.style.transform = 'scale(1)'; }, 250);
                        } else {
                            favBadge.style.display = 'none';
                        }
                    }
                }
            } catch (error) {
                console.error("Erreur Favoris AJAX :", error);
                showGlobalFavToast("Erreur de connexion.", 'danger');
            }
        }
    });
});