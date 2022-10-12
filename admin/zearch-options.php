<?php

use Elastic\Elasticsearch\ClientBuilder;

class ZearchOptions {
	private $zearch_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'zearch_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'zearch_page_init' ) );
	}

	public function zearch_add_plugin_page() {
		add_menu_page(
			'Zearch', // page_title
			'Zearch', // menu_title
			'manage_options', // capability
			'zearch', // menu_slug
			array( $this, 'zearch_create_admin_page' ), // function
			'dashicons-search', // icon_url
			2 // position
		);
	}

	public function zearch_create_admin_page() {
		$this->zearch_options = get_option( 'zearch_option_name' );
        $client = ClientBuilder::create()
        ->setHosts(['http://88.198.32.151:9200'])
        // ->setApiKey()
        ->build();

        $response = $client->info();
        // echo '<pre>';
		// print_r($response);
		// echo '</pre>';
		// echo $response['version']['number']; // 8.0.0

        ?>

		<div class="wrap">
			<h2>Zearch</h2>
			<p></p>
			<?php settings_errors(); ?>
			<form method="post" action="options.php">
				<?php
					settings_fields( 'zearch_option_group' );
					do_settings_sections( 'zearch-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function zearch_page_init() {
		register_setting(
			'zearch_option_group', // option_group
			'zearch_option_name', // option_name
			array( $this, 'zearch_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'zearch_setting_section', // id
			'Settings', // title
			array( $this, 'zearch_section_info' ), // callback
			'zearch-admin' // page
		);

	    $args = array(
			'public'   => true,
			'_builtin' => true
		 );
		  
		 $output = 'names'; // 'names' or 'objects' (default: 'names')
		 $operator = 'and'; // 'and' or 'or' (default: 'and')
		  
		 $post_types = get_post_types( $args, $output, $operator );
		  
		 if ( $post_types ) { // If there are any custom public post types.
		  
			 foreach ( $post_types  as $post_type ) {
				
				add_settings_field(
					'title_'.$post_type , // id
					'Title '.$post_type , // title
					array( $this, 'title_callback' ), // callback
					'zearch-admin', // page
					'zearch_setting_section', // section
					$post_type
				);

			}
				
		}
	}

	public function zearch_sanitize($input, $post_type) {
		$sanitary_values = array();

		if ( isset( $input['title_'.$post_type] ) ) {
			$sanitary_values['title_'.$post_type] = $input['title_'.$post_type];
		}

		return $sanitary_values;
	}

	public function zearch_section_info() {
		
	}
	

	public function title_callback($post_type) {
		foreach ($post_type as $data) {
			printf(
				'<input type="checkbox" name="title_option_name[title_'.$data.']" id="title_'.$data.'" value="title_'.$data.'" %s> <label for="title_'.$data.'">Seachble</label>',
				( isset( $this->zearch_options['title_'.$data] ) && $this->zearch_options['title_'.$data] === 'title_'.$data ) ? 'checked' : ''
			);
		}
		
	}




}
if ( is_admin() )
	$zearch = new ZearchOptions();

//Api settings 
include('api-settings.php');