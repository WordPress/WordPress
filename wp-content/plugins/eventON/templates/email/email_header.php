<?php
/**
 * Email Header
 *
 * @author 		eventON
 * @package 	eventON/Templates/Emails
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$evo_options = get_option('evcal_options_evcal_1');

$wrapper = "
	background-color: #e6e7e8;
	-webkit-text-size-adjust:none !important;
	margin:0;
	padding: 25px 25px 25px 25px;
";

$innner = "
	background-color: #ffffff;
	-webkit-text-size-adjust:none !important;
	margin:0;
	border-radius:5px;
";

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo get_bloginfo( 'name' ); ?></title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<div style="<?php echo $wrapper; ?>">
		<div style='<?php echo $innner;?>'>