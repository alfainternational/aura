@extends('layouts.app')

@section('title', 'Retirar Fondos')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-minus-circle mr-2"></i> Retirar Fondos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <strong>Saldo Disponible:</strong>
                                <h4 class="mb-0">{{ number_format($balance, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('wallet.withdraw.process') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="amount">Monto a Retirar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" min="1" max="{{ $balance }}" step="0.01" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Ingrese el monto que desea retirar de su billetera. El monto máximo disponible es {{ number_format($balance, 2) }}.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="withdrawal_method">Método de Retiro <span class="text-danger">*</span></label>
                            <select class="form-control @error('withdrawal_method') is-invalid @enderror" id="withdrawal_method" name="withdrawal_method" required>
                                <option value="">Seleccione un método de retiro</option>
                                <option value="bank_transfer" {{ old('withdrawal_method') == 'bank_transfer' ? 'selected' : '' }}>Transferencia Bancaria</option>
                                <option value="paypal" {{ old('withdrawal_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="credit_card" {{ old('withdrawal_method') == 'credit_card' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                            </select>
                            @error('withdrawal_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="account_details">Detalles de la Cuenta <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('account_details') is-invalid @enderror" id="account_details" name="account_details" rows="3" required>{{ old('account_details') }}</textarea>
                            @error('account_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted" id="account_details_help">
                                Proporcione los detalles necesarios para procesar su retiro.
                            </small>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Solicitar Retiro
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('wallet.index') }}" class="btn btn-link">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a Mi Billetera
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Información Importante</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Las solicitudes de retiro se procesan en un plazo de 1-3 días hábiles.</li>
                        <li>El monto mínimo de retiro es de $10.00.</li>
                        <li>Para retiros mayores a $1,000, se puede requerir verificación adicional.</li>
                        <li>Es posible que se apliquen comisiones según el método de retiro seleccionado.</li>
                        <li>Si tiene problemas con su retiro, contacte a nuestro servicio de soporte.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Actualizar el texto de ayuda según el método de retiro seleccionado
        $('#withdrawal_method').change(function() {
            var selectedMethod = $(this).val();
            var helpText = '';
            
            switch(selectedMethod) {
                case 'bank_transfer':
                    helpText = 'Ingrese el nombre del banco, número de cuenta, nombre del titular y código SWIFT/BIC si es internacional.';
                    break;
                case 'paypal':
                    helpText = 'Ingrese su dirección de correo electrónico asociada a su cuenta de PayPal.';
                    break;
                case 'credit_card':
                    helpText = 'Ingrese los últimos 4 dígitos de su tarjeta, nombre del titular y banco emisor.';
                    break;
                default:
                    helpText = 'Proporcione los detalles necesarios para procesar su retiro.';
            }
            
            $('#account_details_help').text(helpText);
        });
        
        // Disparar el evento change para configurar el texto inicial
        $('#withdrawal_method').trigger('change');
    });
</script>
@endpush
@endsection
