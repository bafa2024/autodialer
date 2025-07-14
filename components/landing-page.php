<div id="landing-page" class="page-content active">
    <nav class="navbar navbar-expand-lg landing-nav">
        <div class="container">
            <a class="navbar-brand fw-bold" href="javascript:void(0);" onclick="showPage('landing-page')">
                <i class="bi bi-telephone-fill text-primary"></i> AutoDial Pro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                <div class="ms-3">
                    <button class="btn btn-outline-primary me-2" onclick="showPage('login-page')">Login</button>
                    <button class="btn btn-primary" onclick="showPage('signup-page')">Start Free Trial</button>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Supercharge Your Sales with Intelligent Auto Dialing</h1>
                    <p class="lead mb-4">Increase your team's productivity by 300% with our AI-powered auto dialer. Connect with more prospects, close more deals, and grow your business faster.</p>
                    <div class="d-flex gap-3">
                        <button class="btn btn-light btn-lg" onclick="showPage('signup-page')">
                            <i class="bi bi-rocket-takeoff me-2"></i>Start Free Trial
                        </button>
                        <button class="btn btn-outline-light btn-lg">
                            <i class="bi bi-play-circle me-2"></i>Watch Demo
                        </button>
                    </div>
                    <div class="mt-4">
                        <small class="opacity-75">No credit card required • 14-day free trial • Cancel anytime</small>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400/ffffff/5B5FDE?text=AutoDial+Dashboard" 
                         class="img-fluid rounded-3 shadow-lg" alt="Dashboard Preview" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Powerful Features for Modern Sales Teams</h2>
                <p class="lead text-secondary">Everything you need to scale your outbound sales operations</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-robot"></i>
                        </div>
                        <h4>AI-Powered Detection</h4>
                        <p class="text-secondary">Advanced answering machine detection with 98% accuracy using machine learning algorithms.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h4>Multiple Dialing Modes</h4>
                        <p class="text-secondary">Predictive, Progressive, Preview, and Power dialing modes to match your campaign needs.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h4>CRM Integration</h4>
                        <p class="text-secondary">Seamless integration with Salesforce, HubSpot, Pipedrive, and 50+ other CRM platforms.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="text-center">
                <p>&copy; 2024 AutoDial Pro. All rights reserved.</p>
            </div>
        </div>
    </footer>
</div>

<!-- Lazy-loaded sections -->
<div id="call-summarization-section" class="dashboard-section"></div>
<div id="contact-support-section" class="dashboard-section"></div>
