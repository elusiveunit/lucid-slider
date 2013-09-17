<?php
/**
 * Slider template selection metabox.
 *
 * @package Lucid
 * @subpackage Slider
 */

$templates = Lucid_Slider_Utility::get_templates();
$mb->the_field( 'template' );
$chosen_template = $mb->get_the_value();
if ( empty( $chosen_template ) ) $chosen_template = 'default'; ?>

<div id="lsjl-slider-template">
<ul>
<?php foreach ( $templates as $name => $data ) :
	$screenshot = ( ! empty( $templates[$name]['screenshot'] ) ) ? $templates[$name]['screenshot'] : LUCID_SLIDER_ASSETS . 'img/slide-placeholder.png';
	$class = ( $chosen_template == $name ) ? ' class="selected"' : ''; ?>
	<li>
		<label for="lsjl-template-<?php echo $name; ?>"<?php echo $class; ?>>
			<input type="radio" name="<?php $mb->the_name(); ?>" value="<?php echo $name; ?>" id="lsjl-template-<?php echo $name; ?>"<?php $mb->the_radio_state( $name ); ?>>
			<span class="lsjl-template-image"><img src="<?php echo $screenshot; ?>" alt=""></span>
			<b><?php echo $templates[$name]['name']; ?></b>
		</label>
	</li>
<?php endforeach; ?>
</ul>
</div>