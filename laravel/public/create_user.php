<?php
session_start();
header('Content-Type: application/json');

// Verificar que el usuario esté logueado y sea administrador
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Solo procesar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Database connection
try {
    $pdo = new PDO(
        'pgsql:host=postgres;port=5432;dbname=municipal_system',
        'municipal_user',
        'municipal_password_2024',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Obtener y validar datos del formulario
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$role = $_POST['role'] ?? '';
$position = trim($_POST['position'] ?? '');
$department = trim($_POST['department'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$is_active = isset($_POST['is_active']) ? true : false;

$errors = [];

// Validaciones
if (empty($name)) {
    $errors['name'] = 'El nombre es obligatorio';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'El nombre debe tener al menos 2 caracteres';
}

if (empty($email)) {
    $errors['email'] = 'El email es obligatorio';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'El formato del email es inválido';
}

if (empty($password)) {
    $errors['password'] = 'La contraseña es obligatoria';
} elseif (strlen($password) < 8) {
    $errors['password'] = 'La contraseña debe tener al menos 8 caracteres';
}

if ($password !== $password_confirm) {
    $errors['password_confirm'] = 'Las contraseñas no coinciden';
}

if (empty($role)) {
    $errors['role'] = 'El rol es obligatorio';
} elseif (!in_array($role, ['admin', 'secretary', 'reviewer', 'viewer'])) {
    $errors['role'] = 'Rol inválido';
}

// Verificar si el email ya existe
if (!isset($errors['email'])) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors['email'] = 'Este email ya está registrado';
    }
}

// Si hay errores, retornar
if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => 'Por favor corrige los errores en el formulario',
        'errors' => $errors
    ]);
    exit();
}

try {
    // Generar UUID para el nuevo usuario
    $user_id = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    // Encriptar contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $stmt = $pdo->prepare('
        INSERT INTO users (id, name, email, password, role, is_active, email_verified_at, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
    ');
    
    $stmt->execute([
        $user_id,
        $name,
        $email,
        $hashed_password,
        $role,
        $is_active
    ]);
    
    // Registrar la acción en los logs de auditoría
    $audit_id = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $audit_details = json_encode([
        'description' => "Usuario creado: {$name} ({$email})",
        'user_role' => $role,
        'user_department' => $department,
        'created_by_admin' => $_SESSION['user_name']
    ]);
    
    $stmt = $pdo->prepare('
        INSERT INTO audit_logs (id, user_id, action, details, ip_address, user_agent, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
    ');
    
    $stmt->execute([
        $audit_id,
        $_SESSION['user_id'],
        'user_created',
        $audit_details,
        $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
        $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);
    
    // Determinar mensaje según el rol
    $role_messages = [
        'admin' => 'Administrador creado con acceso completo al sistema',
        'secretary' => 'Secretario creado con permisos para gestionar documentos',
        'reviewer' => 'Revisor creado con permisos para revisar y firmar documentos',
        'viewer' => 'Usuario ciudadano creado con acceso de solo lectura'
    ];
    
    $success_message = $role_messages[$role] ?? 'Usuario creado exitosamente';
    
    echo json_encode([
        'success' => true,
        'message' => $success_message,
        'user_id' => $user_id,
        'user_data' => [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'is_active' => $is_active
        ]
    ]);
    
} catch (PDOException $e) {
    error_log('Error creating user: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al crear el usuario. Por favor intenta nuevamente.',
        'error_code' => 'DATABASE_ERROR'
    ]);
}
?>