<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#0d6efd">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<link rel="apple-touch-icon" sizes="192x192" href="{{ asset('icons/icon-192.png') }}">
<script
    src="{{ asset('js/pwa.js') }}"
    data-service-worker-url="{{ asset('sw.js') }}"
    defer
></script>
