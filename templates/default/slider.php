<?php
/**
 * Default slider template.
 *
 * @package Lucid\Slider
 */

$single_slide_class = ( $is_single_slide ) ? ' flexslider-single' : ''; ?>

<div class="flexslider<?php echo $single_slide_class; ?>">
	<ul class="slides">
	<?php foreach ( $slides as $index => $slide ) :
		$slide_id = ( ! empty( $slide['slide-image-id'] ) ) ? $slide['slide-image-id'] : 0;
		$alt = ( ! empty( $slide['slide-image-alt'] ) ) ? esc_attr( $slide['slide-image-alt'] ) : '';

		// Keep this format to get image URL when making your own templates, since
		// it grabs from meta data if available and thus saves database requests.
		$src = ( ! empty( $slides_urls[$slide_id] ) )
			? $slides_urls[$slide_id]
			: Lucid_Slider_Utility::get_slide_image_src( $slide_id, $options['slider-size'] );

		// Output
		if ( ! empty( $src ) ) : ?>
			<li><img src="<?php echo $src; ?>" alt="<?php echo $alt ?>"></li>
		<?php endif;
	endforeach; ?>
	</ul>
</div>