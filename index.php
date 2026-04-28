<?php
require_once 'includes/functions.php';

$allServices = getAllServices();
$categories = ['all', 'social', 'email', 'ecommerce', 'payment', 'gaming', 'dating', 'food', 'travel', 'entertainment', 'productivity'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - 400+ Virtual Numbers for OTP Verification</title>
    <meta name="description" content="Get virtual numbers for 400+ services including WhatsApp, Telegram, Instagram. Instant OTP delivery. 24/7 support.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <i class="fas fa-robot"></i>
                <span><?php echo SITE_NAME; ?></span>
            </div>
            <div class="nav-links">
                <a href="#home">Home</a>
                <a href="#services">Services</a>
                <a href="#pricing">Pricing</a>
                <a href="#how-it-works">How it Works</a>
                <a href="#faq">FAQ</a>
                <a href="dashboard.php" class="btn-outline">Dashboard</a>
                <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-telegram">
                    <i class="fab fa-telegram"></i> Open Bot
                </a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Get Virtual Numbers for <span class="gradient">400+ Services</span></h1>
                <p>Receive SMS online for WhatsApp, Telegram, Instagram, Facebook and 400+ services. Instant delivery, 24/7 availability.</p>
                
                <!-- Search Bar -->
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="serviceSearch" placeholder="Search any service... (e.g., whatsapp, telegram, instagram, gmail)" autocomplete="off">
                        <button id="searchBtn"><i class="fas fa-arrow-right"></i></button>
                    </div>
                    <div id="searchSuggestions" class="search-suggestions"></div>
                </div>
                
                <div class="hero-buttons">
                    <a href="#services" class="btn-primary">Browse All Services <i class="fas fa-arrow-right"></i></a>
                    <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-secondary">
                        <i class="fab fa-telegram"></i> Start Bot
                    </a>
                </div>
                <div class="stats">
                    <div class="stat">
                        <span class="stat-number">400+</span>
                        <span class="stat-label">Services</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">99.9%</span>
                        <span class="stat-label">Success Rate</span>
                    </div>
                    <div class="stat">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
            </div>
            <div class="hero-image">
                <div class="phone-mockup">
                    <div class="phone-screen">
                        <div class="bot-message">🌟 Welcome to Money Maker Bot!</div>
                        <div class="bot-message">📱 400+ Services Available</div>
                        <div class="bot-button">🔍 Search: whatsapp</div>
                        <div class="bot-message small">✓ WhatsApp - ₹10</div>
                        <div class="bot-message small">✓ Telegram - ₹10</div>
                        <div class="bot-message small">✓ Instagram - ₹12</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section with Categories -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2>All Services</h2>
                <p><span id="serviceCount"><?php echo $allServices['total']; ?></span>+ services available for OTP verification</p>
            </div>
            
            <!-- Category Filters -->
            <div class="category-filters">
                <?php foreach($categories as $cat): ?>
                <button class="category-btn <?php echo $cat == 'all' ? 'active' : ''; ?>" data-category="<?php echo $cat; ?>">
                    <i class="<?php echo getCategoryIcon($cat); ?>"></i>
                    <?php echo ucfirst($cat); ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <!-- Services Grid -->
            <div id="servicesGrid" class="services-grid">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i> Loading services...
                </div>
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="pagination"></div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
        <div class="container">
            <div class="section-header">
                <h2>Simple Pricing</h2>
                <p>Pay only for what you use. No hidden charges.</p>
            </div>
            <div class="pricing-grid">
                <div class="pricing-card">
                    <div class="pricing-icon"><i class="fas fa-rocket"></i></div>
                    <h3>Pay Per Number</h3>
                    <div class="price">₹10<span>/number</span></div>
                    <ul>
                        <li><i class="fas fa-check"></i> 1 Virtual Number</li>
                        <li><i class="fas fa-check"></i> OTP Delivery Guarantee</li>
                        <li><i class="fas fa-check"></i> 15 Minutes Validity</li>
                        <li><i class="fas fa-check"></i> Email Support</li>
                    </ul>
                    <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-pricing">Get Started</a>
                </div>
                <div class="pricing-card popular">
                    <div class="popular-badge">Most Popular</div>
                    <div class="pricing-icon"><i class="fas fa-crown"></i></div>
                    <h3>Starter Pack</h3>
                    <div class="price">₹50<span>/5 numbers</span></div>
                    <ul>
                        <li><i class="fas fa-check"></i> 5 Virtual Numbers</li>
                        <li><i class="fas fa-check"></i> Priority OTP Delivery</li>
                        <li><i class="fas fa-check"></i> 20 Minutes Validity</li>
                        <li><i class="fas fa-check"></i> Priority Support</li>
                    </ul>
                    <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-pricing">Get Started</a>
                </div>
                <div class="pricing-card">
                    <div class="pricing-icon"><i class="fas fa-gem"></i></div>
                    <h3>Pro Pack</h3>
                    <div class="price">₹200<span>/25 numbers</span></div>
                    <ul>
                        <li><i class="fas fa-check"></i> 25 Virtual Numbers</li>
                        <li><i class="fas fa-check"></i> Instant OTP Delivery</li>
                        <li><i class="fas fa-check"></i> 30 Minutes Validity</li>
                        <li><i class="fas fa-check"></i> 24/7 Priority Support</li>
                    </ul>
                    <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-pricing">Get Started</a>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get your virtual number in 3 easy steps</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-icon"><i class="fab fa-telegram"></i></div>
                    <h3>Open Bot</h3>
                    <p>Start the Money Maker Bot on Telegram</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-icon"><i class="fas fa-search"></i></div>
                    <h3>Search Service</h3>
                    <p>Search from 400+ services</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-icon"><i class="fas fa-phone"></i></div>
                    <h3>Get Number & OTP</h3>
                    <p>Receive virtual number and get OTP instantly</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq">
        <div class="container">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Got questions? We've got answers</p>
            </div>
            <div class="faq-grid">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How many services are available?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer 400+ services including WhatsApp, Telegram, Instagram, Facebook, Gmail, Amazon, Paytm, and many more.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How long does the number work?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Each number is valid for 15-20 minutes, which is enough time to receive your OTP.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What payment methods are accepted?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept UPI payments through Google Pay, PhonePe, Paytm, and all major UPI apps. Payment is instant and automatic.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Can I search for a specific service?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes! Use the search bar on our website or in the Telegram bot to find any service instantly.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>What if I don't receive the OTP?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>If you don't receive OTP within the validity period, you can cancel the order and get a refund.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>How do I get support?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Contact our support team on Telegram: @NikunjBro. We're available 24/7.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <p>Join thousands of satisfied users and get your virtual numbers instantly</p>
                <a href="https://t.me/<?php echo BOT_USERNAME; ?>" class="btn-cta">
                    <i class="fab fa-telegram"></i> Start Using Bot Now
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Your trusted partner for virtual number services since 2024. Get OTP verification instantly from 400+ services.</p>
                    <div class="social-links">
                        <a href="https://t.me/<?php echo BOT_USERNAME; ?>"><i class="fab fa-telegram"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="#home">Home</a>
                    <a href="#services">Services</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#how-it-works">How it Works</a>
                    <a href="#faq">FAQ</a>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <a href="https://t.me/<?php echo BOT_USERNAME; ?>"><i class="fab fa-telegram"></i> Telegram Bot</a>
                    <a href="https://t.me/NikunjBro"><i class="fab fa-telegram"></i> Support Team</a>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Refund Policy</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 <?php echo SITE_NAME; ?>. All rights reserved. | 400+ Services | 24/7 Support</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>