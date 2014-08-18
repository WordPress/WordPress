<table class="ssp_input widefat" id="ssp_slider_options">
	<tbody>
		
		<?php do_action( 'ssp_options_meta_box_start', $slider_id ); ?>

		<tr id="slider_skin">
			
			<td class="label">
				<label>
					<?php _e( 'Skin', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="skin">
					
					<?php foreach ( $skins as $skin ): ?>
						<option value="<?php echo $skin['path'] ?>" <?php selected( $skin['path'], $active_skin ) ?>>
							<?php echo $skin['name'] ?>
						</option>
					<?php endforeach; ?>
					
					<?php do_action( 'ssp_html_skin_select' ) ?>
				</select>
			</td>
		</tr>

		<tr id="slider_animation">
			
			<td class="label">
				<label>
					<?php _e( 'Animation', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description"></p>
			</td>
			<td>
				<select name="slider_options[animation]">
					<option value="slide" 
					<?php selected( "slide", $slider_options['animation'] ) ?>
					>Slide</option>
					<option value="fade" 
					<?php selected( "fade", $slider_options['animation'] ) ?>
					>Fade</option>

					<?php do_action( 'ssp_html_option_animation_select', $active_skin, $slider_options['animation']  ) ?>

				</select>
			</td>
		</tr>

		<tr id="slider_slideshow">
			
			<td class="label">
				<label>
					<?php _e( 'Slideshow', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Animate slider automatically', SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="radio" name="slider_options[slideshow]" value="true" <?php checked( true, $slider_options['slideshow'] ) ?>
						/> <?php _e( 'Yes', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="slider_options[slideshow]" value="false" <?php checked( false, $slider_options['slideshow'] ) ?>
						/> <?php _e( 'No', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

			</td>
		</tr>

		

		<tr id="slider_height">
			
			<td class="label">
				<label>
					<?php _e( 'Height', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Sets height for all the slides in the slider', SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="radio" name="slider_options[h_responsive]" value="true" <?php checked( true, $slider_options['h_responsive'] ) ?>
						 /> <?php _e( 'Responsive', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="slider_options[h_responsive]" value="false" <?php checked( false, $slider_options['h_responsive'] ) ?>
						 /> <?php _e( 'Fixed', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="text" style='width: 80%' name="slider_options[height]" value="<?php echo $slider_options['height'] ?>" placeholder="The value in this textbox would only be effective if the height is fixed" /> Pixels( px ) 
					</label>
				</p>

			</td>
		</tr>

		<tr id="slider_width">
			
			<td class="label">
				<label>
					<?php _e( 'Width', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Sets width for all the slides in the slider', SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="radio" name="slider_options[w_responsive]" value="true" <?php checked( true, $slider_options['w_responsive'] ) ?>
						 /> <?php _e( 'Responsive', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="slider_options[w_responsive]" value="false" <?php checked( false, $slider_options['w_responsive'] ) ?>
						 /> <?php _e( 'Fixed', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="text" style='width: 80%' name="slider_options[width]" value="<?php echo $slider_options['width'] ?>" placeholder="The value in this textbox would only be effective if the width is fixed" /> Pixels( px ) 
					</label>
				</p>

			</td>
		</tr>


		<tr id="slider_direction">
			
			<td class="label">
				<label>
					<?php _e( 'Direction', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Select the sliding direction', SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="radio" name="slider_options[direction]" value="horizontal" <?php checked( "horizontal", $slider_options['direction'] ) ?>
						/> <?php _e( 'Horizontal', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

				<p>
					<label>
						<input type="radio" name="slider_options[direction]" value="vertical" <?php checked( "vertical", $slider_options['direction'] ) ?>
						/> <?php _e( 'Vertical', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

			</td>
		</tr>

		<tr id="slider_cycle_speed">
			
			<td class="label">
				<label>
					<?php _e( 'Cycle speed', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Set the speed of the slideshow cycling',SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="text" style="width: 80%" name="slider_options[cycle_speed]" value="<?php echo $slider_options['cycle_speed'];  ?>" /> <?php _e( 'Seconds', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

			</td>
		</tr>

		<tr id="slider_animation_speed">
			
			<td class="label">
				<label>
					<?php _e( 'Animation speed', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( '',SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="text" style="width: 80%" name="slider_options[animation_speed]" value="<?php echo $slider_options['animation_speed'];  ?>" /> <?php _e( 'Seconds', SLIDER_PLUGIN_PREFIX ); ?>
					</label>
				</p>

			</td>
		</tr>

		<?php do_action( 'ssp_options_before_control_option', $slider_id ); ?>

		<tr id="slider_controls">
			
			<td class="label">
				<label>
					<?php _e( 'Navigation', SLIDER_PLUGIN_PREFIX ); ?>
				</label>
				<p class="description">
					<?php _e( 'Enable or Disable different navigation and control options' , SLIDER_PLUGIN_PREFIX ); ?>
				</p>
			</td>
			<td>
				<p>
					<label>
						<input type="checkbox" name="slider_options[control_nav]" value="true" <?php checked( true, $slider_options['control_nav'] ) ?>
						/> Pagination
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[direction_nav]" value="true" <?php checked( true, $slider_options['direction_nav'] ) ?>
						/> Previous/Next
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[keyboard_nav]" value="true" <?php checked( true, $slider_options['keyboard_nav'] ) ?>
						/> Keyboard navigation
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[touch_nav]" value="true" <?php checked( true, $slider_options['touch_nav'] ) ?>
						/> Touch swipe
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[caption_box]" value="true" <?php checked( true, $slider_options['caption_box'] ) ?>
						/> Caption Box
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[linkable]" value="true" <?php checked( true, $slider_options['linkable'] ) ?>
						/> Linkable
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[pause_on_hover]" value="true" <?php checked( true, $slider_options['pause_on_hover'] ) ?>
						/> Pause on hover
					</label>
				</p>

				<p>
					<label>
						<input type="checkbox" name="slider_options[thumbnail_navigation]" value="true" <?php checked( true, $slider_options['thumbnail_navigation'] ) ?>
						/> Thumbnail navigation
					</label>
				</p>

				<?php do_action( 'ssp_options_end_control_options', $slider_id ); ?>

			</td>
		</tr>

		<?php do_action( 'ssp_options_meta_box_end', $slider_id ); ?>

	</tbody>
</table>