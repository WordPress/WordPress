<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> 
        <title><?php if ( is_home() ) {
            bloginfo('name'); echo " - "; bloginfo('description');
        } elseif ( is_category() ) {
            single_cat_title(); echo " - "; bloginfo('name');
        } elseif (is_single() || is_page() ) {
            single_post_title();
        } elseif (is_search() ) {
            echo "search"; echo " - "; bloginfo('name');
        } elseif (is_404() ) {
            echo 'not found!';
        } else {
            wp_title('',true);
        } ?></title>
    <meta name="keywords" content="simright,cloud CAE,web-based simulation,online simulation,finite element model converter,finite element model translator,ansys,nastran, abaqus,ls-dyna,openform,code-aster">    
    <meta name="description" content="Simright is a platform for cloud CAE and product design">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="https://oss.simright.com/img/favicon.ico">
    <link rel="stylesheet" href="https://oss.simright.com/static/bootstrap.min.css">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
</head>
<body>
    <header class="highlight">
        <section>
            <a href="/" class="logo"><img src="https://oss.simright.com/images/logo.svg" alt="simright"></a> 
            <div class="login">
            </div> 
            <nav class="navbar navbar-default" role="navigation">
                <li class="active" data-active = "index">
                    <a href="/">
                        <span>Home</span>
                    </a>
                </li>
                <li class="dropdown" data-active = "products">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b>Products</b> &nbsp;<i class="fa fa-angle-down hidden-xs hidden-sm"></i></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/apps">Public Cloud Apps</a></li>
                        <li class="sub-app"><a href="/apps/simright-simulator">Simulator - Structural Analysis</a></li>
                        <li class="sub-app"><a href="/apps/simright-toptimizer">Toptimizer - Topology Optimization</a></li>
                        <li class="sub-app"><a href="/apps/simright-viewer">Viewer - CAD/CAE model viewer</a></li>
                        <li class="sub-app"><a href="/apps/simright-converter">CAE Converter - CAE model converter</a></li>
                        <li class="sub-app"><a href="/apps/simright-cad-converter">CAD Converter - CAD model converter</a></li>
                        <li><a href="/products/private_cloud">Private Cloud Solutions</a></li>
                    </ul>
                </li>
                <li class="dropdown" data-active = "model-library">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><b>Resources</b>&nbsp;<i class="fa fa-angle-down hidden-xs hidden-sm"></i></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/resources/public-projects">Public Projects</a></li>
                        <li><a href="/resources/model-library">Model Library</a></li>
                    </ul>
                </li>
                <li data-active = "price">
                    <a href="/product-price">
                        <span>Pricing</span>
                    </a>
                </li>
                <li data-active = "blog">
                    <a href="/blog" target="_blank">
                        <span>Blog</span>
                    </a>
                </li>
                <li class="dropdown" data-active = "about">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b> About</b>&nbsp;<i class="fa fa-angle-down hidden-xs hidden-sm"></i></span></a>
                    <ul class="dropdown-menu position-right" role="menu">
                        <li><a href="/about_us">About Us</a></li>
                        <li><a href="/contact_us">Contact Us</a></li>
                        <li><a href="/security">Security</a></li>
                        <li><a href="/qualification">公司资质</a></li>
                    </ul>
                </li>
            </nav>
        </section>
    </header>
