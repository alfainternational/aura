@extends('layouts.app')

@section('title', 'Mi Billetera')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-wallet mr-2"></i> Mi Billetera
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card bg-light h-100">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Saldo Disponible</h6>
                                    <h2 class="display-4 font-weight-bold text-primary">{{ number_format($balance, 2) }}</h2>
                                    <div class="mt-4">
                                        <a href="{{ route('wallet.deposit.form') }}" class="btn btn-success mr-2">
                                            <i class="fas fa-plus-circle"></i> Depositar
                                        </a>
                                        <a href="{{ route('wallet.withdraw.form') }}" class="btn btn-warning">
                                            <i class="fas fa-minus-circle"></i> Retirar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card bg-light h-100">
                                <div class="card-body">
                                    <h6 class="text-muted mb-4">Resumen de Transacciones</h6>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-success p-2 mr-3">
                                                    <i class="fas fa-arrow-down text-white"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-muted small">Total Depósitos</span>
                                                    <span class="font-weight-bold">{{ number_format($stats['total_deposits'] ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-warning p-2 mr-3">
                                                    <i class="fas fa-arrow-up text-white"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-muted small">Total Retiros</span>
                                                    <span class="font-weight-bold">{{ number_format($stats['total_withdrawals'] ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-info p-2 mr-3">
                                                    <i class="fas fa-shopping-cart text-white"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-muted small">Total Pagos</span>
                                                    <span class="font-weight-bold">{{ number_format($stats['total_payments'] ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary p-2 mr-3">
                                                    <i class="fas fa-undo text-white"></i>
                                                </div>
                                                <div>
                                                    <span class="d-block text-muted small">Total Reembolsos</span>
                                                    <span class="font-weight-bold">{{ number_format($stats['total_refunds'] ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Últimas Transacciones</h6>
                                    <a href="{{ route('wallet.transactions') }}" class="btn btn-sm btn-primary">
                                        Ver Todas
                                    </a>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
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
                                                            <p>No hay transacciones recientes.</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
