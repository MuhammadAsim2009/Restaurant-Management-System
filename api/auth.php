<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/response.php';

$action = $_REQUEST['action'] ?? '';

switch($action) {
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            unset($user['password_hash']);
            jsonResponse(true, $user, 'Login successful');
        } else {
            jsonResponse(false, null, 'Invalid credentials', 401);
        }
        break;

    case 'register':
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $name = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';
        
        if(!$name || !$email || !$password) {
            jsonResponse(false, null, 'Missing required fields', 400);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hash]);
            jsonResponse(true, null, 'Registration successful. Please login.');
        } catch(PDOException $e) {
            jsonResponse(false, null, 'Email already exists', 400);
        }
        break;

    case 'logout':
        session_destroy();
        jsonResponse(true, null, 'Logged out');
        break;

    case 'me':
        if (isLoggedIn()) {
            $stmt = $pdo->prepare("SELECT id, name, email, role, phone FROM users WHERE id = ?");
            $stmt->execute([getCurrentUserId()]);
            $user = $stmt->fetch();
            jsonResponse(true, $user);
        } else {
            jsonResponse(false, null, 'Not logged in', 401);
        }
        break;

    default:
        jsonResponse(false, null, 'Invalid action', 400);
}
