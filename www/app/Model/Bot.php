<?php

namespace Model;

use Database\Database;

Class Bot {

public function is_bot(int $userId): bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $username = $stmt->fetchColumn();
        $bot = stripos($username, 'bot') !== false;
        error_log("User ID $userId is " . ($bot ? "a bot" : "not a bot") . ". Username: $username");
        return $bot;
    }

}
