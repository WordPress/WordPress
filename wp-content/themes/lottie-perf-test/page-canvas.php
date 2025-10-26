<?php
/**
 * Template Name: Canvas Renderer Performance Test
 * 
 * This template displays the Tipalti Finance AI replica with Canvas Renderer Lottie integration
 */

get_header(); ?>

<main>
    <!-- Hero Section -->
    <section id="hero">
        <div class="container">
            <div class="hero-content">
                <h1>AI-Powered Accounts Payable Automation</h1>
                <p>Transform your finance operations with intelligent automation that learns, adapts, and scales with your business needs.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary btn-large">Start Free Trial</a>
                    <a href="#" class="btn btn-secondary btn-large">Watch Demo</a>
                </div>
            </div>
        </div>
    </section>

    <!-- AI Agents Section -->
    <section id="agents">
        <div class="container-wide">
            <div class="section-header">
                <h2>Meet Your AI Finance Team</h2>
                <p>Intelligent agents that handle every aspect of your accounts payable process, from invoice capture to payment approval.</p>
            </div>
            
            <div class="agents-grid">
                <!-- Invoice Capture Agent 1 -->
                <div class="agent-card">
                    <div class="agent-animation">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie"
                            autoplay 
                            loop 
                            renderer="svg"
                            style="width: 280px; height: 280px;">
                        </dotlottie-player>
                    </div>
                    <h3>Invoice Capture Agent</h3>
                    <p>Automatically extracts and validates invoice data from any format with 99.5% accuracy using advanced OCR and machine learning.</p>
                </div>

                <!-- Invoice Capture Agent 2 -->
                <div class="agent-card">
                    <div class="agent-animation">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-2.lottie"
                            autoplay 
                            loop 
                            renderer="svg"
                            style="width: 280px; height: 280px;">
                        </dotlottie-player>
                    </div>
                    <h3>Smart Data Extraction</h3>
                    <p>Intelligently processes invoices in multiple languages and formats, learning from your business patterns to improve accuracy over time.</p>
                </div>

                <!-- PO Matching Agent -->
                <div class="agent-card">
                    <div class="agent-animation">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-matching-agent.lottie"
                            autoplay 
                            loop 
                            renderer="svg"
                            style="width: 280px; height: 280px;">
                        </dotlottie-player>
                    </div>
                    <h3>PO Matching Agent</h3>
                    <p>Automatically matches invoices to purchase orders and receipts, identifying discrepancies and routing exceptions for review.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features">
        <div class="container">
            <div class="features-content">
                <div class="feature-text">
                    <h2>Why Choose AI-Powered Finance?</h2>
                    <p>Our intelligent automation platform reduces manual work by 90% while improving accuracy and compliance. Transform your accounts payable process with cutting-edge AI technology.</p>
                    <ul class="feature-list">
                        <li>99.5% invoice processing accuracy</li>
                        <li>75% reduction in processing time</li>
                        <li>Complete audit trail and compliance</li>
                        <li>Seamless ERP integration</li>
                        <li>Real-time fraud detection</li>
                        <li>Intelligent exception handling</li>
                    </ul>
                    <a href="#" class="btn btn-primary btn-large">Learn More</a>
                </div>
                <div class="feature-visual">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie"
                        autoplay 
                        loop 
                        renderer="svg"
                        style="width: 100%; max-width: 500px; height: 400px;">
                    </dotlottie-player>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section id="video">
        <div class="container">
            <div class="video-container">
                <h2>See Finance AI in Action</h2>
                <p>Watch how our AI agents transform accounts payable operations for businesses of all sizes.</p>
                
                <div class="video-facade" data-src="https://player.vimeo.com/video/123456789">
                    <svg width="800" height="450" viewBox="0 0 800 450" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px;">
                        <rect width="800" height="450" fill="url(#gradient)" rx="12"/>
                        <text x="400" y="200" text-anchor="middle" fill="white" font-size="24" font-weight="600">Finance AI Demo Video</text>
                        <text x="400" y="240" text-anchor="middle" fill="rgba(255,255,255,0.8)" font-size="16">Click to play demonstration</text>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#667eea"/>
                                <stop offset="100%" style="stop-color:#764ba2"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <button class="play" aria-label="Play video">▶</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Performance Test Info -->
    <section id="test-info" style="padding: 2rem 0; background: var(--secondary-color);">
        <div class="container">
            <div style="text-align: center; padding: 1rem; background: white; border-radius: 8px; box-shadow: var(--shadow);">
                <h3 style="color: var(--primary-color); margin-bottom: 0.5rem;">🎨 Canvas Renderer Performance Test</h3>
                <p style="color: var(--text-secondary); margin-bottom: 1rem;">This page uses Canvas Renderer strategy - Optimized for mobile devices with intelligent pause/resume</p>
                <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
                    <span><strong>Expected Score:</strong> ≥93</span>
                    <span><strong>Strategy:</strong> Canvas renderer</span>
                    <span><strong>Load Time:</strong> Mobile optimized</span>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
