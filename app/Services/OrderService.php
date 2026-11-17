<?php

namespace App\Services;

use App\Mail\OrderStatusMail;
use App\Models\Inventory;
use App\Models\OrderUpdate;
use App\Models\User;
use App\Models\UserCart;
use App\Models\UserOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class OrderService
{
    public const STATUSES = ['placed', 'confirmed', 'shipped', 'delivered', 'cancelled'];

    /**
     * Allowed status transitions. Cancel is allowed until the order ships.
     */
    protected const ALLOWED_TRANSITIONS = [
        'placed' => ['confirmed', 'cancelled'],
        'confirmed' => ['shipped', 'cancelled'],
        'shipped' => ['delivered'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function __construct(
        protected CartService $cartService,
    ) {}

    public function canCancel(UserOrder $order): bool
    {
        return in_array($order->status, ['placed', 'confirmed'], true);
    }

    public function canReturn(UserOrder $order): bool
    {
        if ($order->status !== 'delivered' || ! $order->delivered_at) {
            return false;
        }

        $windowDays = (int) config('shop.return_window_days', 7);

        return $order->delivered_at->greaterThanOrEqualTo(now()->subDays($windowDays));
    }

    /**
     * Cancel an order: restock every item, mark it cancelled, record the
     * timeline entry, and notify the customer. Mail failures never break it.
     */
    public function cancel(UserOrder $order, ?User $user = null, ?string $notes = null): UserOrder
    {
        if (! $this->canCancel($order)) {
            throw ValidationException::withMessages([
                'status' => 'This order can no longer be cancelled.',
            ]);
        }

        DB::transaction(function () use ($order, $user, $notes) {
            $order->loadMissing('items');

            foreach ($order->items as $item) {
                Inventory::where('inventory_id', $item->inventory_id)
                    ->increment('stock', (int) $item->quantity);
            }

            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->save();

            OrderUpdate::create([
                'order_id' => $order->order_id,
                'status' => 'cancelled',
                'notes' => $notes ?? 'Order cancelled.',
                'updated_by' => $user?->user_id ?? $order->user_id,
            ]);
        });

        $this->sendStatusMail($order, 'cancelled', $notes);

        return $order->refresh();
    }

    /**
     * Move an order to a new status, validating the allowed transition,
     * stamping the matching *_at timestamp, recording the timeline
     * entry, and notifying the customer.
     */
    public function transition(UserOrder $order, string $status, ?string $notes = null, ?User $actor = null): UserOrder
    {
        if (! in_array($status, self::STATUSES, true)) {
            throw ValidationException::withMessages([
                'status' => 'Unknown order status: '.$status,
            ]);
        }

        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? [];

        if (! in_array($status, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'Order cannot move from '.$order->status.' to '.$status.'.',
            ]);
        }

        if ($status === 'cancelled') {
            return $this->cancel($order, $actor, $notes);
        }

        DB::transaction(function () use ($order, $status, $notes, $actor) {
            $order->status = $status;

            match ($status) {
                'confirmed' => $order->confirmed_at = now(),
                'shipped' => $order->shipped_at = now(),
                'delivered' => $order->delivered_at = now(),
                default => null,
            };

            $order->save();

            OrderUpdate::create([
                'order_id' => $order->order_id,
                'status' => $status,
                'notes' => $notes,
                'updated_by' => $actor?->user_id ?? $order->user_id,
            ]);
        });

        $this->sendStatusMail($order, $status, $notes);

        return $order->refresh();
    }

    /**
     * Re-add an order's in-stock items to the customer's cart.
     * Out-of-stock items are skipped. Returns the customer's cart.
     */
    public function reorder(UserOrder $order): UserCart
    {
        $user = $order->user;

        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $inventory = Inventory::find($item->inventory_id);

            if (! $inventory || (int) $inventory->stock <= 0) {
                continue;
            }

            $this->cartService->addToCart(
                $item->inventory_id,
                min((int) $item->quantity, (int) $inventory->stock),
                $user
            );
        }

        return $this->cartService->getOrCreateDbCart($user);
    }

    /**
     * Record a return/replacement request (request only — no auto-refund).
     * The order stays delivered; the request is an OrderUpdate with
     * status return_requested.
     */
    public function requestReturn(UserOrder $order, string $reason): OrderUpdate
    {
        if (! $this->canReturn($order)) {
            throw ValidationException::withMessages([
                'status' => 'This order is no longer eligible for a return request.',
            ]);
        }

        $update = OrderUpdate::create([
            'order_id' => $order->order_id,
            'status' => 'return_requested',
            'notes' => $reason,
            'updated_by' => $order->user_id,
        ]);

        $this->sendStatusMail($order, 'return_requested', $reason);

        return $update;
    }

    /**
     * Notify the customer about a status change. Never throws.
     */
    protected function sendStatusMail(UserOrder $order, string $status, ?string $note = null): void
    {
        try {
            $order->loadMissing('user');

            if (! $order->user || ! $order->user->email) {
                return;
            }

            Mail::to($order->user->email)->send(new OrderStatusMail($order, $status, $note));
        } catch (Throwable) {
            // Mail must never break order handling.
        }
    }
}
