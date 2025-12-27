<?php
/*
 * @package Twenty Minutes
 */


 function twenty_minutes_admin_enqueue_scripts() {
    wp_enqueue_style( 'twenty-minutes-admin-style', esc_url( get_template_directory_uri() ).'/css/addon.css' );
}
add_action( 'admin_enqueue_scripts', 'twenty_minutes_admin_enqueue_scripts' );

function twenty_minutes_theme_info_menu_link() {

    $twenty_minutes_theme = wp_get_theme();
    add_theme_page(
        /* translators: 1: Theme name. */
        sprintf( esc_html__( 'Welcome to %1$s', 'twenty-minutes' ), $twenty_minutes_theme->get( 'Name' )),
        esc_html__( 'Theme Demo Import', 'twenty-minutes' ),
        'edit_theme_options',
        'twenty-minutes',
        'twenty_minutes_theme_info_page'
    );
}
add_action( 'admin_menu', 'twenty_minutes_theme_info_menu_link' );

function twenty_minutes_theme_info_page() {

    $twenty_minutes_theme = wp_get_theme();
    ?>
<div class="wrap theme-info-wrap">
    <h1><?php printf( esc_html__( 'Welcome to %1$s', 'twenty-minutes' ), esc_html($twenty_minutes_theme->get( 'Name' ))); ?>
    </h1>
    <p class="theme-description">
    <?php esc_html_e( 'Do you want to configure this theme? Look no further, our easy-to-follow theme documentation will walk you through it.', 'twenty-minutes' ); ?>
    </p>
    <div class="columns-wrapper clearfix theme-demo">
        <div class="column column-quarter clearfix start-box"> 
            <div class="demo-import">
                <div class="theme-name">
                    <h2><?php echo esc_html( $twenty_minutes_theme->get( 'Name' ) ); ?></h2>
                    <p class="version"><?php esc_html_e( 'Version', 'twenty-minutes' ); ?>: <?php echo esc_html( wp_get_theme()->get( 'Version' ) ); ?></p>	
                </div>
                <?php
                    $twenty_minutes_demo_content_file = apply_filters(
                        'twenty_minutes_demo_content_path',
                        get_parent_theme_file_path( '/inc/demo-content.php' )
                    );
                    require $twenty_minutes_demo_content_file;             
                ?>               
                <div id="demo-import-loader">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/images/status.gif'); ?>" alt="<?php echo esc_attr( 'Loading...', 'twenty-minutes'); ?>" />
                </div>
            </div>
        </div>
        <div class="column column-half clearfix">
            <div class="important-link">
                <div class="main-box columns-wrapper clearfix">

                    <div class="themelink column column-half column-border clearfix">
                        <p><strong><?php esc_html_e( 'Free Theme Documentation', 'twenty-minutes' ); ?></strong></p>
                        <p><?php esc_html_e( 'Need more details? Please check our full documentation for detailed theme setup.', 'twenty-minutes' ); ?></p>
                        <a href="<?php echo esc_url( TWENTY_MINUTES_THEME_DOCUMENTATION ); ?>" target="_blank">
                        <?php esc_html_e( 'Documentation', 'twenty-minutes' ); ?>
                        </a>
                    </div>

                    <div class="themelink column column-half column-padding clearfix">
                        <p><strong><?php esc_html_e( 'Need Help?', 'twenty-minutes' ); ?></strong></p>
                        <p><?php esc_html_e( 'Go to our support forum to help you out in case of queries and doubts regarding our theme.', 'twenty-minutes' ); ?></p>
                        <a href="<?php echo esc_url( TWENTY_MINUTES_SUPPORT ); ?>" target="_blank">
                        <?php esc_html_e( 'Contact Us', 'twenty-minutes' ); ?>
                        </a>
                    </div>
                </div>
                <hr>
                <div class="main-box columns-wrapper clearfix">

                    <div class="themelink column column-half column-border clearfix">
                        <p><strong><?php esc_html_e( 'Pro version of our theme', 'twenty-minutes' ); ?></strong></p>
                        <p><?php esc_html_e( 'Are you excited for our theme? Then we will proceed for pro version of theme.', 'twenty-minutes' ); ?></p>
                        <a class="get-premium" href="<?php echo esc_url( TWENTY_MINUTES_PREMIUM_PAGE ); ?>" target="_blank">
                        <?php esc_html_e( 'Get Premium', 'twenty-minutes' ); ?>
                        </a>
                    </div>

                    <div class="themelink column column-half column-padding clearfix">
                        <p><strong><?php esc_html_e( 'Leave us a review', 'twenty-minutes' ); ?></strong></p>
                        <p><?php esc_html_e( 'Are you enjoying our theme? We would love to hear your feedback.', 'twenty-minutes' ); ?></p>
                        <a href="<?php echo esc_url( TWENTY_MINUTES_REVIEW ); ?>" target="_blank">
                        <?php esc_html_e( 'Rate This Theme', 'twenty-minutes' ); ?>
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <div class="column column-quarter clearfix start-box"> 
            <div class="bundle-info">
                <img src="<?php echo esc_url( get_template_directory_uri().'/images/bundle.png'); ?>" alt="<?php echo esc_attr( 'screenshot', 'twenty-minutes'); ?>" class="bundle-image"/>
                <div class="bundle-content themelink">
                    <h3><?php esc_html_e( 'WordPress Theme Bundle', 'twenty-minutes' ); ?></h3>
                    <small><b><?php esc_html_e( 'Get access to a collection of 100+ stunning WordPress themes for just $99 — featuring designs for every business niche!', 'twenty-minutes' ); ?></small></b>
                    <a class="get-premium" href="<?php echo esc_url( TWENTY_MINUTES_BUNDLE_PAGE ); ?>" target="_blank">
                    <?php esc_html_e( 'Get Bundle at 20% OFF', 'twenty-minutes' ); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="getting-started">
        <div class="section">
            <h3><?php 
            /* translators: %s: Theme name. */
            printf( esc_html__( 'Getting started with %s', 'twenty-minutes' ),
            esc_html($twenty_minutes_theme->get( 'Name' ))); ?></h3>
            <div class="columns-wrapper clearfix">
                <div class="column column-half clearfix">
                    <div class="section themelink">
                        <div class="">
                            <a class="" href="<?php echo esc_url( TWENTY_MINUTES_PREMIUM_PAGE ); ?>" target="_blank"><?php esc_html_e( 'Get Premium', 'twenty-minutes' ); ?></a>
                            <a href="<?php echo esc_url( TWENTY_MINUTES_PRO_DEMO ); ?>" target="_blank"><?php esc_html_e( 'View Demo', 'twenty-minutes' ); ?></a>
                            <a class="get-premium" href="<?php echo esc_url( TWENTY_MINUTES_BUNDLE_PAGE ); ?>" target="_blank"><?php esc_html_e( 'Bundle of 100+ Themes at $99', 'twenty-minutes' ); ?></a>
                        </div>
                        <div class="theme-description-1"><?php echo esc_html($twenty_minutes_theme->get( 'Description' )); ?></div>
                    </div>
                </div>
                <div class="column column-half clearfix">
                    <img src="<?php echo esc_url( $twenty_minutes_theme->get_screenshot() ); ?>" alt="<?php echo esc_attr( 'screenshot', 'twenty-minutes'); ?>"/>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="theme-author">
      <p><?php
        /* translators: 1: Theme name, 2: Author name, 3: Call to action text. */
        printf( esc_html__( '%1$s is proudly brought to you by %2$s. If you like this theme, %3$s :)', 'twenty-minutes' ),
            esc_html($twenty_minutes_theme->get( 'Name' )),
            '<a target="_blank" href="' . esc_url( 'https://www.theclassictemplates.com/', 'twenty-minutes' ) . '">classictemplate</a>',
            '<a target="_blank" href="' . esc_url(TWENTY_MINUTES_REVIEW ) . '" title="' . esc_attr__( 'Rate it', 'twenty-minutes' ) . '">' . esc_html_x( 'rate it', 'If you like this theme, rate it', 'twenty-minutes' ) . '</a>'
        );
        ?></p>
    </div>
</div>
<?php
}
?>