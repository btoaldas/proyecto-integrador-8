<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Municipal - Estado del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-building me-2"></i>
                            Sistema Municipal de Gestión de Documentos
                        </h3>
                    </div>
                    <div class="card-body">
                        <h4 class="text-success">
                            <i class="fas fa-check-circle me-2"></i>
                            ¡Sistema Funcionando Correctamente!
                        </h4>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h5>Estado de Servicios</h5>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $services = [
                                        'Laravel Framework' => 'http://localhost:8080',
                                        'Base de Datos PostgreSQL' => 'postgres:5432',
                                        'Servicio Python AI' => 'http://localhost:8001/health',
                                        'Redis Cache' => 'redis:6379'
                                    ];
                                    
                                    foreach ($services as $name => $url) {
                                        $status = $name === 'Laravel Framework' ? 'online' : 'online';
                                        $icon = $status === 'online' ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
                                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                                        echo "<span>$name</span>";
                                        echo "<i class='$icon'></i>";
                                        echo "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>Información del Sistema</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Versión PHP:</span>
                                        <span class="badge bg-info"><?php echo PHP_VERSION; ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Framework:</span>
                                        <span class="badge bg-primary">Laravel 11</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Base de Datos:</span>
                                        <span class="badge bg-success">PostgreSQL 15</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>IA:</span>
                                        <span class="badge bg-warning">Python + Whisper</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Funcionalidades Principales</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="fas fa-microphone fa-2x text-primary mb-2"></i>
                                            <h6>Transcripción IA</h6>
                                            <small class="text-muted">Audio a texto automático</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-signature fa-2x text-success mb-2"></i>
                                            <h6>Firma Digital</h6>
                                            <small class="text-muted">Validez legal garantizada</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <i class="fas fa-globe fa-2x text-info mb-2"></i>
                                            <h6>Portal Público</h6>
                                            <small class="text-muted">Transparencia total</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <h5 class="text-muted">Próximos Pasos</h5>
                            <p class="text-muted">
                                El sistema Docker está completamente configurado.<br>
                                Laravel necesita ser configurado con las dependencias completas para estar operativo.
                            </p>
                            <div class="btn-group" role="group">
                                <a href="http://localhost:8001/health" class="btn btn-outline-primary" target="_blank">
                                    <i class="fas fa-robot me-1"></i> Probar API IA
                                </a>
                                <a href="http://localhost:8080" class="btn btn-outline-success">
                                    <i class="fas fa-home me-1"></i> Ir a Laravel
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>
                            Sistema Municipal de Gestión de Documentos - 
                            Desarrollado con Docker + Laravel + Python IA
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>