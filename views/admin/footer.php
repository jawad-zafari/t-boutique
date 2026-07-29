</main> 
</div> 

    <script src="<?= URL ?>public/assets/js/admin.js" defer></script>

    <script src="<?= URL ?>public/assets/js/admin_layout.js" defer></script>
    
    <?php if (!empty($activeMenu)): ?>
        <?php 
            // SÉCURITÉ CRITIQUE : Assainissement strict du nom de fichier pour empêcher les attaques de type Path Traversal
            $safeMenu = basename($activeMenu);
            $scriptPath = 'public/assets/js/admin_' . $safeMenu . '.js';
            
            // Ingestion dynamique du script JS uniquement s'il existe physiquement sur le serveur
            if (file_exists($scriptPath)): 
        ?>
            <script src="<?= URL ?><?= htmlspecialchars($scriptPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
        <?php endif; ?>
    <?php endif; ?>
    
</body>
</html>