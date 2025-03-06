@extends('layouts.admin')

@section('title', 'Incidentes de Seguridad')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Incidentes de Seguridad</h1>
        <div>
            <a href="{{ route('admin.security.incidents.export') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-download fa-sm text-white-50"></i> Exportar
            </a>
            <a href="{{ route('admin.security.dashboard') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
                <i class="fas fa-chart-line fa-sm text-white-50"></i> Panel de Seguridad
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.security.incidents.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="type">Tipo de Incidente</label>
                        <select class="form-control" id="type" name="type">
                            <option value="">Todos</option>
                            @foreach($incidentTypes as $type)
                                <option value="{{ $type }}" {{ isset($filters['type']) && $filters['type'] == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="severity">Severidad</label>
                        <select class="form-control" id="severity" name="severity">
                            <option value="">Todas</option>
                            @foreach($severities as $severity)
                                <option value="{{ $severity }}" {{ isset($filters['severity']) && $filters['severity'] == $severity ? 'selected' : '' }}>
                                    {{ ucfirst($severity) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="status">Estado</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="unresolved" {{ isset($filters['status']) && $filters['status'] == 'unresolved' ? 'selected' : '' }}>No Resueltos</option>
                            <option value="resolved" {{ isset($filters['status']) && $filters['status'] == 'resolved' ? 'selected' : '' }}>Resueltos</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="ip_address">Dirección IP</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" value="{{ $filters['ip_address'] ?? '' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="user_id">ID de Usuario</label>
                        <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $filters['user_id'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_from">Desde</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to">Hasta</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">Filtrar</button>
                        <a href="{{ route('admin.security.incidents.index') }}" class="btn btn-secondary">Limpiar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Incidentes -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Incidentes de Seguridad</h6>
        </div>
        <div class="card-body">
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
                        @forelse($incidents as $incident)
                            <tr>
                                <td>{{ $incident->id }}</td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $incident->type == 'attack_detected' ? 'danger' : 
                                        ($incident->type == 'possible_attack' ? 'warning' : 
                                        ($incident->type == 'anomalous_activity' ? 'info' : 
                                        ($incident->type == 'blocked_ip_access' ? 'dark' : 
                                        ($incident->type == 'threshold_exceeded' ? 'primary' : 'secondary')))) 
                                    }}">
                                        {{ ucfirst(str_replace('_', ' ', $incident->type)) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($incident->description, 30) }}</td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $incident->severity == 'critical' ? 'danger' : 
                                        ($incident->severity == 'high' ? 'warning' : 
                                        ($incident->severity == 'medium' ? 'info' : 'secondary')) 
                                    }}">
                                        {{ ucfirst($incident->severity) }}
                                    </span>
                                </td>
                                <td>{{ $incident->ip_address }}</td>
                                <td>{{ $incident->user ? $incident->user->name : 'N/A' }}</td>
                                <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($incident->resolved_at)
                                        <span class="badge badge-success">Resuelto</span>
                                    @else
                                        <span class="badge badge-danger">No Resuelto</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.security.incidents.show', $incident) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(!$incident->resolved_at)
                                        <form action="{{ route('admin.security.incidents.resolve', $incident) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($incident->ip_address)
                                        <form action="{{ route('admin.security.block-ip') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="ip_address" value="{{ $incident->ip_address }}">
                                            <input type="hidden" name="reason" value="Bloqueado desde panel de incidentes">
                                            <input type="hidden" name="duration" value="1440">
                                            <input type="hidden" name="security_incident_id" value="{{ $incident->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No se encontraron incidentes de seguridad.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $incidents->appends($filters)->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
