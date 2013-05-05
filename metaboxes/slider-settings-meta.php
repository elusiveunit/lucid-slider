<?php
/**
 * Slider settings metabox.
 *
 * @package Lucid
 * @subpackage Slider
 */

$opt = Lucid_Slider_Utility::get_settings();
$added_sizes = get_intermediate_image_sizes(); ?>

<div id="lsjl-slider-settings">

<?php /*---------- Slider size dropdown ----------*/

// Create a dropdown if there are sizes set
if ( true || ! empty( $opt['image_sizes'] ) ) :

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
		<?php
		// Add registered image sizes
		foreach ( $sizes as $size ) :
			$size_label = str_replace( 'x', '&times;', trim( $size ) );
			$size_name = Lucid_Slider_Utility::get_image_size( $size );
			if ( in_array( $size_name, $added_sizes ) ) : ?>
				<option value="<?php echo $size_name; ?>"<?php $mb->the_select_state( $size_name ); ?>><?php echo $size_label; ?></option>
		<?php endif;
		endforeach;

		// Allow custom sizes to be added
		do_action_ref_array( 'lsjl_slider_size_select', array( &$mb ) ); ?>
	</select>

<?php // Warn if there are no sizes set
else : ?>
	<div class="lsjl-message-error"><p><?php _e( 'You must set image sizes in the settings to use the slider.', 'lucid-slider' ); ?></p></div>
<?php endif; ?>
</div>