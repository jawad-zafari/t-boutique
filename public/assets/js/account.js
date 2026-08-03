/**
 * Logique JavaScript globale pour l'espace Client (SPA Dashboard)
 * Document Clean Code - Vanilla JS (Sécurisé CSRF)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Récupération dynamique de l'URL de base
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // Récupération du jeton CSRF depuis l'attribut data-csrf
    const dashboardWrapper = document.getElementById('mainAccountDashboard');
    const csrfToken = dashboardWrapper ? dashboardWrapper.getAttribute('data-csrf') : '';

    // =========================================================================
    // SYSTÈME DE TOAST PERSONNALISÉ (Notifications)
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
        toast.innerHTML = '';
        
        const icon = document.createElement('i');
        icon.className = (type === 'danger') ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ : Anti-XSS via textNode
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
    // 1. GESTION DES ONGLETS DU DASHBOARD (Single Page Application)
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

    // Gestion du routage après soumission (PRG Pattern)
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
            if(input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // =========================================================================
    // 3. GESTION DES MODALES (Déconnexion & Suppression)
    // =========================================================================
    const btnOpenDeleteModal = document.getElementById('btnOpenDeleteModal');
    const deleteModal = document.getElementById('deleteAccountModal');
    const btnCancelDelete = document.getElementById('btnCancelDelete');

    const btnOpenLogoutModal = document.getElementById('btnOpenLogoutModal');
    const logoutModal = document.getElementById('logoutModal');
    const btnCancelLogout = document.getElementById('btnCancelLogout');

    if (btnOpenDeleteModal && deleteModal) {
        btnOpenDeleteModal.addEventListener('click', () => { deleteModal.classList.add('active'); });
        if(btnCancelDelete) btnCancelDelete.addEventListener('click', () => { deleteModal.classList.remove('active'); });
    }

    if (btnOpenLogoutModal && logoutModal) {
        btnOpenLogoutModal.addEventListener('click', () => { logoutModal.classList.add('active'); });
        if(btnCancelLogout) btnCancelLogout.addEventListener('click', () => { logoutModal.classList.remove('active'); });
    }

    // =========================================================================
    // 4. ACTIVATION D'UN CODE DE REDUCTION
    // =========================================================================
    const btnActivateVoucher = document.getElementById('btnActivateVoucher');
    const voucherInput = document.getElementById('voucherCode');

    if (btnActivateVoucher && voucherInput) {
        btnActivateVoucher.addEventListener('click', async () => {
            const codeValue = voucherInput.value.trim();
            if (codeValue === "") {
                showAccountToast("Veuillez saisir un code de réduction valide.");
                return;
            }
            try {
                const params = new URLSearchParams();
                params.append('code', codeValue);
                params.append('csrf_token', csrfToken);

                const response = await fetch(`${baseUrl}Account/activateVoucher`, {
                    method: 'POST',
                    body: params
                });
                
                if (response.ok) {
                    sessionStorage.setItem('activeDashboardTab', 'tabVouchers');
                    window.location.reload(); 
                } else {
                    showAccountToast("Le code saisi est invalide ou expiré.");
                }
            } catch (error) { 
                console.error(error); 
                showAccountToast("Erreur de connexion au serveur.");
            }
        });
    }

    // =========================================================================
    // 5. GESTION DES DÉTAILS DE LA COMMANDE (MODALE AJAX)
    // =========================================================================
    const btnViewOrders = document.querySelectorAll('.btn-view-order');
    const orderModal = document.getElementById('orderDetailsModal');
    const btnCloseOrderModal = document.getElementById('btnCloseOrderModal');
    
    const loader = document.getElementById('orderDetailsLoader');
    const content = document.getElementById('orderDetailsContent');

    if (btnViewOrders.length > 0 && orderModal) {
        btnViewOrders.forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.getAttribute('data-id');
                
                orderModal.classList.add('active');
                loader.style.display = 'block';
                content.style.display = 'none';
                
                document.getElementById('modalOrderRef').textContent = '#' + orderId;

                try {
                    const response = await fetch(`${baseUrl}Account/getOrderDetails/${orderId}`);
                    const data = await response.json();

                    if (data.status === 'success') {
                        const order = data.order;
                        const products = data.products;

                        document.getElementById('modalOrderDate').textContent = order.created_date;
                        document.getElementById('modalOrderStatus').innerHTML = (order.is_paid == 1) ? '<span class="status-badge-paid">Payée</span>' : '<span class="status-badge-pending">En attente</span>';
                        document.getElementById('modalOrderAddress').textContent = order.address_data || 'Adresse non spécifiée';
                        
                        document.getElementById('modalOrderShipping').textContent = parseFloat(order.shipping_price) > 0 ? new Intl.NumberFormat('fr-FR').format(order.shipping_price) + ' €' : 'Gratuit';
                        document.getElementById('modalOrderTotal').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(order.total_amount) + ' €';

                        const productsContainer = document.getElementById('modalOrderProducts');
                        productsContainer.innerHTML = '';

                        if (products && products.length > 0) {
                            products.forEach(p => {
                                // SÉCURITÉ DWWM : Utilisation stricte de la variable 'quantity'
                                const qty = p.quantity || 1;
                                const price = p.price || 0;
                                const totalPrice = qty * price;
                                const imgSrc = `${baseUrl}public/images/products/${p.id}/product_220.jpg`;
                                
                                const html = `
                                <div class="modal-product-item">
                                    <div class="product-img">
                                        <img src="${imgSrc}" alt="" onerror="this.src='https://placehold.co/60x60/f8f9fa/adb5bd?text=Image'">
                                    </div>
                                    <div class="product-details">
                                        <div class="product-title">${p.title.replace(/</g, "&lt;").replace(/>/g, "&gt;")}</div>
                                        <div class="product-meta">Quantité : ${qty}</div>
                                    </div>
                                    <div class="product-price">${new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(totalPrice)} €</div>
                                </div>`;
                                productsContainer.insertAdjacentHTML('beforeend', html);
                            });
                        } else {
                            productsContainer.innerHTML = '<p class="text-muted-color">Détails des articles indisponibles.</p>';
                        }

                        loader.style.display = 'none';
                        content.style.display = 'block';

                    } else {
                        showAccountToast(data.message);
                        orderModal.classList.remove('active');
                    }
                } catch (error) {
                    console.error("Erreur Fetch Order Details", error);
                    showAccountToast("Une erreur s'est produite lors de la récupération des données.");
                    orderModal.classList.remove('active');
                }
            });
        });
    }

    if (btnCloseOrderModal) {
        btnCloseOrderModal.addEventListener('click', () => { orderModal.classList.remove('active'); });
    }

    // =========================================================================
    // 6. AJOUT AU PANIER DEPUIS LA PAGE FAVORIS (AJAX)
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
                        // SÉCURITÉ DWWM : Utilisation stricte de la variable 'quantity'
                        cartItems.forEach(item => totalCount += parseInt(item.quantity || 1, 10));
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