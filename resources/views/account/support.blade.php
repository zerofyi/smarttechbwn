@extends('account.layout')

@section('account')
<div class="space-y-6">
    <h2 class="text-xl font-bold" style="color: #6B1A00;">Support</h2>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-2" style="color: #6B1A00;">Need help with an order?</h3>
        <p class="text-sm text-gray-600">
            For questions about a specific order &mdash; delivery, cancellation, returns or refunds &mdash;
            please open the order and use the actions there, or contact the store and mention your order number.
        </p>
        <a href="{{ route('account.orders.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-red-800 text-white text-sm font-bold rounded-lg hover:bg-red-900">View My Orders</a>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-3" style="color: #6B1A00;">Common topics</h3>
        <ul class="space-y-3 text-sm text-gray-600">
            <li class="flex gap-2"><i class="fas fa-truck text-red-800 mt-0.5"></i><span><strong class="text-gray-800">Delivery:</strong> free shipping on orders over &#8377;999, otherwise a flat &#8377;49. Track progress from the order timeline on your order page.</span></li>
            <li class="flex gap-2"><i class="fas fa-ban text-red-800 mt-0.5"></i><span><strong class="text-gray-800">Cancellation:</strong> you can cancel an order until it is shipped, from the order page.</span></li>
            <li class="flex gap-2"><i class="fas fa-rotate-left text-red-800 mt-0.5"></i><span><strong class="text-gray-800">Returns:</strong> return or replacement requests can be raised within 7 days of delivery from the order page.</span></li>
            <li class="flex gap-2"><i class="fas fa-file-invoice text-red-800 mt-0.5"></i><span><strong class="text-gray-800">Invoices:</strong> download GST invoices anytime from the Invoices section.</span></li>
            <li class="flex gap-2"><i class="fas fa-indian-rupee-sign text-red-800 mt-0.5"></i><span><strong class="text-gray-800">Payments:</strong> we accept Cash on Delivery and online payment via Razorpay.</span></li>
        </ul>
    </div>

    <div class="bg-white rounded-xl border shadow-sm p-6">
        <h3 class="font-bold text-base mb-2" style="color: #6B1A00;">Contact the store</h3>
        <p class="text-sm text-gray-600">
            Support requests go via the store. Please contact the store directly and include your order number (if any) so the team can help you quickly.
        </p>
        <div class="flex flex-wrap gap-2 mt-4">
            <a href="{{ route('account.orders.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">My Orders</a>
            <a href="{{ route('account.profile') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">My Profile</a>
        </div>
    </div>
</div>
@endsection
