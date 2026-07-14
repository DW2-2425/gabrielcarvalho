<?php
namespace Model;

use Database\Database;

class Room {
    
    // Lista apenas as salas que ainda estão à espera de jogadores
    public function getWaitingRooms() {
        $db = Database::getConnection();
        /**Prepara uma declaração SQL para selecionar as salas que estão à espera de jogadores, 
        juntamente com o nome do dono e a contagem de jogadores */
        $stmt = $db->query("
            SELECT r.id, r.name, u.username as owner_name, 
            (SELECT COUNT(*) FROM room_players rp WHERE rp.room_id = r.id) as player_count 
            FROM rooms r 
            JOIN users u ON r.owner_id = u.id 
            WHERE r.status = 'Waiting'
        ");
        // Executa a declaração SQL e retorna todas as salas que estão à espera de jogadores

        $rooms = $stmt->fetchAll();

        if (!$rooms) {
            return [];
        }
        return $rooms;

        
    }

    // Cria a sala e insere automaticamente o dono como o 1º jogador
    public function createRoom(string $name, int $ownerId) {
        $db = Database::getConnection();
        try { // Inicia uma transação para garantir que ambas as operações (criação da sala e inserção do dono) sejam atômicas
            $db->beginTransaction(); 
            
            $stmt = $db->prepare("INSERT INTO rooms (name, owner_id, status) VALUES (?, ?, 'Waiting')");
            $stmt->execute([$name, $ownerId]);
            $roomId = $db->lastInsertId(); // Obtém o ID da sala recém-criada
            
            $stmt2 = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
            $stmt2->execute([$roomId, $ownerId]);
            
            $db->commit(); // Confirma a transação, garantindo que ambas as operações sejam aplicadas ao banco de dados
            return $roomId; // Retorna o ID da sala recém-criada
        } catch (\Exception $e) {
            $db->rollBack(); // Se ocorrer algum erro durante a transação, desfaz todas as operações realizadas até o momento
            return false;
        }
    }

    // Processa a entrada de um jogador na sala, garantindo que o limite é 4
    public function joinRoom(int $roomId, int $userId) {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT status, (SELECT COUNT(*) FROM room_players WHERE room_id = ?) as count FROM rooms WHERE id = ?");
        $stmt->execute([$roomId, $roomId]);
        $room = $stmt->fetch();

        if ($room && $room['status'] === 'Waiting' && $room['count'] < 4) {
            // Verifica se o jogador já está lá dentro
            $check = $db->prepare("SELECT 1 FROM room_players WHERE room_id = ? AND user_id = ?");
            $check->execute([$roomId, $userId]);
            
            if (!$check->fetch()) { // Se o jogador não estiver na sala, insere-o na tabela room_players
                $insert = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
                $insert->execute([$roomId, $userId]);
            }
            error_log("User $userId joined room $roomId.");
            return true;
        }
        error_log("Failed to join room $roomId for user $userId. Room status: " . ($room['status'] ?? 'N/A') . ", Player count: " . ($room['count'] ?? 'N/A'));
        return false;
    }

    // Traz os dados da sala e a lista de jogadores atuais
    public function getRoomDetails(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT r.*, u.username as owner_name FROM rooms r JOIN users u ON r.owner_id = u.id WHERE r.id = ?");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch();
        
        if ($room) {
            $stmt2 = $db->prepare("SELECT u.username FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ?");
            $stmt2->execute([$roomId]);
            $room['players'] = $stmt2->fetchAll(\PDO::FETCH_COLUMN);
        }
        error_log("Room details for room $roomId: " . json_encode($room));
        return $room;
    }

    public function getCurrentRoomId(int $userId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT room_id FROM room_players WHERE user_id = ?");
        $stmt->execute([$userId]);
        error_log("Current room ID for user $userId: " . $stmt->fetchColumn());
        return $stmt->fetchColumn();
    }

    public function getPlayerCount(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT COUNT(*) FROM room_players WHERE room_id = ?");
        $stmt->execute([$roomId]);
        error_log("Player count for room $roomId: " . $stmt->fetchColumn());
        return (int) $stmt->fetchColumn();
    }

    public function deleteRoom(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        error_log("Attempted to delete room $roomId. Rows affected: " . $stmt->rowCount());
        return $stmt->rowCount() > 0; // Retorna true se a sala foi deletada, false caso contrário
    }

    public function deleteIfEmpty(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE FROM rooms WHERE id = ? AND NOT EXISTS (SELECT 1 FROM room_players WHERE room_id = ?)");
        $stmt->execute([$roomId, $roomId]);
        error_log("Attempted to delete room $roomId if empty. Rows affected: " . $stmt->rowCount());
        return $stmt->rowCount() > 0; // Retorna true se a sala foi deletada, false caso contrário
    }

    public function removeBotFromRoom(int $roomId) {
        $db = Database::getConnection();
        $stmt = $db->prepare("DELETE rp FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ? AND u.username LIKE 'Bot_%'");
        $stmt->execute([$roomId]);
        return $stmt->rowCount() > 0; // Retorna true se algum bot foi removido, false caso contrário
    }

        public function insertBotIntoRoom(int $roomId) {
            $bot = User::getBotUser(); // Obtém a lista de bots disponíveis
            if (!$bot) {
                error_log("No bot user found to insert into room $roomId.");
                return false;
            } else {
                $botCount = count($bot); // Conta quantos bots existem

                while ($botCount < 3) { // Enquanto houver menos de 3 bots, insere mais bots na sala
                    foreach ($bot as $b) { // Itera sobre cada bot disponível
                        $this->joinRoom($roomId, $b['id']); // Insere o bot na sala
                        $botCount++; // Incrementa o contador de bots
                        if ($botCount >= 3) break; // Sai do loop se já houver 3 bots na sala
                    }

                    // Verifica novamente quantos bots existem na sala após a inserção
                    $stmt = Database::getConnection()->prepare("SELECT COUNT(*) FROM room_players rp JOIN users u ON rp.user_id = u.id WHERE rp.room_id = ? AND u.username LIKE 'Bot_%'");
                    $stmt->execute([$roomId]);
                    $botCount = (int) $stmt->fetchColumn(); // Atualiza o contador de bots com a contagem atual na sala
                } // Sai do loop quando houver 3 bots na sala
            }

            return true;
        }

    public function isOwner(int $roomId, int $userId) :bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM rooms WHERE id = ? AND owner_id = ?");
        $stmt->execute([$roomId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function isInRoom(int $roomId, int $userId) :bool {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT 1 FROM room_players WHERE room_id = ? AND user_id = ?");
        $stmt->execute([$roomId, $userId]);
        return (bool) $stmt->fetch();
    }

    public function leaveRoom(int $roomId, int $userId) {
    $db = Database::getConnection();
    
    // Descobre se quem está a tentar sair é o dono da sala
    $stmt = $db->prepare("SELECT owner_id FROM rooms WHERE id = ?");
    $stmt->execute([$roomId]);
    $room = $stmt->fetch();

    if ($room && $room['owner_id'] == $userId) {
        $del = $db->prepare("DELETE FROM rooms WHERE id = ? AND status = 'Waiting'");
        $del->execute([$roomId]);
    } else {
        $del = $db->prepare("DELETE FROM room_players WHERE room_id = ? AND user_id = ?");
        $del->execute([$roomId, $userId]);
    }
}

    public function createTrainingRoom(int $ownerId, string $ownerName) {
        $db = Database::getConnection();
        try {
            $db->beginTransaction();
            
            // 1. Garantir que os bots existem na base de dados
            $bots = ['Bot_Ze', 'Bot_Maria', 'Bot_Quim'];
            $botIds = [];
            
            foreach ($bots as $botName) {
                $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$botName]);
                $bot = $stmt->fetch();
                
                if (!$bot) {
                    $email = strtolower($botName) . '@jogosueca.pt';
                    $hash = password_hash('bot_123456', PASSWORD_DEFAULT);
                    $ins = $db->prepare("INSERT INTO users (username, email, password, is_active) VALUES (?, ?, ?, 1)");
                    $ins->execute([$botName, $email, $hash]);
                    $botIds[] = $db->lastInsertId();
                } else {
                    $botIds[] = $bot['id'];
                }
            }
            
            // 2. Criar a Sala de Treino
            $roomName = "Treino: " . $ownerName;
            $stmt = $db->prepare("INSERT INTO rooms (name, owner_id, status) VALUES (?, ?, 'Waiting')");
            $stmt->execute([$roomName, $ownerId]);
            $roomId = $db->lastInsertId();
            
            // 3. Inserir o Dono e os 3 Bots na sala
            $stmt2 = $db->prepare("INSERT INTO room_players (room_id, user_id) VALUES (?, ?)");
            $stmt2->execute([$roomId, $ownerId]); // Senta o Dono
            foreach ($botIds as $bId) {
                $stmt2->execute([$roomId, $bId]); // Senta os 3 Bots
            }
            
            $db->commit();
            return $roomId;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }
}