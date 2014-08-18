<?php
if ( ! class_exists( 'Easy_Instagram_Utils' ) ) :
	_e( 'Please install the Easy Instagram plugin.', 'Easy_Instagram' );
else :
	$ei_utils = new Easy_Instagram_Utils();
	$kses_author = array( 
		'a' => array( 'href' => array(), 'title' => array(), 'target' => array() )
	);

	$columns = 3;
	$element_padding = 10; // px
	$element_margin = 20; //px
	$current_column = 0;
	$element_width = 0;
	$style = '';

	// Calculate container width based on number of columns, thumbnail width, margin and padding
	
	if ( ! empty( $easy_instagram_elements ) ) {
		$element = $easy_instagram_elements[0];
		$element_width = $element['thumbnail_width'];
		
		$container_width = $columns * ( $element_width + 2 * $element_margin + 2 * $element_padding );
		
		$container_style = sprintf( 'width: %dpx;', $container_width );
		$element_style = sprintf( 'width: %dpx; padding: %dpx; margin: %dpx;', $element_width, $element_padding, $element_margin );
	}
	else {
		$container_style = '';
		$element_style = '';
	}
?>

<div class="easy-instagram-container grid" style='<?php echo esc_attr( $container_style );?>'>
<?php foreach ( $easy_instagram_elements as $element ) : ?>
	<div class='easy-instagram-thumbnail-wrapper' style='<?php echo esc_attr( $element_style );?>'>
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
	<?php $current_column++; ?>
	<?php if ( 0 == $current_column % $columns ) : ?>
	<br class='clear' />
	<?php endif; ?>	
<?php endforeach; ?>
</div>
<br class='clear'>

<?php endif; ?>