<?php
defined( 'ABSPATH' ) || exit;

class Luma_Agenda_Admin {

	public function register_pages() {
		// Settings page under Agendas menu
		add_submenu_page(
			'edit.php?post_type=luma_agenda',
			'Agenda Settings',
			'Settings',
			'manage_options',
			'luma-agenda-settings',
			[ ( new Luma_Agenda_Settings() ), 'render_settings_page' ]
		);

		// Hidden page: the full-screen Agenda Builder
		add_submenu_page(
			null,
			'Agenda Builder',
			'',
			'edit_posts',
			'luma-agenda-editor',
			[ $this, 'render_editor_page' ]
		);
	}

	public function render_editor_page() {
		$post_id = absint( $_GET['post_id'] ?? 0 );

		if ( ! $post_id ) {
			wp_die( 'No agenda ID provided. <a href="' . esc_url( admin_url( 'edit.php?post_type=luma_agenda' ) ) . '">Back to Agendas</a>' );
		}

		$post = get_post( $post_id );

		if ( ! $post || $post->post_type !== 'luma_agenda' ) {
			wp_die( 'Agenda not found. <a href="' . esc_url( admin_url( 'edit.php?post_type=luma_agenda' ) ) . '">Back to Agendas</a>' );
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_die( 'You do not have permission to edit this agenda.' );
		}

		// Pass data to template
		$editor_data = [
			'postId'              => $post_id,
			'nonce'               => wp_create_nonce( 'wp_rest' ),
			'restUrl'             => rest_url( 'luma-agenda/v1/agenda/' ),
			'lumaEventsUrl'       => rest_url( 'luma-agenda/v1/luma-events' ),
			'lumaEventDetailUrl'  => rest_url( 'luma-agenda/v1/luma-event/' ),
			'backUrl'             => admin_url( 'edit.php?post_type=luma_agenda' ),
			'libUrl'              => LUMA_AGENDA_URL . 'assets/lib/',
			'hasApiKey'           => ! empty( get_option( 'luma_agenda_api_key', '' ) ),
			'settingsUrl'         => admin_url( 'edit.php?post_type=luma_agenda&page=luma-agenda-settings' ),
		];

		include LUMA_AGENDA_PATH . 'templates/admin-editor-page.php';
		exit;
	}
}
