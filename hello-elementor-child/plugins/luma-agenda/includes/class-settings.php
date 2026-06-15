<?php
defined( 'ABSPATH' ) || exit;

class Luma_Agenda_Settings {

	public function register() {
		register_setting( 'luma_agenda_settings', 'luma_agenda_api_key', [
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_text_field',
		] );

		add_settings_section(
			'luma_agenda_main',
			'Luma API Configuration',
			null,
			'luma-agenda-settings'
		);

		add_settings_field(
			'luma_agenda_api_key',
			'Luma API Key',
			[ $this, 'render_api_key_field' ],
			'luma-agenda-settings',
			'luma_agenda_main'
		);
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1>Luma Agenda Settings</h1>
			<p>Enter your Luma API key to enable event linking. Get one at <a href="https://lu.ma/developers" target="_blank" rel="noopener">lu.ma/developers</a>.</p>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'luma_agenda_settings' );
				do_settings_sections( 'luma-agenda-settings' );
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	public function render_api_key_field() {
		$value = get_option( 'luma_agenda_api_key', '' );
		?>
		<input type="password"
		       name="luma_agenda_api_key"
		       id="luma_agenda_api_key"
		       value="<?php echo esc_attr( $value ); ?>"
		       class="regular-text"
		       autocomplete="off"
		/>
		<p class="description">Used to fetch your Luma calendar events when linking an agenda to a Luma event.</p>
		<?php
	}
}
