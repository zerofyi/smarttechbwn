@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">Invoices</h2>
    <p class="text-sm text-gray-500 -mt-3">Download GST invoices for your orders. Invoices show an 18% GST breakup (CGST 9% + SGST 9%).</p>

    @if(($orders ?? collect())->isEmpty())
        <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
            <i class="fas fa-file-invoice text-3xl text-gray-300 mb-2"></i>
            <p class="text-sm text-gray-500">No invoices available yet. Invoices appear once you place an order.</p>
            <a href="{{ route('products') }}" class="inline-block mt-3 px-5 py-2 bg-red-800 text-white text-sm font-semibold rounded-lg hover:bg-red-900">Browse Products</a>
        </div>
    @else
        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b bg-gray-50">
                            <th class="py-3 px-4 font-semibold">Order</th>
                            <th class="py-3 px-4 font-semibold">Date</th>
                            <th class="py-3 px-4 font-semibold">Total</th>
                            <th class="py-3 px-4 font-semibold">Status</th>
                            <th class="py-3 px-4 font-semibold text-right">Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b last:border-0">
                                <td class="py-3 px-4 font-semibold text-gray-800">{{ $order->order_number ?? '' }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ optional($order->created_at)->format('d M Y') ?? '' }}</td>
                                <td class="py-3 px-4 font-semibold">&#8377;{{ number_format($order->total ?? 0, 2) }}</td>
                                <td class="py-3 px-4"><span class="text-xs font-semibold px-2 py-1 rounded-full bg-gray-100 text-gray-700 capitalize">{{ $order->status ?? '' }}</span></td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('account.invoices.download', $order->order_id ?? $order->id ?? '') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-800 text-white text-xs font-bold rounded-lg hover:bg-red-900">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($orders, 'links'))
            <div class="mt-6">{{ $orders->links() }}</div>
        @endif
    @endif
</div>
@endsection
