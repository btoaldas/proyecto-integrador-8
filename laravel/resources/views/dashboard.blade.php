@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Dashboard</h1>
            <a href="{{ route('documents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nuevo Documento
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Documentos</h5>
                        <h2 class="mb-0">{{ $stats['total_documents'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Borradores</h5>
                        <h2 class="mb-0">{{ $stats['draft_documents'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-edit fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">En Revisión</h5>
                        <h2 class="mb-0">{{ $stats['pending_review'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-search fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Publicados</h5>
                        <h2 class="mb-0">{{ $stats['published_documents'] }}</h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-globe fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pending Tasks -->
    @if(count($pendingTasks) > 0)
    <div class="col-lg-12 mb-4">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tareas Pendientes
                </h5>
            </div>
            <div class="card-body">
                @foreach($pendingTasks as $task)
                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-2">
                    <span>{{ $task['message'] }}</span>
                    <a href="{{ $task['url'] }}" class="btn btn-sm btn-outline-warning">
                        Ver <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <!-- Recent Documents -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Documentos Recientes
                </h5>
            </div>
            <div class="card-body">
                @if($recentDocuments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentDocuments as $document)
                            <tr>
                                <td>
                                    <strong>{{ Str::limit($document->title, 50) }}</strong>
                                    <br>
                                    <small class="text-muted">Por: {{ $document->creator->name }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($document->document_type) }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'warning',
                                            'review' => 'info',
                                            'approved' => 'primary',
                                            'published' => 'success',
                                            'archived' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$document->status] ?? 'secondary' }}">
                                        {{ ucfirst($document->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $document->updated_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('documents.show', $document) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <a href="{{ route('documents.index') }}" class="btn btn-outline-primary">
                        Ver Todos los Documentos
                    </a>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-file fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay documentos recientes</p>
                    <a href="{{ route('documents.create') }}" class="btn btn-primary">
                        Crear Primer Documento
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Actividad Reciente
                </h5>
            </div>
            <div class="card-body">
                @if($recentActivity->count() > 0)
                <div class="timeline">
                    @foreach($recentActivity as $activity)
                    <div class="timeline-item mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                @php
                                    $actionIcons = [
                                        'created' => 'fas fa-plus text-success',
                                        'updated' => 'fas fa-edit text-warning',
                                        'transcribed' => 'fas fa-microphone text-info',
                                        'generated' => 'fas fa-robot text-primary',
                                        'signed' => 'fas fa-signature text-success',
                                        'published' => 'fas fa-globe text-success'
                                    ];
                                @endphp
                                <i class="{{ $actionIcons[$activity->action] ?? 'fas fa-circle text-secondary' }}"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">
                                    <strong>{{ $activity->user->name }}</strong>
                                    {{ $activity->action === 'created' ? 'creó' : 
                                       ($activity->action === 'updated' ? 'actualizó' : 
                                       ($activity->action === 'transcribed' ? 'transcribió' : 
                                       ($activity->action === 'generated' ? 'generó' : 
                                       ($activity->action === 'signed' ? 'firmó' : 
                                       ($activity->action === 'published' ? 'publicó' : $activity->action))))) }}
                                    @if($activity->document)
                                    <a href="{{ route('documents.show', $activity->document) }}" 
                                       class="text-decoration-none">
                                        {{ Str::limit($activity->document->title, 30) }}
                                    </a>
                                    @endif
                                </p>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hay actividad reciente</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline-item {
    position: relative;
}

.timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 7px;
    top: 25px;
    height: calc(100% - 10px);
    width: 2px;
    background-color: #dee2e6;
}
</style>
@endpush