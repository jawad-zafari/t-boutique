/**
 * Logique JavaScript pour le Panier Offcanvas (Menu Latéral) et la page Panier
 * Architecture Vanilla JS - Sécurisée avec protection CSRF pour les requêtes Ajax
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const cartOverlay = document.getElementById('cartOverlay');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCartBtn = document.getElementById('closeCartBtn');
    const headerCartBtns = document.querySelectorAll('.cart-btn'); 

    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // Fonction pour récupérer le jeton CSRF de façon sécurisée
    function getCsrfToken() {
        const mainCart = document.getElementById('mainCart');
        if (mainCart && mainCart.hasAttribute('data-csrf')) {
            return mainCart.getAttribute('data-csrf');
        }
        const csrfInput = document.querySelector('input[name="csrf_token"]');
        return csrfInput ? csrfInput.value : '';
    }

    // Gestion de l'ouverture/fermeture du panier latéral (Offcanvas)
    function openCart() {
        if(cartSidebar && cartOverlay) {
            cartSidebar.classList.add('active');
            cartOverlay.classList.add('active');
        }
    }

    function closeCart() {
        if(cartSidebar && cartOverlay) {
            cartSidebar.classList.remove('active');
            cartOverlay.classList.remove('active');
        }
    }

    headerCartBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openCart();
        });
    });

    if (closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // =========================================================================
    // 1. MISE À JOUR DYNAMIQUE DE L'INTERFACE (Page + Sidebar)
    // =========================================================================
    function updateCartUI(data) {
        const cartItems = data[0] || [];
        const totalPriceAll = data[1] || 0;
        
        let totalItems = 0;
        cartItems.forEach(item => {
            totalItems += parseInt(item.quantity || item.tedad || 1, 10);
        });

        // Mise à jour de la page principale du panier si on s'y trouve
        const mainCart = document.getElementById('mainCart');
        if (mainCart) {
            // Mettre à jour les sous-totaux globaux
            const totalElements = document.querySelectorAll('.total-all-price');
            totalElements.forEach(el => {
                el.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPriceAll) + ' €';
            });

            // Si le panier devient vide
            if (cartItems.length === 0) {
                location.reload(); // Recharger la page pour afficher l'état panier vide
                return;
            }

            // Mettre à jour chaque ligne de produit
            cartItems.forEach(item => {
                const rowId = item.cartRow;
                const qty = item.quantity || item.tedad || 1;
                const price = item.price || 0;

                const qtyInput = document.querySelector(`.input-qty[data-row="${rowId}"]`);
                if (qtyInput) qtyInput.value = qty;

                const lineTotal = document.querySelector(`.line-total[data-row="${rowId}"]`);
                if (lineTotal) {
                    lineTotal.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(price * qty) + ' €';
                }
            });
        }

        // Mise à jour du tiroir latéral (Sidebar Offcanvas)
        const sidebarBody = document.getElementById('cartSidebarBody');
        const sidebarTotal = document.getElementById('cartSidebarTotal');
        const cartCounter = document.getElementById('cartBadgeCount');

        if (sidebarBody) {
            sidebarBody.innerHTML = '';
            
            if (cartItems.length === 0) {
                sidebarBody.innerHTML = '<p class="text-center text-muted p-20">Votre panier est vide.</p>';
            } else {
                cartItems.forEach(item => {
                    const cardDiv = document.createElement('div');
                    cardDiv.className = 'sidebar-cart-item';

                    const img = document.createElement('img');
                    img.src = baseUrl + 'public/images/products/' + item.id + '/product_220.jpg';
                    img.alt = item.title || 'Produit';
                    img.onerror = function() { this.src = 'https://placehold.co/60x60/f1f3f5/3b5bdb?text=Img'; };

                    const detailsDiv = document.createElement('div');
                    detailsDiv.className = 'sidebar-item-details';

                    const h4 = document.createElement('h4');
                    h4.textContent = item.title || '';

                    const priceDiv = document.createElement('div');
                    priceDiv.className = 'sidebar-item-price';
                    priceDiv.textContent = (item.quantity || 1) + ' x ' + new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(item.price || 0) + ' €';

                    detailsDiv.appendChild(h4);
                    detailsDiv.appendChild(priceDiv);
                    cardDiv.appendChild(img);
                    cardDiv.appendChild(detailsDiv);

                    sidebarBody.appendChild(cardDiv);
                });
            }
        }
        
        if (sidebarTotal) {
            sidebarTotal.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPriceAll) + ' €';
        }
        
        if (cartCounter) {
            cartCounter.textContent = totalItems;
        }
    }

    // =========================================================================
    // 2. ENVOI DES REQUÊTES AJAX (AJOUT, MODIFICATION, SUPPRESSION)
    // =========================================================================

    // A. MODIFIER LA QUANTITÉ (BOUTONS + / - ET INPUT)
    document.body.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-qty')) {
            const btn = e.target;
            const rowId = btn.getAttribute('data-row');
            const input = document.querySelector(`.input-qty[data-row="${rowId}"]`);
            
            if (input) {
                let currentVal = parseInt(input.value, 10) || 1;
                
                if (btn.classList.contains('plus')) {
                    currentVal++;
                } else if (btn.classList.contains('minus') && currentVal > 1) {
                    currentVal--;
                }
                
                input.value = currentVal;
                sendUpdateQtyAjax(rowId, currentVal);
            }
        }

        // B. SUPPRIMER UN ARTICLE
        if (e.target.closest('.btn-remove-item')) {
            const btn = e.target.closest('.btn-remove-item');
            const rowId = btn.getAttribute('data-row');
            
            if (rowId) {
                sendDeleteCartAjax(rowId);
            }
        }
    });

    // Fonction AJAX : Mise à jour quantité
    function sendUpdateQtyAjax(cartRow, quantity) {
        const formData = new FormData();
        formData.append('cartRow', cartRow);
        formData.append('quantity', quantity);
        formData.append('csrf_token', getCsrfToken());

        fetch(baseUrl + 'Cart/updateCart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => updateCartUI(data))
        .catch(err => console.error("Erreur mise à jour panier:", err));
    }

    // Fonction AJAX : Suppression article
    function sendDeleteCartAjax(cartRow) {
        const formData = new FormData();
        formData.append('csrf_token', getCsrfToken());

        fetch(baseUrl + 'Cart/deleteCart/' + cartRow, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Supprimer la carte du DOM immédiatement
            const card = document.querySelector(`.cart-product-card[data-row="${cartRow}"]`);
            if (card) card.remove();
            
            updateCartUI(data);
        })
        .catch(err => console.error("Erreur suppression article:", err));
    }

    // C. AJOUTER AU PANIER DEPUIS LA FICHE PRODUIT (Exemple global)
    const btnAddToCartProduct = document.getElementById('btnAddToCartProduct');
    if (btnAddToCartProduct) {
        btnAddToCartProduct.addEventListener('click', (e) => {
            e.preventDefault();
            
            const productId = btnAddToCartProduct.getAttribute('data-product-id');
            const qtyInput = document.getElementById('productDetailQty');
            const colorSelect = document.getElementById('productColorSelect');
            const guaranteeSelect = document.getElementById('productGuaranteeSelect');

            const formData = new FormData();
            formData.append('quantity', qtyInput ? qtyInput.value : 1);
            formData.append('colorId', colorSelect ? colorSelect.value : 0);
            formData.append('guaranteeId', guaranteeSelect ? guaranteeSelect.value : 0);
            formData.append('csrf_token', getCsrfToken());

            fetch(baseUrl + 'Cart/addToCart/' + productId, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                updateCartUI(data);
                openCart(); // Ouvrir le panier latéral pour confirmation visuelle
            })
            .catch(err => console.error("Erreur ajout panier:", err));
        });
    }

});