<?php

namespace App\Services\Payments;

interface PaymentGatewayInterface
{
    /**
     * Procesar un pago
     * 
     * @param array $paymentData Datos del pago
     * @return array Resultado del procesamiento
     */
    public function processPayment(array $paymentData): array;
    
    /**
     * Verificar el estado de un pago
     * 
     * @param string $transactionId ID de la transacción
     * @return array Estado del pago
     */
    public function checkPaymentStatus(string $transactionId): array;
    
    /**
     * Reembolsar un pago
     * 
     * @param string $transactionId ID de la transacción
     * @param float|null $amount Monto a reembolsar (null para reembolso total)
     * @return array Resultado del reembolso
     */
    public function refundPayment(string $transactionId, ?float $amount = null): array;
    
    /**
     * Obtener métodos de pago disponibles
     * 
     * @return array Métodos de pago
     */
    public function getAvailablePaymentMethods(): array;
}
