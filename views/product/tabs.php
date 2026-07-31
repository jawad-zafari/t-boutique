<?php
/**
 * Vue des Onglets du Produit (tabs.php)
 * Gère l'affichage dynamique des évaluations d'experts, spécifications, avis clients et FAQ.
 */

// Récupération sécurisée avec compatibilité des clés transmises par le contrôleur
$reviews = $data['reviews'] ?? $data['expertReviews'] ?? []; 
$specs = $data['specs'] ?? $data['specifications'] ?? []; 
$comments = $data['comments'] ?? []; 
$paramNames = $data['comment_params'] ?? $data['commentParamNames'] ?? []; 
$paramScores = $data['comment_scores'] ?? $data['commentParamScores'] ?? [];
$questions = $data['questions'] ?? []; 
$answers = $data['answers'] ?? [];

$productInfo = $data['productInfo'] ?? [];
$productId = (int)($productInfo['id'] ?? 0);
$activeTab = $data['activeTab'] ?? 'reviews';
?>

<div class="product-tabs-wrapper">
    
    <nav class="tabs-nav" role="tablist" aria-label="Informations détaillées du produit">
        <button type="button" role="tab" aria-selected="<?= $activeTab === 'expert' ? 'true' : 'false' ?>" aria-controls="tab-expert" id="btn-tab-expert" class="btn-tab <?= $activeTab === 'expert' ? 'active' : '' ?>" data-target="tab-expert">
            <i class="fa-solid fa-pen-nib" aria-hidden="true"></i> Évaluations d'experts
        </button>
        <button type="button" role="tab" aria-selected="<?= $activeTab === 'specs' ? 'true' : 'false' ?>" aria-controls="tab-specs" id="btn-tab-specs" class="btn-tab <?= $activeTab === 'specs' ? 'active' : '' ?>" data-target="tab-specs">
            <i class="fa-solid fa-list-check" aria-hidden="true"></i> Spécifications techniques
        </button>
        <button type="button" role="tab" aria-selected="<?= $activeTab === 'reviews' ? 'true' : 'false' ?>" aria-controls="tab-reviews" id="btn-tab-reviews" class="btn-tab <?= $activeTab === 'reviews' ? 'active' : '' ?>" data-target="tab-reviews">
            <i class="fa-solid fa-comments" aria-hidden="true"></i> Avis
        </button>
        <button type="button" role="tab" aria-selected="<?= $activeTab === 'qa' ? 'true' : 'false' ?>" aria-controls="tab-qa" id="btn-tab-qa" class="btn-tab <?= $activeTab === 'qa' ? 'active' : '' ?>" data-target="tab-qa">
            <i class="fa-solid fa-circle-question" aria-hidden="true"></i> Questions & Réponses
        </button>
    </nav>

    <div class="tab-content-wrapper">
        
        <div id="tab-expert" class="tab-pane <?= $activeTab === 'expert' ? 'active' : '' ?>" role="tabpanel" aria-labelledby="btn-tab-expert">
            <div class="expert-reviews-container">
                <?php if (!empty($reviews)): foreach ($reviews as $rev): ?>
                    <div class="expert-review-card">
                        <h4><?= htmlspecialchars($rev['title'] ?? 'Avis Expert', ENT_QUOTES, 'UTF-8') ?></h4>
                        <p><?= htmlspecialchars($rev['description'] ?? $rev['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php endforeach; else: ?>
                    <p class="empty-text">Aucune évaluation d'expert disponible pour ce produit.</p>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-specs" class="tab-pane <?= $activeTab === 'specs' ? 'active' : '' ?>" role="tabpanel" aria-labelledby="btn-tab-specs">
            <div class="specs-table-wrapper">
                <?php if (!empty($specs)): ?>
                    <table class="specs-table" aria-label="Caractéristiques techniques du produit">
                        <tbody>
                            <?php foreach ($specs as $spec): ?>
                                <tr>
                                    <th scope="row"><?= htmlspecialchars($spec['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></th>
                                    <td><?= htmlspecialchars($spec['value'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="empty-text">Aucune spécification technique disponible.</p>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-reviews" class="tab-pane <?= $activeTab === 'reviews' ? 'active' : '' ?>" role="tabpanel" aria-labelledby="btn-tab-reviews">
            <div class="reviews-section-grid">
                
                <div class="reviews-summary-scores">
                    <h5>Critères d'évaluation</h5>
                    <?php if (!empty($paramNames)): foreach ($paramNames as $pId => $pName): 
                        // SÉCURITÉ : Forçage du typage pour le calcul de la barre de progression
                        $score = isset($paramScores[$pId]) ? (float)$paramScores[$pId] : 3.0;
                    ?>
                        <div class="score-row-item">
                            <span><?= htmlspecialchars(is_array($pName) ? ($pName['title'] ?? '') : $pName, ENT_QUOTES, 'UTF-8') ?></span>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" style="width: <?= min(100, max(0, ($score / 5) * 100)) ?>%"></div>
                            </div>
                            <span><?= number_format($score, 1) ?>/5</span>
                        </div>
                    <?php endforeach; endif; ?>
                    
                    <div class="add-review-shortcut-box">
                        <a href="<?= URL ?>AddComment/index/<?= $productId ?>" class="btn-action-primary">Laisser un avis</a>
                    </div>
                </div>

                <div class="comments-list">
                    <?php if (!empty($comments)): foreach ($comments as $com): ?>
                        <div class="comment-item-card">
                            <div class="comment-header-flex">
                                <strong><?= htmlspecialchars($com['title'] ?? 'Avis Client', ENT_QUOTES, 'UTF-8') ?></strong>
                                <span class="comment-date"><?= htmlspecialchars($com['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <p class="comment-body-text"><?= htmlspecialchars($com['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            
                            <?php if (!empty($com['positive_points'])): ?>
                                <div class="points-block positive">
                                    <i class="fa-solid fa-plus" aria-hidden="true"></i> <?= htmlspecialchars($com['positive_points'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($com['negative_points'])): ?>
                                <div class="points-block negative">
                                    <i class="fa-solid fa-minus" aria-hidden="true"></i> <?= htmlspecialchars($com['negative_points'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="empty-text">Aucun avis client pour le moment.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div id="tab-qa" class="tab-pane <?= $activeTab === 'qa' ? 'active' : '' ?>" role="tabpanel" aria-labelledby="btn-tab-qa">
            <div class="qa-section">
                
                <div class="qa-form-box">
                    <h4><i class="fa-solid fa-comment-dots" aria-hidden="true"></i> Poser une question</h4>
                    <form id="formQuestion" method="post">
                        <label for="questionText" class="sr-only">Votre question :</label>
                        <textarea id="questionText" name="question" class="form-control" rows="3" placeholder="Tapez votre question ici..." required aria-required="true"></textarea>
                        
                        <button type="button" id="btnSubmitQuestion" data-id="<?= $productId ?>" class="btn-action-outline margin-top-md">
                            <i class="fa-solid fa-paper-plane" aria-hidden="true"></i> Soumettre la question
                        </button>
                    </form>
                </div>

                <div class="qa-list">
                    <?php if (!empty($questions)): foreach ($questions as $q): ?>
                        <div class="qa-card">
                            <div class="qa-question">
                                <i class="fa-solid fa-circle-question" aria-hidden="true"></i> <strong>Question :</strong>
                                <p><?= htmlspecialchars($q['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <?php if (isset($answers[$q['id']])): ?>
                                <div class="qa-answer">
                                    <i class="fa-solid fa-reply" aria-hidden="true"></i> <strong>Réponse :</strong>
                                    <p><?= htmlspecialchars($answers[$q['id']]['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="empty-text">Aucune question posée pour l'instant.</p>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>

    </div>
</div>