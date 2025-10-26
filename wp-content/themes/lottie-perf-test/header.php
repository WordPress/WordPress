<!DOCTYPE html>
<?php
/**
 * The header for our theme
 */

?>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header>
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <?php if (is_home() && !is_front_page()) : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" rel="home"><?php bloginfo('name'); ?></a>
                <?php else : ?>
                    <?php bloginfo('name'); ?>
                <?php endif; ?>
            </div>
            
            <nav class="nav-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => false,
                ));
                ?>
                
                <?php if (!is_user_logged_in()) : ?>
                    <a href="<?php echo wp_login_url(); ?>" class="btn btn-primary">Login</a>
                <?php else : ?>
                    <div class="wp-login-links">
                        <a href="<?php echo admin_url(); ?>">Dashboard</a>
                        <a href="<?php echo wp_logout_url(); ?>">Logout</a>
                    </div>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>
