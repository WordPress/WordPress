<?php
if ( ! class_exists( 'Easy_Instagram_Utils' ) ) :
	_e( 'Please install the Easy Instagram plugin.', 'Easy_Instagram' );
else :
	$ei_utils = new Easy_Instagram_Utils();
	$kses_author = array( 
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() )
	);
	if ( ! empty( $easy_instagram_elements ) ) {
		$element = $easy_instagram_elements[0];
		if ( !empty( $element['thumbnail_width'] ) ) {
			$element_width = $element['thumbnail_width'];
			$element_padding = 10; // px
			$element_style = sprintf( 'width: %dpx; padding: %dpx;', $element_width, $element_padding );
		}
	}
	else {
		$element_style = '';
	}	
?>

<div class="easy-instagram-container horizontal">
<?php foreach ( $easy_instagram_elements as $element ) : ?>
<div class='easy-instagram-thumbnail-wrapper horizontal'  style='<?php echo esc_attr( $element_style );?>'>
<?php echo $ei_utils->get_thumbnail_html( $element ); ?>
<?php if ( ! empty( $element['author'] ) ): ?>
	<div class='easy-instagram-thumbnail-author'>
		<?php echo wp_kses( $element['author'], $kses_author ); ?>
	</div>
<?php endif; ?>
<?php if ( ! empty( $element['thumbnail_caption'] ) ) : ?>
	<div class='easy-instagram-thumbnail-caption'>
		<?php echo esc_html( $element['thumbnail_caption'] ); ?>
	</div>
<?php endif; ?>

<?php if ( ! empty( $element['created_at_formatted'] ) ) : ?>
	<div class='easy-instagram-thumbnail-date'>
		<?php echo esc_html( $element['created_at_formatted'] ); ?>
	</div>	
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<br class='clear'>

<?php endif; ?>