/**
 * Logique JavaScript pour la page de Recherche et Filtres Dynamiques
 * Architecture robuste : Vanilla JS & Fetch API (Sécurisé contre les failles XSS)
 */
document.addEventListener("DOMContentLoaded", () => {

    let currentPage = 1;
    
    const searchForm = document.getElementById('searchForm');
    const productsContainer = document.getElementById('productsContainer');
    const paginationContainer = document.getElementById('paginationContainer');
    
    // Récupération dynamique de l'URL de base pour un routage AJAX sans erreur 404
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';
    
    initializeListeners();
    executeSearch(1); // Lancement de la première recherche au chargement

    // ---------------------------------------------------------
    // FONCTION GLOBALE DE NOTIFICATION TOAST (ANTI-XSS)
    // ---------------------------------------------------------
    function showSearchToast(message, type = 'success') {
        let toast = document.getElementById('searchToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'searchToastNotification';
            document.body.appendChild(toast);
        }
        
        toast.className = `toast-notification toast-${type}`;
        
        // SÉCURITÉ : Création des nœuds manuellement pour éviter l'injection de scripts via innerHTML
        toast.innerHTML = '';
        const icon = document.createElement('i');
        icon.className = type === 'danger' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-cart-check';
        icon.setAttribute('aria-hidden', 'true');
        
        const textNode = document.createTextNode(" " + message);
        toast.appendChild(icon);
        toast.appendChild(textNode);
        
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    function initializeListeners() {
        if (!searchForm) return;
        const formElements = searchForm.querySelectorAll('select, input[type="checkbox"]');
        formElements.forEach(element => {
            element.addEventListener('change', () => executeSearch(1));
        });
    }

    async function executeSearch(page) {
        if (!searchForm || !productsContainer) return;

        currentPage = page;
        const formData = new FormData(searchForm);
        formData.append('current_page', currentPage);

        productsContainer.innerHTML = `
            <li class="loading-state" role="status">
                <i class="fa-solid fa-circle-notch fa-spin fa-2x loading-icon" aria-hidden="true"></i>
                <p>Recherche en cours...</p>
            </li>
        `;

        try {
            const response = await fetch(`${baseUrl}Search/doSearch`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (response.ok && !data.error) {
                const products = data[0] || [];
                const totalPages = data[1] || 1;
                
                renderProducts(products);
                renderPagination(totalPages);
            } else {
                productsContainer.innerHTML = `<li class="empty-state">${data.message || data.error || 'Erreur lors de la récupération.'}</li>`;
            }
        } catch (error) {
            productsContainer.innerHTML = '<li class="error-state">Erreur de connexion au serveur.</li>';
        }
    }

    function renderProducts(products) {
        productsContainer.innerHTML = ''; 

        if (products.length === 0) {
            const li = document.createElement('li');
            li.className = 'empty-state';
            li.textContent = "Aucun produit ne correspond à vos critères.";
            productsContainer.appendChild(li);
            return;
        }

        products.forEach(product => {
            const priceValue = parseFloat(product.price || 0);
            const discountValue = parseFloat(product.discount_percent || 0);
            const priceTotal = parseFloat(product.price_total || priceValue);
            
            const formattedPrice = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(priceTotal);
            const formattedOldPrice = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(priceValue);
            
            const li = document.createElement('li');
            li.className = 'product-card hover-glow';
            li.setAttribute('role', 'listitem');

            const btnFav = document.createElement('button');
            btnFav.type = 'button';
            btnFav.className = 'btn-favorite-toggle';
            btnFav.setAttribute('data-id', product.id);
            btnFav.setAttribute('aria-label', 'Ajouter aux favoris');
            btnFav.innerHTML = '<i class="fa-regular fa-heart" aria-hidden="true"></i>';

            let badge = null;
            if (discountValue > 0) {
                badge = document.createElement('div');
                badge.className = 'badge-item badge-discount';
                badge.textContent = `-${discountValue}%`;
            } else if (product.is_special_offer == 1) {
                badge = document.createElement('div');
                badge.className = 'badge-item badge-new';
                badge.textContent = 'Nouveau';
            }

            const linkImg = document.createElement('a');
            linkImg.href = `${baseUrl}Product/index/${product.id}`;
            linkImg.className = 'card-link-wrapper';

            const imgWrapper = document.createElement('div');
            imgWrapper.className = 'image-wrapper';

            const img = document.createElement('img');
            img.src = `${baseUrl}public/images/products/${product.id}/product_220.jpg`;
            img.className = 'product-img';
            img.alt = product.title; 
            img.onerror = function() { this.src = 'https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'; };

            imgWrapper.appendChild(img);
            linkImg.appendChild(imgWrapper);

            const cardContent = document.createElement('div');
            cardContent.className = 'card-content';

            const linkTitle = document.createElement('a');
            linkTitle.href = `${baseUrl}Product/index/${product.id}`;
            linkTitle.className = 'product-title-link';
            
            const titleH4 = document.createElement('h4');
            titleH4.className = 'product-title';
            titleH4.textContent = product.title; 

            linkTitle.appendChild(titleH4);

            const priceRow = document.createElement('div');
            priceRow.className = 'price-cart-row';

            const priceContainer = document.createElement('div');
            priceContainer.className = 'product-price-container';

            if (discountValue > 0) {
                const delPrice = document.createElement('del');
                delPrice.className = 'price-old';
                delPrice.textContent = `${formattedOldPrice} €`;
                
                const spanPrice = document.createElement('span');
                spanPrice.className = 'product-price price-danger';
                spanPrice.textContent = `${formattedPrice} €`;
                
                priceContainer.appendChild(delPrice);
                priceContainer.appendChild(spanPrice);
            } else {
                const spanPrice = document.createElement('span');
                spanPrice.className = 'product-price price-primary';
                spanPrice.textContent = `${formattedPrice} €`;
                priceContainer.appendChild(spanPrice);
            }

            const btnCart = document.createElement('button');
            btnCart.type = 'button';
            btnCart.className = 'btn-quick-add';
            btnCart.setAttribute('data-id', product.id);
            btnCart.setAttribute('aria-label', 'Ajouter au panier');
            btnCart.innerHTML = '<i class="fa-solid fa-cart-plus" aria-hidden="true"></i>';

            priceRow.appendChild(priceContainer);
            priceRow.appendChild(btnCart);

            cardContent.appendChild(linkTitle);
            cardContent.appendChild(priceRow);

            li.appendChild(btnFav);
            if (badge) li.appendChild(badge);
            li.appendChild(linkImg);
            li.appendChild(cardContent);

            productsContainer.appendChild(li);
        });
    }

    function renderPagination(totalPages) {
        if (!paginationContainer) return;
        paginationContainer.innerHTML = ''; 
        
        if (totalPages <= 1) return;

        if (currentPage > 1) {
            const btnPrev = createPageButton(currentPage - 1, '<i class="fa-solid fa-chevron-left" aria-hidden="true"></i> Préc', 'Aller à la page précédente');
            paginationContainer.appendChild(btnPrev);
        }

        let start = Math.max(1, currentPage - 2);
        let end = Math.min(totalPages, currentPage + 2);

        for (let i = start; i <= end; i++) {
            const btn = createPageButton(i, i, `Page ${i}`);
            if (i === currentPage) {
                btn.classList.add('active');
                btn.setAttribute('aria-current', 'page');
            }
            paginationContainer.appendChild(btn);
        }

        if (currentPage < totalPages) {
            const btnNext = createPageButton(currentPage + 1, 'Suiv <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>', 'Aller à la page suivante');
            paginationContainer.appendChild(btnNext);
        }
    }

    function createPageButton(pageNumber, htmlContent, ariaLabel) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-page';
        btn.setAttribute('data-page', pageNumber);
        btn.setAttribute('aria-label', ariaLabel);
        btn.innerHTML = htmlContent; 

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            executeSearch(pageNumber);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        return btn;
    }

    // ---------------------------------------------------------
    // GESTION DU PANIER (AJAX) - AJOUT RAPIDE
    // ---------------------------------------------------------
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
                // SÉCURITÉ : Lecture du jeton caché pour autoriser l'ajout au panier
                const csrfInput = document.getElementById('globalCsrfToken');
                const csrfToken = csrfInput ? csrfInput.value : '';

                const formData = new URLSearchParams();
                formData.append('quantity', '1');
                formData.append('colorId', '0');
                formData.append('guaranteeId', '0');
                formData.append('csrf_token', csrfToken); // Envoi au contrôleur Cart
                
                const response = await fetch(`${baseUrl}Cart/addToCart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                if (response.ok) {
                    const cartData = await response.json();
                    
                    // On s'adapte au format original de Cart.php (qui renvoie un tableau)
                    const cartItems = cartData[0] || [];
                    
                    let totalCount = 0;
                    cartItems.forEach(item => totalCount += parseInt(item.quantity || 1));
                    
                    // Mise à jour de la pastille rouge dans le menu
                    const badge = document.getElementById('navCartCounterBadge');
                    if (badge) {
                        badge.innerText = totalCount;
                        badge.style.transform = "scale(1.5)";
                        setTimeout(() => { badge.style.transform = "scale(1)"; }, 300);
                    }
                    
                    showSearchToast("Produit ajouté au panier !");
                } else {
                    showSearchToast("Erreur lors de la communication.", "danger");
                }
            } catch (error) {
                console.error("Erreur d'ajout au panier :", error);
                showSearchToast("Erreur de connexion.", "danger");
            } finally {
                btnAdd.innerHTML = originalIcon;
                btnAdd.disabled = false;
            }
        }
    });
});