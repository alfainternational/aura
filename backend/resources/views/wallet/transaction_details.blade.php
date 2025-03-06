@extends('layouts.app')

@section('title', 'Detalles de Transacción')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt mr-2"></i> Detalles de Transacción #{{ $transaction->id }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Información General</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">ID de Transacción:</th>
                                            <td>{{ $transaction->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha:</th>
                                            <td>{{ $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipo:</th>
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
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
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
                                        </tr>
                                        <tr>
                                            <th>Monto:</th>
                                            <td class="{{ $transaction->amount > 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                                {{ number_format($transaction->amount, 2) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Descripción:</th>
                                            <td>{{ $transaction->description }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Detalles Adicionales</h6>
                                </div>
                                <div class="card-body">
                                    @if($transaction->transaction_details)
                                        <table class="table table-borderless">
                                            @foreach($transaction->transaction_details as $key => $value)
                                                @if(!is_array($value) && $key != 'rejection_reason')
                                                    <tr>
                                                        <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}:</th>
                                                        <td>{{ $value }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            
                                            @if(isset($transaction->transaction_details['payment_method']))
                                                <tr>
                                                    <th>Método de Pago:</th>
                                                    <td>{{ ucfirst($transaction->transaction_details['payment_method']) }}</td>
                                                </tr>
                                            @endif
                                            
                                            @if(isset($transaction->transaction_details['withdrawal_method']))
                                                <tr>
                                                    <th>Método de Retiro:</th>
                                                    <td>{{ ucfirst($transaction->transaction_details['withdrawal_method']) }}</td>
                                                </tr>
                                            @endif
                                            
                                            @if(isset($transaction->transaction_details['account_details']))
                                                <tr>
                                                    <th>Detalles de Cuenta:</th>
                                                    <td>{{ $transaction->transaction_details['account_details'] }}</td>
                                                </tr>
                                            @endif
                                        </table>
                                    @else
                                        <p class="text-muted">No hay detalles adicionales disponibles para esta transacción.</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($transaction->status == 'rejected' && isset($transaction->transaction_details['rejection_reason']))
                                <div class="card mb-4 border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0">Motivo de Rechazo</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ $transaction->transaction_details['rejection_reason'] }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if($transaction->status == 'pending' && $transaction->type == 'deposit')
                                <div class="card mb-4 border-warning">
                                    <div class="card-header bg-warning text-white">
                                        <h6 class="mb-0">Acciones Pendientes</h6>
                                    </div>
                                    <div class="card-body">
                                        <p>Esta transacción está pendiente de confirmación. Puedes completar el pago ahora.</p>
                                        <a href="{{ route('wallet.deposit.payment', $transaction->id) }}" class="btn btn-primary">
                                            <i class="fas fa-credit-card"></i> Completar Pago
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <a href="{{ route('wallet.transactions') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Volver al Historial
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
