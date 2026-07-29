<?php

/**
 * Modèle ModelAdminQuestion
 * Gère les requêtes BDD des questions, sécurisées contre les injections SQL et les failles Stored XSS.
 */
class ModelAdminQuestion extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getQuestions()
    {
        $sql = "SELECT * FROM questions WHERE parent_id = 0 ORDER BY id DESC";
        $questions = $this->doSelect($sql);

        if (is_array($questions)) {
            foreach ($questions as $key => $q) {
                $sqlAnswer = "SELECT * FROM questions WHERE parent_id = ?";
                $answer = $this->doSelect($sqlAnswer, [(int)$q['id']], true);
                $questions[$key]['admin_answer'] = $answer ? $answer['content'] : '';
            }
        }
        return $questions;
    }

    public function confirm($data)
    {
        if (empty($data['id'])) return;

        foreach ($data['id'] as $id) {
            $safeId = (int)$id;
            
            // SÉCURITÉ CRITIQUE : Utilisation de htmlspecialchars pour une protection robuste contre le XSS stocké
            $questionText = htmlspecialchars(trim($data['question_' . $safeId] ?? ''), ENT_QUOTES, 'UTF-8');
            $answerText = htmlspecialchars(trim($data['answer_' . $safeId] ?? ''), ENT_QUOTES, 'UTF-8');

            // 1. Mettre à jour la question de l'utilisateur
            $this->doQuery("UPDATE questions SET content = ?, is_approved = 1 WHERE id = ?", [$questionText, $safeId]);

            // 2. Gérer la réponse de l'administrateur
            if (!empty($answerText)) {
                $sqlCheck = "SELECT id FROM questions WHERE parent_id = ?";
                $exists = $this->doSelect($sqlCheck, [$safeId]);

                if (!empty($exists)) {
                    // Mettre à jour la réponse existante
                    $this->doQuery("UPDATE questions SET content = ?, is_approved = 1 WHERE id = ?", [$answerText, $exists[0]['id']]);
                } else {
                    // Insérer une nouvelle réponse
                    $qInfo = $this->doSelect("SELECT product_id FROM questions WHERE id = ?", [$safeId], true);
                    $productId = (int)($qInfo['product_id'] ?? 0);
                    $createdAt = date('Y-m-d H:i:s');
                    
                    Model::sessionInit();
                    $adminId = (int)(Model::sessionGet('userId') ?? 1);

                    $this->doQuery("INSERT INTO questions (content, parent_id, product_id, user_id, created_at, is_approved) VALUES (?, ?, ?, ?, ?, 1)", [$answerText, $safeId, $productId, $adminId, $createdAt]);
                }
            }
        }
    }

    public function unconfirm($ids)
    {
        if (empty($ids)) return;
        
        // SÉCURITÉ CRITIQUE : Protection de la clause IN() avec intval pour bloquer l'injection SQL
        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $this->doQuery("UPDATE questions SET is_approved = 0 WHERE id IN ($idsString) OR parent_id IN ($idsString)");
    }

    public function delete($ids)
    {
        if (empty($ids)) return;
        
        // SÉCURITÉ CRITIQUE : Protection de la clause IN() avec intval
        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $this->doQuery("DELETE FROM questions WHERE id IN ($idsString) OR parent_id IN ($idsString)");
    }
}
?>