<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Booking System | Kisii Central Police</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --police-blue: #004a8d;
            --police-gold: #ffd700;
            --accent-red: #d40000;
            --dark-blue: #002b5c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header/Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--dark-blue), var(--police-blue)) !important;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: white !important;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
        }
        
        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link:hover {
            color: var(--police-gold) !important;
        }
        
        .nav-link:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--police-gold);
            bottom: -5px;
            left: 0;
            transition: width 0.3s ease;
        }
        
        .nav-link:hover:after {
            width: 100%;
        }
        
        .btn-police {
            background-color: var(--accent-red);
            color: white;
            border: none;
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 30px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(212, 0, 0, 0.2);
        }
        
        .btn-police:hover {
            background-color: #b30000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(212, 0, 0, 0.3);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0, 42, 92, 0.8), rgba(0, 42, 92, 0.8)), url('/assets/images/hero.jpeg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero:before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%23f8f9fa"></path><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%23f8f9fa"></path><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%23f8f9fa"></path></svg>');
            background-size: cover;
            background-repeat: no-repeat;
        }
        
        .hero h1 {
            font-weight: 800;
            font-size: 3.5rem;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero p {
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        .hero-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Booking System Section */
        .booking-system {
            padding: 100px 0;
            background-color: white;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-header h2 {
            font-weight: 700;
            color: var(--dark-blue);
            position: relative;
            display: inline-block;
            padding-bottom: 15px;
        }
        
        .section-header h2:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 3px;
            background: var(--police-gold);
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .section-header p {
            color: #666;
            max-width: 700px;
            margin: 20px auto 0;
        }
        
        .feature-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
            height: 100%;
            padding: 30px;
            text-align: center;
            background: white;
            margin-bottom: 30px;
            border-top: 4px solid var(--police-blue);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--police-blue);
            margin-bottom: 20px;
            background: rgba(0, 74, 141, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        /* How It Works */
        .how-it-works {
            padding: 100px 0;
            background-color: #f8f9fa;
            position: relative;
            overflow: hidden;
        }
        
        .how-it-works:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" fill="%23004a8d"></path><path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" fill="%23004a8d"></path><path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" fill="%23f8f9fa"></path></svg>');
            background-size: cover;
            background-repeat: no-repeat;
            transform: rotate(180deg);
        }
        
        .step {
            position: relative;
            padding-left: 100px;
            margin-bottom: 40px;
        }
        
        .step-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--police-blue), var(--dark-blue));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .step h4 {
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        /* Testimonials */
        .testimonials {
            padding: 100px 0;
            background-color: white;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin: 15px;
            position: relative;
            border-left: 4px solid var(--police-blue);
        }
        
        .testimonial-card:before {
            content: '\201C';
            font-family: Georgia, serif;
            font-size: 4rem;
            color: rgba(0, 74, 141, 0.1);
            position: absolute;
            top: 10px;
            left: 10px;
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
        }
        
        .testimonial-author img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
            object-fit: cover;
        }
        
        .author-info h5 {
            margin-bottom: 0;
            color: var(--dark-blue);
        }
        
        .author-info p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        
        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--dark-blue), var(--police-blue));
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta-section h2 {
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .cta-section p {
            max-width: 700px;
            margin: 0 auto 30px;
            opacity: 0.9;
        }
        
        /* Footer */
        .footer {
            background: linear-gradient(135deg, #001a38, #002b5c);
            color: white;
            padding: 80px 0 20px;
        }
        
        .footer-logo {
            height: 60px;
            margin-bottom: 20px;
            filter: brightness(0) invert(1);
        }
        
        .footer-links h5 {
            color: var(--police-gold);
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.8);
            display: block;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background: var(--police-gold);
            color: var(--dark-blue);
            transform: translateY(-3px);
        }
        
        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 50px;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .step {
                padding-left: 80px;
            }
            
            .step-number {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero {
                padding: 100px 0;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .hero-buttons .btn {
                width: 100%;
                max-width: 250px;
                margin-bottom: 10px;
            }
            
            .section-header h2 {
                font-size: 1.8rem;
            }
            
            .step {
                padding-left: 0;
                padding-top: 80px;
            }
            
            .step-number {
                top: 0;
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="/assets/images/kenya-police-logo.svg" alt="Police Logo">
                Kisii Central Police
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#how-it-works">How It Works</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li> -->
                    <li class="nav-item ms-lg-3">
                        <a href="/views/auth/login.php" class="btn btn-police">Officer Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Police Booking System</h1>
            <p class="lead">Streamlining police service appointments for Kisii Central residents. Book, manage, and track your police service requests online.</p>
            <div class="hero-buttons">
                <a href="/views/auth/login.php" class="btn btn-police btn-lg">Book a Service</a>
                <a href="/views/auth/login.php" class="btn btn-outline-light btn-lg">Check Status</a>
            </div>
        </div>
    </section>

    <!-- Booking System Features -->
    <section class="booking-system" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Our Booking System</h2>
                <p>Designed specifically for Kisii Central Police to efficiently manage service appointments and officer assignments</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>Online Booking</h4>
                        <p>Citizens can book police services 24/7 without visiting the station, reducing wait times and congestion.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h4>Officer Assignment</h4>
                        <p>Administrators can efficiently assign officers to bookings based on availability and specialization.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4>Real-time Tracking</h4>
                        <p>Both officers and citizens can track appointment status in real-time with automated notifications.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h4>Case Management</h4>
                        <p>Integrated case tracking system that links bookings to subsequent police actions and documentation.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Reporting Dashboard</h4>
                        <p>Comprehensive analytics on booking volumes, service times, and officer workloads.</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Mobile Friendly</h4>
                        <p>Fully responsive design works perfectly on all devices, including smartphones and tablets.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How The Booking System Works</h2>
                <p>Simple steps to schedule and manage police service appointments</p>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="step">
                        <div class="step-number">1</div>
                        <h4>Service Selection</h4>
                        <p>Citizens select the specific police service they require from our online portal, whether it's reporting a case, requesting clearance, or other police services.</p>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">2</div>
                        <h4>Appointment Booking</h4>
                        <p>Choose a convenient date and time from available slots. The system automatically prevents double-booking and manages officer availability.</p>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">3</div>
                        <h4>Officer Assignment</h4>
                        <p>Administrators assign the most appropriate officer based on specialization, workload, and location. Officers receive instant notifications.</p>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">4</div>
                        <h4>Service Completion</h4>
                        <p>After the appointment, officers update the system with outcomes, generating necessary documentation and closing the booking loop.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->


    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Experience Efficient Police Services?</h2>
            <p>Join hundreds of Kisii residents who are using our online booking system for faster, more convenient police services.</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="/views/auth/login.php" class="btn btn-police btn-lg">Book a Service Now</a>
                <a href="#how-it-works" class="btn btn-outline-light btn-lg">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-5 mb-lg-0">
                    <img src="/assets/images/kenya-police-logo.svg" alt="Police Logo" class="footer-logo">
                    <p class="mt-3">Kisii Central Police Booking System - A digital transformation initiative to improve police service delivery.</p>
                    <div class="social-links mt-4">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Services</h5>
                        <a href="#">Case Reporting</a>
                        <a href="#">Clearance Certificates</a>
                        <a href="#">Lost Property</a>
                        <a href="#">Appointments</a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <div class="footer-links">
                        <h5>Resources</h5>
                        <a href="#">How to Book</a>
                        <a href="#">FAQ</a>
                        <a href="#">Service Charges</a>
                        <a href="#">Terms of Use</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="footer-links">
                        <h5>Contact Us</h5>
                        <p><i class="fas fa-map-marker-alt me-2"></i> Kisii Central Police Station, Kisii Town</p>
                        <p><i class="fas fa-phone-alt me-2"></i> +254-7234567</p>
                        <p><i class="fas fa-envelope me-2"></i> bookings@kisiipolice.go.ke</p>
                    </div>
                </div>
            </div>
            <div class="text-center copyright">
                <p>&copy; 2025 Kisii Central Police. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Navbar background change on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
                navbar.style.padding = '10px 0';
            } else {
                navbar.style.boxShadow = '0 2px 15px rgba(0,0,0,0.1)';
                navbar.style.padding = '15px 0';
            }
        });
    </script>
</body>
</html>