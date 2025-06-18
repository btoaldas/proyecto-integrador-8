<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-building me-2"></i>
                {{ config('app.name') }}
            </a>
            <div class="navbar-nav ms-auto">
                @auth
                    <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Cerrar Sesión</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                    <a class="nav-link" href="{{ route('register') }}">Registrarse</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="bg-light py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold text-primary mb-3">
                            Sistema Municipal de Gestión de Documentos
                        </h1>
                        <p class="lead mb-4">
                            Automatiza la gestión de documentos municipales desde la grabación de audio hasta la 
                            publicación con firma digital y validez legal.
                        </p>
                        <div class="d-flex gap-3">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-tachometer-alt me-2"></i>
                                    Ir al Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Iniciar Sesión
                                </a>
                            @endauth
                            <a href="{{ route('documents.public') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-folder-open me-2"></i>
                                Documentos Públicos
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <img src="https://via.placeholder.com/600x400/007bff/ffffff?text=Municipal+System" 
                             class="img-fluid rounded shadow" alt="Sistema Municipal">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col">
                        <h2 class="display-5 fw-bold">Características Principales</h2>
                        <p class="lead text-muted">
                            Tecnología de vanguardia para la gestión eficiente de documentos oficiales
                        </p>
                    </div>
                </div>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-microphone fa-lg"></i>
                                </div>
                                <h5 class="card-title">Transcripción con IA</h5>
                                <p class="card-text text-muted">
                                    Convierte grabaciones de audio a texto automáticamente usando 
                                    inteligencia artificial avanzada.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-file-signature fa-lg"></i>
                                </div>
                                <h5 class="card-title">Firma Digital</h5>
                                <p class="card-text text-muted">
                                    Firma documentos digitalmente con validez legal y certificados 
                                    de autenticidad.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-globe fa-lg"></i>
                                </div>
                                <h5 class="card-title">Repositorio Público</h5>
                                <p class="card-text text-muted">
                                    Publica documentos oficiales de manera transparente y 
                                    accesible para los ciudadanos.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-users fa-lg"></i>
                                </div>
                                <h5 class="card-title">Gestión de Roles</h5>
                                <p class="card-text text-muted">
                                    Control de acceso basado en roles: administradores, 
                                    secretarios, revisores y visualizadores.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-shield-alt fa-lg"></i>
                                </div>
                                <h5 class="card-title">Auditoría Completa</h5>
                                <p class="card-text text-muted">
                                    Registro detallado de todas las acciones y cambios 
                                    realizados en los documentos.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body text-center p-4">
                                <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-cogs fa-lg"></i>
                                </div>
                                <h5 class="card-title">Automatización</h5>
                                <p class="card-text text-muted">
                                    Workflow automático desde la grabación hasta la 
                                    publicación final del documento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p class="mb-0">Sistema integral para la gestión de documentos municipales.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; {{ date('Y') }} Municipalidad. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>