<?php

namespace App\Payment\Providers;

use App\Models\Order;
use App\Payment\Contracts\PaymentProviderInterface;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoProvider implements PaymentProviderInterface
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(
            config('services.mercadopago.access_token')
        );

        if (config('services.mercadopago.sandbox')) {
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        }
    }

    public function buildPixPayload(Order $order): array
    {
        $payload = [
            'transaction_amount' => $order->total / 100,
            'description'        => "Ingressos — {$order->event->title}",
            'payment_method_id'  => 'pix',
            'payer'              => [
                'email'          => $order->user->email,
                'first_name'     => $this->firstName($order->user->name),
                'last_name'      => $this->lastName($order->user->name),
                'identification' => [
                    'type'   => strtoupper($order->user->profile_type ?? 'cpf'),
                    'number' => $order->user->document_number,
                ],
            ],
            'external_reference' => $order->reference,
            'notification_url'   => route('webhooks.mercadopago'),
        ];

        // Split só se o evento configurou E organizador tem MP conectado
        if ($order->event->usesSplit()) {
            $organizer = $order->event->organizer;

            if ($organizer->hasMpConnected() && $organizer->isMpTokenValid()) {
                $payload['application_fee'] = $order->platform_fee;
                $payload['marketplace_fee'] = $order->platform_fee;
                $payload['metadata']        = [
                    'organizer_mp_user_id' => $organizer->mp_user_id,
                    'platform_fee_cents'   => $order->platform_fee,
                    'mode'                 => 'split',
                ];
            }
        } else {
            $payload['metadata'] = [
                'mode'               => 'direct',
                'platform_fee_cents' => $order->platform_fee,
            ];
        }

        return $payload;
    }

    public function processPixPayment(Order $order): array
    {
        try {
            $client  = new PaymentClient();
            $payload = $this->buildPixPayload($order);
            $payment = $client->create($payload);

            if ($payment->status === 'pending') {
                $txInfo = $payment->point_of_interaction->transaction_data;

                return [
                    'status'         => 'pending_pix',
                    'payment_id'     => (string) $payment->id,
                    'pix_qrcode'     => $txInfo->qr_code_base64 ?? null,
                    'pix_copy_paste' => $txInfo->qr_code ?? null,
                ];
            }

            return ['status' => 'failed', 'message' => 'Não foi possível gerar o Pix.'];

        } catch (\Exception $e) {
            Log::error('MercadoPago Pix error', [
                'order'   => $order->reference,
                'message' => $e->getMessage(),
            ]);

            return ['status' => 'failed', 'message' => $e->getMessage()];
        }
    }

    public function getPaymentStatus(string $paymentId): string
    {
        try {
            $client  = new PaymentClient();
            $payment = $client->get((int) $paymentId);

            return match ($payment->status) {
                'approved' => 'paid',
                'rejected' => 'failed',
                'cancelled'=> 'cancelled',
                default    => 'pending',
            };
        } catch (\Exception $e) {
            Log::error('MP getStatus error', ['message' => $e->getMessage()]);
            return 'pending';
        }
    }

    public function validateWebhookSignature(
        string $payload,
        string $signature,
        string $requestId = '',
        string $dataId = ''
    ): bool {
        if (config('services.mercadopago.sandbox')) {
            return true;
        }

        preg_match('/ts=(\d+)/', $signature, $tsMatch);
        preg_match('/v1=([a-f0-9]+)/', $signature, $hashMatch);

        if (empty($tsMatch[1]) || empty($hashMatch[1])) {
            return false;
        }

        $secret   = config('services.mercadopago.webhook_secret', '');
        $manifest = "id:{$dataId};request-id:{$requestId};ts:{$tsMatch[1]};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $hashMatch[1]);
    }

    private function firstName(string $name): string
    {
        return explode(' ', trim($name))[0];
    }

    private function lastName(string $name): string
    {
        $parts = explode(' ', trim($name));
        return count($parts) > 1 ? end($parts) : $parts[0];
    }
}