<!DOCTYPE html>
<html lang="en">

@section('title', 'About - Apply Online Easily')
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
                        <a href="{{route('base')}}" class="nav-item nav-link ">Home</a>
                        <a href="{{route('about')}}" class="nav-item nav-link active">About</a>
                        <a href="{{route('service')}}" class="nav-item nav-link">Service</a>
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
                            <h1 class="text-white animated slideInDown">About Us</h1>
                            <hr class="bg-white mx-auto mt-0" style="width: 90px;">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb justify-content-center">
                                    <li class="breadcrumb-item"><a class="text-white" href="#">Home</a></li>
                                    <li class="breadcrumb-item"><a class="text-white" href="#">Pages</a></li>
                                    <li class="breadcrumb-item text-white active" aria-current="page">About</li>
                                </ol>
                            </nav>
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


        <!-- Loan Benefits - Parallax Section -->
        <div class="parallax-benefits">
            <div class="benefits-overlay">
                <h2 class="benefits-title">Why Our Loans Are The Best Choice</h2>
                <p class="benefits-subtitle">Enjoy flexible, fast, and stress-free financial support.</p>

                <div class="benefits-list">
                    <div class="benefit-item">✔ Fast Approval Process</div>
                    <div class="benefit-item">✔ Minimal Documentation</div>
                    <div class="benefit-item">✔ Low Interest Rates</div>
                    <div class="benefit-item">✔ Flexible Repayment Options</div>
                    <div class="benefit-item">✔ 24/7 Customer Support</div>
                    <div class="benefit-item">✔ 100% Secure & Transparent Process</div>
                </div>
            </div>
        </div>

        <div class="container-xxl py-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="container py-5 px-lg-5">
                <p class="section-title text-secondary justify-content-center">
                    <span></span>Our Features<span></span>
                </p>
                <h1 class="text-center mb-5">Smart Loan Features Built for You</h1>

                <div class="row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="bg-light p-4 rounded h-100 text-center">
                            <i class="fa fa-mobile-alt fa-3x text-primary mb-3"></i>
                            <h5>100% Online Process</h5>
                            <p>Apply, upload documents, and receive funds digitally—no branch visits needed.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="bg-light p-4 rounded h-100 text-center">
                            <i class="fa fa-wallet fa-3x text-primary mb-3"></i>
                            <h5>Flexible EMIs</h5>
                            <p>Choose repayment tenure that fits your monthly budget without stress.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="bg-light p-4 rounded h-100 text-center">
                            <i class="fa fa-id-card fa-3x text-primary mb-3"></i>
                            <h5>Minimal Documentation</h5>
                            <p>Quick KYC and simplified document requirements for faster approval.</p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="bg-light p-4 rounded h-100 text-center">
                            <i class="fa fa-headset fa-3x text-primary mb-3"></i>
                            <h5>24/7 Support</h5>
                            <p>Our support team is always available to assist you through every step.</p>
                        </div>
                    </div>
                </div>
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