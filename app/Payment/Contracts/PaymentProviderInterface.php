<?php

namespace App\Payment\Contracts;

use App\Models\Order;

interface PaymentProviderInterface
{
    public function buildPixPayload(Order $order): array;
    public function processPixPayment(Order $order): array;
    public function getPaymentStatus(string $paymentId): string;
    public function validateWebhookSignature(string $payload, string $signature, string $requestId = '', string $dataId = ''): bool;
}