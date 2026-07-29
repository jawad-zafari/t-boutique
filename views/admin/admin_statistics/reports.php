<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-chart-line" aria-hidden="true"></i> Statistiques et Rapports
        </div>
    </header>

    <form action="<?= URL ?>AdminStat/orderStatistics" method="post" id="formStatistics">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="stats-filter-card form-box-wide mx-auto">
            
            <div class="form-row-half">
                
                <div class="filter-section">
                    <h4><i class="fa-solid fa-calendar-days" aria-hidden="true"></i> Date de début</h4>
                    <div class="date-selectors-grid">
                        <div class="form-group m-0">
                            <label for="startDay" class="sr-only">Jour de début</label>
                            <select id="startDay" name="day1" class="form-control" aria-label="Sélectionner le jour de début">
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group m-0">
                            <label for="startMonth" class="sr-only">Mois de début</label>
                            <select id="startMonth" name="month1" class="form-control" aria-label="Sélectionner le mois de début">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group m-0">
                            <label for="startYear" class="sr-only">Année de début</label>
                            <select id="startYear" name="year1" class="form-control" aria-label="Sélectionner l'année de début">
                                <?php for ($i = 2020; $i <= $data['currentYear']; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="filter-section">
                    <h4><i class="fa-solid fa-calendar-check" aria-hidden="true"></i> Date de fin</h4>
                    <div class="date-selectors-grid">
                        <div class="form-group m-0">
                            <label for="endDay" class="sr-only">Jour de fin</label>
                            <select id="endDay" name="day2" class="form-control" aria-label="Sélectionner le jour de fin">
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group m-0">
                            <label for="endMonth" class="sr-only">Mois de fin</label>
                            <select id="endMonth" name="month2" class="form-control" aria-label="Sélectionner le mois de fin">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= sprintf('%02d', $i) ?>"><?= sprintf('%02d', $i) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="form-group m-0">
                            <label for="endYear" class="sr-only">Année de fin</label>
                            <select id="endYear" name="year2" class="form-control" aria-label="Sélectionner l'année de fin">
                                <?php for ($i = 2020; $i <= $data['currentYear']; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <div class="mt-25 border-top-dashed" style="padding-top: 20px;">
                <button type="submit" class="btn-admin-submit btn-wide w-100" aria-label="Générer le rapport statistique">
                    <i class="fa-solid fa-file-invoice" aria-hidden="true"></i> Générer le rapport
                </button>
            </div>

        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_statistics.js" defer></script>