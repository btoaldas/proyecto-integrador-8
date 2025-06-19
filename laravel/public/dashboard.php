<?php
$page_title = 'Dashboard Administrativo';

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

// Obtener estadísticas para el dashboard del administrador
function getAdminStats($pdo) {
    $stats = [];
    
    // Total de usuarios
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM users WHERE is_active = true');
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Usuarios por rol
    $stmt = $pdo->query('SELECT role, COUNT(*) as count FROM users WHERE is_active = true GROUP BY role');
    $stats['users_by_role'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Total de documentos
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM documents');
    $stats['total_documents'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Documentos por estado
    $stmt = $pdo->query('SELECT status, COUNT(*) as count FROM documents GROUP BY status');
    $stats['documents_by_status'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Documentos creados hoy
    $stmt = $pdo->query('SELECT COUNT(*) as today FROM documents WHERE DATE(created_at) = CURRENT_DATE');
    $stats['documents_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['today'] ?? 0;
    
    // Firmas digitales
    $stmt = $pdo->query('SELECT COUNT(*) as total FROM digital_signatures');
    $stats['total_signatures'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    // Actividad reciente (últimas 10 acciones)
    $stmt = $pdo->query('
        SELECT al.action, al.created_at, al.details, u.name as user_name, d.title as document_title 
        FROM audit_logs al 
        LEFT JOIN users u ON al.user_id = u.id 
        LEFT JOIN documents d ON al.document_id = d.id 
        ORDER BY al.created_at DESC 
        LIMIT 10
    ');
    $stats['recent_activity'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
}

$adminStats = getAdminStats($pdo);

// Preparar contenido de la página
ob_start();
?>

<div class="row">
    <div class="col-12">
        <h1 class="page-title">
            <i class="fas fa-tachometer-alt me-3"></i>Dashboard Administrativo
        </h1>
        <p class="text-muted mb-4">Vista general del sistema municipal de gestión de documentos</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="fw-bold text-primary"><?php echo $adminStats['total_users']; ?></h3>
            <p class="text-muted mb-0">Usuarios Activos</p>
            <small class="text-success">
                <i class="fas fa-arrow-up"></i> Sistema en línea
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-success">
                <i class="fas fa-file-alt"></i>
            </div>
            <h3 class="fw-bold text-success"><?php echo $adminStats['total_documents']; ?></h3>
            <p class="text-muted mb-0">Total Documentos</p>
            <small class="text-info">
                <i class="fas fa-plus"></i> <?php echo $adminStats['documents_today']; ?> hoy
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-warning">
                <i class="fas fa-file-signature"></i>
            </div>
            <h3 class="fw-bold text-warning"><?php echo $adminStats['total_signatures']; ?></h3>
            <p class="text-muted mb-0">Firmas Digitales</p>
            <small class="text-success">
                <i class="fas fa-shield-alt"></i> Seguras
            </small>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stats-card">
            <div class="stats-icon bg-info">
                <i class="fas fa-robot"></i>
            </div>
            <h3 class="fw-bold text-info">
                <i class="fas fa-check text-success"></i>
            </h3>
            <p class="text-muted mb-0">Servicios IA</p>
            <small class="text-success">
                <i class="fas fa-check-circle"></i> Operativo
            </small>
        </div>
    </div>
</div>

<!-- Charts and Detailed Stats -->
<div class="row mb-4">
    <!-- Users by Role Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users me-2"></i>Usuarios por Rol
                </h5>
            </div>
            <div class="card-body">
                <canvas id="usersChart" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Documents by Status Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>Documentos por Estado
                </h5>
            </div>
            <div class="card-body">
                <canvas id="documentsChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                <?php if (empty($adminStats['recent_activity'])): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <p>No hay actividad reciente registrada</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($adminStats['recent_activity'] as $activity): 
                            $details = json_decode($activity['details'], true);
                            $description = $details['description'] ?? ucfirst($activity['action']);
                        ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($description); ?>
                                    </div>
                                    <small class="text-muted">
                                        Por: <?php echo htmlspecialchars($activity['user_name'] ?? 'Sistema'); ?>
                                        • <?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?php 
                                    echo $activity['action'] === 'created' ? 'primary' : 
                                        ($activity['action'] === 'approved' ? 'success' : 
                                        ($activity['action'] === 'reviewed' ? 'warning' : 'info')); 
                                ?>">
                                    <?php echo ucfirst($activity['action']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Usuario
                    </button>
                    <button class="btn btn-outline-success" onclick="location.href='documents.php'">
                        <i class="fas fa-file-plus me-2"></i>Ver Documentos
                    </button>
                    <button class="btn btn-outline-info" onclick="location.href='reports.php'">
                        <i class="fas fa-chart-bar me-2"></i>Generar Reporte
                    </button>
                    <button class="btn btn-outline-warning" onclick="location.href='audit.php'">
                        <i class="fas fa-search me-2"></i>Auditoría
                    </button>
                </div>
                
                <hr class="my-3">
                
                <h6 class="mb-3">Estado del Sistema</h6>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <small>Base de Datos</small>
                        <span class="badge bg-success">Online</span>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <small>Servicio IA</small>
                        <span class="badge bg-success">Activo</span>
                    </div>
                </div>
                <div class="mb-2">
                    <div class="d-flex justify-content-between">
                        <small>Almacenamiento</small>
                        <span class="badge bg-warning">75% usado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Users by Role Chart
const usersCtx = document.getElementById('usersChart').getContext('2d');
const usersChart = new Chart(usersCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($adminStats['users_by_role'])); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($adminStats['users_by_role'])); ?>,
            backgroundColor: [
                '#dc3545', // admin - red
                '#ffc107', // secretary - yellow
                '#28a745', // reviewer - green
                '#17a2b8'  // viewer - blue
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Documents by Status Chart
const documentsCtx = document.getElementById('documentsChart').getContext('2d');
const documentsChart = new Chart(documentsCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($adminStats['documents_by_status'])); ?>,
        datasets: [{
            label: 'Documentos',
            data: <?php echo json_encode(array_values($adminStats['documents_by_status'])); ?>,
            backgroundColor: [
                '#6c757d', // draft - gray
                '#ffc107', // review - yellow
                '#28a745', // approved - green
                '#17a2b8', // published - blue
                '#6f42c1'  // archived - purple
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>

<!-- Modal para Nuevo Usuario -->
<div class="modal fade" id="newUserModal" tabindex="-1" aria-labelledby="newUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="newUserModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="newUserForm" action="create_user.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <!-- Información Personal -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-user me-2"></i>Información Personal
                            </h6>
                            
                            <div class="mb-3">
                                <label for="userName" class="form-label">
                                    <i class="fas fa-id-card me-1"></i>Nombre Completo *
                                </label>
                                <input type="text" class="form-control" id="userName" name="name" required 
                                       placeholder="Ej: Juan Carlos Pérez">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico *
                                </label>
                                <input type="email" class="form-control" id="userEmail" name="email" required 
                                       placeholder="usuario@municipio.gov">
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Contraseña *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="userPassword" name="password" required 
                                           placeholder="Mínimo 8 caracteres">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback"></div>
                                <small class="text-muted">Debe contener al menos 8 caracteres</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userPasswordConfirm" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Confirmar Contraseña *
                                </label>
                                <input type="password" class="form-control" id="userPasswordConfirm" name="password_confirm" required 
                                       placeholder="Repetir contraseña">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <!-- Información Laboral -->
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">
                                <i class="fas fa-briefcase me-2"></i>Información Laboral
                            </h6>
                            
                            <div class="mb-3">
                                <label for="userRole" class="form-label">
                                    <i class="fas fa-user-tag me-1"></i>Rol del Usuario *
                                </label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="">Seleccionar rol...</option>
                                    <option value="admin">
                                        <i class="fas fa-crown"></i> Administrador
                                    </option>
                                    <option value="secretary">
                                        <i class="fas fa-edit"></i> Secretario
                                    </option>
                                    <option value="reviewer">
                                        <i class="fas fa-file-signature"></i> Revisor
                                    </option>
                                    <option value="viewer">
                                        <i class="fas fa-eye"></i> Ciudadano/Viewer
                                    </option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userPosition" class="form-label">
                                    <i class="fas fa-briefcase me-1"></i>Cargo/Posición
                                </label>
                                <input type="text" class="form-control" id="userPosition" name="position" 
                                       placeholder="Ej: Jefe de Departamento">
                            </div>
                            
                            <div class="mb-3">
                                <label for="userDepartment" class="form-label">
                                    <i class="fas fa-building me-1"></i>Departamento
                                </label>
                                <select class="form-select" id="userDepartment" name="department">
                                    <option value="">Seleccionar departamento...</option>
                                    <option value="Administración">Administración</option>
                                    <option value="Secretaría">Secretaría</option>
                                    <option value="Legal">Departamento Legal</option>
                                    <option value="Obras Públicas">Obras Públicas</option>
                                    <option value="Hacienda">Hacienda</option>
                                    <option value="Recursos Humanos">Recursos Humanos</option>
                                    <option value="Sistemas">Tecnología y Sistemas</option>
                                    <option value="Público">Acceso Público</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="userPhone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Teléfono
                                </label>
                                <input type="tel" class="form-control" id="userPhone" name="phone" 
                                       placeholder="Ej: +593 99 123 4567">
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="userActive" name="is_active" checked>
                                    <label class="form-check-label" for="userActive">
                                        <i class="fas fa-user-check me-1"></i>Usuario activo
                                    </label>
                                </div>
                                <small class="text-muted">Los usuarios inactivos no pueden acceder al sistema</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Descripción del Rol -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card bg-light" id="roleDescription" style="display: none;">
                                <div class="card-body">
                                    <h6 class="card-title">Permisos del Rol</h6>
                                    <p class="card-text" id="roleDescriptionText"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="createUserBtn">
                        <i class="fas fa-user-plus me-2"></i>Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts del Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('userRole');
    const roleDescription = document.getElementById('roleDescription');
    const roleDescriptionText = document.getElementById('roleDescriptionText');
    const form = document.getElementById('newUserForm');
    
    // Descripciones de roles
    const roleDescriptions = {
        'admin': {
            title: 'Administrador del Sistema',
            description: 'Acceso completo al sistema. Puede gestionar usuarios, documentos, configuraciones y ver reportes completos. Tiene permisos para aprobar y publicar documentos.'
        },
        'secretary': {
            title: 'Secretario Municipal',
            description: 'Puede crear y editar documentos, grabar sesiones, transcribir audio y enviar documentos para revisión. No puede aprobar documentos finales.'
        },
        'reviewer': {
            title: 'Revisor Legal',
            description: 'Puede revisar documentos enviados por secretarios, realizar correcciones, firmar digitalmente y recomendar aprobación o rechazo.'
        },
        'viewer': {
            title: 'Ciudadano/Visualizador',
            description: 'Solo puede ver documentos públicos publicados. Acceso de solo lectura para transparencia ciudadana.'
        }
    };
    
    // Mostrar descripción del rol seleccionado
    roleSelect.addEventListener('change', function() {
        const selectedRole = this.value;
        if (selectedRole && roleDescriptions[selectedRole]) {
            roleDescriptionText.innerHTML = `
                <strong>${roleDescriptions[selectedRole].title}:</strong><br>
                ${roleDescriptions[selectedRole].description}
            `;
            roleDescription.style.display = 'block';
        } else {
            roleDescription.style.display = 'none';
        }
    });
    
    // Toggle password visibility
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        const passwordField = document.getElementById('userPassword');
        const icon = this.querySelector('i');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Validación del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpiar validaciones previas
        const inputs = form.querySelectorAll('.form-control, .form-select');
        inputs.forEach(input => {
            input.classList.remove('is-invalid', 'is-valid');
        });
        
        let isValid = true;
        const formData = new FormData(form);
        
        // Validar campos requeridos
        const requiredFields = ['name', 'email', 'password', 'role'];
        requiredFields.forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            const value = formData.get(field);
            
            if (!value || value.trim() === '') {
                input.classList.add('is-invalid');
                input.nextElementSibling.textContent = 'Este campo es obligatorio';
                isValid = false;
            } else {
                input.classList.add('is-valid');
            }
        });
        
        // Validar email
        const email = formData.get('email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const emailInput = form.querySelector('[name="email"]');
        
        if (email && !emailRegex.test(email)) {
            emailInput.classList.remove('is-valid');
            emailInput.classList.add('is-invalid');
            emailInput.nextElementSibling.textContent = 'Formato de email inválido';
            isValid = false;
        }
        
        // Validar contraseña
        const password = formData.get('password');
        const passwordConfirm = formData.get('password_confirm');
        const passwordInput = form.querySelector('[name="password"]');
        const passwordConfirmInput = form.querySelector('[name="password_confirm"]');
        
        if (password && password.length < 8) {
            passwordInput.classList.remove('is-valid');
            passwordInput.classList.add('is-invalid');
            passwordInput.nextElementSibling.textContent = 'La contraseña debe tener al menos 8 caracteres';
            isValid = false;
        }
        
        if (password !== passwordConfirm) {
            passwordConfirmInput.classList.add('is-invalid');
            passwordConfirmInput.nextElementSibling.textContent = 'Las contraseñas no coinciden';
            isValid = false;
        } else if (passwordConfirm) {
            passwordConfirmInput.classList.add('is-valid');
        }
        
        // Si es válido, enviar formulario
        if (isValid) {
            const createBtn = document.getElementById('createUserBtn');
            createBtn.disabled = true;
            createBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando...';
            
            // Enviar formulario usando fetch
            fetch('create_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const modal = bootstrap.Modal.getInstance(document.getElementById('newUserModal'));
                    modal.hide();
                    
                    // Mostrar notificación de éxito
                    showNotification('success', 'Usuario creado exitosamente', data.message);
                    
                    // Resetear formulario
                    form.reset();
                    roleDescription.style.display = 'none';
                    
                    // Recargar página después de un momento
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    // Mostrar errores
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                input.nextElementSibling.textContent = data.errors[field];
                            }
                        });
                    }
                    showNotification('error', 'Error al crear usuario', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('error', 'Error del sistema', 'Ocurrió un error inesperado');
            })
            .finally(() => {
                createBtn.disabled = false;
                createBtn.innerHTML = '<i class="fas fa-user-plus me-2"></i>Crear Usuario';
            });
        }
    });
    
    // Función para mostrar notificaciones
    function showNotification(type, title, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        
        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 400px;';
        notification.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            <strong>${title}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
});
</script>

<?php
$page_content = ob_get_clean();
include 'includes/layout.php';
?>