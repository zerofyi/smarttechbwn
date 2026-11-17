<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\UserOrder;
use Illuminate\Support\Facades\Http;
use Throwable;

class PaymentService
{
    /**
     * Razorpay is enabled only when a key is configured. An empty key
     * means COD-only checkout; nothing here may crash in that state.
     */
    public function enabled(): bool
    {
        return (string) config('shop.razorpay.key', '') !== '';
    }

    public function isRazorpayEnabled(): bool
    {
        return $this->enabled();
    }

    /**
     * Create a Razorpay order for a placed UserOrder via plain HTTP
     * (no Razorpay PHP SDK is installed). Returns the decoded Razorpay
     * order array, or null when Razorpay is disabled or the call fails.
     * Never throws.
     */
    public function createRazorpayOrder(UserOrder $order): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        try {
            $response = Http::withBasicAuth(
                (string) config('shop.razorpay.key'),
                (string) config('shop.razorpay.secret')
            )
                ->timeout(15)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => (int) round((float) $order->total * 100),
                    'currency' => 'INR',
                    'receipt' => $order->order_number,
                    'notes' => [
                        'order_id' => $order->order_id,
                        'order_number' => $order->order_number,
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return is_array($data) ? $data : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Verify a Razorpay checkout signature: HMAC-SHA256 of
     * "razorpay_order_id|razorpay_payment_id" with the secret key.
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = $payload['razorpay_order_id'] ?? null;
        $paymentId = $payload['razorpay_payment_id'] ?? null;
        $signature = $payload['razorpay_signature'] ?? null;
        $secret = (string) config('shop.razorpay.secret', '');

        if (! $orderId || ! $paymentId || ! $signature || $secret === '') {
            return false;
        }

        $expected = hash_hmac('sha256', $orderId.'|'.$paymentId, $secret);

        return hash_equals($expected, (string) $signature);
    }

    /**
     * Record the pending COD transaction for an order and link it.
     * If the order already has a transaction, return that one.
     */
    public function recordCodTransaction(UserOrder $order): Transaction
    {
        if ($order->transaction_id) {
            $existing = Transaction::find($order->transaction_id);

            if ($existing) {
                return $existing;
            }
        }

        $transaction = Transaction::create([
            'trz_number' => 'TXN-'.now()->format('Ymd').'-'.str_pad((string) $order->order_id, 6, '0', STR_PAD_LEFT),
            'trz_amount' => $order->total,
            'trz_date' => now()->toDateString(),
            'method' => 'cod',
            'trz_id' => $order->order_number,
            'ref_id' => (string) $order->order_id,
            'status' => 'pending',
            'remarks' => 'Cash on Delivery — Order '.$order->order_number,
            'response' => null,
        ]);

        $order->update(['transaction_id' => $transaction->transaction_id]);

        return $transaction;
    }
}
