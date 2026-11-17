<?php

namespace App\Services;

use App\Models\UserOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Invoice number for an online order is its order number.
     * (No POS Invoice row is created for online orders in v1.)
     */
    public function invoiceNumber(UserOrder $order): string
    {
        return (string) $order->order_number;
    }

    /**
     * Data for the invoice view / PDF.
     *
     * Prices are GST-inclusive at 18%: tax is the GST component inside
     * the order subtotal, split equally into CGST 9% and SGST 9%.
     */
    public function dataForOrder(UserOrder $order): array
    {
        $order->loadMissing(['user', 'address', 'items.inventory.product']);

        $tax = round((float) $order->tax, 2);
        $cgst = round($tax / 2, 2);
        $sgst = round($tax - $cgst, 2);

        return [
            'order' => $order,
            'user' => $order->user,
            'address' => $order->address,
            'items' => $order->items,
            'subtotal' => round((float) $order->subtotal, 2),
            'tax' => $tax,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'shipping' => round((float) $order->shipping, 2),
            'discount' => round((float) $order->discount, 2),
            'total' => round((float) $order->total, 2),
            'invoice_no' => $this->invoiceNumber($order),
            'invoice_date' => $order->created_at,
        ];
    }

    /**
     * Download the invoice PDF for an order.
     */
    public function pdf(UserOrder $order)
    {
        return Pdf::loadView('account.invoices.pdf', $this->dataForOrder($order))
            ->download('invoice-'.$order->order_number.'.pdf');
    }
}
