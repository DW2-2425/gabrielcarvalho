<?php
namespace Controller;

use Model\User;

class ProfileController {
    
    // Construtor: Protege as rotas desta classe, exigindo que o utilizador tenha sessão iniciada
    public function __construct() {

    }

    // Mostra a página de perfil
    public function show($success = null, $error = null) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }
        $profileId = $_SESSION['user_id'];
        $userModel = new User();
        $profileUser = $userModel->findById($profileId);

        if (!$profileUser) {
            die("Utilizador não encontrado.");
        }

        $isOwner = true; // O utilizador logado é o dono do perfil
        
        require_once __DIR__ . '/../../src/view/profile.php';
    }

    // Mostra a página de perfil público
    public function showPublic(string $username) {
        $userModel = new User();
        $profileUser = $userModel->findByUsername($username);

        if (!$profileUser) {
            http_response_code(404);
            die("<div style='text-align:center; padding: 50px; font-family: sans-serif;'>
                    <h2 style='color:#dc3545;'>404 - Utilizador não encontrado</h2>
                    <a href='/'>Voltar ao Lobby</a>
                </div>");
        }

        $isOwner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $profileUser['id']); // Verifica se o utilizador logado é o dono do perfil

        require_once __DIR__ . '/../../src/view/profile.php';
    }
    // Processa a atualização da password
    public function updatePassword() {
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($newPassword) || empty($confirmPassword)) {
            $error = "Preencha todos os campos da password.";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "As passwords não coincidem.";
        } else {
            $userModel = new User();
            if ($userModel->updatePassword($_SESSION['user_id'], $newPassword)) {
                $success = "Password atualizada com sucesso!";
            } else {
                $error = "Erro ao atualizar a password.";
            }
        }

        // Recarregar os dados atualizados para exibir a vista novamente
        $userModel = new User();
        $user = $userModel->findById($_SESSION['user_id']);
        require_once __DIR__ . '/../../src/view/profile.php';
    }

    public function handlePost() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit;
        }

        $formType = $_POST['form_type'] ?? '';

        if ($formType === 'details') {
            $this->updateDetails();
        } elseif ($formType === 'password') {
            $this->updatePassword();
        } else {
            $error = "Ação inválida.";
            $this->show(null, $error);
        }
    }

    private function updateDetails() {
        $bio = trim($_POST['bio'] ?? '');
        $discord = trim($_POST['discord'] ?? '');
        $steam = trim($_POST['steam'] ?? '');
        $instagram = trim($_POST['instagram'] ?? '');

        $userModel = new User();
        $currentUser = $userModel->findById($_SESSION['user_id']);
        $avatar = null;
        if (!empty($currentUser['avatar'])) {
            $avatar = $currentUser['avatar'];
        } else {
            $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($currentUser['username']) . '&background=198754&color=fff&size=150';
        }
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/img/avatars/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileTmpPath = $_FILES['avatar_file']['tmp_name'];
            $fileName = basename($_FILES['avatar_file']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                $newFileName = 'avatar_'. $_SESSION['user_id'] . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $avatar = '/img/avatars/' . $newFileName; // Atualiza o caminho do avatar para o novo arquivo
                } else {
                   return $this->show(null, "Erro ao fazer upload do avatar.");
                   
                }
            } else {
                return $this->show(null, "Formato de arquivo não permitido para o avatar.");
            }
        }
        if ($userModel->updateProfileInfo($_SESSION['user_id'], $avatar, $bio, $discord, $steam, $instagram)) {
            $this->show("Informações atualizadas com sucesso!");
        } else {
            $this->show(null, "Erro ao atualizar as informações do perfil.");
        }
    }
}