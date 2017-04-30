<div id="new_slide_template" style="display: none">
	<div class="slide form_open" style="position:relative">
			<input type="hidden" class="slide_type" name="" value="" />
			<input type="hidden" class="slide_attachment" name='' value="" />
			<div class="slide_meta">
				<table class="ssp widefat">
					<tbody>
						<tr>
							<td class="slide_order">
								<span class="circle"></span>
							</td>
							
							<td class="slide_label">
								<strong>
									<a>change me</a>
								</strong>
								<div class="row_options">
									<span>
										<a class="edit_slide" data-id="">
											<?php _e( 'Edit Slide', SLIDER_PLUGIN_PREFIX ); ?>
										</a>
									</span> |
									<span>
										<a class="delete_slide" data-id="">
											<?php _e( 'Delete Slide', SLIDER_PLUGIN_PREFIX ); ?>
										</a>
									</span>
								</div>
							</td>

							<td class="field_type">
								<?php _e( 'Image', SLIDER_PLUGIN_PREFIX ); ?>
							</td>

						</tr>

					</tbody>
				</table>
			</div>
		<div class="slide_form_mask" style="display: block">
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
								<input name='' class='slide_label_input' type='text' value="Change ME!" data-id="" />
							</td>
						</tr>

						<tr class="slide_type">
							<td class="label">
								<label>
									<span class="required">*</span>
									<?php _e( 'Slide Type', SLIDER_PLUGIN_PREFIX ) ?>
								</label>
								<p class="description">
									This is used by skins to display slides in correct format, and by plugin to show different input options
								</p>
							</td>
							<td>
								<select data-id="" name=''>
									<option value="image" selected>
										<?php _e( 'IMAGE', SLIDER_PLUGIN_PREFIX ); ?>
									</option>
									<option value="html">
										<?php _e( 'HTML', SLIDER_PLUGIN_PREFIX ); ?>
									</option>
									<?php do_action( 'ssp_html_slide_type_select' ) ?>
								</select>
							</td>
						</tr>

						<tr class="slide_html" style="display: none" data-id="">
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

						<tr class="slide_image" data-id="">
							<td class="label">
								<label>
									<span class="required">*</span>
									<?php _e( 'Slide Image', SLIDER_PLUGIN_PREFIX ); ?>
								</label>
								
								<p class="description">
										<?php _e( 'Make sure all the images in the slider are equally sized.', SLIDER_PLUGIN_PREFIX ); ?>
								</p>
								
							</td>
							<td>
								<input readonly class='slide_image_input' type='text' placeholder="Click on Add Image button" value="" />

								<a class="ssp-button grey add_image" data-id="">
									<?php _e( 'Add Image', SLIDER_PLUGIN_PREFIX ) ?>
								</a>

								<div class="slide_image_preview">
									<img src="" />
								</div>

							</td>
						</tr>

					<?php do_action( 'ssp_slide_options_new_slide' ) ?>

					<tr class="slide_edit_close">
						<td class="label"></td>
						<td>
							<a data-id="" class="ssp-button edit_slide grey" style="width: 10%">
								<?php _e( 'Close', SLIDER_PLUGIN_PREFIX ); ?>
							</A>
						</td>
					</tr>

					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>