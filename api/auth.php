<?php
// Ensure the response is always JSON
header('Content-Type: application/json');

// Include necessary files
// Assuming includes/db.php establishes a PDO connection in $pdo with attributes to throw exceptions
// Assuming includes/session.php starts the session (session_start()) with secure cookie settings
// Assuming includes/response.php defines json_success() and json_error() functions
require_once '../includes/db.php';
require_once '../includes/session.php';
require_once '../includes/response.php';

// Helper bridges since response.php only defines jsonResponse
function json_success($message, $data = null) {
    jsonResponse(true, $data, $message, 200);
}
function json_error($message, $statusCode = 400) {
    jsonResponse(false, null, $message, $statusCode);
}

// Accept JSON payload from fetch/axios
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// If the payload isn't JSON, fallback to standard form data
if (!$data) {
    $data = $_POST;
}

// Determine the action to be performed
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'register':
        handleRegister($pdo, $data);
        break;
        
    case 'login':
        handleLogin($pdo, $data);
        break;
        
    case 'me':
        handleMe();
        break;
        
    case 'logout':
        handleLogout();
        break;

    default:
        json_error("Invalid action.", 400);
        break;
}

/**
 * Handle user registration
 * Validates inputs, creates a user, and returns user data (without password).
 */
function handleRegister($pdo, $data) {
    // 1. Sanitize and retrieve inputs
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';
    $phone = trim($data['phone'] ?? ''); // Optional field

    // 2. Validate required fields
    if (empty($name) || empty($email) || empty($password)) {
        json_error("Name, email, and password are required.", 400);
        exit;
    }

    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_error("Invalid email format.", 400);
        exit;
    }

    // 4. Validate password length limit
    if (strlen($password) < 8) {
        json_error("Password must be at least 8 characters long.", 400);
        exit;
    }

    try {
        // 5. Prevent duplicate emails (check if email already registered)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn()) {
            json_error("Email is already registered.", 409); // 409 Conflict
            exit;
        }

        // 6. Securely hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // 7. Insert the user into database (Default role is customer)
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmt->execute([$name, $email, $password_hash, $phone]);
        
        $new_user_id = $pdo->lastInsertId();

        // 8. Output success response omitting sensitive information
        json_success("Registration successful.", [
            'id' => $new_user_id,
            'name' => $name,
            'email' => $email,
            'role' => 'customer',
            'phone' => $phone
        ]);

    } catch (PDOException $e) {
        // Log the actual error internally: error_log($e->getMessage());
        // Do not expose database-level errors to the client
        json_error("Registration failed due to a server error. Please try again later.", 500);
    }
}

/**
 * Handle user login
 * Validates credentials and sets up the session if successful.
 */
function handleLogin($pdo, $data) {
    /* 
     * [Recommendation]: Rate limiting could be implemented here using Redis or a database table
     * to track failed login attempts per IP/Email. This is crucial for protecting against brute force attacks.
     */

    // 1. Retrieve inputs
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        json_error("Email and password are required.", 400);
        exit;
    }

    try {
        // 2. Fetch the user info by email
        $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role, phone FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Verify user exists and password is correct
        // A generic error is returned deliberately to prevent email enumeration or user existence disclosure
        if (!$user || !password_verify($password, $user['password_hash'])) {
            json_error("Invalid credentials.", 401); // 401 Unauthorized
            exit;
        }

        // 4. Authenticate the session
        // It's a good practice to regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_phone'] = $user['phone'] ?? '';
        $_SESSION['role'] = $user['role']; // For isAdmin() check in auth_guard

        // 5. Return user details (Never pass back the hash!)
        json_success("Login successful.", [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'phone' => $user['phone'] ?? '',
            'cart_data' => getCartItems($pdo, $user['id'])
        ]);

    } catch (PDOException $e) {
        // Log internally: error_log($e->getMessage());
        json_error("Login failed due to a server error.", 500);
    }
}

/**
 * Returns data about the currently authenticated user
 */
function handleMe() {
    global $pdo;
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        json_success("User authenticated.", [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role'],
            'phone' => $_SESSION['user_phone'] ?? '',
            'cart_data' => getCartItems($pdo, $_SESSION['user_id'])
        ]);
    } else {
        // Return null data gracefully for 'Not logged in' state
        json_success("Not logged in.", null);
    }
}

/**
 * Logs out the authenticated user by completely wiping the session
 */
function handleLogout() {
    // 1. Unset all of the session variables
    $_SESSION = array();

    // 2. Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // 3. Finally, destroy the session
    session_destroy();

    json_success("Logged out successfully.");
}

/**
 * Helper to fetch cart items with details
 */
function getCartItems($pdo, $userId) {
    try {
        $stmt = $pdo->prepare("
            (SELECT 
                CONCAT('item_', m.id) as cartId, 
                m.id, 
                'item' as type, 
                m.name, 
                m.price, 
                c.quantity as qty
            FROM cart c
            JOIN menu_items m ON c.item_id = m.id
            WHERE c.user_id = ? AND c.item_type = 'item')
            UNION ALL
            (SELECT 
                CONCAT('deal_', d.id) as cartId, 
                d.id, 
                'deal' as type, 
                d.name, 
                d.price, 
                c.quantity as qty
            FROM cart c
            JOIN deals d ON c.item_id = d.id
            WHERE c.user_id = ? AND c.item_type = 'deal')
        ");
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}
