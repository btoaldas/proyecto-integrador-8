<?php
session_start();

// Database connection
try {
    $pdo = new PDO(
        'pgsql:host=postgres;port=5432;dbname=municipal_system',
        'municipal_user',
        'municipal_password_2024',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        exit();
    }
    
    // Buscar usuario en la base de datos
    $stmt = $pdo->prepare('SELECT id, name, email, password, role, is_active FROM users WHERE email = ? AND is_active = true');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Login exitoso
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Registrar último acceso
        $stmt = $pdo->prepare('UPDATE users SET remember_token = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([session_id(), $user['id']]);
        
        // Redirigir al dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        // Login fallido
        header('Location: login.php?error=invalid_credentials');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>