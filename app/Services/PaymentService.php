<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PaymentService
{
    public function __construct()
    {
        MercadoPagoConfig::setAccessToken(
            config('services.mercadopago.access_token')
        );

        if (config('services.mercadopago.sandbox')) {
            MercadoPagoConfig::setRuntimeEnviroment(
                MercadoPagoConfig::LOCAL
            );
        }
    }

    // ── Pix ───────────────────────────────────────────────────

    public function processPixPayment(Order $order): array
    {
        try {
            $client = new PaymentClient();

            $payment = $client->create([
                'transaction_amount' => $order->total / 100, // MP usa reais, não centavos
                'description'        => "Ingressos — {$order->event->title}",
                'payment_method_id'  => 'pix',
                'payer'              => [
                    'email'             => $order->user->email,
                    'first_name'        => $this->firstName($order->user->name),
                    'last_name'         => $this->lastName($order->user->name),
                    'identification'    => [
                        'type'   => strtoupper($order->user->profile_type ?? 'cpf'),
                        'number' => $order->user->document_number,
                    ],
                ],
                'external_reference' => $order->reference,
                'notification_url'   => route('webhooks.mercadopago'),
            ]);

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

    // ── Webhook ───────────────────────────────────────────────

    public function validateWebhookSignature(
        string $payload,
        string $signatureHeader,
        string $requestId = ''
    ): bool {
        // Formato do header: ts=<timestamp>,v1=<hash>
        preg_match('/ts=(\d+)/', $signatureHeader, $tsMatch);
        preg_match('/v1=([a-f0-9]+)/', $signatureHeader, $hashMatch);

        if (empty($tsMatch[1]) || empty($hashMatch[1])) {
            return false;
        }

        $ts       = $tsMatch[1];
        $received = $hashMatch[1];
        $secret   = config('services.mercadopago.webhook_secret', '');

        $manifest = "id:{$requestId};request-id:{$requestId};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        // Em sandbox, aceita qualquer assinatura para facilitar testes
        if (config('services.mercadopago.sandbox')) {
            return true;
        }

        return hash_equals($expected, $received);
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
            Log::error('MercadoPago getStatus error', ['message' => $e->getMessage()]);
            return 'pending';
        }
    }

    // ── Helpers ───────────────────────────────────────────────

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