<?php
// Verificar sesión
session_start();
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];
$user_email = $_SESSION['user_email'];

// Configurar menú según el rol del usuario
function getMenuItems($role) {
    $menus = [
        'admin' => [
            ['icon' => 'fas fa-tachometer-alt', 'title' => 'Dashboard', 'url' => 'dashboard.php', 'active' => true],
            ['icon' => 'fas fa-users', 'title' => 'Usuarios', 'url' => 'users.php'],
            ['icon' => 'fas fa-file-alt', 'title' => 'Documentos', 'url' => 'documents.php'],
            ['icon' => 'fas fa-file-signature', 'title' => 'Firmas Digitales', 'url' => 'signatures.php'],
            ['icon' => 'fas fa-history', 'title' => 'Auditoría', 'url' => 'audit.php'],
            ['icon' => 'fas fa-robot', 'title' => 'Servicios IA', 'url' => 'ai_services.php'],
            ['icon' => 'fas fa-cog', 'title' => 'Configuración', 'url' => 'settings.php'],
            ['icon' => 'fas fa-chart-bar', 'title' => 'Reportes', 'url' => 'reports.php']
        ],
        'secretary' => [
            ['icon' => 'fas fa-tachometer-alt', 'title' => 'Dashboard', 'url' => 'dashboard.php', 'active' => true],
            ['icon' => 'fas fa-file-alt', 'title' => 'Mis Documentos', 'url' => 'documents.php'],
            ['icon' => 'fas fa-plus-circle', 'title' => 'Nuevo Documento', 'url' => 'document_create.php'],
            ['icon' => 'fas fa-microphone', 'title' => 'Transcripción', 'url' => 'transcription.php'],
            ['icon' => 'fas fa-clock', 'title' => 'En Revisión', 'url' => 'pending_review.php']
        ],
        'reviewer' => [
            ['icon' => 'fas fa-tachometer-alt', 'title' => 'Dashboard', 'url' => 'dashboard.php', 'active' => true],
            ['icon' => 'fas fa-file-signature', 'title' => 'Por Revisar', 'url' => 'review_documents.php'],
            ['icon' => 'fas fa-check-circle', 'title' => 'Aprobados', 'url' => 'approved_documents.php'],
            ['icon' => 'fas fa-file-alt', 'title' => 'Todos los Documentos', 'url' => 'documents.php']
        ],
        'viewer' => [
            ['icon' => 'fas fa-tachometer-alt', 'title' => 'Dashboard', 'url' => 'dashboard.php', 'active' => true],
            ['icon' => 'fas fa-file-alt', 'title' => 'Documentos Públicos', 'url' => 'public_documents.php'],
            ['icon' => 'fas fa-search', 'title' => 'Búsqueda', 'url' => 'search.php']
        ]
    ];
    
    return $menus[$role] ?? $menus['viewer'];
}

$menuItems = getMenuItems($user_role);
$current_page = basename($_SERVER['PHP_SELF']);

function getRoleBadge($role) {
    $badges = [
        'admin' => '<span class="badge bg-danger">Administrador</span>',
        'secretary' => '<span class="badge bg-warning">Secretario</span>',
        'reviewer' => '<span class="badge bg-success">Revisor</span>',
        'viewer' => '<span class="badge bg-info">Ciudadano</span>'
    ];
    return $badges[$role] ?? '<span class="badge bg-secondary">Usuario</span>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Sistema Municipal'; ?> - Sistema Municipal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 70px;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.collapsed {
            width: 70px;
        }
        
        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #34495e;
            background: rgba(0,0,0,0.1);
        }
        
        .sidebar-brand {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ecf0f1;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .sidebar-brand i {
            margin-right: 0.5rem;
            font-size: 1.5rem;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 1rem;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: #ecf0f1;
            border-left: 4px solid #3498db;
        }
        
        .sidebar-menu i {
            width: 20px;
            margin-right: 1rem;
            text-align: center;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 70px;
        }
        
        .top-header {
            background: white;
            padding: 0 2rem;
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .content-area {
            padding: 2rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #3498db, #2980b9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .page-title {
            color: #2c3e50;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            color: #2c3e50;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .content-area {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-building"></i>
                <span class="brand-text">Sistema Municipal</span>
            </a>
        </div>
        
        <ul class="sidebar-menu">
            <?php foreach ($menuItems as $item): ?>
                <li>
                    <a href="<?php echo $item['url']; ?>" 
                       class="<?php echo ($current_page === $item['url']) ? 'active' : ''; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span class="menu-text"><?php echo $item['title']; ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            
            <li style="border-top: 2px solid rgba(255,255,255,0.2); margin-top: 1rem;">
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="menu-text">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0"><?php echo $page_title ?? 'Dashboard'; ?></h5>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 2)); ?>
                </div>
                <div>
                    <div class="fw-bold"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="small text-muted">
                        <?php echo getRoleBadge($user_role); ?>
                    </div>
                </div>
                <div class="dropdown ms-3">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>
        
        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($page_content)) echo $page_content; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
    <script>
        // Sidebar toggle functionality
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });
        
        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('show');
            });
        }
        
        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                const toggle = document.getElementById('sidebarToggle');
                
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>
</html>