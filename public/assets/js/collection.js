/**
 * Logique JavaScript pour la page Collection
 * Sécurisé (Anti-XSS, CSRF, Routing dynamique) et Junior-Friendly
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // 1. DÉTECTION DYNAMIQUE DE L'URL DE BASE ET DU JETON CSRF
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    const mainWrapper = document.getElementById('collectionMainWrapper');
    const csrfToken = mainWrapper ? mainWrapper.getAttribute('data-csrf') : '';

    // =========================================================================
    // 2. GESTION DES FILTRES ET TRIS (AUTO-SUBMIT & PAGINATION)
    // =========================================================================
    const filterForm = document.getElementById('collectionFilterForm');
    
    if (filterForm) {
        const formElements = filterForm.querySelectorAll('select, input[type="checkbox"]');
        
        formElements.forEach(element => {
            element.addEventListener('change', () => {
                // Effet visuel de chargement fluide
                const grid = document.querySelector('.products-grid-layout');
                if (grid) {
                    grid.style.opacity = '0.4';
                    grid.style.pointerEvents = 'none';
                    grid.style.transition = 'opacity 0.3s ease';
                }

                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                
                const currentUrl = new URL(window.location.href);
                currentUrl.search = params.toString();
                window.location.href = currentUrl.toString();
            });
        });

        // Pagination intelligente (qui conserve les filtres actifs)
        const paginationLinks = document.querySelectorAll('.pagination-wrapper .page-link');
        if (paginationLinks.length > 0) {
            const currentParams = new URLSearchParams(new FormData(filterForm)).toString();
            
            paginationLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault(); 
                    const targetUrl = link.getAttribute('href');
                    
                    if (currentParams) {
                        const separator = targetUrl.includes('?') ? '&' : '?';
                        window.location.href = targetUrl + separator + currentParams;
                    } else {
                        window.location.href = targetUrl;
                    }
                });
            });
        }
    }

    // =========================================================================
    // 3. SYSTÈME DE NOTIFICATION TOAST (ANTI-XSS)
    // =========================================================================
    function showCollectionToast(message, type = 'success') {
        let toast = document.getElementById('collectionToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'collectionToastNotification';
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
        toast.innerHTML = '';
        
        const icon = document.createElement('i');
        icon.className = (type === 'danger') ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-cart-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ CRITIQUE (Anti-XSS) : Utilisation de textNode pour le message
        const textNode = document.createTextNode(message);

        toast.appendChild(icon);
        toast.appendChild(textNode);
        
        toast.style.opacity = '1';
        toast.style.display = 'block';
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3000);
    }

    // =========================================================================
    // 4. MISE À JOUR DU PANIER LATÉRAL (DOM SÉCURISÉ)
    // =========================================================================
    function updateCartSidebar(items, totalPrice) {
        const sidebarBody = document.getElementById('cartSidebarBody');
        const sidebarTotal = document.getElementById('cartSidebarTotal');
        
        if (!sidebarBody) return;
        sidebarBody.innerHTML = ''; // Nettoyage propre
        
        if (items.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-cart-msg';
            emptyDiv.textContent = 'Votre panier est vide.';
            sidebarBody.appendChild(emptyDiv);
            if (sidebarTotal) sidebarTotal.textContent = '0,00 €';
            return;
        }
        
        items.forEach(item => {
            const qty = item.quantity || item.tedad || 1;
            
            // SÉCURITÉ (Anti-XSS) : Construction manuelle du DOM
            const cardDiv = document.createElement('div');
            cardDiv.className = 'cart-item-card';

            const img = document.createElement('img');
            img.src = `${baseUrl}public/images/products/${item.id}/product_220.jpg`;
            img.alt = item.title;
            img.className = 'item-img';
            img.onerror = function() { this.src = 'https://placehold.co/80x80/f5f5f5/111?text=Img'; };

            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'item-details';

            const h4 = document.createElement('h4');
            h4.className = 'item-title';
            h4.textContent = item.title; // Les scripts malveillants sont neutralisés ici

            const priceDiv = document.createElement('div');
            priceDiv.className = 'item-price';
            priceDiv.textContent = `${new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(item.price)} €`;

            const controlsDiv = document.createElement('div');
            controlsDiv.className = 'item-controls';

            const qtyWrapper = document.createElement('div');
            qtyWrapper.className = 'qty-wrapper';
            
            // Les variables ici sont garanties comme étant des entiers (cartRow)
            qtyWrapper.innerHTML = `
                <button type="button" class="btn-qty minus" data-row="${item.cartRow}">-</button>
                <input type="text" class="input-qty" value="${qty}" readonly data-row="${item.cartRow}">
                <button type="button" class="btn-qty plus" data-row="${item.cartRow}">+</button>
            `;

            const btnRemove = document.createElement('button');
            btnRemove.type = 'button';
            btnRemove.className = 'btn-remove-item';
            btnRemove.setAttribute('data-row', item.cartRow);
            btnRemove.innerHTML = '<i class="fa-solid fa-trash-can" aria-hidden="true"></i>';

            controlsDiv.appendChild(qtyWrapper);
            controlsDiv.appendChild(btnRemove);

            detailsDiv.appendChild(h4);
            detailsDiv.appendChild(priceDiv);
            detailsDiv.appendChild(controlsDiv);

            cardDiv.appendChild(img);
            cardDiv.appendChild(detailsDiv);

            sidebarBody.appendChild(cardDiv);
        });
        
        if (sidebarTotal) {
            sidebarTotal.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPrice) + ' €';
        }
    }

    // =========================================================================
    // 5. AJOUT AU PANIER VIA AJAX (AVEC PROTECTION CSRF)
    // =========================================================================
    document.addEventListener('click', async (e) => {
        const btnAdd = e.target.closest('.btn-quick-add');
        if (btnAdd) {
            e.preventDefault();
            const productId = btnAdd.getAttribute('data-id');
            if (!productId) return;

            const originalIcon = btnAdd.innerHTML;
            btnAdd.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i>';
            btnAdd.disabled = true;

            try {
                const formData = new URLSearchParams();
                formData.append('quantity', '1');
                formData.append('colorId', '0');
                formData.append('guaranteeId', '0');
                
                // SÉCURITÉ CRITIQUE : Injection du jeton CSRF lu depuis le conteneur HTML
                formData.append('csrf_token', csrfToken); 
                
                const response = await fetch(`${baseUrl}Cart/addToCart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                if (response.ok) {
                    const cartData = await response.json();
                    
                    // Gestion sécurisée des erreurs du contrôleur (Ex: CSRF Invalide)
                    if (cartData.status === 'error') {
                        showCollectionToast(cartData.message, 'danger');
                        return;
                    }

                    const cartItems = cartData[0] || [];
                    const totalPrice = cartData[1] || 0;
                    
                    let totalCount = 0;
                    cartItems.forEach(item => totalCount += parseInt(item.quantity || item.tedad || 1));
                    
                    const badge = document.getElementById('navCartCounterBadge');
                    if (badge) {
                        badge.textContent = totalCount;
                        badge.style.transform = "scale(1.5)";
                        setTimeout(() => { badge.style.transform = "scale(1)"; }, 300);
                    }
                    
                    updateCartSidebar(cartItems, totalPrice);
                    showCollectionToast("Produit ajouté au panier !");
                } else {
                    showCollectionToast("Erreur lors de l'ajout.", "danger");
                }
            } catch (error) {
                console.error("Erreur d'ajout au panier :", error);
                showCollectionToast("Erreur de connexion.", "danger");
            } finally {
                btnAdd.innerHTML = originalIcon;
                btnAdd.disabled = false;
            }
        }
    });

});