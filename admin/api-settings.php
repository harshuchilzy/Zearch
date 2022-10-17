<?php 
/**
 * Generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */

class ApiSettings {
	private $api_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'api_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'api_settings_page_init' ) );
	}

	public function api_settings_add_plugin_page() {
		add_submenu_page(
            'ezearch', // menu_slug
			'Api Settings', // page_title
			'Api Settings', // menu_title
			'manage_options', // capability
			'api-settings', // menu_slug
			array( $this, 'api_settings_create_admin_page' ) // function
		);
	}

	public function api_settings_create_admin_page() {
		$this->api_settings_options = get_option( 'api_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Search field weight Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'api_settings_option_group' );
					do_settings_sections( 'api-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function api_settings_page_init() {
		register_setting(
			'api_settings_option_group', // option_group
			'api_settings_option_name', // option_name
			array( $this, 'api_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'api_settings_setting_section', // id
			'Settings', // title
			array( $this, 'api_settings_section_info' ), // callback
			'api-settings-admin' // page
		);

		add_settings_field(
			'api_key_0', // id
			'Api key', // title
			array( $this, 'api_key_0_callback' ), // callback
			'api-settings-admin', // page
			'api_settings_setting_section' // section
		);

		add_settings_field(
			'host_1', // id
			'Host', // title
			array( $this, 'host_1_callback' ), // callback
			'api-settings-admin', // page
			'api_settings_setting_section' // section
		);
	}

	public function api_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}

		if ( isset( $input['host_1'] ) ) {
			$sanitary_values['host_1'] = sanitize_text_field( $input['host_1'] );
		}

		return $sanitary_values;
	}

	public function api_settings_section_info() {
		
	}

	public function api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="api_settings_option_name[api_key_0]" id="api_key_0" value="%s">',
			isset( $this->api_settings_options['api_key_0'] ) ? esc_attr( $this->api_settings_options['api_key_0']) : ''
		);
	}

	public function host_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="api_settings_option_name[host_1]" id="host_1" value="%s">',
			isset( $this->api_settings_options['host_1'] ) ? esc_attr( $this->api_settings_options['host_1']) : ''
		);
	}

}
if ( is_admin() )
	$api_settings = new ApiSettings();

/* 
 * Retrieve this value with:
 * $api_settings_options = get_option( 'api_settings_option_name' ); // Array of All Options
 * $api_key_0 = $api_settings_options['api_key_0']; // Api key
 * $host_1 = $api_settings_options['host_1']; // Host
 */
