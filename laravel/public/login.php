<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Municipal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh;">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100 justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="card shadow-lg border-0">
                    <div class="row no-gutters">
                        <!-- Left side - Login Form -->
                        <div class="col-md-6 col-lg-7">
                            <div class="card-body p-5">
                                <div class="text-center mb-4">
                                    <i class="fas fa-building fa-3x text-primary mb-3"></i>
                                    <h2 class="h3 text-dark mb-2">Sistema Municipal</h2>
                                    <p class="text-muted">Gestión de Documentos Oficiales</p>
                                </div>

                                <?php if (isset($_GET['error'])): ?>
                                    <div class="alert alert-danger">
                                        <?php if ($_GET['error'] === 'invalid_credentials'): ?>
                                            Credenciales incorrectas. Verifica tu email y contraseña.
                                        <?php elseif ($_GET['error'] === 'empty_fields'): ?>
                                            Por favor completa todos los campos.
                                        <?php else: ?>
                                            Error en el inicio de sesión.
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (isset($_GET['message']) && $_GET['message'] === 'logged_out'): ?>
                                    <div class="alert alert-success">
                                        Has cerrado sesión exitosamente.
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="login_handler.php" id="loginForm">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-user me-2"></i>Correo Electrónico
                                        </label>
                                        <input type="email" 
                                               class="form-control form-control-lg" 
                                               id="email" 
                                               name="email" 
                                               required 
                                               placeholder="usuario@municipio.gov">
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Contraseña
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control form-control-lg" 
                                                   id="password" 
                                                   name="password" 
                                                   required 
                                                   placeholder="••••••••">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Recordar sesión
                                        </label>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-4">
                                    <small class="text-muted">
                                        ¿Problemas para acceder? Contacta al administrador del sistema
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Right side - Test Users -->
                        <div class="col-md-6 col-lg-5 bg-light">
                            <div class="card-body p-4">
                                <h5 class="text-center mb-4">
                                    <i class="fas fa-users text-info me-2"></i>Usuarios de Prueba
                                </h5>
                                <p class="text-muted text-center small mb-4">
                                    Haz clic en cualquier usuario para autocompletar el formulario
                                </p>

                                <div class="test-users">
                                    <!-- Administrador -->
                                    <div class="card mb-3 user-card" 
                                         data-email="admin@municipio.gov" 
                                         data-password="admin123"
                                         style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-crown"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Administrador</h6>
                                                    <small class="text-muted">admin@municipio.gov</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-danger">Admin</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Editor/Secretario -->
                                    <div class="card mb-3 user-card" 
                                         data-email="secretario@municipio.gov" 
                                         data-password="secretario123"
                                         style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-edit"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Secretario</h6>
                                                    <small class="text-muted">secretario@municipio.gov</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-warning">Secretary</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Revisor/Firmador -->
                                    <div class="card mb-3 user-card" 
                                         data-email="revisor@municipio.gov" 
                                         data-password="revisor123"
                                         style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-file-signature"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Revisor</h6>
                                                    <small class="text-muted">revisor@municipio.gov</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success">Reviewer</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Público/Viewer -->
                                    <div class="card mb-3 user-card" 
                                         data-email="publico@municipio.gov" 
                                         data-password="publico123"
                                         style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                    <i class="fas fa-eye"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">Ciudadano</h6>
                                                    <small class="text-muted">publico@municipio.gov</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-info">Viewer</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light mt-4">
                                    <small>
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Demo:</strong> Estos usuarios están precargados para pruebas del sistema.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .user-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }

    .user-card:active {
        transform: translateY(0);
    }

    .avatar {
        min-width: 45px;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding: 15px;
        }
        
        .card-body {
            padding: 2rem !important;
        }
        
        .col-md-6:last-child {
            border-top: 1px solid #dee2e6;
            margin-top: 0;
        }
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill form when clicking test users
        const userCards = document.querySelectorAll('.user-card');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        
        userCards.forEach(card => {
            card.addEventListener('click', function() {
                const email = this.dataset.email;
                const password = this.dataset.password;
                
                // Remove previous selection styling
                userCards.forEach(c => c.classList.remove('border-primary'));
                
                // Add selection styling
                this.classList.add('border-primary');
                
                // Fill form
                emailInput.value = email;
                passwordInput.value = password;
                
                // Add visual feedback
                emailInput.classList.add('is-valid');
                passwordInput.classList.add('is-valid');
                
                // Remove validation classes after 2 seconds
                setTimeout(() => {
                    emailInput.classList.remove('is-valid');
                    passwordInput.classList.remove('is-valid');
                }, 2000);
            });
        });
        
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            if (type === 'text') {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    });
    </script>
</body>
</html>