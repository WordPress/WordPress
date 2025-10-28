<?php
/**
 * Template Name: Step 1 - Local Player Test
 * Description: Tipalti Finance AI replica - Step 1: Complete Tipalti AI page implementation
 */

get_header(); ?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(135deg, #080707 0%, #1a1a1a 100%); min-height: 525px; padding: 80px 0; position: relative; overflow: hidden;">
    <div class="hero-background" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url('https://tipalti.com/en-eu/wp-content/uploads/sites/3/2025/09/Tipalti-AI-Header-1440x522-300dpi.jpg'); background-size: cover; background-position: center; opacity: 0.3;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="hero-content" style="text-align: center; color: white;">
            <div class="hero-logo" style="margin-bottom: 20px;">
                <img src="https://tipalti.com/wp-content/uploads/2025/09/Tipalti-AI-Logo_White-300x90.png" 
                     alt="Tipalti AI Logo" 
                     style="width: 200px; height: auto;" />
            </div>
            <h1 class="hero-title" style="font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 500; margin-bottom: 20px; line-height: 1.2;">
                Redefine Finance<br>Productivity with AI
            </h1>
            <p class="hero-description" style="font-size: 1.25rem; line-height: 1.6; margin-bottom: 40px; max-width: 800px; margin-left: auto; margin-right: auto; padding: 0 20px;">
                Powering Tipalti Accounts Payable with Tipalti AI Assistant and Agents. Unlock new levels of productivity and elevate your focus from operational tasks to more strategy and value.
            </p>
            <div class="hero-cta" style="margin-bottom: 40px;">
                <button class="btn-primary" style="background: #4d62d3; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 1rem; cursor: pointer; margin: 0 10px;">Request a Demo</button>
            </div>
            <!-- Performance Metrics (Hidden by default, shown for testing) -->
            <div class="performance-metrics" style="margin-top: 20px; padding: 10px; background: rgba(0,0,0,0.3); border-radius: 8px; font-size: 12px; display: inline-block;">
                <strong>Step 1 Performance:</strong>
                FCP: <span id="fcp">-</span> | 
                LCP: <span id="lcp">-</span> | 
                TBT: <span id="tbt">-</span> | 
                CLS: <span id="cls">-</span> | 
                Score: <span id="score">-</span>
            </div>
        </div>
    </div>
</section>

<!-- AI Assistant Section -->
<section class="ai-assistant-section" style="padding: 80px 0; background: #fafafa;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="ai-assistant-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <div class="assistant-video" style="position: relative; border-radius: 24px; overflow: hidden; aspect-ratio: 16/9;">
                <lite-vimeo 
                    videoid="1121254619" 
                    aspect-ratio="16/9"
                    autoplay
                    muted
                    loop
                    style="width: 100%; height: 100%;">
                </lite-vimeo>
            </div>
            <div class="assistant-text">
                <p class="section-tag" style="color: #4d62d3; font-size: 0.875rem; font-weight: 500; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Tipalti AI Assistant</p>
                <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">Meet Your New Sidekick: Tipalti AI Assistant</h2>
                <p style="font-size: 1.125rem; line-height: 1.6; margin-bottom: 36px; color: #6c6c6c;">
                    Tipalti's 24/7 AI Assistant is a conversational tool for finance professionals that combines deep Tipalti knowledge of your finance workflows—such as invoices, purchase requests, and purchase orders—with advanced reasoning to save you time on routine tasks, deliver insights, and uncover opportunities.
                </p>
                <button class="btn-secondary" style="background: #4d62d3; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 1rem; cursor: pointer;">Request a Demo</button>
            </div>
        </div>
    </div>
</section>

<!-- AI Agents Section -->
<section class="ai-agents-section" style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <p class="section-tag" style="color: #4d62d3; font-size: 0.875rem; font-weight: 500; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Tipalti AI Agents</p>
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">Get to Know the Tipalti AI Agents</h2>
            <p style="font-size: 1.25rem; line-height: 1.6; color: #6c6c6c; max-width: 800px; margin: 0 auto;">
                Tipalti AI agents autonomously execute prescribed routine finance tasks, such as generating reports, identifying approvers, or creating purchase requests, freeing you to focus on what matters most.
            </p>
        </div>
        
        <div class="agents-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px; margin-bottom: 40px;">
            <div class="agent-card" style="display: flex; flex-direction: column; align-items: flex-start;">
                <div class="agent-animation" style="margin-bottom: 24px; width: 200px; height: 200px; position: relative;">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px; position: absolute; top: 0; left: 0;" 
                        loop 
                        autoplay
                        data-lazy>
                    </dotlottie-player>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Reporting Agent</h3>
                <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c;">Access real-time spend insights at your fingertips. Instantly create custom reports using natural language.</p>
            </div>

            <div class="agent-card" style="display: flex; flex-direction: column; align-items: flex-start;">
                <div class="agent-animation" style="margin-bottom: 24px; width: 200px; height: 200px; position: relative;">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px; position: absolute; top: 0; left: 0;" 
                        loop 
                        autoplay
                        data-lazy>
                    </dotlottie-player>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Bill Approvers Agent</h3>
                <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c;">Eliminate approval bottlenecks. The system predicts and recommends the right approver—fast.</p>
            </div>

            <div class="agent-card" style="display: flex; flex-direction: column; align-items: flex-start;">
                <div class="agent-animation" style="margin-bottom: 24px; width: 200px; height: 200px; position: relative;">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-request-agent.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 200px; height: 200px; position: absolute; top: 0; left: 0;" 
                        loop 
                        autoplay
                        data-lazy>
                    </dotlottie-player>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Purchase Request Agent</h3>
                <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c;">Auto-generate complete purchase requests from simple employee descriptions.</p>
            </div>
        </div>
    </div>
</section>

<!-- AI Agents Workflow Section -->
<section class="agents-workflow-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="workflow-header" style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414; max-width: 900px; margin-left: auto; margin-right: auto;">
                Tipalti AI Agents streamline finance workflows, working for you even when you don't see them.
            </h2>
        </div>
        
        <!-- Workflow Slider/Tabs -->
        <div class="workflow-tabs" style="background: white; border-radius: 24px; padding: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
            <div class="workflow-inner" style="display: flex; gap: 40px; align-items: stretch;">
                <div class="media-viewer" style="flex: 1; display: flex; align-items: center; justify-content: center; width: 480px; height: 360px; position: relative; margin: 0 auto;">
                    <dotlottie-player 
                        class="workflow-player"
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie"
                        background="transparent"
                        speed="1"
                        style="width: 480px; height: 360px; position: absolute; top: 0; left: 0;"
                        loop
                        autoplay
                        data-lazy>
                    </dotlottie-player>
                </div>
                <div class="tab-navigation" style="display: flex; flex-direction: column; gap: 20px; border-left: 1px solid #efefef; padding-left: 40px; min-width: 300px;">
                <div class="tab-item active" data-tab="invoice-capture" style="padding: 20px; border-radius: 12px; background: #e4edfb; cursor: pointer; border-left: 4px solid #4d62d3;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 8px; color: #141414;">Invoice Capture Agent</h3>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Cut manual invoice coding time with AI invoice automation that captures invoice data and fills fields instantly</p>
                </div>
                <div class="tab-item" data-tab="tax-form" style="padding: 20px; border-radius: 12px; cursor: pointer; border-left: 4px solid transparent;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 8px; color: #141414;">Tax Form Scan Agent</h3>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Ensure supplier tax compliance with automation that extracts W-9 data and accelerates onboarding.</p>
                </div>
                <div class="tab-item" data-tab="po-matching" style="padding: 20px; border-radius: 12px; cursor: pointer; border-left: 4px solid transparent;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 8px; color: #141414;">PO Matching Agent</h3>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Reduce manual matching tasks—let AI analyze contextual descriptions to automatically match bills and POs.</p>
                </div>
                <div class="tab-item" data-tab="erp-sync" style="padding: 20px; border-radius: 12px; cursor: pointer; border-left: 4px solid transparent;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 8px; color: #141414;">ERP Sync Resolution Agent</h3>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Resolve ERP and accounting software sync issues in minutes, not hours, with automation that diagnoses and guides every fix.</p>
                </div>
                <div class="tab-item" data-tab="expense-receipt" style="padding: 20px; border-radius: 12px; cursor: pointer; border-left: 4px solid transparent;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 8px; color: #141414;">Expense Receipt Scan Agent</h3>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Capture employee expenses effortlessly with automation that extracts receipt data and accelerates expense reporting submission.</p>
                </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Productivity Section -->
<section class="productivity-section" style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">Boost Productivity Without Sacrificing Control</h2>
            <p style="font-size: 1.25rem; line-height: 1.6; color: #6c6c6c; max-width: 800px; margin: 0 auto;">
                Tipalti AI works with built-in financial controls and checks, ensuring efficiency while keeping you in charge.
            </p>
        </div>
        
        <div class="productivity-features" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px;">
            <div class="feature-card" style="background: #efefef; border-radius: 24px; padding: 40px; position: relative; overflow: hidden;">
                <div style="background-image: url('https://tipalti.com/wp-content/uploads/2025/09/whitegrid.svg'); background-size: cover; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.5;"></div>
                <div style="position: relative; z-index: 2;">
                    <h3 style="font-size: 1.75rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Duplicate Bill<br>Detection</h3>
                    <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c; margin-bottom: 40px;">
                        Prevent fraud and overpayments. Tipalti AI strengthens AP controls by flagging duplicate invoices and anomalies early.
                    </p>
                    <div class="feature-animation" style="text-align: center; width: 300px; height: 200px; position: relative; margin: 0 auto;">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/duplicate-bill-detection.lottie" 
                            background="transparent" 
                            speed="1" 
                            style="width: 300px; height: 200px; position: absolute; top: 0; left: 0;" 
                            loop 
                            autoplay
                            data-lazy>
                        </dotlottie-player>
                    </div>
                </div>
            </div>

            <div class="feature-card" style="background: #efefef; border-radius: 24px; padding: 40px; position: relative; overflow: hidden;">
                <div style="background-image: url('https://tipalti.com/wp-content/uploads/2025/09/whitegrid.svg'); background-size: cover; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.5;"></div>
                <div style="position: relative; z-index: 2;">
                    <h3 style="font-size: 1.75rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Approval Chain and<br>Audit Trails</h3>
                    <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c; margin-bottom: 40px;">
                        Manage approvals, payments, and audit trails across multiple entities in one consolidated view with clear visibility into each entity.
                    </p>
                    <div class="feature-animation" style="text-align: center; width: 300px; height: 200px; position: relative; margin: 0 auto;">
                        <dotlottie-player 
                            src="<?php echo get_template_directory_uri(); ?>/assets/lottie/approval-chains-and-audit-trail.lottie" 
                            background="transparent" 
                            speed="1" 
                            style="width: 300px; height: 200px; position: absolute; top: 0; left: 0;" 
                            loop 
                            autoplay
                            data-lazy>
                        </dotlottie-player>
                    </div>
                </div>
            </div>
        </div>

        <!-- PO Matching Section -->
        <div class="po-matching-card" style="background: #efefef; border-radius: 24px; padding: 40px; position: relative; overflow: hidden; margin-bottom: 40px;">
            <div style="background-image: url('https://tipalti.com/wp-content/uploads/2025/09/grid.png'); background-size: cover; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.5;"></div>
            <div style="position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                <div>
                    <h3 style="font-size: 1.75rem; font-weight: 500; margin-bottom: 16px; color: #141414;">Two and Three-way<br>PO Matching</h3>
                    <p style="font-size: 1rem; line-height: 1.6; color: #6c6c6c;">
                        Strengthen reviews with built-in two- and three-way PO matching at both the header and line level, combining automation with control.
                    </p>
                </div>
                <div class="po-animation" style="text-align: center; width: 400px; height: 300px; position: relative; margin: 0 auto;">
                    <dotlottie-player 
                        src="<?php echo get_template_directory_uri(); ?>/assets/lottie/two-and-three-way-po-matching.lottie" 
                        background="transparent" 
                        speed="1" 
                        style="width: 400px; height: 300px; position: absolute; top: 0; left: 0;" 
                        loop 
                        autoplay
                        data-lazy>
                    </dotlottie-player>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <button class="btn-secondary" style="background: #4d62d3; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 1rem; cursor: pointer;">Request a Demo</button>
        </div>
    </div>
</section>

<!-- Global AP Teams Section -->
<section class="global-ap-section" style="padding: 80px 0; background: #fafafa;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="global-ap-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
            <div class="global-ap-text">
                <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">AI Built for Global AP Teams</h2>
                <p style="font-size: 1.125rem; line-height: 1.6; margin-bottom: 36px; color: #6c6c6c;">
                    Tipalti AI enhances accuracy, accelerates workflows, and strengthens control across global AP operations for finance teams. It's built on Tipalti's platform for global payments in multiple currencies, supplier management, multi-entity operations, multi-language onboarding, and end-to-end AP.
                </p>
                <button class="btn-link" style="background: none; border: none; color: #4d62d3; font-size: 1rem; cursor: pointer; text-decoration: underline;">Learn More →</button>
            </div>
            <div class="global-ap-video" style="position: relative; border-radius: 24px; overflow: hidden; aspect-ratio: 1/1;">
                <lite-vimeo 
                    videoid="1118182888" 
                    aspect-ratio="1/1"
                    autoplay
                    muted
                    loop
                    style="width: 100%; height: 100%;">
                </lite-vimeo>
            </div>
        </div>
    </div>
</section>

<!-- Customer Testimonials Section -->
<section class="testimonials-section" style="padding: 80px 0; background: white;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-header" style="text-align: center; margin-bottom: 60px;">
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">
                Don't Just Take Our Word for It, See What<br>Our Customers Are Saying
            </h2>
        </div>
        
        <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
            <div class="testimonial-card" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="company-logo" style="background: #e8eef8; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                    <img src="https://tipalti.com/wp-content/uploads/customer-toolkit-logos/sugarcrm.svg" alt="SugarCRM" style="height: 40px;">
                </div>
                <blockquote style="font-size: 1rem; line-height: 1.6; color: #141414; margin-bottom: 20px; font-style: italic;">
                    "Just used the Reporting Agent and love it! I created a report in minutes that would have taken a lot longer, as it involved multiple vendors."
                </blockquote>
                <div class="testimonial-author">
                    <p style="font-weight: 500; margin-bottom: 4px; color: #141414;">Sondra Brandt</p>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Accounting Manager, SugarCRM</p>
                </div>
            </div>

            <div class="testimonial-card" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="company-logo" style="background: #ffecbc; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                    <img src="https://tipalti.com/wp-content/uploads/customer-toolkit-logos/Lantern-New-Logo.png" alt="Lantern Community Services" style="height: 40px;">
                </div>
                <blockquote style="font-size: 1rem; line-height: 1.6; color: #141414; margin-bottom: 20px; font-style: italic;">
                    "Tipalti's AI capabilities make our work so much easier. With invoice capture and real-time reporting, my team has complete visibility into approvals, coding history, and potential errors."
                </blockquote>
                <div class="testimonial-author">
                    <p style="font-weight: 500; margin-bottom: 4px; color: #141414;">Kanan Mammadov</p>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">VP of Procurement, Lantern Community Services</p>
                </div>
            </div>

            <div class="testimonial-card" style="background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="company-logo" style="background: #ffecbc; padding: 20px; border-radius: 12px; margin-bottom: 20px; text-align: center;">
                    <img src="https://tipalti.com/wp-content/uploads/customer-toolkit-logos/lendable-logo.png" alt="Lendable" style="height: 40px;">
                </div>
                <blockquote style="font-size: 1rem; line-height: 1.6; color: #141414; margin-bottom: 20px; font-style: italic;">
                    "I've been using Tipalti's Reporting Agent for building and sending out payment reports. It's been perfect for that. I just tell it what I need and it saves me a lot of time."
                </blockquote>
                <div class="testimonial-author">
                    <p style="font-weight: 500; margin-bottom: 4px; color: #141414;">Jemima Westwood</p>
                    <p style="font-size: 0.875rem; color: #6c6c6c; margin: 0;">Accounts Associate, Lendable</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section" style="padding: 80px 0; background: linear-gradient(135deg, #141414 0%, #2a2a2a 100%); color: white; position: relative; overflow: hidden;">
    <div style="background-image: url('https://tipalti.com/wp-content/uploads/2025/09/footer-block-getty.png'); background-position: 50% 100%; background-repeat: no-repeat; background-size: contain; position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.3;"></div>
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px; position: relative; z-index: 2;">
        <div class="cta-content" style="text-align: center;">
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2;">Want to Learn More About Tipalti AI?</h2>
            <p style="font-size: 1.25rem; line-height: 1.6; margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">
                Book a demo to see Tipalti AI in action.
            </p>
            <button class="btn-primary" style="background: #4d62d3; color: white; border: none; padding: 16px 32px; border-radius: 8px; font-size: 1.125rem; cursor: pointer;">Request a Demo</button>
        </div>
    </div>
</section>

<!-- Resources Section -->
<section class="resources-section" style="padding: 80px 0; background: #fafafa;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="section-header" style="text-align: left; margin-bottom: 60px;">
            <p class="section-tag" style="color: #4d62d3; font-size: 0.875rem; font-weight: 500; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">Recommendations</p>
            <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">Access All Insights on Tipalti AI</h2>
        </div>
        
        <div class="resources-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px;">
            <div class="resource-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="resource-header" style="background: #e8eef8; padding: 30px 30px 20px;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; color: #141414; margin: 0;">AI in Finance: What It Means for Teams, Compliance, and Growth</h3>
                </div>
                <div class="resource-body" style="padding: 20px 30px 30px;">
                    <div class="resource-tags" style="margin-bottom: 16px;">
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">AI</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">Financial Technology</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Finops</span>
                    </div>
                    <p style="font-size: 0.875rem; line-height: 1.6; color: #6c6c6c; margin: 0;">Discover how AI is reshaping finance—from fraud detection and risk management to predictive analytics and compliance—driving smarter decisions and sustainable growth...</p>
                </div>
            </div>

            <div class="resource-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="resource-header" style="background: #efefef; padding: 30px 30px 20px;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; color: #141414; margin: 0;">Complete Guide to AI in Accounts Payable</h3>
                </div>
                <div class="resource-body" style="padding: 20px 30px 30px;">
                    <div class="resource-tags" style="margin-bottom: 16px;">
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">Accounts Payable</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">AI</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Automation Trends</span>
                    </div>
                    <p style="font-size: 0.875rem; line-height: 1.6; color: #6c6c6c; margin: 0;">Learn how artificial intelligence is revolutionizing processes, enhancing efficiency, and driving bottom-line results in this exploration of the impact of AI on accounts payable...</p>
                </div>
            </div>

            <div class="resource-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
                <div class="resource-header" style="background: #e8eef8; padding: 30px 30px 20px;">
                    <h3 style="font-size: 1.25rem; font-weight: 500; color: #141414; margin: 0;">Ultimate Guide to AI Invoice Processing</h3>
                </div>
                <div class="resource-body" style="padding: 20px 30px 30px;">
                    <div class="resource-tags" style="margin-bottom: 16px;">
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">Accounts Payable</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-right: 8px;">AI</span>
                        <span style="background: #e4edfb; color: #4d62d3; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">AP Automation</span>
                    </div>
                    <p style="font-size: 0.875rem; line-height: 1.6; color: #6c6c6c; margin: 0;">Explore the top applications and benefits of AI invoice processing. From streamlined cash flow management to increased transparency and control, discover if it's right for your business...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" style="padding: 80px 0; background: #efefef;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <div class="faq-content" style="display: grid; grid-template-columns: 1fr 1fr; gap: 80px;">
            <div class="faq-header">
                <p class="section-tag" style="color: #4d62d3; font-size: 0.875rem; font-weight: 500; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.5px;">FAQs</p>
                <h2 style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 500; margin-bottom: 24px; line-height: 1.2; color: #141414;">Still Have<br>Questions?</h2>
            </div>
            
            <div class="faq-list">
                <details style="border-bottom: 1px solid #cccccc; padding: 20px 0;">
                    <summary style="cursor: pointer; font-size: 1.25rem; font-weight: 500; color: #141414; list-style: none;">
                        What is Tipalti AI?
                    </summary>
                    <div style="padding-top: 16px; color: #6c6c6c; line-height: 1.6;">
                        <p>Tipalti AI powers Tipalti AI Agents and the conversational agent Tipalti AI Assistant. Tipalti AI is embedded throughout Tipalti financial automation solutions—accounts payable, global payouts, procurement, employee expenses, supplier management, and tax compliance. It streamlines and automates manual workflows or processes, such as invoice automation, purchase order matching, tax compliance, and ERP system sync resolution.</p>
                        <p>It is designed with an agentic focus in mind to automate tasks where needed and collaborate with finance teams to accomplish more complex work and higher business needs across their financial operations.</p>
                    </div>
                </details>

                <details style="border-bottom: 1px solid #cccccc; padding: 20px 0;">
                    <summary style="cursor: pointer; font-size: 1.25rem; font-weight: 500; color: #141414; list-style: none;">
                        How is AI used in accounts payable?
                    </summary>
                    <div style="padding-top: 16px; color: #6c6c6c; line-height: 1.6;">
                        <p>AI transforms accounts payable from manual, error-prone tasks, into faster, smarter, and more reliable workflows. By applying automation and intelligence across the AP finance lifecycle, AI can help with:</p>
                        <ul>
                            <li><strong>AI Invoice automation</strong> - Use OCR and NLP to automatically extract data from invoices, match them to POs, and assign GL codes.</li>
                            <li><strong>Accelerate approvals</strong> - Route invoices to the right stakeholders based on rules and context.</li>
                            <li><strong>Enhance fraud detection</strong> - Flag duplicate invoices, unusual vendor activity, or out-of-policy spend in real time.</li>
                            <li><strong>Enable self-service</strong> - AI assistants can instantly answer questions about pending approvals and payment status.</li>
                        </ul>
                    </div>
                </details>

                <details style="border-bottom: 1px solid #cccccc; padding: 20px 0;">
                    <summary style="cursor: pointer; font-size: 1.25rem; font-weight: 500; color: #141414; list-style: none;">
                        What makes Tipalti AI different?
                    </summary>
                    <div style="padding-top: 16px; color: #6c6c6c; line-height: 1.6;">
                        <p>Tipalti AI is built specifically for finance teams and integrated directly into the Tipalti platform. It combines deep domain knowledge of financial processes with advanced AI capabilities to deliver practical, immediate value while maintaining the controls and compliance requirements that finance teams need.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>
</section>

<style>
/* Responsive Styles */
@media (max-width: 768px) {
    .ai-assistant-content,
    .global-ap-content {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    
    .agents-grid {
        grid-template-columns: 1fr !important;
    }
    
    .productivity-features {
        grid-template-columns: 1fr !important;
    }
    
    .po-matching-card > div {
        grid-template-columns: 1fr !important;
        gap: 30px !important;
    }
    
    .testimonials-grid {
        grid-template-columns: 1fr !important;
    }
    
    .resources-grid {
        grid-template-columns: 1fr !important;
    }
    
    .faq-content {
        grid-template-columns: 1fr !important;
        gap: 40px !important;
    }
    
    .tab-navigation {
        border-right: none !important;
        padding-right: 0 !important;
        min-width: auto !important;
    }
    
    .workflow-tabs {
        padding: 20px !important;
    }
    
    .hero-section {
        padding: 60px 0 !important;
    }
    
    .ai-assistant-section,
    .ai-agents-section,
    .agents-workflow-section,
    .productivity-section,
    .global-ap-section,
    .testimonials-section,
    .cta-section,
    .resources-section,
    .faq-section {
        padding: 60px 0 !important;
    }
    
    .container {
        padding: 0 16px !important;
    }
    
    h1, h2 {
        text-align: center !important;
    }
    
    .section-header {
        text-align: center !important;
    }
    
    .faq-header {
        text-align: center !important;
    }
}

@media (max-width: 480px) {
    .feature-card,
    .po-matching-card {
        padding: 20px !important;
    }
    
    .testimonial-card,
    .resource-card {
        margin: 0 !important;
    }
    
    .tab-item {
        padding: 15px !important;
    }
    
    .workflow-tabs {
        padding: 15px !important;
    }
}

/* Button hover effects - CLS optimized */
.btn-primary:hover,
.btn-secondary:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    transition: transform 0.3s ease, opacity 0.3s ease;
    will-change: transform, opacity;
}

/* Tab interaction - CLS optimized */
.tab-item {
    transition: background-color 0.3s ease, border-left-color 0.3s ease;
    will-change: background-color, border-left-color;
}

.tab-item:hover {
    background: #f0f4ff !important;
}

.tab-item.active {
    background: #e4edfb !important;
    border-left-color: #4d62d3 !important;
}

/* Agent card hover effects - CLS optimized */
.agent-card {
    transition: transform 0.3s ease, opacity 0.3s ease;
    will-change: transform, opacity;
}

.agent-card:hover {
    transform: scale(1.03);
    opacity: 0.95;
}

/* Feature card hover effects - CLS optimized */
.feature-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, box-shadow;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

/* Testimonial card hover effects - CLS optimized */
.testimonial-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, box-shadow;
}

.testimonial-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}

/* Resource card hover effects - CLS optimized */
.resource-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, box-shadow;
}

.resource-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}

/* FAQ details styling */
details[open] summary {
    margin-bottom: 16px;
}

details summary::-webkit-details-marker {
    display: none;
}

details summary::after {
    content: '+';
    float: right;
    font-size: 1.5rem;
    font-weight: 300;
}

details[open] summary::after {
    content: '−';
}

/* Animation for Lottie players */
.dotlottie-player {
    max-width: 100%;
    height: auto;
}

/* Performance metrics styling */
.performance-metrics {
    font-family: 'Courier New', monospace;
    background: rgba(0,0,0,0.8) !important;
    color: #00ff00;
    border: 1px solid #333;
}
</style>

<script>
// Performance measurement script with CLS prevention
document.addEventListener('DOMContentLoaded', function() {
    // Hide skeleton loading once Lottie animations are loaded
    const lottiePlayers = document.querySelectorAll('dotlottie-player');
    
    lottiePlayers.forEach(player => {
        player.addEventListener('ready', function() {
            // Remove skeleton loading animation
            const container = this.parentElement;
            if (container) {
                container.style.background = 'transparent';
                const skeleton = container.querySelector('::before');
                if (skeleton) {
                    skeleton.style.display = 'none';
                }
            }
        });
        
        // Fallback: hide skeleton after 3 seconds
        setTimeout(() => {
            const container = player.parentElement;
            if (container) {
                container.style.background = 'transparent';
            }
        }, 3000);
    });
    
    // Tab functionality
    const tabItems = document.querySelectorAll('.tab-item');
    const workflowPlayer = document.querySelector('.workflow-player');
    const lottieMap = {
        'invoice-capture': '<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie',
        'tax-form': '<?php echo get_template_directory_uri(); ?>/assets/lottie/digitize-tax-form-collection.lottie',
        'po-matching': '<?php echo get_template_directory_uri(); ?>/assets/lottie/po-request-agent.lottie',
        'erp-sync': '<?php echo get_template_directory_uri(); ?>/assets/lottie/erp-sync-resolution-agent.lottie',
        'expense-receipt': '<?php echo get_template_directory_uri(); ?>/assets/lottie/scan-expenses-receipt-agent.lottie'
    };
    tabItems.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabItems.forEach(t => {
                t.classList.remove('active');
                t.style.background = '';
                t.style.borderLeftColor = 'transparent';
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            this.style.background = '#e4edfb';
            this.style.borderLeftColor = '#4d62d3';

            // Swap Lottie source
            const key = this.getAttribute('data-tab');
            if (workflowPlayer && key && lottieMap[key]) {
                // Pause, change src, play to avoid flashes
                try { workflowPlayer.pause(); } catch(e) {}
                workflowPlayer.setAttribute('src', lottieMap[key]);
                // Some players need a tick before play
                setTimeout(() => { try { workflowPlayer.play(); } catch(e) {} }, 50);
            }
        });
    });
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
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
        
        // Calculate a simple performance score
        const fcp = fcpEntry ? fcpEntry.startTime : 0;
        const lcp = perfData ? (perfData.loadEventEnd - perfData.loadEventStart) : 0;
        let score = 100;
        
        if (fcp > 1800) score -= 20;
        else if (fcp > 1000) score -= 10;
        
        if (lcp > 2500) score -= 20;
        else if (lcp > 1500) score -= 10;
        
        document.getElementById('score').textContent = Math.max(score, 0);
    }
});
</script>

<!-- Load optimized scripts -->
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/lite-vimeo-embed.js" defer></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/lottie-lazy-loader.js" defer></script>

<?php get_footer(); ?>
