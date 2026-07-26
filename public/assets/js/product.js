/**
 * Logique JavaScript pour la page Produit
 * Document Clean Code - 100% Vanilla JS (Carrousel, Tabs, Panier AJAX & Questions)
 * Hautement sécurisé (Anti-XSS via textNode, Gestion robuste des erreurs JSON)
 */
document.addEventListener("DOMContentLoaded", () => {

    // Garde de sécurité : Empêche les exécutions multiples du script lors de rechargements partiels
    if (window.productScriptEventsBound) return;
    window.productScriptEventsBound = true;

    // Détermination dynamique de l'URL de base pour garantir le bon fonctionnement du routage
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // SÉCURITÉ : Récupération du jeton CSRF injecté de manière sécurisée dans la vue
    const productWrapper = document.getElementById('mainProductWrapper');
    const csrfToken = productWrapper ? productWrapper.getAttribute('data-csrf') : '';

    // =========================================================================
    // 1. SYSTÈME DE NOTIFICATION TOAST (SÉCURITÉ ANTI-XSS CRITIQUE)
    // =========================================================================
    function showProductToast(message, type = 'success') {
        let toast = document.getElementById('productToastNotification');
        
        // Création dynamique du composant s'il n'existe pas
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'productToastNotification';
            
            // Configuration des styles de base
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.padding = '15px 25px';
            toast.style.borderRadius = '8px';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
            toast.style.transition = 'opacity 0.3s ease-in-out';
            document.body.appendChild(toast);
        }
        
        toast.style.backgroundColor = (type === 'danger') ? '#e03131' : '#2b8a3e';
        toast.innerHTML = ''; // Réinitialisation propre
        
        const icon = document.createElement('i');
        icon.className = (type === 'danger') ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ : Utilisation exclusive de createTextNode pour prévenir toute injection XSS
        const textNode = document.createTextNode(message);

        toast.appendChild(icon);
        toast.appendChild(textNode);
        
        toast.style.opacity = '1';
        toast.style.display = 'block';
        
        // Disparition automatique après 3 secondes
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3000);
    }

    // =========================================================================
    // 2. GESTION DE LA GALERIE D'IMAGES
    // =========================================================================
    const mainImageNode = document.getElementById('mainProductImageNode');
    const thumbnails = document.querySelectorAll('.thumb-item-box');

    if (mainImageNode && thumbnails.length > 0) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const newSrc = this.getAttribute('data-src');
                if (newSrc) {
                    mainImageNode.src = newSrc;
                    
                    // Gestion de l'état visuel actif
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });
    }

    // =========================================================================
    // 3. GESTION DU MODAL DE ZOOM (ACCESSIBILITÉ)
    // =========================================================================
    const zoomModal = document.getElementById('imageZoomModal');
    const zoomedImage = document.getElementById('zoomedImage');
    const btnTriggerImageZoom = document.getElementById('btnTriggerImageZoom');
    const closeZoomModal = document.getElementById('closeZoomModal');

    if (zoomModal && zoomedImage && mainImageNode) {
        const openZoom = () => {
            zoomedImage.src = mainImageNode.src;
            zoomModal.style.display = 'flex';
        };

        if (btnTriggerImageZoom) btnTriggerImageZoom.addEventListener('click', openZoom);
        mainImageNode.addEventListener('click', openZoom);

        const closeZoom = () => { zoomModal.style.display = 'none'; };
        
        if (closeZoomModal) closeZoomModal.addEventListener('click', closeZoom);
        zoomModal.addEventListener('click', (e) => {
            if (e.target === zoomModal) closeZoom(); // Ferme si on clique à l'extérieur
        });
    }

    // =========================================================================
    // 4. GESTION DYNAMIQUE DES ONGLETS (TABS)
    // =========================================================================
    const tabButtons = document.querySelectorAll('.btn-tab');
    const tabPanes = document.querySelectorAll('.tab-pane');

    if (tabButtons.length > 0) {
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Réinitialisation des états
                tabButtons.forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                tabPanes.forEach(p => p.classList.remove('active'));
                
                // Activation de la cible
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
                
                const targetPane = document.getElementById(targetId);
                if (targetPane) targetPane.classList.add('active');
            });
        });
    }

    // =========================================================================
    // 5. AJOUT AU PANIER (AJAX SÉCURISÉ & ROBUSTE)
    // =========================================================================
    const btnAddToCart = document.getElementById('btnAddToCart');

    if (btnAddToCart) {
        btnAddToCart.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            if (!productId) return;

            // Feedback visuel de chargement
            const originalIcon = this.innerHTML;
            this.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Traitement...';
            this.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('quantity', '1');
                formData.append('colorId', '0');
                formData.append('guaranteeId', '0');
                formData.append('csrf_token', csrfToken); // SÉCURITÉ

                const response = await fetch(`${baseUrl}Product/addToCart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                // ROBUSTESSE : Gestion sécurisée du parsing JSON pour éviter les crashs de l'interface
                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    throw new Error("Format de réponse inattendu du serveur.");
                }

                if (response.ok && result.status !== 'error') {
                    showProductToast("Le produit a été ajouté à votre panier avec succès !");
                    
                    // Mise à jour de l'icône du panier dans le header
                    const badge = document.getElementById('navCartCounterBadge');
                    if (badge && result.totalItems) {
                        badge.textContent = result.totalItems;
                        badge.style.display = 'inline-flex';
                        badge.style.transform = "scale(1.4)";
                        setTimeout(() => { badge.style.transform = "scale(1)"; }, 300);
                    }
                } else {
                    showProductToast(result.message || "Action non autorisée.", "danger");
                }
            } catch (error) {
                console.error("Erreur d'ajout au panier :", error);
                showProductToast("Erreur de communication avec le serveur.", "danger");
            } finally {
                this.innerHTML = originalIcon;
                this.disabled = false;
            }
        });
    }

    // =========================================================================
    // 6. SOUMISSION DES QUESTIONS (AJAX SÉCURISÉ & ROBUSTE)
    // =========================================================================
    const btnSubmitQuestion = document.getElementById('btnSubmitQuestion');
    const textareaQuestion = document.getElementById('questionText');

    if (btnSubmitQuestion && textareaQuestion) {
        btnSubmitQuestion.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            const questionText = textareaQuestion.value.trim();

            if (!questionText) {
                showProductToast("Veuillez saisir votre question avant de soumettre.", "danger");
                textareaQuestion.focus();
                return;
            }

            if (!productId) return;

            try {
                btnSubmitQuestion.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';
                btnSubmitQuestion.disabled = true;

                const formData = new URLSearchParams();
                formData.append('question', questionText);
                formData.append('csrf_token', csrfToken); // SÉCURITÉ

                const response = await fetch(`${baseUrl}Product/addQuestion/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                // ROBUSTESSE : Protection contre les erreurs 500 retournant du HTML
                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    throw new Error("Erreur de format de réponse du serveur.");
                }

                if (response.ok && result.status === 'success') {
                    textareaQuestion.value = '';
                    showProductToast(result.message);
                } else {
                    showProductToast(result.message || "Erreur lors de l'envoi de la requête.", "danger");
                }
            } catch (error) {
                console.error("Erreur Q&A:", error);
                showProductToast(error.message || "Erreur de réseau.", "danger");
            } finally {
                btnSubmitQuestion.innerHTML = '<i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Soumettre la question';
                btnSubmitQuestion.disabled = false;
            }
        });
    }

});