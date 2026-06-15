<?php
/**
 * Plugin Name: Luma Agenda
 * Plugin URI:  https://techequity-ai.org
 * Description: Display Luma event agendas with a custom designed timeline. Uses [luma_agenda id="POST_ID"] shortcode.
 * Version:     1.0.0
 * Author:      TechEquity AI
 * Text Domain: luma-agenda
 */

defined( 'ABSPATH' ) || exit;

define( 'LUMA_AGENDA_VERSION', '1.0.0' );
define( 'LUMA_AGENDA_PATH', plugin_dir_path( __FILE__ ) );
define( 'LUMA_AGENDA_URL', plugin_dir_url( __FILE__ ) );

require_once LUMA_AGENDA_PATH . 'includes/class-cpt.php';
require_once LUMA_AGENDA_PATH . 'includes/class-settings.php';
require_once LUMA_AGENDA_PATH . 'includes/class-rest-api.php';
require_once LUMA_AGENDA_PATH . 'includes/class-admin.php';
require_once LUMA_AGENDA_PATH . 'includes/class-shortcode.php';

add_action( 'init', function () {
	( new Luma_Agenda_CPT() )->register();
	( new Luma_Agenda_Shortcode() )->register();
} );

add_action( 'admin_init', function () {
	( new Luma_Agenda_Settings() )->register();
} );

add_action( 'rest_api_init', function () {
	( new Luma_Agenda_REST() )->register_routes();
} );

add_action( 'admin_menu', function () {
	( new Luma_Agenda_Admin() )->register_pages();
} );
