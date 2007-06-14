<?php

$img = 'kubrickheader.jpg';

// If we don't have image processing support, redirect.
if ( ! function_exists('imagecreatefromjpeg') )
	die(header("Location: kubrickheader.jpg"));

// Assign and validate the color values
$default = false;
$vars = array('upper'=>array('r1', 'g1', 'b1'), 'lower'=>array('r2', 'g2', 'b2'));
foreach ( $vars as $var => $subvars ) {
	if ( isset($_GET[$var]) ) {
		foreach ( $subvars as $index => $subvar ) {
			$length = strlen($_GET[$var]) / 3;
			$v = substr($_GET[$var], $index * $length, $length);
			if ( $length == 1 ) $v = '' . $v . $v;
			$$subvar = hexdec( $v );
			if ( $$subvar < 0 || $$subvar > 255 )
				$default = true;
		}
	} else {
		$default = true;
	}
}

if ( $default )
	list ( $r1, $g1, $b1, $r2, $g2, $b2 ) = array ( 105, 174, 231, 65, 128, 182 );

// Create the image
$im = imagecreatefromjpeg($img);

// Get the background color, define the rectangle height
$white = imagecolorat( $im, 15, 15 );
$h = 182;

// Define the boundaries of the rounded edges ( y => array ( x1, x2 ) )
$corners = array(
	0 => array ( 25, 734 ),
	1 => array ( 23, 736 ),
	2 => array ( 22, 737 ),
	3 => array ( 21, 738 ),
	4 => array ( 21, 738 ),
	177 => array ( 21, 738 ),
	178 => array ( 21, 738 ),
	179 => array ( 22, 737 ),
	180 => array ( 23, 736 ),
	181 => array ( 25, 734 ),
	);

// Blank out the blue thing
for ( $i = 0; $i < $h; $i++ ) {
	$x1 = 19;
	$x2 = 740;
	imageline( $im, $x1, 18 + $i, $x2, 18 + $i, $white );
}

// Draw a new color thing
for ( $i = 0; $i < $h; $i++ ) {
	$x1 = 20;
	$x2 = 739;
	$r = ( $r2 - $r1 != 0 ) ? $r1 + ( $r2 - $r1 ) * ( $i / $h ) : $r1;
	$g = ( $g2 - $g1 != 0 ) ? $g1 + ( $g2 - $g1 ) * ( $i / $h ) : $g1;
	$b = ( $b2 - $b1 != 0 ) ? $b1 + ( $b2 - $b1 ) * ( $i / $h ) : $b1;
	$color = imagecolorallocate( $im, $r, $g, $b );
	if ( array_key_exists($i, $corners) ) {
		imageline( $im, $x1, 18 + $i, $x2, 18 + $i, $white );
		list ( $x1, $x2 ) = $corners[$i];
	}
	imageline( $im, $x1, 18 + $i, $x2, 18 + $i, $color );
}

//die;
header("Content-Type: image/jpeg");
imagejpeg($im, '', 92);
imagedestroy($im);
?>
