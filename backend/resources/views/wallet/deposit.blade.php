@extends('layouts.app')

@section('title', 'Depositar Fondos')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle mr-2"></i> Depositar Fondos
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('wallet.deposit.process') }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="amount">Monto a Depositar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" min="1" step="0.01" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Ingrese el monto que desea depositar en su billetera.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_method">Método de Pago <span class="text-danger">*</span></label>
                            <select class="form-control @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="">Seleccione un método de pago</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                                <option value="debit_card" {{ old('payment_method') == 'debit_card' ? 'selected' : '' }}>Tarjeta de Débito</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Transferencia Bancaria</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div id="payment_details" class="d-none">
                            <!-- Los campos específicos para cada método de pago se cargarán dinámicamente con JavaScript -->
                            <div id="credit_card_fields" class="payment-method-fields d-none">
                                <div class="form-group">
                                    <label for="card_number">Número de Tarjeta</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expiry_date">Fecha de Expiración</label>
                                            <input type="text" class="form-control" id="expiry_date" placeholder="MM/AA">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cvv">CVV</label>
                                            <input type="text" class="form-control" id="cvv" placeholder="123">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="bank_transfer_fields" class="payment-method-fields d-none">
                                <div class="alert alert-info">
                                    <p class="mb-0">
                                        <strong>Instrucciones para Transferencia Bancaria:</strong><br>
                                        Realice una transferencia a la siguiente cuenta bancaria:
                                    </p>
                                    <ul class="mt-2 mb-0">
                                        <li>Banco: Banco Nacional</li>
                                        <li>Titular: Aura Payments Inc.</li>
                                        <li>Cuenta: 1234-5678-9012-3456</li>
                                        <li>Referencia: Su ID de usuario ({{ auth()->id() }})</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-check-circle mr-2"></i> Continuar con el Depósito
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
                        <li>Los depósitos se procesan generalmente en menos de 24 horas.</li>
                        <li>El monto mínimo de depósito es de $1.00.</li>
                        <li>Para depósitos mayores a $10,000, se puede requerir verificación adicional.</li>
                        <li>Si tiene problemas con su depósito, contacte a nuestro servicio de soporte.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Mostrar/ocultar campos específicos según el método de pago seleccionado
        $('#payment_method').change(function() {
            var selectedMethod = $(this).val();
            
            // Ocultar todos los campos específicos
            $('.payment-method-fields').addClass('d-none');
            
            if (selectedMethod) {
                // Mostrar el contenedor principal
                $('#payment_details').removeClass('d-none');
                
                // Mostrar los campos específicos para el método seleccionado
                $('#' + selectedMethod + '_fields').removeClass('d-none');
            } else {
                // Ocultar el contenedor principal si no hay método seleccionado
                $('#payment_details').addClass('d-none');
            }
        });
        
        // Disparar el evento change para configurar la vista inicial
        $('#payment_method').trigger('change');
    });
</script>
@endpush
@endsection
