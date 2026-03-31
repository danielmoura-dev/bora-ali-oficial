<?php

namespace App\Payment;

use App\Models\Event;
use App\Payment\Contracts\PaymentProviderInterface;
use App\Payment\Providers\MercadoPagoProvider;
use App\Payment\Providers\PagarMeProvider;

class PaymentManager
{
    public function for(Event $event): PaymentProviderInterface
    {
        return match ($event->payment_provider) {
            'mercadopago' => new MercadoPagoProvider(),
            'pagarme'     => new PagarMeProvider(),
            default       => throw new \InvalidArgumentException(
                "Provider desconhecido: {$event->payment_provider}"
            ),
        };
    }

    public static function availableProviders(): array
    {
        return [
            'mercadopago' => [
                'label'   => 'Mercado Pago',
                'methods' => ['pix'],
                'active'  => true,
            ],
            'pagarme' => [
                'label'   => 'Pagar.me',
                'methods' => ['pix', 'credit_card'],
                'active'  => false, // ativa quando CNPJ disponível
            ],
        ];
    }

    public static function availableModes(): array
    {
        return [
            'direct' => 'Direto na plataforma (sem necessidade de conta no MP)',
            'split'  => 'Split automático (organizador recebe direto)',
        ];
    }
}