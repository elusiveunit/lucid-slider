<?php
/**
 * Create a slider widget.
 * 
 * @package Lucid_Slider
 */

// Block direct requests
if ( ! defined( 'ABSPATH' ) ) die( 'Nope' );

/**
 * Widget for choosing and displaying a slider.
 */
class Lucid_Slider_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'lucid-slider-widget',
			__( 'Lucid Slider', 'lucid-slider' ),
			array(
				'classname' => 'lucid-slider-widget',
				'description' => __( 'Display a slider', 'lucid-slider' )
			)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 * @param array $args			The array of form elements
	 * @param array $instance		Current saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$slider = ( ! empty( $instance['slider'] ) ) ? $instance['slider'] : 0;

		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		
		lucid_slider( $slider );

		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 * @param array $new_instance		Values just sent to be saved.
	 * @param array $old_instance		Previously saved values from database.
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['slider'] = absint( $new_instance['slider'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 * @param array $instance		Previously saved values from database.
	 */
	function form( $instance ) {

		// Defaults
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'slider' => ''
		) );

		$title = esc_attr( $instance['title'] );
		$slider = esc_attr( $instance['slider'] );

		$sliders = get_posts( array(
			'post_type' => 'slider',
			'numberposts' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		) ); ?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'lucid-slider' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"></p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'slider' ); ?>"><?php _e( 'Slider:', 'lucid-slider' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'slider' ); ?>" name="<?php echo $this->get_field_name( 'slider' ); ?>" class="widefat">
				<?php foreach ( $sliders as $key => $data ) :
					$selected = ( $data->ID == $slider ) ? ' selected="selected"' : '';
					echo "<option{$selected} value=\"{$data->ID}\">{$data->post_title}</option>";
				endforeach; ?>
			</select>
		</p>

	<?php }
}