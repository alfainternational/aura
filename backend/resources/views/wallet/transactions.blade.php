@extends('layouts.app')

@section('title', 'Historial de Transacciones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i> Historial de Transacciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form action="{{ route('wallet.transactions') }}" method="GET" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="type" class="mr-2">Tipo:</label>
                                    <select name="type" id="type" class="form-control form-control-sm">
                                        <option value="">Todos</option>
                                        <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Depósitos</option>
                                        <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Retiros</option>
                                        <option value="payment" {{ request('type') == 'payment' ? 'selected' : '' }}>Pagos</option>
                                        <option value="refund" {{ request('type') == 'refund' ? 'selected' : '' }}>Reembolsos</option>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="status" class="mr-2">Estado:</label>
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="">Todos</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completadas</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Fallidas</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazadas</option>
                                    </select>
                                </div>
                                <div class="form-group mr-3">
                                    <label for="per_page" class="mr-2">Mostrar:</label>
                                    <select name="per_page" id="per_page" class="form-control form-control-sm">
                                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($transaction->type == 'deposit')
                                            <span class="badge badge-success">Depósito</span>
                                        @elseif($transaction->type == 'withdrawal')
                                            <span class="badge badge-warning">Retiro</span>
                                        @elseif($transaction->type == 'payment')
                                            <span class="badge badge-info">Pago</span>
                                        @elseif($transaction->type == 'refund')
                                            <span class="badge badge-primary">Reembolso</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $transaction->type }}</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($transaction->description, 30) }}</td>
                                    <td class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td>
                                        @if($transaction->status == 'completed')
                                            <span class="badge badge-success">Completada</span>
                                        @elseif($transaction->status == 'pending')
                                            <span class="badge badge-warning">Pendiente</span>
                                        @elseif($transaction->status == 'failed')
                                            <span class="badge badge-danger">Fallida</span>
                                        @elseif($transaction->status == 'rejected')
                                            <span class="badge badge-danger">Rechazada</span>
                                        @else
                                            <span class="badge badge-secondary">{{ $transaction->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('wallet.transaction.details', $transaction->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                            <p>No se encontraron transacciones con los filtros seleccionados.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $transactions->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
