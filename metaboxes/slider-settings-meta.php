<div id="lsjl-slider-settings">

<?php $opt = Lucid_Slider_Utility::get_settings();

/*---------- Slider size dropdown ----------*/

// Create a dropdown if there are sizes set
if ( ! empty( $opt['image_sizes'] ) ) :

	// Saved as '600x200<newline>'
	$sizes = explode( "\n", trim( $opt['image_sizes'] ) );
	
	$mb->the_field( 'slider-size' );

	$chosen_slider_size = $mb->get_the_value();
	if ( empty( $chosen_slider_size ) ) : ?>
		<div class="lsjl-message-notice"><p><?php _e( 'Remember to choose a size.', 'lucid-slider' ); ?></p></div>
	<?php endif; ?>

	<label for="<?php $mb->the_name(); ?>"><?php _e( 'Slider size', 'lucid-slider' ); ?></label>
	<select name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" class="widefat">
		<option value="full"<?php $mb->the_select_state( 'full' ); ?>><?php _e( 'Full size', 'lucid-slider' ); ?></option>
		<?php foreach ( $sizes as $size ) : $size = trim( $size ); ?>
			<option value="<?php echo $size; ?>"<?php $mb->the_select_state( $size ); ?>><?php echo $size; ?></option>
		<?php endforeach; ?>
	</select>

<?php // Warn if there are no sizes set
else : ?>
	<div class="lsjl-message-error"><p><?php _e( 'You must set image sizes in the settings to use the slider.', 'lucid-slider' ); ?></p></div>
<?php endif; ?>
</div>