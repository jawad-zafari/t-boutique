/**
 * Logique JavaScript pour le Panier Offcanvas (Menu Latéral) et la page Panier
 * Architecture Vanilla JS - Sécurisé avec protection CSRF pour les requêtes Ajax
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const cartOverlay = document.getElementById('cartOverlay');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCartBtn = document.getElementById('closeCartBtn');
    const headerCartBtns = document.querySelectorAll('.cart-btn'); 

    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    function getCsrfToken() {
        const mainCart = document.getElementById('mainCart');
        if (mainCart && mainCart.hasAttribute('data-csrf')) {
            return mainCart.getAttribute('data-csrf');
        }
        const csrfInput = document.querySelector('input[name="csrf_token"]');
        return csrfInput ? csrfInput.value : '';
    }

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

    if(closeCartBtn) closeCartBtn.addEventListener('click', closeCart);
    if(cartOverlay) cartOverlay.addEventListener('click', closeCart);

    function showToastNotification(message, type = 'success') {
        let toast = document.getElementById('cartToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'cartToastNotification';
            document.body.appendChild(toast);
        }
        
        toast.className = 'toast-notification';
        
        const span = document.createElement('span');
        span.textContent = message;

        if (type === 'danger') {
            toast.classList.add('toast-danger');
            toast.innerHTML = `<i class="fa-solid fa-trash-can"></i> `;
        } else if (type === 'error') {
            toast.classList.add('toast-danger'); 
            toast.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> `;
        } else {
            toast.classList.add('toast-success');
            toast.innerHTML = `<i class="fa-solid fa-circle-check"></i> `;
        }
        
        toast.appendChild(span);
        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    document.addEventListener('submit', async (e) => {
        const form = e.target.closest('.add-to-cart-form');
        if (form) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            let actionUrl = form.getAttribute('action');
            if(!actionUrl.startsWith('http') && !actionUrl.startsWith(baseUrl)) {
                 actionUrl = baseUrl + actionUrl;
            }

            const formData = new FormData(form);

            if (!formData.has('csrf_token')) {
                formData.append('csrf_token', getCsrfToken());
            }

            try {
                const response = await fetch(actionUrl, { method: 'POST', body: formData });
                const result = await response.json();
                
                if (result.status === 'error') {
                    showToastNotification(result.message, 'error');
                    return;
                }
                
                rebuildCartDOM(result[0], result[1]);
                showToastNotification("Produit ajouté au panier", 'success'); 
                
            } catch (error) {
                console.error("Erreur technique lors de l'ajout au panier :", error);
                showToastNotification("Erreur de connexion", 'error');
            }
        }
    }, true);

    document.body.addEventListener('click', (e) => {
        
        const btnMinus = e.target.closest('.btn-qty.minus');
        if (btnMinus) {
            const cartRow = btnMinus.getAttribute('data-row');
            const container = btnMinus.closest('.quantity-selector-modern') || btnMinus.closest('.qty-wrapper');
            if (container) {
                const input = container.querySelector('.input-qty');
                let currentVal = parseInt(input.value);
                if (currentVal > 1) {
                    currentVal--;
                    input.value = currentVal;
                    updateCartItem(currentVal, cartRow);
                }
            }
            return;
        }

        const btnPlus = e.target.closest('.btn-qty.plus');
        if (btnPlus) {
            const cartRow = btnPlus.getAttribute('data-row');
            const container = btnPlus.closest('.quantity-selector-modern') || btnPlus.closest('.qty-wrapper');
            if (container) {
                const input = container.querySelector('.input-qty');
                let currentVal = parseInt(input.value);
                if (currentVal < 30) {
                    currentVal++;
                    input.value = currentVal;
                    updateCartItem(currentVal, cartRow);
                }
            }
            return;
        }

        const btnRemove = e.target.closest('.btn-remove-item');
        if (btnRemove) {
            const cartRow = btnRemove.getAttribute('data-row');
            deleteCartItem(cartRow);
        }
    });

    async function updateCartItem(quantity, cartRow) {
        const url = `${baseUrl}Cart/updateCart`; 
        const params = new URLSearchParams();
        params.append('cartRow', cartRow); 
        params.append('quantity', quantity);
        params.append('csrf_token', getCsrfToken()); 
        
        try {
            const response = await fetch(url, { method: 'POST', body: params });
            const result = await response.json();
            
            if (result.status === 'error') {
                showToastNotification(result.message, 'error');
                return;
            }

            if (document.getElementById('mainCart')) {
                window.location.reload();
            } else {
                rebuildCartDOM(result[0], result[1]);
            }
        } catch (error) { console.error(error); }
    }

    async function deleteCartItem(cartRow) {
        try {
            const url = `${baseUrl}Cart/deleteCart/${cartRow}`;
            const params = new URLSearchParams();
            params.append('csrf_token', getCsrfToken()); 

            const response = await fetch(url, { method: 'POST', body: params });
            const result = await response.json();
            
            if (result.status === 'error') {
                showToastNotification(result.message, 'error');
                return;
            }

            if (document.getElementById('mainCart')) {
                window.location.reload();
            } else {
                rebuildCartDOM(result[0], result[1]);
                showToastNotification("Produit retiré du panier", 'danger');
            }
        } catch (error) { console.error(error); }
    }

    function rebuildCartDOM(cartArray, totalPriceAll) {
        const sidebarBody = document.getElementById('cartSidebarBody');
        const sidebarTotal = document.getElementById('cartSidebarTotal');
        const cartCounter = document.getElementById('headerCartCounter') || document.getElementById('navCartCounterBadge') || document.querySelector('.cart-counter'); 
        
        if (!sidebarBody) return;
        
        sidebarBody.innerHTML = '';
        let totalItems = 0;

        if (!cartArray || cartArray.length === 0) {
            // CONSTRUCTION SÉCURISÉE DU NOUVEAU DESIGN "PANIER VIDE" POUR LE SIDEBAR
            const emptyContainer = document.createElement('div');
            emptyContainer.className = 'sidebar-empty-state';

            const emptyMsg = document.createElement('div');
            emptyMsg.className = 'empty-msg-text';
            emptyMsg.textContent = 'Votre panier est vide.';

            const emptyIconBox = document.createElement('div');
            emptyIconBox.className = 'empty-bg-icon';
            
            const iconElement = document.createElement('i');
            iconElement.className = 'fa-solid fa-cart-arrow-down';
            iconElement.setAttribute('aria-hidden', 'true');
            
            emptyIconBox.appendChild(iconElement);
            emptyContainer.appendChild(emptyMsg);
            emptyContainer.appendChild(emptyIconBox);
            
            sidebarBody.appendChild(emptyContainer);
        } else {
            cartArray.forEach(item => {
                const qty = item.quantity ?? item.tedad ?? 1;
                totalItems += parseInt(qty);
                const priceFormatted = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(item.price);
                
                const cardDiv = document.createElement('div');
                cardDiv.className = 'cart-item-card';

                const img = document.createElement('img');
                img.src = `${baseUrl}public/images/products/${item.id}/product_220.jpg`;
                img.className = 'item-img';
                img.alt = item.title;
                img.onerror = function() { this.src = 'https://placehold.co/80x80/f5f5f5/111?text=Img'; };

                const detailsDiv = document.createElement('div');
                detailsDiv.className = 'item-details';

                const h4 = document.createElement('h4');
                h4.className = 'item-title';
                h4.textContent = item.title; 

                const priceDiv = document.createElement('div');
                priceDiv.className = 'item-price';
                priceDiv.textContent = `${priceFormatted} €`;

                const controlsDiv = document.createElement('div');
                controlsDiv.className = 'item-controls';

                const qtyWrapper = document.createElement('div');
                qtyWrapper.className = 'qty-wrapper';

                const btnMinus = document.createElement('button');
                btnMinus.type = 'button';
                btnMinus.className = 'btn-qty minus';
                btnMinus.setAttribute('data-row', item.cartRow);
                btnMinus.textContent = '-';

                const inputQty = document.createElement('input');
                inputQty.type = 'text';
                inputQty.className = 'input-qty';
                inputQty.value = qty;
                inputQty.readOnly = true;
                inputQty.setAttribute('data-row', item.cartRow);

                const btnPlus = document.createElement('button');
                btnPlus.type = 'button';
                btnPlus.className = 'btn-qty plus';
                btnPlus.setAttribute('data-row', item.cartRow);
                btnPlus.textContent = '+';

                qtyWrapper.appendChild(btnMinus);
                qtyWrapper.appendChild(inputQty);
                qtyWrapper.appendChild(btnPlus);

                const btnRemove = document.createElement('button');
                btnRemove.type = 'button';
                btnRemove.className = 'btn-remove-item';
                btnRemove.setAttribute('data-row', item.cartRow);
                btnRemove.innerHTML = '<i class="fa-solid fa-trash-can"></i>'; 

                controlsDiv.appendChild(qtyWrapper);
                controlsDiv.appendChild(btnRemove);

                detailsDiv.appendChild(h4);
                detailsDiv.appendChild(priceDiv);
                detailsDiv.appendChild(controlsDiv);

                cardDiv.appendChild(img);
                cardDiv.appendChild(detailsDiv);

                sidebarBody.appendChild(cardDiv);
            });
        }
        
        if (sidebarTotal) {
            sidebarTotal.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPriceAll) + ' €';
        }
        
        if (cartCounter) {
            cartCounter.textContent = totalItems;
            cartCounter.classList.add('pulse-anim');
            setTimeout(() => cartCounter.classList.remove('pulse-anim'), 300);
        }
    }
});