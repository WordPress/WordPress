<div class="slides_header">
	<table class="ssp widefat">
		<thead>
			<tr>
				<th class="slide_order">
					<?php _e('Slide Order', SLIDER_PLUGIN_PREFIX ); ?>
				</th>
				<th class="slide_label">
					<?php _e('Slide Label', SLIDER_PLUGIN_PREFIX ); ?>
				</th>
				<th class="slide_type">
					<?php _e('Slide Type', SLIDER_PLUGIN_PREFIX ); ?>
				</th>
			</tr>
		</thead>
	</table>
</div>

<div class="slides">
	
	<div class="no_slides_message" style="<?php echo $style_display ?>">
		No slides. Click the <strong>+ Add Slide</strong> to create your first slide.
	</div>

	<?php 
	foreach( $slides as $key => $slide ): 
		$slide = wp_parse_args( $slide, $default_slide ); 
	?>

		<div class="slide" id="<?php echo $key; ?>" style="position:relative">
			
			<input type="hidden" class="slide_attachment" name='<?php echo "slides[{$key}][attachment]" ?>' value="<?php echo $slide['attachment'] ?>" />
			<div class="slide_meta">
				<table class="ssp widefat">
					<tbody>
						<tr>
							<td class="slide_order">
								<span class="circle"><?php echo $key+1; ?></span>
							</td>
							
							<td class="slide_label">
								<strong>
									<a>
										<?php echo $slide['label']; ?>
									</a>
								</strong>
								<div class="row_options">
									<span>
										<a class="edit_slide" data-id="<?php echo $key ?>">
											<?php _e( 'Edit Slide', SLIDER_PLUGIN_PREFIX ); ?>
										</a>
									</span> |
									<span>
										<a class="delete_slide" data-id="<?php echo $key ?>">
											<?php _e( 'Delete Slide', SLIDER_PLUGIN_PREFIX ); ?>
										</a>
									</span>
								</div>
							</td>

							<td class="field_type">
								<?php echo strtoupper( $slide['type'] ) ?> 
							</td>

						</tr>

					</tbody>
				</table>
			</div>
			<div class="slide_form_mask" style="display: none">
			<div class="slide_form">
				<table class="ssp_input widefat ssp_slide_form_table">
					<tbody>
						<tr class="slide_label">
							<td class="label">
								<label>
									<span class="required">*</span>
									<?php _e( 'Slide Label', SLIDER_PLUGIN_PREFIX ); ?>
								</label>

								<p class="description">
										<?php _e( 'This is used for naming the slide in different places and finding slides.', SLIDER_PLUGIN_PREFIX ); ?>
								</p>
								
							</td>
							<td>
								<input class="slide_label_input" name='<?php echo "slides[{$key}][label]" ?>' type='text' value="<?php echo $slide['label'] ?>" data-id="<?php echo $key; ?>" />
							</td>
						</tr>

						<tr class="slide_type">
							<td class="label">
								<label>
									<?php _e( 'Slide Type', SLIDER_PLUGIN_PREFIX ) ?>
								</label>
								<p class="description">
									This is used by skins to display slides in correct format, and by plugin to show different input options
								</p>
							</td>
							<td>
								<select data-id="<?php echo $key ?>" name='<?php echo "slides[{$key}][type]" ?>'>
									<option value="image" <?php selected( 'image', $slide['type'] ) ?>>
										<?php _e( 'IMAGE', SLIDER_PLUGIN_PREFIX ); ?>
									</option>
									<option value="html" <?php selected( 'html', $slide['type'] ) ?>>
										<?php _e( 'HTML', SLIDER_PLUGIN_PREFIX ); ?>
									</option>
									<?php do_action( 'ssp_html_slide_type_select' ) ?>
								</select>
							</td>
						</tr>

						<tr class="slide_html"  data-id="<?php echo $key ?>">
							<td class="label">
								<label>
									<?php _e( 'Slide HTML', 'ssp' ); ?>
									
								</label>
								<p class="description">
									<?php _e( 'This feature is only available in the pro version.', 'ssp' ) ?>
								</p>
								<a href="http://rocketplugins.com/wordpress-slider-plugin/" class="ssp-button">
								<?php _e( 'Buy Now', 'ssp' ) ?></a>
							</td>
							<td>
								<!--<textarea rows="10" name=''></textarea> -->
								<img src="<?php echo plugins_url( 'images/html-feature.png', SLIDER_PLUGIN_MAIN_FILE ) ?>" />
							</td>
						</tr>



						<tr class="slide_image"  data-id="<?php echo $key ?>">
							<td class="label">
								<label>
									<?php _e( 'Slide Image', SLIDER_PLUGIN_PREFIX ); ?>
								</label>
								
								<p class="description">
										<?php _e( 'Make sure all the images in the slider are equally sized.', SLIDER_PLUGIN_PREFIX ); ?>
								</p>
								
							</td>
							<td>
								<input readonly class='slide_image_input' type='text' value="<?php echo $slide['image']['url'] ?>" placeholder="Click on Add Image button" /> 

								<a class="ssp-button grey add_image" data-id="<?php echo $key ?>">
									<?php _e( 'Add Image', SLIDER_PLUGIN_PREFIX ) ?>
								</a>

								<div class="slide_image_preview">
									<img src="<?php echo $slide['image']['url'] ?>" />
								</div>

							</td>
						</tr>

					<?php do_action( 'ssp_slide_options' ) ?>

					<tr class="slide_edit_close">
						<td class="label"></td>
						<td>
							<a data-id="<?php echo $key ?>" class="ssp-button edit_slide grey" style="width: 10%">
								<?php _e( 'Close', SLIDER_PLUGIN_PREFIX ); ?>
							</A>
						</td>
					</tr>

					</tbody>
				</table>
			</div>
	</div>
		</div>
	
	<?php endforeach; ?>

</div>

<div class="table_footer">
	<div class="order_message">
		<?php _e( 'Drag and drop to reorder', SLIDER_PLUGIN_PREFIX ); ?>
	</div>
	<a href="javascript:;" id="add_slide_button" class="ssp-button">
		<?php _e( '+ Add Slide', SLIDER_PLUGIN_PREFIX ); ?>
	</a>
</div>

<input type="hidden" value="<?php echo count($slides); ?>" id="next_slide_id" />

<?php include muneeb_ssp_view_path('new_slide_template.php') ?>