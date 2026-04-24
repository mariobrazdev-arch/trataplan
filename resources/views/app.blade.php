<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#022c22">
    <meta name="google-site-verification" content="cZi4cL1B8eTbbr28-TSq943Sgyo36Y26zGw3iwRAhJY">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="512x512" href="/android-chrome-512x512.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap">

    <!-- JSON-LD: LocalBusiness structured data -->
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "@id": "https://trataplan.com.br/#business",
        "name": "Trataplan – Madeira Tratada",
        "description": "Referência em tratamento de madeiras no Oeste Catarinense. Eucalipto e Pinus Autoclavado para construção civil, cercas, decks e paisagismo.",
        "url": "https://trataplan.com.br",
        "logo": "https://trataplan.com.br/og-default.jpg",
        "image": "https://trataplan.com.br/og-default.jpg",
        "telephone": "+55-49-9960-0329",
        "email": "trataplan@trataplan.com.br",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "SC-283",
            "addressLocality": "Planalto Alegre",
            "addressRegion": "SC",
            "postalCode": "89882-000",
            "addressCountry": "BR"
        },
        "geo": {
            "@type": "GeoCoordinates",
            "latitude": "-26.9527",
            "longitude": "-52.8176"
        },
        "openingHoursSpecification": [
            {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday"],
                "opens": "08:00",
                "closes": "18:00"
            }
        ],
        "sameAs": [
            "https://www.instagram.com/trataplan",
            "https://www.facebook.com/trataplan"
        ],
        "priceRange": "$$",
        "currenciesAccepted": "BRL",
        "paymentAccepted": "Cash, Credit Card, Bank Transfer",
        "areaServed": {
            "@type": "State",
            "name": "Santa Catarina"
        }
    }
    </script>
    @endverbatim

    @inertiaHead

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @inertia
</body>
</html>
