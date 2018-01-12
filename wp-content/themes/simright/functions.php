<?php
/** widgets */
if( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name' => 'Viedeo_list_classification',
		'id'  => 'sidebar-1',
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h4>',
		'after_title' => '</h4>'
	));
}
?>

<?php
/** 注册字符串 */
pll_register_string('nav_home','Home');
pll_register_string('nav_products','Products');
pll_register_string('nav_products_public','Public Cloud Apps');
pll_register_string('nav_products_private','Private Cloud Solutions');
?>
