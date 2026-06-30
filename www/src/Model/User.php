<?php
namespace Model;

class User {
    public function create(string $username, string $email, string $password) {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(16)); 
        $stmt = $db->prepare("INSERT INTO users (username, email, password, activation_token, is_active) VALUES (?, ?, ?, ?, 0)");
        
        if ($stmt->execute([$username, $email, $hash, $token])) {
            return $token;
        }
        return false;
    }

    public function checkExists(string $username, string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

    public function activateAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // [ATUALIZADO] Verifica credenciais para o Login e traz também o email
    public function verifyCredentials(string $username, string $password) {
        $db = Database::getConnection();
        // Adicionado 'email' ao SELECT para podermos enviar à API
        $stmt = $db->prepare("SELECT id, username, email, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}