/**
 * Logique JavaScript pour la gestion des produits, galeries et critiques
 * Architecture Vanilla JS propre - Réutilisation intelligente du code (Principe DRY)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Garde de sécurité : Empêche les liaisons d'événements multiples
    if (window.adminProductEventsBound) return;
    window.adminProductEventsBound = true;

    // Utilisation des fonctions globales de admin.js
    const showToast = window.showGlobalAdminToast || alert;
    const showConfirm = window.showGlobalAdminConfirm || ((msg, cb) => { if(confirm(msg)) cb(); });

    // =========================================================================
    // 1. SYSTÈME DYNAMIQUE DE SUPPRESSION (PRODUITS, GALERIE, CRITIQUES)
    // =========================================================================
    // Un seul bloc de code (DRY) gère les 3 types de suppressions de ce module !
    const deleteActions = [
        { btnId: 'btnDeleteProducts', formId: 'formProductsSelection' },
        { btnId: 'btnDeleteGallery', formId: 'formGallerySelection' },
        { btnId: 'btnDeleteReview', formId: 'formReviewsSelection' }
    ];

    deleteActions.forEach(action => {
        const btn = document.getElementById(action.btnId);
        const form = document.getElementById(action.formId);

        if (btn && form) {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const checkedBoxes = form.querySelectorAll('.row-checkbox:checked');
                
                if (checkedBoxes.length === 0) {
                    showToast("Veuillez sélectionner au moins un élément pour cette action.", "danger");
                    return;
                }

                showConfirm("Êtes-vous sûr de vouloir supprimer définitivement la sélection ?", () => {
                    form.submit();
                });
            });
        }
    });

    // =========================================================================
    // 2. GESTION DES TAGS (COULEURS ET GARANTIES) - SÉCURISÉ ANTI-XSS
    // =========================================================================
    function setupTagSelection(selectId, containerId, inputName) {
        const selectEl = document.getElementById(selectId);
        const containerEl = document.getElementById(containerId);
        
        if (selectEl && containerEl) {
            selectEl.addEventListener('change', function() {
                const value = this.value;
                const title = this.options[this.selectedIndex].getAttribute('data-title');
                
                if (value !== "0" && value !== "") {
                    const existing = containerEl.querySelector(`input[value="${value}"]`);
                    if (existing) {
                        showToast("Cet élément est déjà ajouté.", "danger");
                        this.selectedIndex = 0;
                        return;
                    }

                    // Création sécurisée des éléments DOM
                    const span = document.createElement('span');
                    span.className = 'tag-item';
                    span.textContent = title + " "; 

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = inputName;
                    input.value = value;

                    const icon = document.createElement('i');
                    icon.className = 'fa-solid fa-circle-xmark btn-remove-tag';
                    icon.setAttribute('aria-hidden', 'true');
                    icon.title = 'Retirer cet élément';

                    span.appendChild(input);
                    span.appendChild(icon);
                    containerEl.appendChild(span);
                    
                    this.selectedIndex = 0; 
                }
            });
        }
    }

    setupTagSelection('colorSelect', 'colorsContainer', 'color[]');
    setupTagSelection('garanteeSelect', 'garanteesContainer', 'garantee[]');

    // Suppression d'un tag (Event Delegation)
    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('btn-remove-tag')) {
            const tagItem = event.target.closest('.tag-item');
            if (tagItem) tagItem.remove();
        }
    });

    // =========================================================================
    // 3. SÉLECTIONNER / DÉSÉLECTIONNER TOUT
    // =========================================================================
    const selectAllCheckboxes = document.getElementById("selectAllCheckboxes");
    if (selectAllCheckboxes) {
        selectAllCheckboxes.addEventListener("change", (e) => {
            const isChecked = e.target.checked;
            const rowCheckboxes = document.querySelectorAll(".row-checkbox");
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

});