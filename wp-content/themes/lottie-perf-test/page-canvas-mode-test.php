<?php
/**
 * Template Name: Step 2 - Canvas Mode Test
 * Description: Tipalti Finance AI replica - Step 2: Local player + Canvas renderer
 */

get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Finance AI</h1>
                <p class="hero-subtitle">Intelligent automation that puts you in control</p>
                <p class="hero-description">
                    Transform your accounts payable with AI-powered agents that automate invoice processing, 
                    approval workflows, and compliance—while keeping finance teams in the driver's seat.
                </p>
                <div class="hero-cta">
                    <button class="btn-primary">Get Started</button>
                    <button class="btn-secondary">Request Demo</button>
                </div>
                <!-- Performance Metrics (Hidden by default, shown for testing) -->
                <div class="performance-metrics" style="margin-top: 20px; padding: 10px; background: rgba(0,0,0,0.1); border-radius: 8px; font-size: 12px;">
                    <strong>Step 2 Performance (Canvas Renderer):</strong>
                    FCP: <span id="fcp">-</span> | 
                    LCP: <span id="lcp">-</span> | 
                    TBT: <span id="tbt">-</span> | 
                    CLS: <span id="cls">-</span> | 
                    Score: <span id="score">-</span>
                </div>
            </div>
            <div class="hero-animation">
                <lottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 500px; height: 400px;" 
                    loop 
                    autoplay
                    renderer="canvas">
                </lottie-player>
            </div>
        </div>
    </div>
</section>

<!-- AI Agents Section -->
<section class="ai-agents-section">
    <div class="container">
        <div class="section-header">
            <h2>Meet Your AI Finance Team</h2>
            <p>Specialized AI agents that handle routine tasks so your team can focus on strategic work</p>
        </div>
        
        <div class="agents-grid">
            <div class="agent-card">
                <div class="agent-animation">
                    <lottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas">
                    </lottie-player>
                </div>
                <h3>Invoice Capture Agent</h3>
                <p>Automatically captures, validates, and codes invoices with 99.5% accuracy using advanced OCR and machine learning.</p>
                <ul class="agent-features">
                    <li>Smart data extraction</li>
                    <li>Auto GL coding</li>
                    <li>Duplicate detection</li>
                </ul>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <lottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas">
                    </lottie-player>
                </div>
                <h3>Bill Approvers Agent</h3>
                <p>Intelligently routes invoices to the right approvers and learns from patterns to speed up future approvals.</p>
                <ul class="agent-features">
                    <li>Smart routing</li>
                    <li>Approval workflows</li>
                    <li>Pattern learning</li>
                </ul>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <lottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-matching-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas">
                    </lottie-player>
                </div>
                <h3>PO Matching Agent</h3>
                <p>Matches invoices with purchase orders by analyzing contextual descriptions, not just exact matches.</p>
                <ul class="agent-features">
                    <li>Contextual matching</li>
                    <li>Exception handling</li>
                    <li>Variance analysis</li>
                </ul>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <lottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/duplicate-bill-detection.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas">
                    </lottie-player>
                </div>
                <h3>Duplicate Bill Detection</h3>
                <p>Proactively identifies and prevents duplicate payments using advanced pattern recognition.</p>
                <ul class="agent-features">
                    <li>Real-time scanning</li>
                    <li>Pattern recognition</li>
                    <li>Fraud prevention</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
    <div class="container">
        <div class="section-header">
            <h2>Why Finance Teams Choose Tipalti AI</h2>
        </div>
        
        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon">⚡</div>
                <h3>10x Faster Processing</h3>
                <p>Reduce invoice processing time from days to minutes with intelligent automation.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">🎯</div>
                <h3>99.5% Accuracy</h3>
                <p>AI-powered validation ensures accurate data capture and coding every time.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">🔒</div>
                <h3>Enhanced Security</h3>
                <p>Built-in fraud detection and compliance monitoring protect your business.</p>
            </div>
            
            <div class="benefit-item">
                <div class="benefit-icon">📊</div>
                <h3>Real-time Insights</h3>
                <p>Get instant visibility into your AP processes with intelligent reporting.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Transform Your Finance Operations?</h2>
            <p>Join thousands of finance teams who trust Tipalti AI to automate their accounts payable.</p>
            <div class="cta-buttons">
                <button class="btn-primary">Start Free Trial</button>
                <button class="btn-secondary">Schedule Demo</button>
            </div>
        </div>
    </div>
</section>

<script>
// Performance measurement script
document.addEventListener('DOMContentLoaded', function() {
    // Measure performance metrics
    if ('performance' in window) {
        const perfData = performance.getEntriesByType('navigation')[0];
        const paintEntries = performance.getEntriesByType('paint');
        
        // FCP
        const fcpEntry = paintEntries.find(entry => entry.name === 'first-contentful-paint');
        if (fcpEntry) {
            document.getElementById('fcp').textContent = Math.round(fcpEntry.startTime) + 'ms';
        }
        
        // LCP (approximation)
        if (perfData) {
            document.getElementById('lcp').textContent = Math.round(perfData.loadEventEnd - perfData.loadEventStart) + 'ms';
        }
    }
});
</script>

<?php get_footer(); ?>
