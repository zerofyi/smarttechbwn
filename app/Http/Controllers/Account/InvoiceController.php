<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function index()
    {
        $orders = auth()->user()->orders()->latest()->paginate(10);

        return view('account.invoices.index', compact('orders'));
    }

    public function download($order)
    {
        return $this->pdf($order);
    }

    /**
     * Alias used by the account.orders.invoice route
     * (GET /account/orders/{order}/invoice) — same PDF download.
     */
    public function show($order)
    {
        return $this->pdf($order);
    }

    protected function pdf($order)
    {
        $order = auth()->user()->orders()
            ->where('order_id', (int) $order)
            ->first();

        if (! $order) {
            abort(404);
        }

        return (new InvoiceService)->pdf($order);
    }
}
