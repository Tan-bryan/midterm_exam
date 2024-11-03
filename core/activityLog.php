<?php
class ActivityLog {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function logAction($userId, $username, $action) {
        $stmt = $this->pdo->prepare('INSERT INTO activity_log (user_id, username, action) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $username, $action]);
    }

    public function getLogs($userId) {
        $stmt = $this->pdo->prepare('SELECT * FROM activity_log WHERE user_id = ? ORDER BY timestamp DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
