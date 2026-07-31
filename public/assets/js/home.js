/**
 * Logique JavaScript pour la page d'accueil (Home)
 * Sécurité: Protection contre XSS (DOM), intégration CSRF pour Fetch API, routing sécurisé.
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Récupération de l'URL de base pour garantir un routing correct
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // SÉCURITÉ : Récupération du jeton CSRF injecté dans la vue
    const homeWrapper = document.getElementById('homeMainWrapper');
    const csrfToken = homeWrapper ? homeWrapper.getAttribute('data-csrf') : '';

    // =========================================================================
    // 1. GESTION DU SLIDER PRINCIPAL
    // =========================================================================
    const sliderTrack = document.getElementById('sliderTrack');
    const slides = document.querySelectorAll('.slide');
    const btnNext = document.getElementById('btnNext');
    const btnPrev = document.getElementById('btnPrev');
    const dotsContainer = document.getElementById('sliderDots');

    if (sliderTrack && slides.length > 0) {
        const totalSlides = slides.length;
        let currentIndex = 0;
        let autoPlayInterval;

        if (dotsContainer) {
            slides.forEach((_, index) => {
                const dot = document.createElement('button');
                dot.classList.add('dot');
                dot.setAttribute('role', 'tab');
                dot.setAttribute('aria-label', `Slide ${index + 1}`);
                if (index === 0) dot.classList.add('active');
                
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateSliderPosition();
                    resetAutoPlay();
                });
                dotsContainer.appendChild(dot);
            });
        }

        const updateSliderPosition = () => {
            sliderTrack.style.transform = `translateX(-${currentIndex * 100}%)`;
            if (dotsContainer) {
                document.querySelectorAll('.slider-dots .dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentIndex);
                });
            }
        };

        const moveToNextSlide = () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            updateSliderPosition();
        };

        const moveToPrevSlide = () => {
            currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
            updateSliderPosition();
        };

        if (btnNext) btnNext.addEventListener('click', () => { moveToNextSlide(); resetAutoPlay(); });
        if (btnPrev) btnPrev.addEventListener('click', () => { moveToPrevSlide(); resetAutoPlay(); });

        const startAutoPlay = () => { autoPlayInterval = setInterval(moveToNextSlide, 6000); };
        const resetAutoPlay = () => { clearInterval(autoPlayInterval); startAutoPlay(); };

        if (totalSlides > 1) startAutoPlay();
    }

    // =========================================================================
    // 2. GESTION DYNAMIQUE DU CAROUSEL DES MARQUES
    // =========================================================================
    const brandsCarouselTrack = document.getElementById('brandsCarouselTrack');
    const brandsBtnNext = document.getElementById('brandsBtnNext');
    const brandsBtnPrev = document.getElementById('brandsBtnPrev');

    if (brandsCarouselTrack) {
        const getBrandScrollStep = () => {
            const firstBrandItem = brandsCarouselTrack.querySelector('.brands-carousel-item');
            return firstBrandItem ? firstBrandItem.clientWidth + 15 : 120;
        };

        if (brandsBtnNext) {
            brandsBtnNext.addEventListener('click', () => {
                brandsCarouselTrack.scrollBy({ left: getBrandScrollStep(), behavior: 'smooth' });
            });
        }

        if (brandsBtnPrev) {
            brandsBtnPrev.addEventListener('click', () => {
                brandsCarouselTrack.scrollBy({ left: -getBrandScrollStep(), behavior: 'smooth' });
            });
        }
    }

    // =========================================================================
    // 3. GESTION DYNAMIQUE DU CAROUSEL BOUTIQUE TV
    // =========================================================================
    const tvCarouselTrack = document.getElementById('tvCarouselTrack');
    const tvBtnNext = document.getElementById('tvBtnNext');
    const tvBtnPrev = document.getElementById('tvBtnPrev');

    if (tvCarouselTrack) {
        const getTvScrollStep = () => {
            const firstTvItem = tvCarouselTrack.querySelector('.tv-carousel-item');
            return firstTvItem ? firstTvItem.clientWidth + 20 : 300;
        };

        if (tvBtnNext) {
            tvBtnNext.addEventListener('click', () => {
                tvCarouselTrack.scrollBy({ left: getTvScrollStep(), behavior: 'smooth' });
            });
        }

        if (tvBtnPrev) {
            tvBtnPrev.addEventListener('click', () => {
                tvCarouselTrack.scrollBy({ left: -getTvScrollStep(), behavior: 'smooth' });
            });
        }
    }

    // =========================================================================
    // 4. LECTURE VIDÉO INLINE (BOUTIQUE TV) - SÉCURISÉ (Création DOM)
    // =========================================================================
    document.addEventListener('click', (e) => {
        const tvTrigger = e.target.closest('.tv-image-container');
        if (tvTrigger) {
            const parentItem = tvTrigger.closest('.tv-carousel-item');
            let videoSrc = parentItem.getAttribute('data-video-src');
            
            if (videoSrc) {
                if (videoSrc.includes('youtube.com') && !videoSrc.includes('autoplay=')) {
                    videoSrc += (videoSrc.includes('?') ? '&' : '?') + 'autoplay=1';
                }
                
                // Vider l'élément parent en toute sécurité
                parentItem.innerHTML = '';
                
                // SÉCURITÉ : Création manuelle de l'iframe pour éviter toute injection HTML/JS
                const iframe = document.createElement('iframe');
                iframe.src = videoSrc;
                iframe.setAttribute('frameborder', '0');
                iframe.setAttribute('allowfullscreen', 'true');
                iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
                iframe.style.width = '100%';
                iframe.style.aspectRatio = '16/9';
                iframe.style.borderRadius = '8px';
                iframe.style.border = '1px solid #e2e8f0';
                iframe.style.display = 'block';
                
                parentItem.appendChild(iframe);
            }
        }
    });

    // =========================================================================
    // 5. GESTION DU CAROUSEL DES ACTUALITÉS (NEWS)
    // =========================================================================
    const newsCarouselTrack = document.getElementById('newsCarouselTrack');
    const newsBtnNext = document.getElementById('newsBtnNext');
    const newsBtnPrev = document.getElementById('newsBtnPrev');

    if (newsCarouselTrack) {
        const getNewsScrollStep = () => {
            const firstNewsItem = newsCarouselTrack.querySelector('.news-card');
            return firstNewsItem ? firstNewsItem.clientWidth + 20 : 350;
        };

        if (newsBtnNext) {
            newsBtnNext.addEventListener('click', () => {
                newsCarouselTrack.scrollBy({ left: getNewsScrollStep(), behavior: 'smooth' });
            });
        }

        if (newsBtnPrev) {
            newsBtnPrev.addEventListener('click', () => {
                newsCarouselTrack.scrollBy({ left: -getNewsScrollStep(), behavior: 'smooth' });
            });
        }
    }

    // =========================================================================
    // 6. GESTION DU MODAL DYNAMIQUE DES ACTUALITÉS - SÉCURISÉ (Anti-XSS)
    // =========================================================================
    const newsModal = document.getElementById('newsModal');
    const closeNewsModal = document.getElementById('closeNewsModal');
    const newsModalImg = document.getElementById('newsModalImg');
    const newsModalTitle = document.getElementById('newsModalTitle');
    const newsModalDate = document.getElementById('newsModalDate');
    const newsModalDesc = document.getElementById('newsModalDesc');

    document.addEventListener('click', (e) => {
        const newsItem = e.target.closest('.clickable-news-item');
        if (newsItem && newsModal) {
            // SÉCURITÉ : Utilisation de textContent pour neutraliser l'injection de scripts malveillants
            if (newsModalTitle) newsModalTitle.textContent = newsItem.getAttribute('data-title') || '';
            if (newsModalDate) newsModalDate.textContent = newsItem.getAttribute('data-date') || '';
            if (newsModalDesc) newsModalDesc.textContent = newsItem.getAttribute('data-desc') || '';
            if (newsModalImg) newsModalImg.src = newsItem.getAttribute('data-img') || '';

            newsModal.classList.add('show');
        }
    });

    const closeNewsModalWindow = () => {
        if (newsModal) newsModal.classList.remove('show');
    };

    if (closeNewsModal) closeNewsModal.addEventListener('click', closeNewsModalWindow);
    if (newsModal) {
        newsModal.addEventListener('click', (e) => {
            if (e.target === newsModal) closeNewsModalWindow();
        });
    }

    // =========================================================================
    // 7. GESTION DU PANIER (AJAX) - SÉCURISÉ AVEC CSRF ET DOM SÉCURISÉ
    // =========================================================================
    function showHomeToast(message, type = 'success') {
        let toast = document.getElementById('homeToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'homeToastNotification';
            document.body.appendChild(toast);
        }
        
        toast.className = `toast-notification toast-${type}`;
        
        // SÉCURITÉ : Anti-XSS sur le message du toast via textContent
        const span = document.createElement('span');
        span.textContent = message;

        toast.innerHTML = `<i class="fa-solid fa-cart-check"></i> `;
        toast.appendChild(span);
        toast.classList.add('show');
        
        setTimeout(() => { toast.classList.remove('show'); }, 3000);
    }

    function updateCartSidebar(items, totalPrice) {
        const sidebarBody = document.getElementById('cartSidebarBody');
        const sidebarTotal = document.getElementById('cartSidebarTotal');
        
        if (!sidebarBody) return;
        sidebarBody.innerHTML = ''; 
        
        if (items.length === 0) {
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'empty-cart-msg';
            emptyDiv.textContent = 'Votre panier est vide.';
            sidebarBody.appendChild(emptyDiv);
            if (sidebarTotal) sidebarTotal.textContent = '0.00 €';
            return;
        }
        
        items.forEach(item => {
            const qty = item.quantity || 1;
            const price = parseFloat(item.price).toFixed(2);
            
            const cardDiv = document.createElement('div');
            cardDiv.className = 'cart-item-card';

            const img = document.createElement('img');
            img.src = `${baseUrl}public/images/products/${parseInt(item.id, 10)}/product_220.jpg`;
            img.alt = item.title || 'Produit';
            img.className = 'item-img';
            img.onerror = function() { this.src = 'https://placehold.co/80x80/f5f5f5/111?text=Img'; };

            const detailsDiv = document.createElement('div');
            detailsDiv.className = 'item-details';

            const h4 = document.createElement('h4');
            h4.className = 'item-title';
            h4.textContent = item.title; 

            const priceDiv = document.createElement('div');
            priceDiv.className = 'item-price';
            priceDiv.textContent = `${price} €`;

            const controlsDiv = document.createElement('div');
            controlsDiv.className = 'item-controls';

            const qtyWrapper = document.createElement('div');
            qtyWrapper.className = 'qty-wrapper';

            // SÉCURITÉ : Création sécurisée des éléments pour éviter l'injection DOM
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
        
        if (sidebarTotal) {
            sidebarTotal.textContent = parseFloat(totalPrice).toFixed(2) + ' €';
        }
    }

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
                
                // SÉCURITÉ CRITIQUE : Envoi du jeton CSRF lu depuis le HTML
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch(`${baseUrl}Cart/addToCart/${productId}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                if (response.ok) {
                    const cartData = await response.json();
                    
                    // Si l'ajout échoue (ex: CSRF expiré)
                    if (cartData.status === 'error') {
                        showHomeToast(cartData.message, 'danger');
                        return;
                    }

                    const cartItems = cartData[0] || [];
                    const totalPrice = cartData[1] || 0;
                    
                    let totalCount = 0;
                    cartItems.forEach(item => totalCount += parseInt(item.quantity || 1, 10));
                    
                    const badge = document.getElementById('navCartCounterBadge');
                    if (badge) {
                        badge.textContent = totalCount;
                        badge.style.transform = "scale(1.5)";
                        setTimeout(() => { badge.style.transform = "scale(1)"; }, 300);
                    }
                    
                    updateCartSidebar(cartItems, totalPrice);
                    showHomeToast("Produit ajouté au panier !");
                } else {
                    showHomeToast("Erreur lors de l'ajout.", "danger");
                }
            } catch (error) {
                console.error("Erreur d'ajout au panier :", error);
                showHomeToast("Erreur de connexion au serveur.", "danger");
            } finally {
                btnAdd.innerHTML = originalIcon;
                btnAdd.disabled = false;
            }
        }
    });

});