/**
 * Logique JavaScript pour le Header, Carrousel et Recherche
 * Code écrit en Vanilla JS - Clean Code et Sécurisé (Anti-XSS)
 */
document.addEventListener("DOMContentLoaded", () => {

    // Récupération dynamique de l'URL de base pour éviter les erreurs de routage (404)
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // --- 1. GESTION DU MENU DE NAVIGATION SUR MOBILE ---
    const btnToggleNav = document.getElementById('btnToggleNav');
    const mainNavigation = document.getElementById('mainNavigation');

    if (btnToggleNav && mainNavigation) {
        btnToggleNav.addEventListener('click', function() {
            mainNavigation.classList.toggle('active');
            
            const icon = this.querySelector('i');
            if (icon) {
                if (mainNavigation.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-xmark');
                } else {
                    icon.classList.remove('fa-xmark');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }

    // --- 2. LOGIQUE DU DEFILEMENT DU CARROUSEL (DESKTOP) ---
    const scrollContainer = document.getElementById('navScrollContainer');
    const btnNext = document.getElementById('btnNavNext');
    const btnPrev = document.getElementById('btnNavPrev');

    if (scrollContainer && btnNext && btnPrev) {
        const scrollStep = 250;

        btnNext.addEventListener('click', () => {
            scrollContainer.scrollLeft += scrollStep;
        });

        btnPrev.addEventListener('click', () => {
            scrollContainer.scrollLeft -= scrollStep;
        });
    }

    // --- 3. GESTION DE LA RECHERCHE EN DIRECT (AUTOCOMPLETE) SÉCURISÉE ---
    const headerKeyword = document.getElementById('headerKeyword');
    const headerAutoSuggest = document.getElementById('headerAutoSuggest');
    let headerTypingTimer;

    if (headerKeyword && headerAutoSuggest) {
        
        headerKeyword.addEventListener('input', function() {
            clearTimeout(headerTypingTimer);
            const keyword = this.value.trim();
            
            if (keyword.length >= 2) {
                // Attendre 400ms avant d'envoyer la requête
                headerTypingTimer = setTimeout(() => fetchHeaderSuggestions(keyword), 400);
            } else {
                headerAutoSuggest.style.display = 'none';
                headerAutoSuggest.innerHTML = '';
            }
        });

        // Fermer la liste si l'utilisateur clique ailleurs
        document.addEventListener('click', (e) => {
            if (!headerKeyword.contains(e.target) && !headerAutoSuggest.contains(e.target)) {
                headerAutoSuggest.style.display = 'none';
            }
        });
    }

    async function fetchHeaderSuggestions(keyword) {
        const params = new URLSearchParams();
        params.append('keyword', keyword);

        try {
            // SÉCURITÉ : Utilisation de l'URL de base dynamique
            const response = await fetch(`${baseUrl}Search/autoSuggest`, {
                method: 'POST',
                body: params
            });
            const products = await response.json();
            
            headerAutoSuggest.innerHTML = '';
            
            if (products.length > 0) {
                products.forEach(product => {
                    const price = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(product.price);
                    
                    // SÉCURITÉ (Anti-XSS) : Création des nœuds DOM manuellement (Pas de innerHTML)
                    const li = document.createElement('li');
                    
                    const img = document.createElement('img');
                    img.src = `${baseUrl}public/images/products/${product.id}/product_220.jpg`;
                    img.className = 'suggest-img';
                    img.alt = product.title;
                    img.onerror = function() { this.src = 'https://placehold.co/50x50/f1f3f5/3b5bdb?text=Img'; };
                    
                    const detailsDiv = document.createElement('div');
                    detailsDiv.className = 'suggest-details';
                    
                    const titleSpan = document.createElement('span');
                    titleSpan.className = 'suggest-title';
                    // textContent bloque toute balise HTML malveillante
                    titleSpan.textContent = product.title; 
                    
                    const priceSpan = document.createElement('span');
                    priceSpan.className = 'suggest-price';
                    priceSpan.textContent = `${price} €`;
                    
                    detailsDiv.appendChild(titleSpan);
                    detailsDiv.appendChild(priceSpan);
                    
                    li.appendChild(img);
                    li.appendChild(detailsDiv);
                    
                    // Redirection directe vers la page du produit lors du clic
                    li.addEventListener('click', () => {
                        window.location.href = `${baseUrl}Product/index/${product.id}`;
                    });
                    
                    headerAutoSuggest.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.className = 'empty-suggest';
                
                const span = document.createElement('span');
                span.textContent = "Aucun produit trouvé.";
                
                li.appendChild(span);
                headerAutoSuggest.appendChild(li);
            }
            
            headerAutoSuggest.style.display = 'block';
        } catch (error) {
            console.error("Erreur de recherche en direct :", error);
        }
    }

});