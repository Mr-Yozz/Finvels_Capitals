<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Loan Management System | Fast, Secure & Easy Loans')</title>

    <meta name="description" content="@yield('description', 'A secure and user-friendly loan management system for fast loan processing, easy EMI tracking, repayment reminders, and complete digital loan handling.')">

    <meta name="keywords" content="loan management, loan software, EMI tracking, finance software, loan application, personal loan, digital loans, microfinance software">

    <meta name="author" content="FinVels">

    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook / LinkedIn -->
    <meta property="og:title" content="@yield('title', 'Loan Management System')">
    <meta property="og:description" content="@yield('description', 'Fast loan approvals and easy EMI tracking with our digital loan management system.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ asset('images/seo/og-image.jpg') }}">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Loan Management System')">
    <meta name="twitter:description" content="@yield('description', 'Easy loan processing, repayments, and customer management.')">
    <meta name="twitter:image" content="{{ asset('images/seo/twitter-image.jpg') }}">


    <!-- Favicon -->
    <!-- Favicon Set -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('images/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#ffffff">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500&family=Jost:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{asset('lib/animate/animate.min.css')}}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">
    <link href="{{asset('lib/lightbox/css/lightbox.min.css')}}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    <!-- Schema (JSON-LD Structured Data) -->
    <!-- <script type="application/ld+json">
        {{--
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "Loan Management System",
            "description": "Complete digital loan management solution with fast approvals, EMI tracking, borrower profiles, and secure repayments.",
            "url": "{{ url('/') }}",
            "applicationCategory": "FinanceApplication",
            "operatingSystem": "Web",
            "creator": {
                "@type": "Organization",
                "name": "FinVels",
                "url": "{{ url('/') }}"
            }
        --}}
    </script> -->
</head>