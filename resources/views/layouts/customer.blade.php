<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @env('production')
        <script defer src="https://analytics.smarttechbwn.in/script.js" data-website-id="3fbaa426-2876-4da2-bee0-1e424abbf24d"></script>
    @endenv
    <style>
        :root {
            --primary-color: #365ffa;
        }
        .product-card {
            transition: all 0.3s ease;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .banner-slide {
            min-width: 100%;
            transition: all 0.5s ease;
        }
        .banner-container {
            scroll-behavior: smooth;
        }
        .scroll-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scroll-container::-webkit-scrollbar {
            display: none;
        }
        .desktop-container {
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    @include('customer.components.header')

    <main class="pt-16 pb-20 md:pb-0">
        @yield('content')
    </main>

    @include('components.customer.footer')
    @include('customer.components.navigation')

    @yield('scripts')
</body>
</html>
