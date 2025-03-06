@extends('layouts.admin')

@section('title', 'Panel de Seguridad')

@section('styles')
<style>
    .security-card {
        transition: all 0.3s ease;
    }
    .security-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Título de la página -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Panel de Seguridad</h1>
        <a href="{{ route('admin.security.incidents.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-list fa-sm text-white-50"></i> Ver todos los incidentes
        </a>
    </div>

    <!-- Filtros de fecha -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Filtrar por fecha</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.security.dashboard') }}" method="GET" class="form-inline">
                <div class="form-group mb-2 mr-2">
                    <label for="date_from" class="mr-2">Desde:</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from', $startDate->format('Y-m-d')) }}">
                </div>
                <div class="form-group mb-2 mr-2">
                    <label for="date_to" class="mr-2">Hasta:</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to', $endDate->format('Y-m-d')) }}">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Filtrar</button>
            </form>
        </div>
    </div>

    <!-- Tarjetas de estadísticas -->
    <div class="row">
        <!-- Total de incidentes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 security-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de incidentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['total_incidents'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incidentes sin resolver -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 security-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Incidentes sin resolver</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['unresolved_incidents'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- IPs bloqueadas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 security-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">IPs bloqueadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $blockedIps->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ban fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tipos de incidentes -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 security-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tipos de incidentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ count($report['incidents_by_type']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Gráfico de incidentes por día -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Incidentes de seguridad por día</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incidentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de incidentes por tipo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Incidentes por tipo</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incidentsByTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de incidentes por severidad -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Incidentes por severidad</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="incidentsBySeverityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- IPs más problemáticas -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">IPs con más incidentes</h6>
                </div>
                <div class="card-body">
                    @if(count($report['top_offending_ips']) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>IP</th>
                                        <th>Incidentes</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['top_offending_ips'] as $ip => $count)
                                        @php
                                            $isBlocked = $blockedIps->where('ip_address', $ip)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $ip }}</td>
                                            <td>{{ $count }}</td>
                                            <td>
                                                @if($isBlocked)
                                                    <span class="badge badge-danger">Bloqueada</span>
                                                    @if($isBlocked->expires_at)
                                                        <small>(hasta {{ $isBlocked->expires_at->format('d/m/Y H:i') }})</small>
                                                    @endif
                                                @else
                                                    <span class="badge badge-success">No bloqueada</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!$isBlocked)
                                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#blockIpModal" data-ip="{{ $ip }}">
                                                        <i class="fas fa-ban"></i> Bloquear
                                                    </button>
                                                @else
                                                    <form action="{{ route('admin.security.unblock-ip') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="ip_address" value="{{ $ip }}">
                                                        <button type="submit" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-unlock"></i> Desbloquear
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No hay datos de IPs con incidentes para el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Incidentes recientes sin resolver -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Incidentes recientes sin resolver</h6>
            <a href="{{ route('admin.security.incidents.index', ['status' => 'unresolved']) }}" class="btn btn-sm btn-primary">
                Ver todos
            </a>
        </div>
        <div class="card-body">
            @if($recentIncidents->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Severidad</th>
                                <th>IP</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentIncidents as $incident)
                                <tr>
                                    <td>{{ $incident->id }}</td>
                                    <td>
                                        <span class="badge badge-{{ $incident->severity == 'critical' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $incident->type }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($incident->description, 50) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $incident->severity == 'critical' ? 'danger' : ($incident->severity == 'high' ? 'warning' : ($incident->severity == 'medium' ? 'info' : 'secondary')) }}">
                                            {{ $incident->severity }}
                                        </span>
                                    </td>
                                    <td>{{ $incident->ip_address }}</td>
                                    <td>{{ $incident->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.security.incidents.show', $incident) }}" class="btn btn-sm btn-primary">
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
                    <p class="text-muted">No hay incidentes sin resolver.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para bloquear IP -->
<div class="modal fade" id="blockIpModal" tabindex="-1" role="dialog" aria-labelledby="blockIpModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.security.block-ip') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="blockIpModalLabel">Bloquear dirección IP</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ip_address" id="ipAddressInput">
                    
                    <div class="form-group">
                        <label for="reason">Motivo del bloqueo:</label>
                        <input type="text" class="form-control" id="reason" name="reason" required>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Configurar el modal para bloquear IP
        $('#blockIpModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var ip = button.data('ip');
            var modal = $(this);
            modal.find('#ipAddressInput').val(ip);
            modal.find('#blockIpModalLabel').text('Bloquear dirección IP: ' + ip);
        });
        
        // Gráfico de incidentes por día
        var ctx = document.getElementById('incidentsChart').getContext('2d');
        var incidentsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($report['dates']),
                datasets: [{
                    label: 'Incidentes',
                    data: @json($report['incidents_by_date']),
                    backgroundColor: 'rgba(78, 115, 223, 0.2)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de incidentes por tipo
        var typeLabels = [];
        var typeData = [];
        var typeColors = [];
        
        @foreach($report['incidents_by_type'] as $type => $count)
            typeLabels.push("{{ $type }}");
            typeData.push({{ $count }});
            typeColors.push("{{ $chartColors[min(array_key_last($chartColors), count($typeLabels) - 1)] }}");
        @endforeach
        
        var typeCtx = document.getElementById('incidentsByTypeChart').getContext('2d');
        var incidentsByTypeChart = new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeData,
                    backgroundColor: typeColors,
                    hoverOffset: 4
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
        
        // Gráfico de incidentes por severidad
        var severityLabels = [];
        var severityData = [];
        var severityColors = [];
        
        @foreach($report['incidents_by_severity'] as $severity => $count)
            severityLabels.push("{{ $severity }}");
            severityData.push({{ $count }});
            severityColors.push("{{ $severityColors[$severity] ?? $chartColors[0] }}");
        @endforeach
        
        var severityCtx = document.getElementById('incidentsBySeverityChart').getContext('2d');
        var incidentsBySeverityChart = new Chart(severityCtx, {
            type: 'pie',
            data: {
                labels: severityLabels,
                datasets: [{
                    data: severityData,
                    backgroundColor: severityColors,
                    hoverOffset: 4
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
    });
</script>
@endsection
