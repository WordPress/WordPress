<?php
/**
 * Template Name: Step 2 - Canvas Mode Test
 * Description: Local player file + Canvas renderer
 */

get_header(); ?>

<div class="container">
    <div class="test-header">
        <h1>Step 2: Local Player File + Canvas Renderer</h1>
        <p class="test-description">Local player with Canvas renderer for improved performance</p>
        <div class="test-metrics">
            <span class="metric">FCP: <span id="fcp">-</span></span>
            <span class="metric">LCP: <span id="lcp">-</span></span>
            <span class="metric">TBT: <span id="tbt">-</span></span>
            <span class="metric">CLS: <span id="cls">-</span></span>
            <span class="metric">Score: <span id="score">-</span></span>
        </div>
    </div>

    <div class="lottie-grid">
        <div class="lottie-item">
            <h3>Invoice Capture Agent</h3>
            <lottie-player 
                src="<?php echo get_template_directory_uri(); ?>/assets/lottie/invoice-capture-agent-1.lottie" 
                background="transparent" 
                speed="1" 
                style="width: 300px; height: 300px;" 
                loop 
                autoplay
                renderer="canvas">
            </lottie-player>
        </div>

        <div class="lottie-item">
            <h3>Bill Approvers Agent</h3>
            <lottie-player 
                src="<?php echo get_template_directory_uri(); ?>/assets/lottie/bill-approvers-agent.lottie" 
                background="transparent" 
                speed="1" 
                style="width: 300px; height: 300px;" 
                loop 
                autoplay
                renderer="canvas">
            </lottie-player>
        </div>

        <div class="lottie-item">
            <h3>Duplicate Bill Detection</h3>
            <lottie-player 
                src="<?php echo get_template_directory_uri(); ?>/assets/lottie/duplicate-bill-detection.lottie" 
                background="transparent" 
                speed="1" 
                style="width: 300px; height: 300px;" 
                loop 
                autoplay
                renderer="canvas">
            </lottie-player>
        </div>

        <div class="lottie-item">
            <h3>PO Matching Agent</h3>
            <lottie-player 
                src="<?php echo get_template_directory_uri(); ?>/assets/lottie/po-matching-agent.lottie" 
                background="transparent" 
                speed="1" 
                style="width: 300px; height: 300px;" 
                loop 
                autoplay
                renderer="canvas">
            </lottie-player>
        </div>
    </div>

    <div class="test-info">
        <h2>Implementation Details</h2>
        <ul>
            <li><strong>Player Source:</strong> Local dotlottie-player.min.js</li>
            <li><strong>Loading Method:</strong> Standard WordPress enqueue</li>
            <li><strong>Renderer:</strong> Canvas (improved performance)</li>
            <li><strong>Loading Strategy:</strong> Synchronous</li>
            <li><strong>Caching:</strong> Basic WordPress caching</li>
        </ul>
    </div>
</div>

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
