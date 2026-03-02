@php
    $settings = $page['props']['settings'] ?? [];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- BASIC SEO -->
    <title inertia>{{ $settings['title_web'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $settings['des_web'] ?? '' }}">
    <meta name="keywords" content="{{ $settings['meta_keywords'] ?? '' }}">
    <meta name="author" content="{{ $settings['site_name'] ?? '' }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ $settings['favicon'] ?? '/favicon.ico' }}">

    <!-- GOOGLE VERIFY -->
    @if (!empty($settings['google_site_verification']))
        <meta name="google-site-verification" content="{{ $settings['google_site_verification'] }}">
    @endif

    <!-- OPEN GRAPH (Facebook, Zalo, Telegram) -->
    <meta property="og:title" content="{{ $settings['og_title'] ?? ($settings['title_web'] ?? '') }}">
    <meta property="og:description" content="{{ $settings['og_description'] ?? ($settings['des_web'] ?? '') }}">
    <meta property="og:image" content="{{ $settings['og_image'] ?? ($settings['image_seo'] ?? '') }}">
    <meta property="og:url" content="{{ $settings['og_url'] ?? url()->current() }}">
    <meta property="og:type" content="{{ $settings['og_type'] ?? 'website' }}">
    <meta property="og:site_name" content="{{ $settings['site_name'] ?? '' }}">

    <!-- TWITTER -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $settings['og_title'] ?? '' }}">
    <meta name="twitter:description" content="{{ $settings['og_description'] ?? '' }}">
    <meta name="twitter:image" content="{{ $settings['og_image'] ?? '' }}">

    {{-- GOOGLE ANALYTICS --}}
    @if (!empty($settings['google_analytics_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_analytics_id'] }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $settings['google_analytics_id'] }}');
        </script>
    @endif


    {{-- GOOGLE TAG MANAGER --}}
    @if (!empty($settings['google_tag_manager_id']))
        <script>
            (function(w, d, s, l, i) {
                w[l] = w[l] || [];
                w[l].push({
                    'gtm.start': new Date().getTime(),
                    event: 'gtm.js'
                });
                var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s),
                    dl = l != 'dataLayer' ? '&l=' + l : '';
                j.async = true;
                j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
                f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '{{ $settings['google_tag_manager_id'] }}');
        </script>
    @endif
    {{-- GOOGLE ADS --}}
    @if (!empty($settings['google_ads_id']))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['google_ads_id'] }}"></script>
        <script>
            gtag('config', '{{ $settings['google_ads_id'] }}');
        </script>
    @endif
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    {{-- GTM NOSCRIPT --}}
    @if (!empty($settings['google_tag_manager_id']))
        <noscript>
            <iframe src="https://www.googletagmanager.com/ns.html?id={{ $settings['google_tag_manager_id'] }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
        </noscript>
    @endif
    @inertia
</body>

</html>
