<!DOCTYPE html>
<html lang="en">

@section('title', 'Service - Apply Online Easily')
@section('description', 'Apply for a personal loan instantly with minimum documentation and fast approval.')
@include('admin.layouts.header')

<body>
    <div class="container-xxl bg-white p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Navbar & Hero Start -->
        <div class="container-xxl position-relative p-0">
            <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
                <a href="" class="navbar-brand p-0">
                    <!-- <h1 class="m-0">FinVels</h1> -->
                    <img src="{{asset('images/finvels.jpeg')}}" alt="Logo">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <div class="navbar-nav mx-auto py-0">
                        <a href="{{route('base')}}" class="nav-item nav-link">Home</a>
                        <a href="{{route('about')}}" class="nav-item nav-link">About</a>
                        <a href="{{route('service')}}" class="nav-item nav-link active">Service</a>
                        <!-- <a href="project.html" class="nav-item nav-link">Project</a>
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                            <div class="dropdown-menu m-0">
                                <a href="team.html" class="dropdown-item">Our Team</a>
                                <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                                <a href="404.html" class="dropdown-item">404 Page</a>
                            </div>
                        </div> -->
                        <a href="{{route('contact')}}" class="nav-item nav-link">Contact</a>
                    </div>
                    <a href="{{route('home')}}" class="btn rounded-pill py-2 px-4 ms-3 d-none d-lg-block">Login</a>
                    <a href="{{route('home')}}" class="btn btn-sm py-1 px-3 me-2 d-block d-lg-none">Login</a>
                </div>
            </nav>

            <div class="container-xxl py-5 bg-primary hero-header">
                <div class="container my-5 py-5 px-lg-5">
                    <div class="row g-5 py-5">
                        <div class="col-12 text-center">
                            <h1 class="text-white animated slideInDown">Service</h1>
                            <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-center">
                                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                                    <li class="breadcrumb-item text-white active" aria-current="page">Service</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->


        <!-- Service Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="row g-4">
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="feature-item bg-light rounded text-center p-4">
                            <i class="fa fa-3x fa-mail-bulk text-primary mb-4"></i>
                            <h5 class="mb-3">About Us FocusStory</h5>
                            <p class="m-0">Founded in [Year], Finvels Capitel was established to bridge the financing gap for low-income individuals and rural entrepreneurs often excluded by traditional banking. We started with a handful of villages and have grown into a trusted institution, driven by our commitment to economic independence.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="feature-item bg-light rounded text-center p-4">
                            <i class="fa fa-3x fa-search text-primary mb-4"></i>
                            <h5 class="mb-3">Our Services Focus (Products)Group Microloans</h5>
                            <p class="m-0">For Self-Help Groups (SHGs).Individual Enterprise Loans: For solo small business growth.Process: Simple 4-step process (Inquiry $\rightarrow$ Assessment $\rightarrow$ Training $\rightarrow$ Disbursement).</p>
                        </div>
                    </div>
                    <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="feature-item bg-light rounded text-center p-4">
                            <i class="fa fa-3x fa-laptop-code text-primary mb-4"></i>
                            <h5 class="mb-3">Contact Us Focus Information</h5>
                            <p class="m-0">Our dedicated field officers are ready to answer your questions and guide you. Contact us through the form below or visit our Head Office.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Service End -->


        <!-- Loan Management - Parallax Service Section -->
        <div class="parallax-services">
            <div class="services-overlay">
                <h2 class="services-title">Complete Loan Management Services</h2>
                <p class="services-subtitle">
                    We provide end-to-end digital loan management solutions designed to make borrowing
                    faster, easier, and more transparent for individuals and businesses.
                </p>

                <p class="services-description">
                    From online loan applications to automated eligibility checks, real-time status tracking,
                    and instant document verification, our platform ensures a smooth and hassle-free borrowing experience.
                    With secure data processing, flexible loan options, and dedicated customer support, we help you
                    manage your finances confidently and conveniently—all from one smart dashboard.
                </p>

                <p class="services-description">
                    Whether you’re looking for personal loans, business loans, or emergency credit support,
                    our streamlined system gives you the clarity and control you need at every step.
                </p>
            </div>
        </div>


        <!-- Footer Start -->
        @include('admin.layouts.footer')
        <!-- Footer End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-secondary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('lib/wow/wow.min.js')}}"></script>
    <script src="{{ asset('lib/easing/easing.min.js')}}"></script>
    <script src="{{ asset('lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('lib/counterup/counterup.min.js')}}"></script>
    <script src="{{asset('lib/owlcarousel/owl.carousel.min.js')}}"></script>
    <script src="{{asset('lib/isotope/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('lib/lightbox/js/lightbox.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset('js/main.js')}}"></script>
</body>

</html>