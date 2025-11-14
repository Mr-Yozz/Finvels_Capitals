<!DOCTYPE html>
<html lang="en">


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
                        <a href="{{route('base')}}" class="nav-item nav-link active">Home</a>
                        <a href="{{route('about')}}" class="nav-item nav-link">About</a>
                        <a href="{{route('service')}}" class="nav-item nav-link">Service</a>
                        <a href="{{route('contact')}}" class="nav-item nav-link">Contact</a>
                    </div>
                    <a href="{{route('home')}}" class="btn rounded-pill py-2 px-4 ms-3 d-none d-lg-block">Login</a>
                    <a href="{{route('home')}}" class="btn btn-sm py-1 px-3 me-2 d-block d-lg-none">Login</a>
                </div>
            </nav>

            <div class="container-xxl bg-primary hero-header">
                <div class="container px-lg-5">
                    <div class="row g-5 align-items-end">
                        <div class="col-lg-6 text-center text-lg-start">
                            <h1 class="text-white mb-4 animated slideInDown">Empowering Dreams, Building Futures.Mission</h1>
                            <p class="text-white pb-3 animated slideInDown">Providing accessible microfinance to foster entrepreneurship.Key CTAs: Apply for a Loan Today / Learn About Our Impact.Trust Points: Transparent, Accessible, Supportive.</p>
                            <a href="#" class="btn btn-secondary py-sm-3 px-sm-5 rounded-pill me-3 animated slideInLeft">Read More</a>
                            <a href="{{route('contact')}}" class="btn btn-light py-sm-3 px-sm-5 rounded-pill animated slideInRight">Contact Us</a>
                        </div>
                        <div class="col-lg-6 text-center text-lg-start">
                            <img class="img-fluid animated zoomIn" src="{{asset('images/guest/hero.png')}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Feature Start -->
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
        <!-- Feature End -->

        <!-- Service Start -->
        <div class="container-xxl py-5">
            <div class="container py-5 px-lg-5">
                <div class="wow fadeInUp" data-wow-delay="0.1s">
                    <p class="section-title text-secondary justify-content-center"><span></span>Our Services<span></span></p>
                    <h1 class="text-center mb-5">Loan Solutions We Provide</h1>
                </div>
                <div class="row g-4">

                    <!-- Personal Loans -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-user fa-2x"></i>
                            </div>
                            <h5 class="mb-3">Personal Loans</h5>
                            <p class="m-0">Quick and flexible personal loans designed to help you meet expenses such as education, travel, medical, and emergencies.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Business Loans -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-briefcase fa-2x"></i>
                            </div>
                            <h5 class="mb-3">Small Business Loans</h5>
                            <p class="m-0">Funding solutions for startups and growing businesses with easy documentation and fast approval.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Home Loans -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-home fa-2x"></i>
                            </div>
                            <h5 class="mb-3">Home Loans</h5>
                            <p class="m-0">Affordable home loan plans with flexible tenure options and the lowest possible interest rates.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Loan Calculator -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-calculator fa-2x"></i>
                            </div>
                            <h5 class="mb-3">EMI Calculator</h5>
                            <p class="m-0">Easily estimate your monthly repayments and plan your loans with our accurate EMI calculator tool.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Credit Score Check -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-chart-line fa-2x"></i>
                            </div>
                            <h5 class="mb-3">Credit Score Check</h5>
                            <p class="m-0">Check your credit score instantly and understand how it impacts your loan eligibility and interest rates.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                    <!-- Loan Management Portal -->
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item d-flex flex-column text-center rounded">
                            <div class="service-icon flex-shrink-0">
                                <i class="fa fa-mobile-alt fa-2x"></i>
                            </div>
                            <h5 class="mb-3">Loan Management Portal</h5>
                            <p class="m-0">Track applications, download statements, update KYC, view EMIs, and manage your loan anytime from your dashboard.</p>
                            <a class="btn btn-square" href=""><i class="fa fa-arrow-right"></i></a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Service End -->

        <!-- Testimonial Start -->
        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container py-5 px-lg-5">
                <p class="section-title text-secondary justify-content-center">
                    <span></span>Loan Benefits<span></span>
                </p>
                <h1 class="text-center mb-5">Why Choose Our Loan Services?</h1>

                <div class="owl-carousel testimonial-carousel">

                    <!-- Benefit 1 -->
                    <div class="testimonial-item bg-light rounded my-4">
                        <p class="fs-5">
                            <i class="fa fa-check-circle fa-4x text-primary mt-n4 me-3"></i>
                            Get quick loan approval with minimum documentation. Our simplified process ensures faster access to funds when you need them the most.
                        </p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="{{asset('images/images.png')}}" style="width: 65px; height: 65px;">
                            <div class="ps-4">
                                <h5 class="mb-1">Fast Approval</h5>
                                <span>Quick & Hassle-Free</span>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="testimonial-item bg-light rounded my-4">
                        <p class="fs-5">
                            <i class="fa fa-check-circle fa-4x text-primary mt-n4 me-3"></i>
                            Enjoy affordable interest rates that fit your financial capability, helping you repay comfortably without any burden.
                        </p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="{{asset('images/low.png')}}" style="width: 65px; height: 65px;">
                            <div class="ps-4">
                                <h5 class="mb-1">Low Interest Rates</h5>
                                <span>Affordable & Transparent</span>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="testimonial-item bg-light rounded my-4">
                        <p class="fs-5">
                            <i class="fa fa-check-circle fa-4x text-primary mt-n4 me-3"></i>
                            Choose a repayment period that suits your income and lifestyle. Our flexible EMIs allow you to manage finances comfortably.
                        </p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="{{asset('images/flex.png')}}" style="width: 65px; height: 65px;">
                            <div class="ps-4">
                                <h5 class="mb-1">Flexible EMI Options</h5>
                                <span>Easy Monthly Payments</span>
                            </div>
                        </div>
                    </div>

                    <!-- Benefit 4 -->
                    <div class="testimonial-item bg-light rounded my-4">
                        <p class="fs-5">
                            <i class="fa fa-check-circle fa-4x text-primary mt-n4 me-3"></i>
                            Manage your loan anytime from our online portal — track EMIs, download statements, update details, and more.
                        </p>
                        <div class="d-flex align-items-center">
                            <img class="img-fluid flex-shrink-0 rounded-circle" src="{{asset('images/images (3).jpg')}}" style="width: 65px; height: 65px;">
                            <div class="ps-4">
                                <h5 class="mb-1">Online Loan Management</h5>
                                <span>24/7 Access</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Testimonial End -->

        @include('admin.layouts.footer')
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('lib/wow/wow.min.js')}}"></script>
    <script src="{{asset('lib/easing/easing.min.js')}}"></script>
    <script src="{{asset('lib/waypoints/waypoints.min.js')}}"></script>
    <script src="{{asset('lib/counterup/counterup.min.js')}}"></script>
    <script src="{{asset('lib/owlcarousel/owl.carousel.min.js')}}"></script>
    <script src="{{asset('lib/isotope/isotope.pkgd.min.js')}}"></script>
    <script src="{{asset('lib/lightbox/js/lightbox.min.js')}}"></script>

    <!-- Template Javascript -->
    <script src="{{asset('js/main.js')}}"></script>
</body>

</html>