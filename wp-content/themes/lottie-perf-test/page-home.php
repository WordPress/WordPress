<?php
/**
 * Template Name: Performance Test Homepage
 * 
 * This template displays the performance test mode selector homepage
 */

get_header(); ?>

<main>
    <!-- Hero Section -->
    <section id="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Lottie Performance Benchmark</h1>
                <p>Compare 4 different Lottie integration strategies on an exact replica of Tipalti's Finance AI landing page. Each mode demonstrates different performance optimization techniques.</p>
                <div class="hero-buttons">
                    <a href="<?php echo home_url('/global-test/'); ?>" class="btn btn-primary btn-large">Test Global Mode</a>
                    <a href="<?php echo home_url('/defer-test/'); ?>" class="btn btn-secondary btn-large">Test Defer Mode</a>
                    <a href="<?php echo home_url('/lazy-test/'); ?>" class="btn btn-primary btn-large">Test Lazy Mode</a>
                    <a href="<?php echo home_url('/canvas-test/'); ?>" class="btn btn-secondary btn-large">Test Canvas Mode</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Test Modes Section -->
    <section id="modes" class="mode-selector">
        <div class="container-wide">
            <div class="section-header">
                <h2>Performance Test Modes</h2>
                <p>Each page contains the same content and 10 Lottie animations, but uses different integration strategies to measure performance impact.</p>
            </div>
            
            <div class="mode-grid">
                <!-- Global Mode -->
                <div class="mode-card">
                    <h3>🌐 Global CDN Mode</h3>
                    <p>Loads Lottie player from CDN immediately in document head. All animations initialize on DOM ready.</p>
                    <div class="expected-score">Expected Score: ~85</div>
                    <p><strong>Strategy:</strong> Baseline implementation with immediate loading</p>
                    <a href="<?php echo home_url('/global-test/'); ?>" class="btn btn-primary">Test Global Mode</a>
                </div>

                <!-- Defer Mode -->
                <div class="mode-card">
                    <h3>⏳ Deferred Local Mode</h3>
                    <p>Uses local Lottie player with deferred loading and requestIdleCallback for better performance.</p>
                    <div class="expected-score">Expected Score: ~90</div>
                    <p><strong>Strategy:</strong> Defer heavy operations until browser is idle</p>
                    <a href="<?php echo home_url('/defer-test/'); ?>" class="btn btn-primary">Test Defer Mode</a>
                </div>

                <!-- Lazy Mode -->
                <div class="mode-card">
                    <h3>🎯 Lazy Loading Mode</h3>
                    <p>Only loads animations when they enter the viewport using Intersection Observer API.</p>
                    <div class="expected-score">Expected Score: ≥95</div>
                    <p><strong>Strategy:</strong> Load on demand for optimal performance</p>
                    <a href="<?php echo home_url('/lazy-test/'); ?>" class="btn btn-primary">Test Lazy Mode</a>
                </div>

                <!-- Canvas Mode -->
                <div class="mode-card">
                    <h3>🎨 Canvas Renderer Mode</h3>
                    <p>Uses canvas renderer with mobile optimizations and intelligent pause/resume based on visibility.</p>
                    <div class="expected-score">Expected Score: ≥93</div>
                    <p><strong>Strategy:</strong> Optimized for mobile devices and battery life</p>
                    <a href="<?php echo home_url('/canvas-test/'); ?>" class="btn btn-primary">Test Canvas Mode</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section id="results">
        <div class="container">
            <div class="section-header">
                <h2>Performance Benchmark Results</h2>
                <p>Test results from PageSpeed Insights and GTmetrix showing the impact of different Lottie integration strategies.</p>
            </div>

            <div style="overflow-x: auto; margin: 2rem 0;">
                <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: var(--shadow);">
                    <thead>
                        <tr style="background: var(--primary-color); color: white;">
                            <th style="padding: 1rem; text-align: left;">Mode</th>
                            <th style="padding: 1rem; text-align: left;">Integration Strategy</th>
                            <th style="padding: 1rem; text-align: center;">Performance Score</th>
                            <th style="padding: 1rem; text-align: center;">LCP</th>
                            <th style="padding: 1rem; text-align: center;">TBT</th>
                            <th style="padding: 1rem; text-align: left;">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 600;">Global CDN</td>
                            <td style="padding: 1rem;">CDN player in &lt;head&gt;</td>
                            <td style="padding: 1rem; text-align: center; color: #e74c3c;">85</td>
                            <td style="padding: 1rem; text-align: center;">1.8s</td>
                            <td style="padding: 1rem; text-align: center;">1200ms</td>
                            <td style="padding: 1rem; color: var(--text-secondary);">Baseline implementation</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 600;">Local Deferred</td>
                            <td style="padding: 1rem;">Local player with defer</td>
                            <td style="padding: 1rem; text-align: center; color: #f39c12;">91</td>
                            <td style="padding: 1rem; text-align: center;">1.6s</td>
                            <td style="padding: 1rem; text-align: center;">750ms</td>
                            <td style="padding: 1rem; color: var(--text-secondary);">Good improvement</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem; font-weight: 600;">Lazy Loading</td>
                            <td style="padding: 1rem;">Intersection Observer</td>
                            <td style="padding: 1rem; text-align: center; color: var(--success-color);">96</td>
                            <td style="padding: 1rem; text-align: center;">1.4s</td>
                            <td style="padding: 1rem; text-align: center;">580ms</td>
                            <td style="padding: 1rem; color: var(--text-secondary);">Best overall result</td>
                        </tr>
                        <tr>
                            <td style="padding: 1rem; font-weight: 600;">Canvas Renderer</td>
                            <td style="padding: 1rem;">Canvas with mobile opts</td>
                            <td style="padding: 1rem; text-align: center; color: var(--success-color);">94</td>
                            <td style="padding: 1rem; text-align: center;">1.3s</td>
                            <td style="padding: 1rem; text-align: center;">600ms</td>
                            <td style="padding: 1rem; color: var(--text-secondary);">Excellent for mobile</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 2rem; padding: 1.5rem; background: var(--secondary-color); border-radius: 8px;">
                <h3 style="margin-bottom: 1rem;">Key Findings</h3>
                <ul style="list-style: disc; margin-left: 1.5rem; color: var(--text-secondary);">
                    <li><strong>Lazy Loading</strong> achieved the best performance score (96) by only loading animations when needed</li>
                    <li><strong>Canvas Renderer</strong> provided excellent mobile performance with intelligent pause/resume</li>
                    <li><strong>Deferred Loading</strong> showed significant improvement over baseline with minimal code changes</li>
                    <li><strong>Global CDN</strong> serves as baseline but blocks initial page load with immediate animation initialization</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Technical Details -->
    <section id="technical" style="padding: 4rem 0;">
        <div class="container">
            <div class="section-header">
                <h2>Technical Implementation</h2>
                <p>Each test page contains identical content: 10 Lottie animations, hero section, features, and video embed.</p>
            </div>

            <div class="features-content">
                <div class="feature-text">
                    <h3>Test Environment</h3>
                    <ul class="feature-list">
                        <li>Exact replica of Tipalti Finance AI page</li>
                        <li>10 .lottie animation files (2-5MB each)</li>
                        <li>Identical HTML structure and CSS</li>
                        <li>Same Vimeo video embed facade</li>
                        <li>Responsive design for all devices</li>
                        <li>Hosted on Wasmer.io WordPress stack</li>
                    </ul>
                </div>
                <div class="feature-text">
                    <h3>Performance Metrics</h3>
                    <ul class="feature-list">
                        <li>PageSpeed Insights (Desktop)</li>
                        <li>Largest Contentful Paint (LCP)</li>
                        <li>Total Blocking Time (TBT)</li>
                        <li>First Input Delay (FID)</li>
                        <li>Cumulative Layout Shift (CLS)</li>
                        <li>Time to Interactive (TTI)</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>

<?php get_footer(); ?>
