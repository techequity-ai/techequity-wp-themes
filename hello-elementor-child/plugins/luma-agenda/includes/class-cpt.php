<?php
defined( 'ABSPATH' ) || exit;

class Luma_Agenda_CPT {

	public function register() {
		register_post_type( 'luma_agenda', [
			'labels'              => [
				'name'               => 'Agendas',
				'singular_name'      => 'Agenda',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Agenda',
				'edit_item'          => 'Edit Agenda',
				'new_item'           => 'New Agenda',
				'view_item'          => 'View Agenda',
				'search_items'       => 'Search Agendas',
				'not_found'          => 'No agendas found',
				'not_found_in_trash' => 'No agendas in trash',
				'menu_name'          => 'Agendas',
			],
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'menu_icon'           => 'dashicons-calendar-alt',
			'supports'            => [ 'title' ],
			'capability_type'     => 'post',
			'has_archive'         => false,
		] );

		add_filter( 'manage_luma_agenda_posts_columns', [ $this, 'add_columns' ] );
		add_action( 'manage_luma_agenda_posts_custom_column', [ $this, 'render_columns' ], 10, 2 );
		add_filter( 'post_row_actions', [ $this, 'row_actions' ], 10, 2 );
	}

	public function add_columns( $columns ) {
		unset( $columns['date'] );
		$columns['event_date']  = 'Event Date';
		$columns['shortcode']   = 'Shortcode';
		$columns['edit_agenda'] = 'Builder';
		return $columns;
	}

	public function render_columns( $column, $post_id ) {
		$data = get_post_meta( $post_id, '_luma_agenda_data', true );
		if ( $column === 'event_date' ) {
			echo esc_html( $data['eventDate'] ?? '—' );
		} elseif ( $column === 'shortcode' ) {
			echo '<code>[luma_agenda id="' . (int) $post_id . '"]</code>';
		} elseif ( $column === 'edit_agenda' ) {
			$url = admin_url( 'admin.php?page=luma-agenda-editor&post_id=' . $post_id );
			echo '<a href="' . esc_url( $url ) . '" class="button button-small">Open Builder</a>';
		}
	}

	public function row_actions( $actions, $post ) {
		if ( $post->post_type !== 'luma_agenda' ) {
			return $actions;
		}
		$url = admin_url( 'admin.php?page=luma-agenda-editor&post_id=' . $post->ID );
		$actions['edit_agenda'] = '<a href="' . esc_url( $url ) . '">Open Builder</a>';
		return $actions;
	}
}
