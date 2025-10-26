<?php
/**
 * The template for displaying the footer
 */

?>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php esc_html_e('Solutions', 'lottie-perf-test'); ?></h3>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
            </div>
            
            <div class="footer-section">
                <h3><?php esc_html_e('Performance Tests', 'lottie-perf-test'); ?></h3>
                <ul>
                    <li><a href="<?php echo home_url('/global-test/'); ?>">Global CDN Mode</a></li>
                    <li><a href="<?php echo home_url('/defer-test/'); ?>">Deferred Mode</a></li>
                    <li><a href="<?php echo home_url('/lazy-test/'); ?>">Lazy Loading Mode</a></li>
                    <li><a href="<?php echo home_url('/canvas-test/'); ?>">Canvas Mode</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php esc_html_e('Resources', 'lottie-perf-test'); ?></h3>
                <ul>
                    <li><a href="<?php echo home_url('/performance-dashboard/'); ?>">Performance Dashboard</a></li>
                    <li><a href="<?php echo home_url('/documentation/'); ?>">Documentation</a></li>
                    <li><a href="<?php echo home_url('/results/'); ?>">Test Results</a></li>
                    <li><a href="<?php echo home_url('/contact/'); ?>">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3><?php esc_html_e('About', 'lottie-perf-test'); ?></h3>
                <ul>
                    <li><a href="<?php echo home_url('/about/'); ?>">Project Overview</a></li>
                    <li><a href="<?php echo home_url('/methodology/'); ?>">Methodology</a></li>
                    <li><a href="<?php echo home_url('/analysis/'); ?>">Results Analysis</a></li>
                    <li><a href="<?php echo home_url('/github/'); ?>">GitHub Repository</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Built with WordPress for comprehensive Lottie performance analysis.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
