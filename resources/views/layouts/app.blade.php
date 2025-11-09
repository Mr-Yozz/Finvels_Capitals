<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Loan Management') }}</title>
    <link rel="shortcut icon" href="{{ asset('images/finvels.jpeg') }}" type="image/x-icon">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/side.css')}}">

    <!-- Scripts -->
    <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
    <style>
        /* Sidebar Dropdown Hover Behavior */
        /* Sidebar Dropdown Hover */
        /* .sidebar {
            position: relative;
            z-index: 100;
           
        }

        .sidebar .dropdown {
            position: relative;
        }

        .sidebar .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            display: none;
            z-index: 1050;
           
            background-color: #212529;
            border: none;
            min-width: 180px;
            padding: 0;
        }

        .sidebar .dropdown:hover>.dropdown-menu {
            display: block;
        }

        .sidebar .dropdown-menu .dropdown-item {
            color: #fff;
            padding: 8px 15px;
        }

        .sidebar .dropdown-menu .dropdown-item:hover {
            background-color: #343a40;
        }*/

        .sidebar {
            /* position: relative; */
            z-index: 100;
            
        }

        .sidebar .dropdown:hover>.dropdown-menu {
            display: block;
            margin-top: 0;
        }

        .sidebar .dropdown-menu {
            background-color: #2412c6ff;
            border: none;
            padding: 0;
            left: 9.0rem;
            bottom: -32px;
            min-width: 180px;
        }

        .sidebar .dropdown-menu .dropdown-item {
            color: #fff;
            padding: 8px 15px;
        }

        .sidebar .dropdown-menu .dropdown-item:hover {
            background-color: #5055e6ff;
        }
    </style>
    @yield('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <nav class="navbar nav navbar-expand-lg shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/finvels.jpeg') }}" alt="Logo" class="logo-img">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left -->
                <ul class="navbar-nav me-auto"></ul>

                <!-- Right -->
                <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @if (Route::has('register'))
                    <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @endif
                    @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </div>
                    </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <!-- @isset($header)
    <header class="py-3 shadow-sm">
        <div class="container">
            <h4>{{ $header }}</h4>
        </div>
    </header>
    @endisset -->

    @include('admin.side')

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer>
        &copy; {{ date('Y') }} {{ config('app.name', 'Loan Management') }} — All rights reserved.
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>