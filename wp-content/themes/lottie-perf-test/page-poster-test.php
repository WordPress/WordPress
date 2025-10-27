<?php
/**
 * Template Name: Step 7 - Poster Test
 * Description: Tipalti Finance AI replica - Step 7: Conditional + Poster fallback for below-fold animations
 */

get_header(); ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">Tipalti AI</h1>
                <p class="hero-subtitle">Redefine Finance Productivity with AI</p>
                <p class="hero-description">
                    Intelligent finance automation that learns your business, streamlines your workflows, and scales with your growth—putting you in complete control of your financial operations.
                </p>
                <div class="hero-cta">
                    <button class="btn-primary">Get Started</button>
                    <button class="btn-secondary">Request Demo</button>
                </div>
                <!-- Performance Metrics (Hidden by default, shown for testing) -->
                <div class="performance-metrics" style="margin-top: 20px; padding: 10px; background: rgba(0,0,0,0.1); border-radius: 8px; font-size: 12px;">
                    <strong>Step 7 Performance (Poster Fallback):</strong>
                    FCP: <span id="fcp">-</span> | 
                    LCP: <span id="lcp">-</span> | 
                    TBT: <span id="tbt">-</span> | 
                    CLS: <span id="cls">-</span> | 
                    Score: <span id="score">-</span>
                </div>
            </div>
            <div class="hero-animation">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 500px; height: 400px;" 
                    loop 
                    autoplay
                    renderer="canvas">
                </dotlottie-player>
            </div>
        </div>
    </div>
</section>

<!-- Sidekick Section -->
<section class="sidekick-section">
    <div class="container">
        <div class="sidekick-content">
            <div class="sidekick-text">
                <h2>Meet Your New Sidekick: Tipalti AI Assistant</h2>
                <p>AI that works alongside your finance team to automate routine tasks, provide intelligent insights, and help you make faster, more informed decisions.</p>
                <button class="btn-primary">Learn More</button>
            </div>
            <div class="sidekick-animation">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 400px; height: 300px;" 
                    loop 
                    autoplay
                    renderer="canvas"
                    data-lazy="true"
                    poster="<?php echo get_template_directory_uri(); ?>/assets/images/bill-approvers-poster.png">
                </dotlottie-player>
            </div>
        </div>
    </div>
</section>

<!-- Get to Know AI Agents Section -->
<section class="ai-agents-section">
    <div class="container">
        <div class="section-header">
            <h2>Get to Know the Tipalti AI Agents</h2>
            <p>Meet the intelligent agents that streamline finance workflows, working for you even when you don't see them.</p>
        </div>
        
        <div class="agents-grid">
            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/invoice-capture-poster.png">
                    </dotlottie-player>
                </div>
                <h3>Invoice Capture Agent</h3>
                <p>Automatically captures, validates, and codes invoices with 99.5% accuracy using advanced OCR and machine learning.</p>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/bill-approvers-poster.png">
                    </dotlottie-player>
                </div>
                <h3>Bill Approvers Agent</h3>
                <p>Intelligently routes invoices to the right approvers and learns from patterns to speed up future approvals.</p>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-matching-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/po-matching-poster.png">
                    </dotlottie-player>
                </div>
                <h3>Purchase Request Agent</h3>
                <p>Streamlines purchase order creation and approval workflows with intelligent automation.</p>
            </div>
        </div>
    </div>
</section>

<!-- AI Agents Workflow Section -->
<section class="agents-workflow-section">
    <div class="container">
        <div class="workflow-header">
            <h2>Tipalti AI Agents streamline finance workflows, working for you even when you don't see them.</h2>
        </div>
        
        <div class="workflow-grid">
            <div class="workflow-item">
                <div class="workflow-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/scan-expenses-receipt-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 300px; height: 250px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/scan-expenses-poster.png">
                    </dotlottie-player>
                </div>
                <div class="workflow-content">
                    <h3>Invoice Capture Agent</h3>
                    <p>Scans and extracts data from invoices automatically, reducing manual data entry by 95%.</p>
                    <h4>Key Features:</h4>
                    <ul>
                        <li>OCR technology</li>
                        <li>Smart data extraction</li>
                        <li>Auto GL coding</li>
                    </ul>
                </div>
            </div>

            <div class="workflow-item">
                <div class="workflow-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-request-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 300px; height: 250px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/po-request-poster.png">
                    </dotlottie-player>
                </div>
                <div class="workflow-content">
                    <h3>PO Matching Agent</h3>
                    <p>Intelligently matches invoices to purchase orders, handling exceptions automatically.</p>
                    <h4>Key Features:</h4>
                    <ul>
                        <li>Contextual matching</li>
                        <li>Exception handling</li>
                        <li>Variance analysis</li>
                    </ul>
                </div>
            </div>

            <div class="workflow-item">
                <div class="workflow-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/erp-sync-resolution-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 300px; height: 250px;" 
                        loop 
                        autoplay
                        renderer="canvas"
                        data-lazy="true"
                        poster="<?php echo get_template_directory_uri(); ?>/assets/images/erp-sync-poster.png">
                    </dotlottie-player>
                </div>
                <div class="workflow-content">
                    <h3>ERP Sync Resolution Agent</h3>
                    <p>Automatically resolves ERP synchronization issues and maintains data integrity.</p>
                    <h4>Key Features:</h4>
                    <ul>
                        <li>Real-time sync monitoring</li>
                        <li>Automatic error resolution</li>
                        <li>Data validation</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Productivity Section -->
<section class="productivity-section">
    <div class="container">
        <div class="productivity-content">
            <div class="productivity-text">
                <h2>Boost Productivity. Without Sacrificing Control.</h2>
                <p>Tipalti AI enhances your team's capabilities while keeping you in the driver's seat of every financial decision.</p>
            </div>
            
            <div class="productivity-features">
                <div class="feature-item">
                    <div class="feature-animation">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/duplicate-bill-detection.lottie" 
                            background="transparent" 
                            speed="1" 
                            style="width: 200px; height: 150px;" 
                            loop 
                            autoplay
                            renderer="canvas"
                            data-lazy="true"
                            poster="<?php echo get_template_directory_uri(); ?>/assets/images/duplicate-bill-poster.png">
                        </dotlottie-player>
                    </div>
                    <h3>Duplicate Bill Detection</h3>
                    <p>Proactively identifies and prevents duplicate payments using advanced pattern recognition.</p>
                </div>

                <div class="feature-item">
                    <div class="feature-animation">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/approval-chains-and-audit-trail.lottie" 
                            background="transparent" 
                            speed="1" 
                            style="width: 200px; height: 150px;" 
                            loop 
                            autoplay
                            renderer="canvas"
                            data-lazy="true"
                            poster="<?php echo get_template_directory_uri(); ?>/assets/images/approval-chains-poster.png">
                        </dotlottie-player>
                    </div>
                    <h3>Approval Chains and Audit Trail</h3>
                    <p>Maintains complete audit trails while streamlining approval workflows.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Two and Three-way PO Matching Section -->
<section class="po-matching-section">
    <div class="container">
        <div class="po-matching-content">
            <div class="po-matching-text">
                <h2>Two and Three-way PO Matching</h2>
                <p>Automated matching of purchase orders, invoices, and receipts with intelligent exception handling.</p>
                <button class="btn-primary">Learn More</button>
            </div>
            <div class="po-matching-animation">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/two-and-three-way-po-matching.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 500px; height: 400px;" 
                    loop 
                    autoplay
                    renderer="canvas"
                    data-lazy="true"
                    poster="<?php echo get_template_directory_uri(); ?>/assets/images/po-matching-large-poster.png">
                </dotlottie-player>
            </div>
        </div>
    </div>
</section>

<!-- Invoice Capture Agent 2 Section -->
<section class="invoice-capture-2-section">
    <div class="container">
        <div class="invoice-capture-content">
            <div class="invoice-animation">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-2.lottie" 
                    background="transparent" 
                    speed="1" 
                    style="width: 400px; height: 350px;" 
                    loop 
                    autoplay
                    renderer="canvas"
                    data-lazy="true"
                    poster="<?php echo get_template_directory_uri(); ?>/assets/images/invoice-capture-2-poster.png">
                </dotlottie-player>
            </div>
            <div class="invoice-text">
                <h2>AI Built for Global AP Teams</h2>
                <p>Advanced invoice processing that handles multiple currencies, languages, and regional compliance requirements.</p>
                <ul>
                    <li>Multi-language OCR</li>
                    <li>Currency conversion</li>
                    <li>Regional tax compliance</li>
                    <li>Global approval workflows</li>
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