<?php
/**
 * Twenty8teen custom widgets
 * @package Twenty8teen
 */

/**
 * Supply a list of class names to present for widget styles.
 */
function twenty8teen_widget_class_choices( $dir ) {
	$choices = array_merge( array(
		'clear' => __( 'Clear Float', 'twenty8teen' ),
		'width-full' => __( 'Full width', 'twenty8teen' ),
		'width-3quarters' => __( 'Three-quarters width', 'twenty8teen' ),
		'width-2thirds' => __( 'Two-thirds width', 'twenty8teen' ),
		'width-half' => __( 'One-half width', 'twenty8teen' ),
		'width-third' => __( 'One-third width', 'twenty8teen' ),
		'width-quarter' => __( 'One-quarter width', 'twenty8teen' ),
		'body-font' => __( 'Body font', 'twenty8teen' ),
		'titles-font' => __( 'Titles font', 'twenty8teen' ),
	), twenty8teen_area_class_choices() );
	return apply_filters( 'twenty8teen_widget_class_choices', $choices, $dir );
}


/**
 * Widget for displaying template parts chosen by the user. The files in the
 * theme and child theme template-parts folder are available in the select.
 */
class Twenty8teen_template_part_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'twenty8teen-template-part',  // Base ID
			 __( '2018 Template Part', 'twenty8teen' ),   // Name
			 array(
				'classname' => 'widget_template_part',
				'description' => __( 'A theme template part', 'twenty8teen' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array( 'title' => '', 'part' => '', 'align' => '', 'class' => array() ) );
		if ( ! empty( $instance['part'] ) ) {
			$part = sanitize_file_name( $instance['part'] );
			$align = esc_attr( $instance['align'] );
			$class = array_map( 'sanitize_html_class', array_filter( (array) $instance['class'] ) );
			$class[] = $align ? ( 'align' . $align ) : '';
			$class = array_unique( array_filter( $class ) );
			$class = join( ' ', array_map( 'esc_attr', $class ) );

			if ( preg_match( '/class=([\'"])(.+?)\1/i', $args['before_widget'], $match ) ) {
				$before = explode( ' ', $match[2] );
				// Always add a custom template-part class.
				$args['before_widget'] = str_ireplace ( $match[2],
					$match[2] . ' template-part-' . $part, $args['before_widget'] );
				// Conditionally add the style classes if it has 'widget' class.
				if ( in_array( 'widget', $before ) ) {
					$args['before_widget'] = str_ireplace ( $match[2],
						$match[2] . ' ' . $class, $args['before_widget'] );
					$class = null;  //Don't want these applied twice.
				}
			}
			echo $args['before_widget'];

			twenty8teen_widget_set_classes( $part, $class );
			get_template_part( apply_filters( 'twenty8teen_widget_template_arg',
				'template-parts/' . $part ) );
			twenty8teen_widget_set_classes( $part, null );

			echo $args['after_widget'];
		}
	}

	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args( (array) $new_instance,
			array( 'title' => '', 'part' => '', 'align' => '', 'class' => array() ) );
		$instance = wp_parse_args( (array) $old_instance,
			array( 'title' => '', 'part' => '', 'align' => '', 'class' => array() ) );
		$instance['align'] = sanitize_html_class( $new_instance['align'] );
		$instance['class'] = array_map( 'sanitize_html_class', (array) $new_instance['class'] );
		$instance['part'] = sanitize_file_name( $new_instance['part'] );
		$instance['title'] = ucwords( str_replace( '-', ' ', $instance['part'] ) );
		return $instance;
	}

	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array( 'title' => '', 'part' => '', 'align' => '', 'class' => array() ) );
		$title = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$part = empty( $instance['part'] ) ? '' : esc_attr( $instance['part'] );
		$align = empty( $instance['align'] ) ? '' : esc_attr( $instance['align'] );
		$class = (array) $instance['class'];
		$class = count( $class ) ? array_map( 'esc_attr', $class ) : array();
		$id_title = esc_attr( $this->get_field_id( 'title' ) );
		$id_part = esc_attr( $this->get_field_id( 'part' ) );
		$id_align = esc_attr( $this->get_field_id( 'align' ) );
		$id_class = esc_attr( $this->get_field_id( 'class' ) );
		?>
		<p>
			<input class="widefat" id="<?php echo $id_title; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="hidden" value="<?php echo $title; ?>">
		</p>
		<p>
			<label for="<?php echo $id_part; ?>">
			<?php esc_html_e( 'Template Part:', 'twenty8teen' ); ?></label>
			<select id="<?php echo $id_part; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'part' ) ); ?>">
				<option value="" <?php selected( $part, '' ); ?>>--</option>
			<?php
			$files = twenty8teen_get_files( 'template-parts' );
			foreach ( $files as $file => $nicefile ) {
				$selected = selected( $part, $file, false );
				echo "\n\t<option value='" . esc_attr( $file ) . "' $selected>$nicefile</option>";
			}
			?></select>
		</p>
		<p>
			<label for="<?php echo $id_align; ?>">
			<?php esc_html_e( 'Alignment:', 'twenty8teen' ); ?></label>
			<select id="<?php echo $id_align; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>">
				<option value="" <?php selected( $align, '' ); ?>>--</option>
				<option value="left" <?php selected( $align, 'left' ); ?>> <?php esc_html_e( 'Left', 'twenty8teen' ); ?> </option>
				<option value="center" <?php selected( $align, 'center' ); ?>> <?php esc_html_e( 'Center', 'twenty8teen' ); ?> </option>
				<option value="right" <?php selected( $align, 'right' ); ?>> <?php esc_html_e( 'Right', 'twenty8teen' ); ?> </option>
			</select>
		</p>
		<p>
			<label for="<?php echo $id_class; ?>">
			<?php esc_html_e( 'Styles:', 'twenty8teen' ); ?></label>
			<select id="<?php echo $id_class; ?>" multiple="multiple"
				name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>[]">
				<option value="" <?php echo ( count( $class ) ? '' : 'selected' ); ?>>--</option>
				<?php
				$choices = twenty8teen_widget_class_choices( 'template-parts' );
				foreach ( $choices as $aclass => $info ) {
					$selected = selected( in_array( $aclass, $class ) );
					echo "\n\t<option value='" . $aclass . "' $selected>$info</option>";
				}
				?></select>
		</p>
		<?php
		return 'options';
	} // form

}

/**
 * Widget for displaying loop parts chosen by the user. The files in the
 * theme and child theme loop-parts folder are available to select. This widget
 * constitutes "The Loop" for displaying the parts chosen. The style class
 * applies to the widget, not the parts.
 */
class Twenty8teen_loop_part_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'twenty8teen-loop-part',  // Base ID.
			 __( '2018 Loop Parts', 'twenty8teen' ),   // Name.
			 array(
				'classname' => 'widget_loop_part',
				'description' => __( 'The theme Loop with configurable template parts', 'twenty8teen' ),
				'customize_selective_refresh' => true,
			)
		);
	}

	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance,
			array( 'title' => '', 'part' => array(), 'align' => array(), 'class' => array() ) );
		$title = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$class = array_map( 'sanitize_html_class', array_filter( (array) $instance['class'] ) );
		$class = join( ' ', array_map( 'esc_attr', $class ) );
		echo $args['before_widget'];

		if ( have_posts() ) {
			/* Start the Loop */
			while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
				<?php
				$post_format = twenty8teen_get_type_or_format();
				foreach ( $instance['part'] as $i => $part ) {
					if ( ! empty( $part ) ) {
						$part = sanitize_file_name( $part );
						$align = esc_attr( $instance['align'][$i] );
						$align = $align ? ( 'align' . $align ) : null;

						twenty8teen_widget_set_classes( $part, $align );
						get_template_part( apply_filters( 'twenty8teen_widget_loop_arg',
							'loop-parts/' . $part ), $post_format );
						twenty8teen_widget_set_classes( $part, null );

					}
				} // foreach ?>
					<div class="clear"></div>
				</article><!-- #post-<?php the_ID(); ?> -->

			<?php
			endwhile;
			wp_reset_postdata();
		}  // if

		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$new_instance = wp_parse_args( (array) $new_instance,
			array( 'title' => '', 'part' => array(), 'align' => array(), 'class' => array() ) );
		$instance = wp_parse_args( (array) $old_instance,
			array( 'title' => '', 'part' => array(), 'align' => array(), 'class' => array() ) );
		$instance['align'] = array_map( 'esc_attr', (array) $new_instance['align'] );
		$instance['class'] = array_map( 'sanitize_html_class', (array) $new_instance['class'] );
		$instance['part'] = array_map( 'sanitize_file_name', (array) $new_instance['part'] );
		$instance['title'] = trim( join( ', ', $instance['part'] ), ', ' );
		$instance['title'] = ucwords( str_replace( '-', ' ', $instance['title'] ) );
		return $instance;
	}

	public function form( $instance ) {
		$files = twenty8teen_get_files( 'loop-parts' );
		$many = count( $files );
		$instance = wp_parse_args( (array) $instance,
			array( 'title' => '',
				'part' => array_pad( array(), $many, '' ),
				'align' => array_pad( array(), $many, '' ),
				'class' => array(),
			) );
		$class = (array) $instance['class'];
		$class = count( $class ) ? array_map( 'esc_attr', $class ) : array();
		$title = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
		$id_title = esc_attr( $this->get_field_id( 'title' ) );
		$id_class = esc_attr( $this->get_field_id( 'class' ) );
		?>
		<p>
			<input class="widefat" id="<?php echo $id_title; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
				type="hidden" value="<?php echo $title; ?>">
		</p>
		<?php

		for ( $i = 0; $i < $many; $i++ ) {
			$part = ! empty( $instance['part'][$i] ) ? esc_attr( $instance['part'][$i] ) : '';
			$align = ! empty( $instance['align'][$i] ) ? esc_attr( $instance['align'][$i] ) : '';
			$id_part = esc_attr( $this->get_field_id( 'part' ) ) . $i;
			$id_align = esc_attr( $this->get_field_id( 'align' ) ) . $i;
			?>

		<p>
			<label for="<?php echo $id_part; ?>">
			<?php esc_html_e( 'Loop Part:', 'twenty8teen' ); ?></label>
			<select id="<?php echo $id_part; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'part' ) ); ?>[]">
				<option value="" <?php selected( $part, '' ); ?>>--</option>
				<?php
				foreach ( $files as $file => $nicefile ) {
					$selected = selected( $part, $file, false );
					echo "\n\t<option value='" . esc_attr( $file ) . "' $selected>$nicefile</option>";
				}
			?></select>

			<label for="<?php echo $id_align; ?>">
			<?php esc_html_e( 'Align:', 'twenty8teen' ); ?></label>
			<select id="<?php echo $id_align; ?>"
				name="<?php echo esc_attr( $this->get_field_name( 'align' ) ); ?>[]">
				<option value="" <?php selected( $align, '' ); ?>>--</option>
				<option value="none clear" <?php selected( $align, 'none clear' ); ?>> <?php esc_html_e( 'Clear', 'twenty8teen' ); ?> </option>
				<option value="left" <?php selected( $align, 'left' ); ?>> <?php esc_html_e( 'Left', 'twenty8teen' ); ?> </option>
				<option value="left clear" <?php selected( $align, 'left clear' ); ?>> <?php esc_html_e( 'Left, Clear', 'twenty8teen' ); ?> </option>
				<option value="center" <?php selected( $align, 'center' ); ?>> <?php esc_html_e( 'Center', 'twenty8teen' ); ?> </option>
				<option value="right" <?php selected( $align, 'right' ); ?>> <?php esc_html_e( 'Right', 'twenty8teen' ); ?> </option>
				<option value="right clear" <?php selected( $align, 'right clear' ); ?>> <?php esc_html_e( 'Right, Clear', 'twenty8teen' ); ?> </option>
			</select>
		</p>
		<?php
		} // for ?>
	<p>
		<label for="<?php echo $id_class; ?>">
		<?php esc_html_e( 'Styles:', 'twenty8teen' ); ?></label>
		<select id="<?php echo $id_class; ?>" multiple="multiple"
			name="<?php echo esc_attr( $this->get_field_name( 'class' ) ); ?>[]">
			<option value="" <?php echo ( count( $class ) ? '' : 'selected' ); ?>>--</option>
			<?php
			$choices = twenty8teen_widget_class_choices( 'loop-parts' );
			foreach ( $choices as $aclass => $info ) {
				$selected = selected( in_array( $aclass, $class ) );
				echo "\n\t<option value='" . esc_attr( $aclass ) . "' $selected>$info</option>";
			}
			?></select>
	</p>
	<?php
		return 'options';
	}  // form

}
