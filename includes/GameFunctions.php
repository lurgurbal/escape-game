<?php
// includes/GameFunctions.php
declare(strict_types=1);

class GameFunctions {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getCurrentEnigma(int $level): array {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM enigmes 
                WHERE niveau_requis = :level 
                ORDER BY RAND() 
                LIMIT 1
            ");
            $stmt->bindParam(':level', $level, PDO::PARAM_INT);
            $stmt->execute();
            
            $enigma = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$enigma) {
                throw new Exception("No enigma found for this level");
            }
            
            return $enigma;
        } catch (PDOException $e) {
            error_log("getCurrentEnigma error: " . $e->getMessage());
            return [
                'titre' => 'Error',
                'consigne' => 'An error occurred while loading the enigma',
                'reponse_attendue' => '',
                'niveau_requis' => 1
            ];
        }
    }

    public function checkAnswer(int $userId, int $enigmaId, string $answer): bool {
        try {
            // Get expected answer
            $stmt = $this->db->prepare("SELECT reponse_attendue FROM enigmes WHERE id = :id");
            $stmt->execute([':id' => $enigmaId]);
            $enigma = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$enigma) {
                return false;
            }

            $correct = (strtolower(trim($answer))) === strtolower(trim($enigma['reponse_attendue']));
            
            // Record attempt
            $stmt = $this->db->prepare("
                INSERT INTO reponses 
                (user_id, enigme_id, reponse_donnee, resultat, date) 
                VALUES (:user_id, :enigme_id, :reponse, :resultat, NOW())
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':enigme_id' => $enigmaId,
                ':reponse' => $answer,
                ':resultat' => $correct ? 1 : 0
            ]);

            // If correct, update progress
            if ($correct) {
                $this->updateUserProgress($userId, $enigmaId);
            }
            
            return $correct;
        } catch (PDOException $e) {
            error_log("checkAnswer error: " . $e->getMessage());
            return false;
        }
    }

    public function updateUserProgress(int $userId, int $enigmaId): bool {
        try {
            // Get points for this enigma
            $stmt = $this->db->prepare("SELECT points FROM enigmes WHERE id = ?");
            $stmt->execute([$enigmaId]);
            $points = $stmt->fetchColumn();
            
            if (!$points) {
                $points = 10; // Default value
            }

            // Add points to user
            $stmt = $this->db->prepare("
                UPDATE utilisateurs 
                SET total_points = total_points + ? 
                WHERE id = ?
            ");
            $stmt->execute([$points, $userId]);
            
            // Check for level up
            return $this->checkLevelUp($userId);
        } catch (PDOException $e) {
            error_log("updateUserProgress error: " . $e->getMessage());
            return false;
        }
    }

    public function checkLevelUp(int $userId): bool {
        try {
            $stmt = $this->db->prepare("SELECT total_points FROM utilisateurs WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) return false;

            $stmt = $this->db->prepare("SELECT MAX(id) FROM levels WHERE required_points <= ?");
            $stmt->execute([$user['total_points']]);
            $newLevel = $stmt->fetchColumn();

            if ($newLevel) {
                $stmt = $this->db->prepare("UPDATE utilisateurs SET current_level = ? WHERE id = ?");
                $stmt->execute([$newLevel, $userId]);
                
                // Record level achievement
                $stmt = $this->db->prepare("
                    INSERT INTO user_levels (user_id, level_id, achieved_at) 
                    VALUES (?, ?, NOW())
                    ON DUPLICATE KEY UPDATE achieved_at = NOW()
                ");
                $stmt->execute([$userId, $newLevel]);
                
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("checkLevelUp error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserProgress(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT l.id, l.name, l.required_points, u.total_points as progress,
                       ul.achieved_at,
                       CASE WHEN u.total_points >= l.required_points THEN 1 ELSE 0 END as achieved
                FROM levels l
                JOIN utilisateurs u ON u.id = ?
                LEFT JOIN user_levels ul ON ul.level_id = l.id AND ul.user_id = u.id
                ORDER BY l.required_points ASC
            ");
            $stmt->execute([$userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("getUserProgress error: " . $e->getMessage());
            return [];
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
                WHERE r.user_id = ?
                ORDER BY r.date DESC
                LIMIT 10
            ");
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("getAnswerHistory error: " . $e->getMessage());
            return [];
        }
    }

    public function getGlobalStats(): array {
        $stats = [];
        
        try {
            // Total users
            $stmt = $this->db->query("SELECT COUNT(*) as total_users FROM utilisateurs");
            $stats['total_users'] = (int)$stmt->fetchColumn();
            
            // Average level
            $stmt = $this->db->query("SELECT AVG(current_level) as avg_level FROM utilisateurs");
            $avgLevel = $stmt->fetchColumn();
            $stats['avg_level'] = round((float)$avgLevel, 2);
            
            // Total answers
            $stmt = $this->db->query("SELECT COUNT(*) as total_answers FROM reponses");
            $stats['total_answers'] = (int)$stmt->fetchColumn();
            
            // Success rate
            $stmt = $this->db->query("
                SELECT (SUM(resultat) / COUNT(*)) * 100 as success_rate 
                FROM reponses
            ");
            $stats['success_rate'] = round((float)$stmt->fetchColumn(), 2);
            
        } catch (PDOException $e) {
            error_log("getGlobalStats error: " . $e->getMessage());
        }
        
        return $stats;
    }

    public function getCurrentLevel(int $userId): array {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    l.name,
                    u.total_points as progress,
                    (SELECT required_points FROM levels WHERE id = l.id + 1) as required_points
                FROM utilisateurs u
                JOIN levels l ON u.current_level = l.id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            
            $level = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $level ?: [
                'name' => 'Débutant',
                'progress' => 0,
                'required_points' => 100
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

    public function getAllLevels(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM levels ORDER BY required_points ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("getAllLevels error: " . $e->getMessage());
            return [];
        }
    }

    public function addLevel(string $name, int $requiredPoints, string $description = ''): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO levels (name, required_points, description) 
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$name, $requiredPoints, $description]);
        } catch (PDOException $e) {
            error_log("addLevel error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteLevel(int $levelId): bool {
        try {
            // First check if any users have this level
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM utilisateurs 
                WHERE current_level = ?
            ");
            $stmt->execute([$levelId]);
            
            if ($stmt->fetchColumn() > 0) {
                throw new Exception("Cannot delete level - users have this level assigned");
            }
            
            $stmt = $this->db->prepare("DELETE FROM levels WHERE id = ?");
            return $stmt->execute([$levelId]);
        } catch (PDOException $e) {
            error_log("deleteLevel error: " . $e->getMessage());
            return false;
        }
    }
}