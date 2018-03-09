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
    <meta property='og:title' content="<?php the_title(); ?>"/>
    <meta property="og:site_name" content="Simright">
    <meta property='og:image' content="<?php post_thumbnail_src('thumbnail'); ?>"/>
    <meta property='og:description' content="Simright is a platform for cloud CAE and product design"/>
    <meta property='og:url' content="<?php the_permalink()?>" />
    <meta name="keywords" content="simright,cloud CAE,web-based simulation,online simulation,finite element model converter,finite element model translator,ansys,nastran, abaqus,ls-dyna,openform,code-aster">    
    <meta name="description" content="Simright is a platform for cloud CAE and product design">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" href="https://oss.simright.com/img/favicon.ico">
    <link rel="stylesheet" href="https://oss.simright.com/static/bootstrap.min.css">
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>?v3.0" type="text/css" media="screen" />
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-86805722-1', 'auto');
    ga('send', 'pageview');
    </script>
    <!--GA Code-->
</head>
<?php flush(); ?>
<body>
    <header class="highlight">
        <section>
            <a href="/" class="logo"><img src="https://oss.simright.com/images/logo.svg" alt="simright"></a> 
            <div class="login">
            </div> 
            <nav class="navbar navbar-default" role="navigation">
                <li data-active = "index">
                    <a href="/">
                        <span>
                        <?php pll_e('Home'); ?>
                        </span>
                    </a>
                </li>
                <li class="dropdown" data-active = "products">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b><?php pll_e('Products'); ?></b> &nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/apps"><?php pll_e('Public Cloud Apps'); ?></a></li>
                        <li class="sub-app"><a href="/apps/simright-simulator"><?php pll_e('Simulator - Structural Analysis'); ?></a></li>
                        <li class="sub-app"><a href="/apps/simright-toptimizer"><?php pll_e('Toptimizer - Topology Optimization'); ?></a></li>
                        <li class="sub-app"><a href="/apps/simright-webmesher"><b  data-i18n="base.nav.webmesher"><?php pll_e('WebMesher – Pre-processor'); ?></b><b style="font-size: 12px;position: relative;top: -6px;left: 5px;color: #ec4114;">Beta</b></a></li>
                        <li class="sub-app"><a href="/apps/simright-viewer"><?php pll_e('Viewer - CAD/CAE model viewer'); ?></a></li>
                        <li class="sub-app"><a href="/apps/simright-converter"><?php pll_e('CAE Converter - CAE model converter'); ?></a></li>
                        <li class="sub-app"><a href="/apps/simright-cad-converter"><?php pll_e('CAD Converter - CAD model converter'); ?></a></li>
                        <li><a href="/products/private_cloud"><?php pll_e('Private Cloud Solutions'); ?></a></li>
                    </ul>
                </li>
                <li class="dropdown" data-active = "model-library">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><b><?php pll_e('Resources'); ?></b>&nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/resources/public-projects"><?php pll_e('Public Projects'); ?></a></li>
                        <li><a href="/resources/model-library"><?php pll_e('Model Library'); ?></a></li>
                    </ul>
                </li>
                <li data-active = "price">
                    <a href="/product-price">
                        <span><?php pll_e('Pricing'); ?></span>
                    </a>
                </li>
                <li class="dropdown" data-active = "learning">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b><?php pll_e('Learning'); ?></b>&nbsp; <i class="glyphicon glyphicon-chevron-down"></i></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="/<?php echo pll_current_language() ?>/category/video"><?php pll_e('Video Library'); ?></a></li>
                        <li><a href="/<?php echo pll_current_language() ?>/category/blogs"><?php pll_e('Blog'); ?></a></li>
                        <li><a href="/changelog" ><?php pll_e('Changelog'); ?></a></li>
                        <li><a href="/<?php echo pll_current_language() ?>/category/features" ><?php pll_e('Features'); ?></a></li>
                    </ul>
                </li>
                <li class="active" class="dropdown" data-active = "about" style="position:relative;">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b><?php pll_e('About'); ?></b>&nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span></a>
                    <ul class="dropdown-menu position-right" role="menu">
                        <li><a href="/about_us"><?php pll_e('About Us'); ?></a></li>
                        <li><a href="/contact_us"><?php pll_e('Contact Us'); ?></a></li>
                        <li><a href="/<?php echo pll_current_language() ?>/category/joinus"><?php pll_e('Join Us'); ?></a></li>
                        <li><a href="/<?php echo pll_current_language() ?>/category/news"><?php pll_e('News'); ?></a></li>
                        <li><a href="/security"><?php pll_e('Security'); ?></a></li>
                    </ul>
                </li>
                <li class="dropdown">    
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="open-google-translate">
                        <span>
                            <?php $ptranslations = pll_the_languages( array( 'show_flags' => 0,'show_names' => 0 ,'hide_current'=> 0,'hide_if_no_translation' => 1,'raw' => 1 ) );
                                foreach($ptranslations as $value){
                                    if($value['current_lang']){
                                        echo '<b>'. $value['name'].'</b>' ;
                                    }
                            } ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu position-right" role="menu" id="open-google-translate-menu">
                        <?php pll_the_languages(array( 'raw' => 0 )); ?>
                        <?php pll_the_languages(array( 'raw' => 1 )); ?>
                    </ul>
                </li>
            </nav>
        </section>
    </header>
