<?php
// includes/GameFunctionsNew.php
declare(strict_types=1);

class GameFunctionsNew {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getUserProgress(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    l.name, 
                    up.progress,
                    l.required_points,
                    up.created_at as achieved_at,
                    (up.progress >= l.required_points) as achieved
                FROM user_progress up
                JOIN levels l ON up.level_id = l.id
                WHERE up.user_id = :user_id
                ORDER BY up.created_at DESC
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [
                [
                    'name' => 'Débutant',
                    'progress' => 0,
                    'required_points' => 0,
                    'achieved_at' => date('Y-m-d H:i:s'),
                    'achieved' => 0
                ]
            ];
        } catch (PDOException $e) {
            error_log("getUserProgress error: " . $e->getMessage());
            return [];
        }
    }

    public function getCurrentLevel(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    l.name,
                    MAX(up.progress) as progress,
                    l.required_points
                FROM user_progress up
                JOIN levels l ON up.level_id = l.id
                WHERE up.user_id = :user_id
                GROUP BY l.name, l.required_points
                ORDER BY progress DESC
                LIMIT 1
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
                'name' => 'N/A',
                'progress' => 0,
                'required_points' => 0
            ];
        } catch (PDOException $e) {
            error_log("getCurrentLevel error: " . $e->getMessage());
            return [
                'name' => 'Erreur',
                'progress' => 0,
                'required_points' => 0
            ];
        }
    }

    public function getAnswerHistory(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    e.titre,
                    r.reponse_donnee,
                    r.resultat,
                    r.date
                FROM reponses r
                JOIN enigmes e ON r.enigme_id = e.id
                WHERE r.user_id = :user_id
                ORDER BY r.date DESC
                LIMIT 10
            ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("getAnswerHistory error: " . $e->getMessage());
            return [];
        }
        
    }
}