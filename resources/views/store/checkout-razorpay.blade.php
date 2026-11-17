@extends('layouts.store')

@section('title', 'Pay Online')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="bg-white rounded-xl border shadow-sm p-8 text-center">
        <h1 class="text-2xl font-bold mb-2" style="color: #6B1A00;">Complete Your Payment</h1>
        <p class="text-sm text-gray-600 mb-6">
            Order <span class="font-semibold text-gray-800">{{ $order->order_number ?? '' }}</span>
            &middot; Amount <span class="font-semibold text-red-800">&#8377;{{ number_format($order->total ?? 0, 2) }}</span>
        </p>

        <div id="rzp-error" class="hidden mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm"></div>
        <div id="rzp-processing" class="hidden mb-4 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm">
            Verifying your payment&hellip; please wait.
        </div>

        <button id="rzp-retry" type="button" class="hidden px-6 py-3 bg-red-800 text-white font-bold rounded-xl hover:bg-red-900">
            Retry Payment
        </button>

        <a href="{{ route('checkout.index') }}" class="block mt-4 text-sm text-gray-500 hover:underline">Back to checkout</a>
        <a href="{{ route('account.orders.show', $order->order_id ?? $order->id ?? '') }}" class="block mt-2 text-sm text-red-800 font-semibold hover:underline">View Order</a>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    var errorBox = document.getElementById('rzp-error');
    var processingBox = document.getElementById('rzp-processing');
    var retryBtn = document.getElementById('rzp-retry');

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
        retryBtn.classList.remove('hidden');
    }

    function openRazorpay() {
        errorBox.classList.add('hidden');
        retryBtn.classList.add('hidden');

        var options = {
            key: @json($key ?? ''),
            amount: {{ (int) ($razorpayOrder['amount'] ?? 0) }},
            currency: 'INR',
            name: 'SmartTech',
            description: 'Order {{ $order->order_number ?? '' }}',
            order_id: @json($razorpayOrder['id'] ?? ''),
            prefill: {
                name: @json($order->user->name ?? ''),
                email: @json($order->user->email ?? ''),
                contact: @json($order->user->mobile ?? '')
            },
            theme: { color: '#6B1A00' },
            handler: function (response) {
                processingBox.classList.remove('hidden');
                fetch(@json(route('checkout.razorpay.verify')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': @json(csrf_token()),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
                .then(function (result) {
                    if (result.ok && result.data && result.data.success !== false) {
                        window.location.href = @json(route('checkout.success', $order->order_id ?? $order->id ?? ''));
                    } else {
                        processingBox.classList.add('hidden');
                        showError((result.data && result.data.message) || 'Payment verification failed. Please try again or contact the store.');
                    }
                })
                .catch(function () {
                    processingBox.classList.add('hidden');
                    showError('Could not verify your payment. If money was debited, please contact the store with your order number.');
                });
            },
            modal: {
                ondismiss: function () {
                    showError('Payment was cancelled. You can retry or choose Cash on Delivery at checkout.');
                }
            }
        };

        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            showError('Payment failed: ' + ((response.error && response.error.description) || 'Unknown error. Please try again.'));
        });
        rzp.open();
    }

    retryBtn.addEventListener('click', openRazorpay);
    window.addEventListener('load', openRazorpay);
})();
</script>
@endsection
