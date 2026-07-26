</main> 
</div> 

    <script src="<?= URL ?>public/assets/js/admin.js" defer></script>

    <script src="<?= URL ?>public/assets/js/admin_layout.js" defer></script>
    
    <?php if (!empty($activeMenu)): ?>
        <?php 
            // SÉCURITÉ : Assainissement du nom de fichier pour éviter les attaques de type Path Traversal
            $safeMenu = basename($activeMenu);
            $scriptPath = 'public/assets/js/admin_' . $safeMenu . '.js';
            
            if (file_exists($scriptPath)): 
        ?>
            <script src="<?= URL ?><?= htmlspecialchars($scriptPath, ENT_QUOTES, 'UTF-8') ?>" defer></script>
        <?php endif; ?>
    <?php endif; ?>
    
</body>
</html>