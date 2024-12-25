<?php
// Reward.php - Reward and mission management
require_once 'config.php';

class Reward {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function createMissionTemplate($data) {
        try {
            $query = "INSERT INTO Mission_Templates 
                    (name, description, points, requirements) 
                    VALUES (:name, :description, :points, :requirements)";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":description", $data['description']);
            $stmt->bindParam(":points", $data['points']);
            $stmt->bindParam(":requirements", $data['requirements']);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Mission template created successfully',
                    'mission_id' => $this->conn->lastInsertId()
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to create mission template'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function assignMissionToUser($userId, $missionId) {
        try {
            // Get mission template details
            $mission = $this->getMissionTemplate($missionId);
            if(!$mission) {
                return ['status' => 'error', 'message' => 'Mission template not found'];
            }

            $query = "INSERT INTO Rewards 
                    (user_id, mission_id, mission_name, status, points_earned) 
                    VALUES (:user_id, :mission_id, :mission_name, 'pending', :points)";

            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":mission_id", $missionId);
            $stmt->bindParam(":mission_name", $mission['name']);
            $stmt->bindParam(":points", $mission['points']);

            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Mission assigned successfully'
                ];
            }
            return ['status' => 'error', 'message' => 'Failed to assign mission'];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function completeMission($userId, $rewardId) {
        try {
            $this->conn->beginTransaction();

            // Update reward status
            $query = "UPDATE Rewards 
                     SET status = 'completed' 
                     WHERE reward_id = :reward_id AND user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":reward_id", $rewardId);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            // Get points earned
            $points = $this->getRewardPoints($rewardId);

            // Update user points
            $query = "UPDATE Users 
                     SET points = points + :points 
                     WHERE user_id = :user_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":points", $points);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            $this->conn->commit();
            return ['status' => 'success', 'message' => 'Mission completed successfully'];
        } catch(PDOException $e) {
            $this->conn->rollBack();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getUserMissions($userId) {
        try {
            $query = "SELECT r.*, m.description, m.requirements 
                     FROM Rewards r 
                     LEFT JOIN Mission_Templates m ON r.mission_id = m.mission_id 
                     WHERE r.user_id = :user_id 
                     ORDER BY r.created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            return [
                'status' => 'success',
                'missions' => $stmt->fetchAll()
            ];
        } catch(PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    private function getMissionTemplate($missionId) {
        $query = "SELECT * FROM Mission_Templates WHERE mission_id = :mission_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":mission_id", $missionId);
        $stmt->execute();
        return $stmt->fetch();
    }

    private function getRewardPoints($rewardId) {
        $query = "SELECT points_earned FROM Rewards WHERE reward_id = :reward_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":reward_id", $rewardId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['points_earned'] ?? 0;
    }
}