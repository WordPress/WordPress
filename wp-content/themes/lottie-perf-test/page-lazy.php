<?php
/**
 * Template Name: Lazy Lottie Performance Test
 * Description: Exact Tipalti Finance AI replica - Lazy Loading mode
 */

get_header(); ?>

<div class="performance-mode-indicator lazy">Lazy Loading Mode</div>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">Finance automation that puts you in charge</h1>
            <div class="hero-actions">
                <a href="#" class="btn btn-primary">Get Started</a>
                <a href="#" class="btn btn-secondary">Log In</a>
            </div>
        </div>
    </div>
</section>

<!-- Tipalti AI Assistant Section -->
<section class="ai-assistant-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Tipalti AI Assistant</span>
            <h2 class="section-title">Meet Your New Sidekick: Tipalti AI Assistant</h2>
            <p class="section-description">
                Tipalti's 24/7 AI Assistant is a conversational tool for finance professionals that combines deep Tipalti knowledge of your finance workflows—such as invoices, purchase requests, and purchase orders—with advanced reasoning to save you time on routine tasks, deliver insights, and uncover opportunities.
            </p>
            <a href="#" class="btn btn-primary">Request a Demo</a>
        </div>
    </div>
</section>

<!-- Tipalti AI Agents Section -->
<section class="ai-agents-section">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Tipalti AI Agents</span>
            <h2 class="section-title">Get to Know the Tipalti AI Agents</h2>
            <p class="section-description">
                Tipalti AI agents autonomously execute prescribed routine finance tasks, such as generating reports, identifying approvers, or creating purchase requests, freeing you to focus on what matters most.
            </p>
        </div>

        <!-- AI Agents Grid -->
        <div class="agents-grid">
            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        renderer="svg"
                        data-lazy="true"
                        style="width: 300px; height: 300px;">
                    </dotlottie-player>
                </div>
                <h3 class="agent-title">Reporting Agent</h3>
                <p class="agent-description">Access real-time spend insights at your fingertips. Instantly create custom reports using natural language.</p>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        renderer="svg"
                        data-lazy="true"
                        style="width: 300px; height: 300px;">
                    </dotlottie-player>
                </div>
                <h3 class="agent-title">Bill Approvers Agent</h3>
                <p class="agent-description">Eliminate approval bottlenecks. The system predicts and recommends the right approver—fast.</p>
            </div>

            <div class="agent-card">
                <div class="agent-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-request-agent.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 300px; height: 300px;">
                    </dotlottie-player>
                </div>
                <h3 class="agent-title">Purchase Request Agent</h3>
                <p class="agent-description">Auto-generate complete purchase requests from simple employee descriptions.</p>
            </div>
        </div>
    </div>
</section>

<!-- AI Agents Workflow Section -->
<section class="workflow-section">
    <div class="container">
        <h2 class="section-title">Tipalti AI Agents streamline finance workflows, working for you even when you don't see them.</h2>
        
        <div class="workflow-content">
            <div class="workflow-visual">
                <div class="invoice-mockup">
                    <!-- Invoice UI mockup with Lottie animation -->
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 400px; height: 300px;">
                    </dotlottie-player>
                </div>
            </div>
            
            <div class="workflow-agents">
                <div class="agent-item">
                    <h3>Invoice Capture Agent</h3>
                    <p>Cut manual invoice coding time with AI invoice automation that captures invoice data and fills fields instantly</p>
                </div>
                <div class="agent-item">
                    <h3>Tax Form Scan Agent</h3>
                </div>
                <div class="agent-item">
                    <h3>PO Matching Agent</h3>
                </div>
                <div class="agent-item">
                    <h3>ERP Sync Resolution Agent</h3>
                </div>
                <div class="agent-item">
                    <h3>Expense Receipt Scan Agent</h3>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Financial Controls Section -->
<section class="controls-section">
    <div class="container">
        <h2 class="section-title">Boost Productivity Without Sacrificing Control</h2>
        <p class="section-description">Tipalti AI works with built-in financial controls and checks, ensuring efficiency while keeping you in charge.</p>
        
        <div class="controls-grid">
            <div class="control-card">
                <h3>Duplicate Bill Detection</h3>
                <p>Prevent fraud and overpayments. Tipalti AI strengthens AP controls by flagging duplicate invoices and anomalies early.</p>
                <div class="control-visual">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/duplicate-bill-detection.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 300px; height: 200px;">
                    </dotlottie-player>
                </div>
            </div>
            
            <div class="control-card">
                <h3>Approval Chain and Audit Trails</h3>
                <p>Manage approvals, payments, and audit trails across multiple entities in one consolidated view with clear visibility into each entity.</p>
                <div class="control-visual">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/approval-chains-and-audit-trail.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 300px; height: 200px;">
                    </dotlottie-player>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PO Matching Section -->
<section class="po-matching-section">
    <div class="container">
        <div class="po-content">
            <div class="po-text">
                <h2>Two and Three-way PO Matching</h2>
                <p>Strengthen reviews with built-in two- and three-way PO matching at both the header and line level, combining automation with control.</p>
            </div>
            <div class="po-visual">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/two-and-three-way-po-matching.lottie" 
                    autoplay loop 
                    renderer="svg" 
                    style="width: 400px; height: 300px;">
                </dotlottie-player>
            </div>
        </div>
        <div class="cta-center">
            <a href="#" class="btn btn-primary">Request a Demo</a>
        </div>
    </div>
</section>

<!-- Global AP Section -->
<section class="global-ap-section">
    <div class="container">
        <div class="global-content">
            <div class="global-text">
                <h2>AI Built for Global AP Teams</h2>
                <p>Tipalti AI enhances accuracy, accelerates workflows, and strengthens control across global AP operations for finance teams. It's built on Tipalti's platform for global payments in multiple currencies, supplier management, multi-entity operations, multi-language onboarding, and comprehensive tax compliance.</p>
            </div>
            <div class="global-visual">
                <dotlottie-player 
                    src="<?php echo get_template_directory_uri(); ?>/assets/lottie/erp-sync-resolution-agent.lottie" 
                    autoplay loop 
                    renderer="svg" 
                    style="width: 300px; height: 300px;">
                </dotlottie-player>
            </div>
        </div>
    </div>
</section>

<!-- Customer Testimonials -->
<section class="testimonials-section">
    <div class="container">
        <h2 class="section-title">Don't Just Take Our Word for It, See What Our Customers Are Saying</h2>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">Just used the Reporting Agent and love it! I created a report in minutes that would have taken a lot longer, as it involved multiple vendors.</p>
                <div class="testimonial-author">
                    <strong>Sondra Brandt</strong><br>
                    Accounting Manager, SugarCRM
                </div>
                <div class="testimonial-logo">SugarCRM</div>
            </div>
            
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">Tipalti's AI capabilities make our work so much easier. With invoice capture and real-time reporting, my team has complete visibility into approvals, coding history, and potential errors.</p>
                <div class="testimonial-author">
                    <strong>Kanan Mammadov</strong><br>
                    VP of Procurement, Lantern Community Services
                </div>
                <div class="testimonial-logo">LANTERN</div>
            </div>
            
            <div class="testimonial-card">
                <div class="quote-icon">"</div>
                <p class="testimonial-text">I've been using Tipalti's Reporting Agent for building and sending out payment reports. It's been perfect for that. I just tell it what I need and it saves me a lot of time.</p>
                <div class="testimonial-author">
                    <strong>Jemima Westwood</strong><br>
                    Accounts Associate, Lendable
                </div>
                <div class="testimonial-logo">Lendable</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Want to Learn More About Tipalti AI?</h2>
        <p>Book a demo to see Tipalti AI in action.</p>
        <a href="#" class="btn btn-primary">Request a Demo</a>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section">
    <div class="container">
        <span class="section-label">Recommendations</span>
        <h2 class="section-title">Access All Insights on Tipalti AI</h2>
        
        <div class="resources-grid">
            <div class="resource-card">
                <h3>AI in Finance: What It Means for Teams, Compliance, and Growth</h3>
                <div class="resource-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/scan-expenses-receipt-agent.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 200px; height: 150px;">
                    </dotlottie-player>
                </div>
                <div class="resource-tags">
                    <span class="tag">AI</span>
                    <span class="tag">Financial Technology</span>
                    <span class="tag">Finops</span>
                </div>
                <p>Discover how AI is reshaping finance—from fraud detection and risk management to predictive analytics and compliance—driving smarter decisions and sustainable growth.</p>
            </div>
            
            <div class="resource-card">
                <h3>Complete Guide to AI in Accounts Payable</h3>
                <div class="resource-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-2.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 200px; height: 150px;">
                    </dotlottie-player>
                </div>
                <div class="resource-tags">
                    <span class="tag">Accounts Payable</span>
                    <span class="tag">AI</span>
                    <span class="tag">Automation Trends</span>
                </div>
                <p>Learn how artificial intelligence is revolutionizing processes, enhancing efficiency, and driving bottom-line results in this exploration of the impact of AI on accounts payable.</p>
            </div>
            
            <div class="resource-card">
                <h3>Ultimate Guide to AI Invoice Processing</h3>
                <div class="resource-animation">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-matching-agent.lottie" 
                        loop 
                        renderer="svg"
                        data-lazy="true" 
                        style="width: 200px; height: 150px;">
                    </dotlottie-player>
                </div>
                <div class="resource-tags">
                    <span class="tag">Accounts Payable</span>
                    <span class="tag">AI</span>
                    <span class="tag">AP Automation</span>
                </div>
                <p>Explore the top applications and benefits of AI invoice processing. From streamlined cash flow management to increased transparency and control, discover if it's right for your business.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQs Section -->
<section class="faqs-section">
    <div class="container">
        <div class="faqs-content">
            <div class="faqs-header">
                <span class="section-label">FAQs</span>
                <h2 class="section-title">Still Have<br>Questions?</h2>
            </div>
            
            <div class="faqs-list">
                <div class="faq-item">
                    <h3>What is Tipalti AI?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-item">
                    <h3>How is AI used in accounts payable?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-item">
                    <h3>How does AI improve the efficiency of AP workflow?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-item">
                    <h3>How do AI agents work in the context of AP automation?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-item">
                    <h3>What is the difference between AI invoice automation, accounts payable AI, and AI-powered AP automation?</h3>
                    <span class="faq-toggle">+</span>
                </div>
                <div class="faq-item">
                    <h3>Can AI improve global compliance and reduce fraud in AP?</h3>
                    <span class="faq-toggle">+</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Vimeo Video Section -->
<section class="video-section">
    <div class="container">
        <h2 class="section-title">See Tipalti AI in Action</h2>
        <div class="video-container">
            <div class="video-facade" data-src="https://player.vimeo.com/video/123456789">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/video-poster.jpg" alt="Tipalti AI Demo Video" width="1280" height="720" loading="lazy">
                <button class="play-button">▶</button>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>