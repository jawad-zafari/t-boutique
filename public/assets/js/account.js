/**
 * Logique JavaScript globale pour l'espace Client (SPA Dashboard)
 * Document Clean Code - Vanilla JS 
 * Sécurité: Protection CSRF stricte & DOM-based XSS prévention (createElement)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    const dashboardWrapper = document.getElementById('mainAccountDashboard');
    const csrfToken = dashboardWrapper ? dashboardWrapper.getAttribute('data-csrf') : '';

    // =========================================================================
    // SYSTÈME DE TOAST PERSONNALISÉ (Sécurisé)
    // =========================================================================
    function showAccountToast(message, type = 'danger') {
        let toast = document.getElementById('accountToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'accountToastNotification';
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
        toast.innerHTML = ''; // Nettoyage initial sécurisé
        
        const icon = document.createElement('i');
        icon.className = (type === 'danger') ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ CRITIQUE : Utilisation de textNode pour prévenir toute injection XSS
        const textNode = document.createTextNode(message); 

        toast.appendChild(icon);
        toast.appendChild(textNode);

        toast.style.opacity = '1';
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    // =========================================================================
    // 1. GESTION DES ONGLETS DU DASHBOARD (SPA avec persistance)
    // =========================================================================
    const navItems = document.querySelectorAll('.account-nav-list .nav-item[data-target]');
    const tabContents = document.querySelectorAll('.account-tab-content');

    function switchTab(targetId) {
        if (!targetId) return;
        navItems.forEach(nav => nav.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));

        const activeNav = document.querySelector(`[data-target="${targetId}"]`);
        const targetContent = document.getElementById(targetId);

        if (activeNav && targetContent) {
            activeNav.classList.add('active');
            targetContent.classList.add('active');
            // Persistance de l'état pour une meilleure UX
            sessionStorage.setItem('activeDashboardTab', targetId);
        }
    }

    if (navItems.length > 0 && tabContents.length > 0) {
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.alert-sticky').forEach(alert => {
                    alert.style.display = 'none';
                });
                switchTab(this.getAttribute('data-target'));
            });
        });
    }

    // Vérification des paramètres URL pour forcer l'affichage de l'onglet Infos
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') || urlParams.has('error')) {
        switchTab('tabInfo');
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
        window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
    } else {
        const savedTab = sessionStorage.getItem('activeDashboardTab');
        if (savedTab) switchTab(savedTab);
    }

    const btnViewAllOrdersShortcut = document.getElementById('btnViewAllOrdersShortcut');
    if (btnViewAllOrdersShortcut) {
        btnViewAllOrdersShortcut.addEventListener('click', () => switchTab('tabOrders'));
    }

    // =========================================================================
    // 2. AFFICHER/MASQUER LE MOT DE PASSE
    // =========================================================================
    const togglePasswords = document.querySelectorAll('.toggle-password');
    togglePasswords.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const icon = this.querySelector('i');
            if (input && input.type === 'password') {
                input.type = 'text';
                if(icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
            } else if (input) {
                input.type = 'password';
                if(icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
            }
        });
    });

    // =========================================================================
    // 3. GESTION DES MODALES (Déconnexion & Suppression)
    // =========================================================================
    const openModal = (id) => {
        const el = document.getElementById(id);
        if (el) el.classList.add('active');
    };
    const closeModal = (id) => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('active');
    };

    document.getElementById('btnOpenDeleteModal')?.addEventListener('click', () => openModal('deleteAccountModal'));
    document.getElementById('btnCancelDelete')?.addEventListener('click', () => closeModal('deleteAccountModal'));

    document.getElementById('btnOpenLogoutModal')?.addEventListener('click', () => openModal('logoutModal'));
    document.getElementById('btnCancelLogout')?.addEventListener('click', () => closeModal('logoutModal'));

    // =========================================================================
    // 4. GESTION DES DÉTAILS DE LA COMMANDE (Sécurisé Anti-XSS)
    // =========================================================================
    const btnViewOrders = document.querySelectorAll('.btn-view-order');
    const orderModal = document.getElementById('orderDetailsModal');
    
    const loader = document.getElementById('orderDetailsLoader');
    const content = document.getElementById('orderDetailsContent');

    if (btnViewOrders.length > 0 && orderModal) {
        btnViewOrders.forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.getAttribute('data-id');
                if (!orderId) return;
                
                orderModal.classList.add('active');
                if (loader) loader.style.display = 'block';
                if (content) content.style.display = 'none';
                
                const refElem = document.getElementById('modalOrderRef');
                // SÉCURITÉ : textContent utilisé à la place de innerHTML
                if (refElem) refElem.textContent = '#' + orderId;

                try {
                    const response = await fetch(`${baseUrl}Account/getOrderDetails/${orderId}`);
                    const data = await response.json();

                    if (data.status === 'success') {
                        const order = data.order;
                        const products = data.products;

                        document.getElementById('modalOrderDate').textContent = order.created_date || '';
                        document.getElementById('modalOrderAddress').textContent = order.address_data || 'Adresse non spécifiée';
                        
                        const statusContainer = document.getElementById('modalOrderStatus');
                        if (statusContainer) {
                            statusContainer.innerHTML = '';
                            const statusSpan = document.createElement('span');
                            statusSpan.className = (order.is_paid == 1) ? 'status-badge-paid' : 'status-badge-pending';
                            statusSpan.textContent = (order.is_paid == 1) ? 'Payée' : 'En attente';
                            statusContainer.appendChild(statusSpan);
                        }
                        
                        document.getElementById('modalOrderShipping').textContent = parseFloat(order.shipping_price) > 0 ? new Intl.NumberFormat('fr-FR').format(order.shipping_price) + ' €' : 'Gratuit';
                        document.getElementById('modalOrderTotal').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(order.total_amount) + ' €';

                        const productsContainer = document.getElementById('modalOrderProducts');
                        if (productsContainer) {
                            productsContainer.innerHTML = ''; // Nettoyage

                            if (products && products.length > 0) {
                                products.forEach(p => {
                                    const qty = p.tedad || p.quantity || 1;
                                    const price = p.price || 0;
                                    const totalPrice = qty * price;
                                    const imgSrc = `${baseUrl}public/images/products/${parseInt(p.id)}/product_220.jpg`;
                                    
                                    // SÉCURITÉ : Création stricte des éléments du DOM (DOM Building)
                                    const itemDiv = document.createElement('div');
                                    itemDiv.className = 'modal-product-item';

                                    const imgContainer = document.createElement('div');
                                    imgContainer.className = 'product-img';
                                    const img = document.createElement('img');
                                    img.src = imgSrc;
                                    img.alt = p.title || 'Produit';
                                    img.onerror = function() { this.src = 'https://placehold.co/60x60/f8f9fa/adb5bd?text=Image'; };
                                    imgContainer.appendChild(img);

                                    const detailsContainer = document.createElement('div');
                                    detailsContainer.className = 'product-details';
                                    
                                    const titleDiv = document.createElement('div');
                                    titleDiv.className = 'product-title';
                                    titleDiv.textContent = p.title || 'Produit inconnu'; // Injection sécurisée

                                    const metaDiv = document.createElement('div');
                                    metaDiv.className = 'product-meta';
                                    metaDiv.textContent = `Quantité : ${qty}`;

                                    detailsContainer.appendChild(titleDiv);
                                    detailsContainer.appendChild(metaDiv);

                                    const priceDiv = document.createElement('div');
                                    priceDiv.className = 'product-price';
                                    priceDiv.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPrice) + ' €';

                                    itemDiv.appendChild(imgContainer);
                                    itemDiv.appendChild(detailsContainer);
                                    itemDiv.appendChild(priceDiv);

                                    productsContainer.appendChild(itemDiv);
                                });
                            } else {
                                const emptyMsg = document.createElement('p');
                                emptyMsg.className = 'text-muted-color';
                                emptyMsg.textContent = 'Détails des articles indisponibles.';
                                productsContainer.appendChild(emptyMsg);
                            }
                        }

                        if (loader) loader.style.display = 'none';
                        if (content) content.style.display = 'block';

                    } else {
                        showAccountToast(data.message);
                        closeModal('orderDetailsModal');
                    }
                } catch (error) {
                    console.error("Erreur Fetch Order Details", error);
                    showAccountToast("Une erreur s'est produite lors de la récupération des données.");
                    closeModal('orderDetailsModal');
                }
            });
        });
    }

    document.getElementById('btnCloseOrderModal')?.addEventListener('click', () => closeModal('orderDetailsModal'));

    // =========================================================================
    // 5. AJOUT AU PANIER DEPUIS LA PAGE FAVORIS (AJAX + CSRF)
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
                // SÉCURITÉ : Récupération du jeton CSRF
                let csrfForCart = csrfToken;
                if(!csrfForCart) {
                    const csrfInput = document.querySelector('input[name="csrf_token"]');
                    if(csrfInput) csrfForCart = csrfInput.value;
                }

                const formData = new URLSearchParams();
                formData.append('quantity', '1');
                formData.append('colorId', '0');
                formData.append('guaranteeId', '0');
                formData.append('csrf_token', csrfForCart);
                
                const response = await fetch(`${baseUrl}Cart/addToCart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const responseData = await response.json();

                if (response.ok && !responseData.error) {
                    const cartItems = responseData[0] || [];
                    
                    let totalCount = 0;
                    if(Array.isArray(cartItems)) {
                        cartItems.forEach(item => totalCount += parseInt(item.quantity || item.tedad || 1, 10));
                    } else if (responseData.totalItems) {
                        totalCount = parseInt(responseData.totalItems, 10);
                    }
                    
                    const badge = document.getElementById('navCartCounterBadge');
                    if (badge) {
                        badge.innerText = totalCount;
                        badge.style.display = 'inline-flex';
                        badge.style.transform = "scale(1.5)";
                        setTimeout(() => { badge.style.transform = "scale(1)"; }, 300);
                    }
                    
                    showAccountToast("Produit ajouté au panier avec succès !", "success");
                } else {
                    showAccountToast(responseData.message || "Erreur lors de l'ajout.", "danger");
                }
            } catch (error) {
                console.error("Erreur d'ajout :", error);
                showAccountToast("Erreur de connexion au serveur.", "danger");
            } finally {
                btnAdd.innerHTML = originalIcon;
                btnAdd.disabled = false;
            }
        }
    });

});