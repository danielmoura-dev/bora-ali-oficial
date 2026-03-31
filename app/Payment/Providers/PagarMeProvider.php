<?php

namespace App\Payment\Providers;

use App\Models\Order;
use App\Payment\Contracts\PaymentProviderInterface;

class PagarMeProvider implements PaymentProviderInterface
{
    public function buildPixPayload(Order $order): array
    {
        // TODO: implementar quando CNPJ estiver disponível
        throw new \RuntimeException('Pagar.me ainda não implementado.');
    }

    public function processPixPayment(Order $order): array
    {
        throw new \RuntimeException('Pagar.me ainda não implementado.');
    }

    public function getPaymentStatus(string $paymentId): string
    {
        throw new \RuntimeException('Pagar.me ainda não implementado.');
    }

    public function validateWebhookSignature(
        string $payload,
        string $signature,
        string $requestId = ''
    ): bool {
        throw new \RuntimeException('Pagar.me ainda não implementado.');
    }
}