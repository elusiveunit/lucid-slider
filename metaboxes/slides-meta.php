<?php
/**
 * Slider slide management metabox.
 *
 * @package Lucid\Slider
 */
?>

<div id="lsjl-slides">

<div class="lsjl-message-notice" id="lsjl-sort-message"><p><?php _e( 'Remember to save your sort order!', 'lucid-slider' ); ?></p></div>

<?php
/**
 * Fires before the slides meta block; use to add custom fields to the slider.
 *
 * @param WPAlchemy_MetaBox $mb Metabox object
 */
do_action( 'lsjl_slides_meta_start', $mb );

while ( $mb->have_fields_and_multi( 'slide-group' ) ) :
	$mb->the_group_open( 'div' ); ?>

	<div class="lsjl-move-handle" title="<?php _e( 'Move', 'lucid-slider' ); ?>"><i class="lsjl-move-icon"></i></div>

	<?php /*---------- Slide image ID ----------*/
	$mb->the_field( 'slide-image-id' ); ?>
	<input type="hidden" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" class="lsjl-slide-thumbnail-id" value="<?php $mb->the_value(); ?>">

	<?php /*---------- Slide thumbnail image ----------*/
	$mb->the_field( 'slide-image-thumbnail' ); ?>
	<div class="lsjl-slide-thumbnail">
		<input type="hidden" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" class="lsjl-slide-thumbnail-field" value="<?php $mb->the_value(); ?>">

		<?php $lsjl_slide_thumbnail_url = $mb->get_the_value();

		if ( empty( $lsjl_slide_thumbnail_url ) ) :
			$lsjl_slide_thumbnail_url = LUCID_SLIDER_ASSETS . 'img/slide-placeholder.png';
		endif;

		//BEWARE THE UGLY NESTING ?>
		<span class="lsjl-thumb-wrap-1"><span class="lsjl-thumb-wrap-2"><img src="<?php echo $lsjl_slide_thumbnail_url; ?>" alt=""></span></span>
	</div>

	<?php
	/*
	 * NO HTML COMMENTS INSIDE lsjl-fields-wrap, for correct element.children
	 * count in IE < 8.
	 */
	?>
	<div class="lsjl-fields-wrap<?php if ( apply_filters( 'lsjl_show_all_slide_fields', false ) ) echo ' always-expanded'; ?>">
		<div class="lsjl-fields-wrap-inner">
			<?php /*---------- Slide image URL field ----------*/
			$mb->the_field( 'slide-image-url' ); ?>
			<div class="lsjl-slide-url lsjl-field-group">
				<label for="<?php $mb->the_name(); ?>"><?php _e( 'Image:', 'lucid-slider' ); ?></label>
				<input type="text" readonly="readonly" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" class="lsjl-slide-url-field" value="<?php $mb->the_value(); ?>">

				<a href="#" title="<?php _e( 'Add an image', 'lucid-slider' ); ?>" class="button lsjl-upload" id="<?php $mb->the_name(); ?>-upload" onclick="return false;" data-uploader-title="<?php _e( 'Choose slide image', 'lucid-slider' ); ?>" data-uploader-button-text="<?php _e( 'Choose image', 'lucid-slider' ); ?>"><?php _e( 'Choose...', 'lucid-slider' ); ?></a>
			</div>

			<?php /*---------- Slide image alt text field ----------*/
			$mb->the_field( 'slide-image-alt' ); ?>
			<div class="lsjl-slide-alt lsjl-field-group">
				<label for="<?php $mb->the_name(); ?>"><?php _e( 'Alt text:', 'lucid-slider' ); ?></label>
				<input type="text" name="<?php $mb->the_name(); ?>" id="<?php $mb->the_name(); ?>" class="lsjl-slide-alt-field" value="<?php $mb->the_value(); ?>">
				<span class="description"><?php _e( 'Image description for search engines and visually impaired people.', 'lucid-slider' ); ?></span>
			</div>

			<div class="lsjl-extra-fields">
				<?php
				/**
				 * Fires after the default slide fields; use to add custom fields
				 * to each slide.
				 *
				 * @param WPAlchemy_MetaBox $mb Metabox object
				 */
				do_action( 'lsjl_meta_fields_end', $mb ); ?>
			</div>
		</div>
	</div>

	<a href="#<?php strtolower( _ex( 'show-all', 'hash slug for expand link', 'lucid-slider' ) ); ?>" class="lsjl-expand-group" data-show-text="<?php _e( 'Show all fields', 'lucid-slider' ); ?>" data-hide-text="<?php _e( 'Hide fields', 'lucid-slider' ); ?>"><?php _e( 'Show all fields', 'lucid-slider' ); ?></a>

	<a href="#<?php strtolower( _ex( 'remove', 'hash slug for remove link', 'lucid-slider' ) ); ?>" class="lsjl-remove-slide dodelete"><?php _e( 'Remove', 'lucid-slider' ); ?></a>

	<?php $mb->the_group_close();
endwhile;

/**
 * Fires after the slides meta block; use to add custom fields to the slider.
 *
 * @param WPAlchemy_MetaBox $mb Metabox object
 */
do_action( 'lsjl_slides_meta_end', $mb ); ?>

<p><button class="button docopy-slide-group"><?php _e( 'Add slide', 'lucid-slider' ); ?></button></p>

</div>