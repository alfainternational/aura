@extends('layouts.admin')

@section('title', 'Detalles del Incidente de Seguridad')

@section('styles')
<style>
    .incident-card {
        transition: all 0.3s ease;
    }
    .incident-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .data-label {
        font-weight: bold;
        color: #4e73df;
    }
    .json-viewer {
        background-color: #f8f9fc;
        border-radius: 0.35rem;
        padding: 1rem;
        max-height: 400px;
        overflow-y: auto;
    }
    pre {
        margin: 0;
    }
    .badge-critical {
        background-color: #e74a3b;
        color: white;
    }
    .badge-high {
        background-color: #f6c23e;
        color: white;
    }
    .badge-medium {
        background-color: #36b9cc;
        color: white;
    }
    .badge-low {
        background-color: #858796;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Título de la página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detalles del Incidente de Seguridad #{{ $incident->id }}</h1>
        <div>
            <a href="{{ route('admin.security.incidents.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Volver a la lista
            </a>
            <a href="{{ route('admin.security.dashboard') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-tachometer-alt fa-sm text-white-50"></i> Panel de Seguridad
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información del incidente -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4 incident-card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Incidente</h6>
                    <div>
                        @if($incident->resolved_at)
                            <span class="badge badge-success">Resuelto</span>
                        @else
                            <span class="badge badge-danger">No Resuelto</span>
                            <button type="button" class="btn btn-sm btn-success ml-2" data-toggle="modal" data-target="#resolveModal">
                                <i class="fas fa-check"></i> Marcar como Resuelto
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><span class="data-label">Tipo:</span> 
                                <span class="badge badge-{{ $incident->severity == 'critical' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'info' : 'secondary')) }}">
                                    {{ $incident->type }}
                                </span>
                            </p>
                            <p><span class="data-label">Severidad:</span> 
                                <span class="badge badge-{{ $incident->severity }}">
                                    {{ ucfirst($incident->severity) }}
                                </span>
                            </p>
                            <p><span class="data-label">Fecha:</span> {{ $incident->created_at->format('d/m/Y H:i:s') }}</p>
                            @if($incident->resolved_at)
                                <p><span class="data-label">Resuelto el:</span> {{ $incident->resolved_at->format('d/m/Y H:i:s') }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p>
                                <span class="data-label">Dirección IP:</span> 
                                {{ $incident->ip_address }}
                                @if($isIpBlocked)
                                    <span class="badge badge-danger ml-2">Bloqueada</span>
                                @endif
                            </p>
                            <p><span class="data-label">Usuario:</span> 
                                @if($incident->user)
                                    <a href="{{ route('admin.users.show', $incident->user->id) }}">{{ $incident->user->name }}</a>
                                @else
                                    <span class="text-muted">No autenticado</span>
                                @endif
                            </p>
                            <p><span class="data-label">User Agent:</span> <span class="text-muted">{{ Str::limit($incident->user_agent, 50) }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <p class="data-label">Descripción:</p>
                            <div class="p-3 bg-light rounded">
                                {{ $incident->description }}
                            </div>
                        </div>
                    </div>

                    @if($incident->resolved_at && $incident->resolution_notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <p class="data-label">Notas de Resolución:</p>
                                <div class="p-3 bg-light rounded">
                                    {{ $incident->resolution_notes }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($incident->data)
                        <div class="row">
                            <div class="col-12">
                                <p class="data-label">Datos Adicionales:</p>
                                <div class="json-viewer">
                                    <pre id="json-renderer">{{ json_encode($incident->data, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Acciones y Ubicación -->
        <div class="col-xl-4 col-lg-5">
            <!-- Acciones -->
            <div class="card shadow mb-4 incident-card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones</h6>
                </div>
                <div class="card-body">
                    @if($incident->ip_address)
                        @if(!$isIpBlocked)
                            <button type="button" class="btn btn-danger btn-block mb-3" data-toggle="modal" data-target="#blockIpModal">
                                <i class="fas fa-ban"></i> Bloquear IP {{ $incident->ip_address }}
                            </button>
                        @else
                            <form action="{{ route('admin.security.unblock-ip') }}" method="POST" class="mb-3">
                                @csrf
                                <input type="hidden" name="ip_address" value="{{ $incident->ip_address }}">
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-unlock"></i> Desbloquear IP {{ $incident->ip_address }}
                                </button>
                            </form>
                        @endif
                    @endif

                    @if(!$incident->resolved_at)
                        <button type="button" class="btn btn-success btn-block mb-3" data-toggle="modal" data-target="#resolveModal">
                            <i class="fas fa-check"></i> Marcar como Resuelto
                        </button>
                    @endif

                    <a href="{{ route('admin.security.incidents.index', ['ip_address' => $incident->ip_address]) }}" class="btn btn-info btn-block mb-3">
                        <i class="fas fa-search"></i> Ver Incidentes de esta IP
                    </a>

                    @if($incident->user)
                        <a href="{{ route('admin.security.incidents.index', ['user_id' => $incident->user->id]) }}" class="btn btn-info btn-block">
                            <i class="fas fa-search"></i> Ver Incidentes de este Usuario
                        </a>
                    @endif
                </div>
            </div>

            <!-- Ubicación -->
            @if($incident->ip_address)
                <div class="card shadow mb-4 incident-card">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Ubicación</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <img src="https://ipinfo.io/{{ $incident->ip_address }}/map?token=YOUR_TOKEN" class="img-fluid rounded" alt="Mapa de ubicación">
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>País</th>
                                    <td>{{ $incident->data['location']['country'] ?? 'Desconocido' }}</td>
                                </tr>
                                <tr>
                                    <th>Ciudad</th>
                                    <td>{{ $incident->data['location']['city'] ?? 'Desconocido' }}</td>
                                </tr>
                                <tr>
                                    <th>Región</th>
                                    <td>{{ $incident->data['location']['region'] ?? 'Desconocido' }}</td>
                                </tr>
                                <tr>
                                    <th>Coordenadas</th>
                                    <td>{{ $incident->data['location']['lat'] ?? '?' }}, {{ $incident->data['location']['lon'] ?? '?' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Incidentes Relacionados -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Incidentes Relacionados</h6>
        </div>
        <div class="card-body">
            @if($relatedIncidents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Severidad</th>
                                <th>IP</th>
                                <th>Usuario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($relatedIncidents as $relatedIncident)
                                <tr>
                                    <td>{{ $relatedIncident->id }}</td>
                                    <td>{{ $relatedIncident->type }}</td>
                                    <td>{{ Str::limit($relatedIncident->description, 50) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $relatedIncident->severity == 'critical' ? 'danger' : ($relatedIncident->severity == 'high' ? 'warning' : ($relatedIncident->severity == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $relatedIncident->severity }}
                                        </span>
                                    </td>
                                    <td>{{ $relatedIncident->ip_address }}</td>
                                    <td>
                                        @if($relatedIncident->user)
                                            {{ $relatedIncident->user->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $relatedIncident->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($relatedIncident->resolved_at)
                                            <span class="badge badge-success">Resuelto</span>
                                        @else
                                            <span class="badge badge-danger">No Resuelto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.security.incidents.show', $relatedIncident) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">No hay incidentes relacionados.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para resolver incidente -->
<div class="modal fade" id="resolveModal" tabindex="-1" role="dialog" aria-labelledby="resolveModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.security.incidents.resolve', $incident) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="resolveModalLabel">Resolver Incidente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="resolution_notes">Notas de Resolución:</label>
                        <textarea class="form-control" id="resolution_notes" name="resolution_notes" rows="4" placeholder="Describa cómo se resolvió este incidente..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Marcar como Resuelto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para bloquear IP -->
<div class="modal fade" id="blockIpModal" tabindex="-1" role="dialog" aria-labelledby="blockIpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.security.block-ip') }}" method="POST">
                @csrf
                <input type="hidden" name="ip_address" value="{{ $incident->ip_address }}">
                <input type="hidden" name="security_incident_id" value="{{ $incident->id }}">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="blockIpModalLabel">Bloquear IP {{ $incident->ip_address }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">Motivo del bloqueo:</label>
                        <input type="text" class="form-control" id="reason" name="reason" value="Bloqueado por incidente de seguridad #{{ $incident->id }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Duración (minutos):</label>
                        <input type="number" class="form-control" id="duration" name="duration" min="1" max="43200" placeholder="Dejar en blanco para bloqueo permanente">
                        <small class="form-text text-muted">Dejar en blanco para un bloqueo permanente. Máximo 30 días (43200 minutos).</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Bloquear IP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Formatear JSON para mejor visualización
        try {
            const jsonViewer = document.getElementById('json-renderer');
            if (jsonViewer) {
                const jsonData = JSON.parse(jsonViewer.textContent);
                jsonViewer.textContent = JSON.stringify(jsonData, null, 2);
            }
        } catch (e) {
            console.error('Error al formatear JSON:', e);
        }
    });
</script>
@endsection
