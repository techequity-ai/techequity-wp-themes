<?php
defined( 'ABSPATH' ) || exit;

class Luma_Agenda_REST {

	public function register_routes() {
		register_rest_route( 'luma-agenda/v1', '/agenda/(?P<id>\d+)', [
			[
				'methods'             => 'GET',
				'callback'            => [ $this, 'get_agenda' ],
				'permission_callback' => [ $this, 'check_edit_permission' ],
				'args'                => [ 'id' => [ 'validate_callback' => 'is_numeric' ] ],
			],
			[
				'methods'             => 'POST',
				'callback'            => [ $this, 'save_agenda' ],
				'permission_callback' => [ $this, 'check_edit_permission' ],
				'args'                => [ 'id' => [ 'validate_callback' => 'is_numeric' ] ],
			],
		] );

		register_rest_route( 'luma-agenda/v1', '/luma-events', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_luma_events' ],
			'permission_callback' => [ $this, 'check_edit_permission_any' ],
		] );

		register_rest_route( 'luma-agenda/v1', '/luma-event/(?P<api_id>[^/]+)', [
			'methods'             => 'GET',
			'callback'            => [ $this, 'get_luma_event_detail' ],
			'permission_callback' => [ $this, 'check_edit_permission_any' ],
			'args'                => [ 'api_id' => [ 'sanitize_callback' => 'sanitize_text_field' ] ],
		] );
	}

	public function check_edit_permission( $request ) {
		$post_id = (int) $request['id'];
		return current_user_can( 'edit_post', $post_id );
	}

	public function check_edit_permission_any() {
		return current_user_can( 'edit_posts' );
	}

	public function get_agenda( $request ) {
		$post_id = (int) $request['id'];
		$post    = get_post( $post_id );

		if ( ! $post || $post->post_type !== 'luma_agenda' ) {
			return new WP_Error( 'not_found', 'Agenda not found', [ 'status' => 404 ] );
		}

		return rest_ensure_response( [
			'id'             => $post_id,
			'title'          => $post->post_title,
			'agenda_data'    => get_post_meta( $post_id, '_luma_agenda_data', true ) ?: null,
			'theme'          => get_post_meta( $post_id, '_luma_agenda_theme', true ) ?: null,
			'last_saved'     => get_post_meta( $post_id, '_luma_agenda_last_saved', true ) ?: '',
			'luma_event_id'  => get_post_meta( $post_id, '_luma_event_id', true ) ?: '',
			'luma_event_url' => get_post_meta( $post_id, '_luma_event_url', true ) ?: '',
		] );
	}

	public function save_agenda( $request ) {
		$post_id = (int) $request['id'];
		$post    = get_post( $post_id );

		if ( ! $post || $post->post_type !== 'luma_agenda' ) {
			return new WP_Error( 'not_found', 'Agenda not found', [ 'status' => 404 ] );
		}

		$body = $request->get_json_params();

		if ( isset( $body['agenda_data'] ) && is_array( $body['agenda_data'] ) ) {
			update_post_meta( $post_id, '_luma_agenda_data', $body['agenda_data'] );
			if ( ! empty( $body['agenda_data']['title'] ) ) {
				wp_update_post( [
					'ID'         => $post_id,
					'post_title' => sanitize_text_field( $body['agenda_data']['title'] ),
				] );
			}
		}

		if ( isset( $body['theme'] ) && is_array( $body['theme'] ) ) {
			update_post_meta( $post_id, '_luma_agenda_theme', $body['theme'] );
		}

		if ( ! empty( $body['last_saved'] ) ) {
			update_post_meta( $post_id, '_luma_agenda_last_saved', sanitize_text_field( $body['last_saved'] ) );
		}

		return rest_ensure_response( [ 'success' => true, 'id' => $post_id ] );
	}

	public function get_luma_event_detail( $request ) {
		$api_key = get_option( 'luma_agenda_api_key', '' );
		if ( empty( $api_key ) ) {
			return new WP_Error( 'no_api_key', 'Luma API key not configured.', [ 'status' => 400 ] );
		}

		$event_api_id  = $request['api_id'];
		$transient_key = 'luma_event_detail_' . md5( $api_key . $event_api_id );
		$cached        = get_transient( $transient_key );
		if ( $cached !== false ) {
			return rest_ensure_response( $cached );
		}

		$response = wp_remote_get(
			'https://public-api.luma.com/v1/event/get?api_id=' . rawurlencode( $event_api_id ),
			[
				'headers' => [
					'x-luma-api-key' => $api_key,
					'Accept'          => 'application/json',
				],
				'timeout' => 15,
			]
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'luma_error', $response->get_error_message(), [ 'status' => 502 ] );
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( $code !== 200 ) {
			return new WP_Error( 'luma_error', 'Luma API returned status ' . $code, [ 'status' => 502 ] );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! $body ) {
			return new WP_Error( 'luma_error', 'Invalid response from Luma API', [ 'status' => 502 ] );
		}

		set_transient( $transient_key, $body, 5 * MINUTE_IN_SECONDS );
		return rest_ensure_response( $body );
	}

	public function get_luma_events( $request ) {
		$api_key = get_option( 'luma_agenda_api_key', '' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'no_api_key', 'Luma API key not configured in Agendas → Settings.', [ 'status' => 400 ] );
		}

		$transient_key = 'luma_agenda_events_' . md5( $api_key );

		// Allow forced cache bust via ?refresh=1
		if ( $request && $request->get_param( 'refresh' ) ) {
			delete_transient( $transient_key );
		} else {
			$cached = get_transient( $transient_key );
			if ( $cached !== false ) {
				return rest_ensure_response( $cached );
			}
		}

		// Paginate through all events (Luma returns ~25 per page)
		$all_entries = [];
		$cursor      = null;

		for ( $page = 0; $page < 10; $page++ ) {
			$url = 'https://public-api.luma.com/v1/calendar/list-events';
			if ( $cursor ) {
				$url .= '?pagination_cursor=' . rawurlencode( $cursor );
			}

			$response = wp_remote_get( $url, [
				'headers' => [
					'x-luma-api-key' => $api_key,
					'Accept'          => 'application/json',
				],
				'timeout' => 15,
			] );

			if ( is_wp_error( $response ) ) {
				if ( empty( $all_entries ) ) {
					return new WP_Error( 'luma_error', $response->get_error_message(), [ 'status' => 502 ] );
				}
				break;
			}

			$code = wp_remote_retrieve_response_code( $response );
			if ( $code !== 200 ) {
				if ( empty( $all_entries ) ) {
					return new WP_Error( 'luma_error', 'Luma API returned status ' . $code, [ 'status' => 502 ] );
				}
				break;
			}

			$page_body = json_decode( wp_remote_retrieve_body( $response ), true );
			if ( ! $page_body ) {
				break;
			}

			$all_entries = array_merge( $all_entries, $page_body['entries'] ?? [] );

			if ( empty( $page_body['has_more'] ) ) {
				break;
			}
			$cursor = $page_body['next_cursor'] ?? null;
			if ( ! $cursor ) {
				break;
			}
		}

		$body = [ 'entries' => $all_entries ];
		set_transient( $transient_key, $body, 5 * MINUTE_IN_SECONDS );

		return rest_ensure_response( $body );
	}
}
