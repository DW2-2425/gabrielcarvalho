<?php
namespace Model;

use Services\JwtService;
use Database\Database;

class User {

    // Cria um novo utilizador e retorna o token de ativação
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

       // Verifica se o username ou email já existem na base de dados 
    public function checkExists(string $username, string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch() !== false;
    }

        // Ativa a conta do utilizador com base no token de ativação
    public function activateAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // Recupera a conta do utilizador com base no token de ativação
    public function recoveryAccount(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ? AND is_active = 0");
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    // Verifica as credenciais do utilizador (username e password) e retorna os dados do utilizador se forem válidas
    public function verifyCredentials(string $username, string $password) {
        $db = Database::getConnection(); 
        $stmt = $db->prepare("SELECT id, username, email, password, is_active FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        $getPasswordHash = $this->getPasswordHash($username);

        if ($user && password_verify($password, $getPasswordHash)) {
            return $user;
        }
        return false;
    }

    // Obtém os utilizadores com username que começa com "Bot_"
    public static function getBotUser() {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username FROM users WHERE username LIKE 'Bot_%' ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtém os dados do utilizador pelo ID
    public function findById(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, avatar, bio, discord, steam, instagram, games_played, games_won, created_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Obtém o último ID inserido na tabela users
    public function getLastInsertedId() {
        $db = Database::getConnection();
        return $db->lastInsertId();
    }

    // Atualiza a password do utilizador
    public function updatePassword(int $id, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    // Atualiza as informações do perfil do utilizador
    public function updateProfileInfo(int $id, string $avatar, string $bio, string $discord, string $steam, string $instagram) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET avatar = ?, bio = ?, discord = ?, steam = ?, instagram = ? WHERE id = ?");
        return $stmt->execute([$avatar, $bio, $discord, $steam, $instagram, $id]);
    }

    // Obtém os dados do utilizador pelo username
    public function findByUsername(string $username) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, username, email, avatar, bio, discord, steam, instagram, games_played, games_won, created_at FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // Obtém os dados do utilizador pelo email
    public function findByEmail(string $email) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Obtém o token JWT da API para o utilizador, se as credenciais forem válidas
    public function getJwtTokenForUser(string $email, string $password) {
        return JwtService::getToken($email, $password);
    }

        // Obtém o hash da password do utilizador pelo username
    public function getPasswordHash(string $username) { 
        $db = Database::getConnection(); // Obtém a conexão com a base de dados usando a classe Database
        $stmt = $db->prepare("SELECT password FROM users WHERE username = ?");
        // Prepara uma declaração SQL para selecionar o hash da password do utilizador com base no username fornecido
        $stmt->execute([$username]);
        // Executa a declaração SQL com o username fornecido como parâmetro
        $user = $stmt->fetch();
        // Retorna o hash da password se o utilizador for encontrado, caso contrário retorna null
        return $user ? $user['password'] : null;
    }


        // Deleta a conta do utilizador pelo ID
    public function deleteAccount(int $id) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Define o token de recuperação para o utilizador com base no ID
    public function setRecoveryToken(int $userId, string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE users SET recovery_token = ? WHERE id = ?");
        $token = $stmt->execute([$token, $userId]);
        return $token;
    }

    // Obtém os dados do utilizador pelo token de recuperação
    public function findByRecoveryToken(string $token) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE recovery_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    // Reseta a password do utilizador com base no token de recuperação
    public function resetPasswordWithToken(string $token, string $newPassword) {
        $db = Database::getConnection();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ?, recovery_token = NULL WHERE recovery_token = ?");
        return $stmt->execute([$hash, $token]);
    }

}