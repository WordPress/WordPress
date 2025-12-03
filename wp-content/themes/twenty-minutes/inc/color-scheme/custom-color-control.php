<?php

  $twenty_minutes_first_color = get_theme_mod('twenty_minutes_first_color');
  $twenty_minutes_color_scheme_css = '';

  /*------------------ Global First Color -----------*/

  if ($twenty_minutes_first_color) {
    $twenty_minutes_color_scheme_css .= ':root {';
    $twenty_minutes_color_scheme_css .= '--first-theme-color: ' . esc_attr($twenty_minutes_first_color) . ' !important;';
    $twenty_minutes_color_scheme_css .= '} ';
  }

  //---------------------------------Logo-Max-height--------- 
  $twenty_minutes_logo_width = get_theme_mod('twenty_minutes_logo_width');

  if($twenty_minutes_logo_width != false){

    $twenty_minutes_color_scheme_css .='.logo img{';

      $twenty_minutes_color_scheme_css .='width: '.esc_html($twenty_minutes_logo_width).'px;';

    $twenty_minutes_color_scheme_css .='}';
  }

  // by default header
  $twenty_minutes_hide_categorysec = get_theme_mod('twenty_minutes_hide_categorysec', false);

  if($twenty_minutes_hide_categorysec != true){

      $twenty_minutes_color_scheme_css .='.page-template-template-home-page .header{';

          $twenty_minutes_color_scheme_css .='position: static;';

      $twenty_minutes_color_scheme_css .='}';

  }

/*--------------------------- Woocommerce Product Image Border Radius -------------------*/

$twenty_minutes_woo_product_img_border_radius = get_theme_mod('twenty_minutes_woo_product_img_border_radius');
  if($twenty_minutes_woo_product_img_border_radius != false){
    $twenty_minutes_color_scheme_css .='.woocommerce ul.products li.product a img{';
    $twenty_minutes_color_scheme_css .='border-radius: '.esc_attr($twenty_minutes_woo_product_img_border_radius).'px;';
    $twenty_minutes_color_scheme_css .='}';
}

/*--------------------------- Woocommerce Product Sale Position -------------------*/    

$twenty_minutes_product_sale_position = get_theme_mod( 'twenty_minutes_product_sale_position','Left');
if($twenty_minutes_product_sale_position == 'Right'){
    $twenty_minutes_color_scheme_css .='.woocommerce ul.products li.product .onsale{';
        $twenty_minutes_color_scheme_css .='left:auto !important; right:.5em !important;';
    $twenty_minutes_color_scheme_css .='}';
}else if($twenty_minutes_product_sale_position == 'Left'){
    $twenty_minutes_color_scheme_css .='.woocommerce ul.products li.product .onsale {';
        $twenty_minutes_color_scheme_css .='right:auto !important; left:.5em !important;';
    $twenty_minutes_color_scheme_css .='}';
}        

/*--------------------------- Shop page pagination -------------------*/

$twenty_minutes_wooproducts_nav = get_theme_mod('twenty_minutes_wooproducts_nav', 'Yes');
if($twenty_minutes_wooproducts_nav == 'No'){
  $twenty_minutes_color_scheme_css .='.woocommerce nav.woocommerce-pagination{';
    $twenty_minutes_color_scheme_css .='display: none;';
  $twenty_minutes_color_scheme_css .='}';
}

/*--------------------------- Related Product -------------------*/

$twenty_minutes_related_product_enable = get_theme_mod('twenty_minutes_related_product_enable',true);
if($twenty_minutes_related_product_enable == false){
  $twenty_minutes_color_scheme_css .='.related.products{';
    $twenty_minutes_color_scheme_css .='display: none;';
  $twenty_minutes_color_scheme_css .='}';
}

/*--------------------------- Scroll to top positions -------------------*/

$twenty_minutes_scroll_position = get_theme_mod( 'twenty_minutes_scroll_position','Right');
if($twenty_minutes_scroll_position == 'Right'){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .='right: 20px;';
    $twenty_minutes_color_scheme_css .='}';
}else if($twenty_minutes_scroll_position == 'Left'){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .='left: 20px;';
    $twenty_minutes_color_scheme_css .='}';
}else if($twenty_minutes_scroll_position == 'Center'){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .='right: 50%;left: 50%;';
    $twenty_minutes_color_scheme_css .='}';
}

/*--------------------------- Scroll to Top Button Shape -------------------*/

$twenty_minutes_scroll_top_shape = get_theme_mod('twenty_minutes_scroll_top_shape', 'circle');
if($twenty_minutes_scroll_top_shape == 'box' ){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .=' border-radius: 0%';
    $twenty_minutes_color_scheme_css .='}';
}elseif($twenty_minutes_scroll_top_shape == 'curved' ){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .=' border-radius: 20%';
    $twenty_minutes_color_scheme_css .='}';
}elseif($twenty_minutes_scroll_top_shape == 'circle' ){
    $twenty_minutes_color_scheme_css .='#button{';
        $twenty_minutes_color_scheme_css .=' border-radius: 50%;';
    $twenty_minutes_color_scheme_css .='}';
}