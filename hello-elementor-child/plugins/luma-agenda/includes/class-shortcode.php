<?php
defined( 'ABSPATH' ) || exit;

class Luma_Agenda_Shortcode {

	public function register() {
		add_shortcode( 'luma_agenda', [ $this, 'render' ] );
	}

	public function render( $atts ) {
		$atts = shortcode_atts(
			[
				'id'   => 0,
				'slug' => '',
			],
			$atts,
			'luma_agenda'
		);

		$post = null;

		if ( $atts['id'] ) {
			$post = get_post( (int) $atts['id'] );
		} elseif ( $atts['slug'] ) {
			$post = get_page_by_path( $atts['slug'], OBJECT, 'luma_agenda' );
		}

		if ( ! $post || $post->post_type !== 'luma_agenda' ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<p style="color:#b91c1c;font-size:13px;">Luma Agenda: no agenda found for id="' . esc_attr( $atts['id'] ) . '".</p>';
			}
			return '';
		}

		$agenda_data = get_post_meta( $post->ID, '_luma_agenda_data', true );
		$theme       = get_post_meta( $post->ID, '_luma_agenda_theme', true );

		if ( empty( $agenda_data ) || ! isset( $agenda_data['sessions'] ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				$edit_url = admin_url( 'admin.php?page=luma-agenda-editor&post_id=' . $post->ID );
				return '<p style="color:#92400e;font-size:13px;">Luma Agenda: this agenda has no sessions yet. <a href="' . esc_url( $edit_url ) . '">Open Builder →</a></p>';
			}
			return '';
		}

		wp_enqueue_style(
			'luma-agenda-public',
			LUMA_AGENDA_URL . 'assets/css/agenda-public.css',
			[],
			LUMA_AGENDA_VERSION
		);
		wp_enqueue_script(
			'luma-agenda-public',
			LUMA_AGENDA_URL . 'assets/js/agenda-public.js',
			[],
			LUMA_AGENDA_VERSION,
			true
		);

		$data_json  = wp_json_encode( $agenda_data );
		$theme_json = wp_json_encode( $theme ?: [] );

		return sprintf(
			'<div class="luma-agenda-display" data-agenda="%s" data-theme="%s"></div>',
			esc_attr( $data_json ),
			esc_attr( $theme_json )
		);
	}
}
