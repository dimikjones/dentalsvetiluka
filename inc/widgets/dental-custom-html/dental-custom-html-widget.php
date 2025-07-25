<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Dental_Custom_Html_Widget
 */
class Dental_Custom_Html_Widget extends WP_Widget {

	/**
	 * Constructs the new widget.
	 *
	 * @see WP_Widget::__construct()
	 */
	public function __construct() {
		// Instantiate the parent object.
		parent::__construct(
		// Base ID.
			'dental_custom_html_widget',
			// Widget name.
			__( 'Dental Custom HTML Widget', 'dentalsvetiluka' ),
			array(
				'description' => __( 'Allows you to add custom HTML with a textarea field.', 'dentalsvetiluka' ),
			)
		);
	}

	/**
	 * The widget's HTML output.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Display arguments including before_title, after_title,
	 * before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo esc_attr( $args['before_title'] ) . apply_filters( 'widget_title', esc_html( $instance['title'] ) ) . esc_attr( $args['after_title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( ! empty( $instance['custom_html'] ) ) {
			echo '<div class="dental-custom-html-content">';
			echo apply_filters( 'dental_custom_html_content', $instance['custom_html'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}
		echo wp_kses_post( $args['after_widget'] );
	}

	/**
	 * The widget update handler.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance The new instance of the widget.
	 * @param array $old_instance The old instance of the widget.
	 * @return array The updated instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                = array();
		$instance['title']       = sanitize_text_field( $new_instance['title'] );
		$instance['custom_html'] = allsmiles_wp_kses_html( 'html', $new_instance['custom_html'] );
		return $instance;
	}

	/**
	 * Output the admin widget options form HTML.
	 *
	 * @param array $instance The current widget settings.
	 * @return string The HTML markup for the form.
	 */
	public function form( $instance ) {
		$title       = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$custom_html = isset( $instance['custom_html'] ) ? allsmiles_wp_kses_html( 'html', $instance['custom_html'] ) : '';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'dentalsvetiluka' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'custom_html' ) ); ?>"><?php esc_html_e( 'Custom HTML:', 'dentalsvetiluka' ); ?></label>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'custom_html' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'custom_html' ) ); ?>" rows="8"><?php echo esc_textarea( $custom_html ); ?></textarea>
		</p>
		<?php
	}
}

add_action( 'widgets_init', 'dentalsvetiluka_custom_html_widget_registration' );

/**
 * Register the new widget.
 *
 * @see 'widgets_init'
 */
function dentalsvetiluka_custom_html_widget_registration() {
	register_widget( 'Dental_Custom_Html_Widget' );
}
