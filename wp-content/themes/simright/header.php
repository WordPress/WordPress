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
	<?php wp_head(); ?>
</head>
<?php flush(); ?>
<body>
    <header class="highlight">
        <section>
            <a href="https://www.simright.com" class="logo"><img src="https://oss.simright.com/images/logo.svg" alt="simright"></a> 
            <div class="login">
            </div> 
            <nav class="navbar navbar-default" role="navigation">
                <li data-active = "index">
                    <a href="https://www.simright.com">
                        <span>
                        Home
                        </span>
                    </a>
                </li>
                <li class="dropdown" data-active = "products">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b>Products</b> &nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="https://www.simright.com/apps">Public Cloud Apps</a></li>
                        <li class="sub-app"><a href="https://www.simright.com/apps/simright-simulator">Simulator - Structural Analysis</a></li>
                        <li class="sub-app"><a href="https://www.simright.com/apps/simright-toptimizer">Toptimizer - Topology Optimization</a></li>
                        <li class="sub-app"><a href="https://www.simright.com/apps/simright-viewer">Viewer - CAD/CAE model viewer</a></li>
                        <li class="sub-app"><a href="https://www.simright.com/apps/simright-converter">CAE Converter - CAE model converter</a></li>
                        <li class="sub-app"><a href="https://www.simright.com/apps/simright-cad-converter">CAD Converter - CAD model converter</a></li>
                        <li><a href="https://www.simright.com/products/private_cloud">Private Cloud Solutions</a></li>
                    </ul>
                </li>
                <li class="dropdown" data-active = "model-library">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span><b>Resources</b>&nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span>
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="https://www.simright.com/resources/public-projects">Public Projects</a></li>
                        <li><a href="https://www.simright.com/resources/model-library">Model Library</a></li>
                    </ul>
                </li>
                <li data-active = "price">
                    <a href="https://www.simright.com/product-price">
                        <span>Pricing</span>
                    </a>
                </li>
                <li data-active = "blog">
                    <a href="https://www.simright.com/blog" target="_blank">
                        <span>Blog</span>
                    </a>
                </li>
                <li class="active" class="dropdown" data-active = "about">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span><b> About</b>&nbsp;<i class="glyphicon glyphicon-chevron-down"></i></span></a>
                    <ul class="dropdown-menu position-right" role="menu">
                        <li><a href="https://www.simright.com/about_us">About Us</a></li>
                        <li><a href="https://www.simright.com/contact_us">Contact Us</a></li>
                        <li><a href="https://www.simright.com/security">Security</a></li>
                        <li><a href="https://www.simright.com/qualification">公司资质</a></li>
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