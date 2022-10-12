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
				    $post_types = get_post_types( '', 'names' ); 
					foreach ( $post_types as $post_type ) {
					   echo '<h4>' . $post_type . '</h4>';
					   settings_fields( 'zearch_option_group' );
					   do_settings_sections( 'zearch-admin' );
					
					 $args = array(
						'public'   => true,
						'_builtin' => false
						
					  ); 
					  $output = 'names'; // or objects
					  $operator = 'and'; // 'and' or 'or'
					  $taxonomies = get_taxonomies( $args, $output, $operator ); 
					  if ( $taxonomies ) {
						  echo '<ul>';
						  foreach ( $taxonomies  as $taxonomy ) {
							  echo '<li>' . $taxonomy . '</li>';
						  }
						  echo '</ul>';	
					  }
					}
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

		add_settings_field(
			'title_0', // id
			'Title', // title
			array( $this, 'title_0_callback' ), // callback
			'zearch-admin', // page
			'zearch_setting_section' // section
		);

		add_settings_field(
			'content_1', // id
			'Content', // title
			array( $this, 'content_1_callback' ), // callback
			'zearch-admin', // page
			'zearch_setting_section' // section
		);

		add_settings_field(
			'excerpt_2', // id
			'Excerpt', // title
			array( $this, 'excerpt_2_callback' ), // callback
			'zearch-admin', // page
			'zearch_setting_section' // section
		);

		add_settings_field(
			'author_3', // id
			'Author', // title
			array( $this, 'author_3_callback' ), // callback
			'zearch-admin', // page
			'zearch_setting_section' // section
		);
	}

	public function zearch_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['title_0'] ) ) {
			$sanitary_values['title_0'] = $input['title_0'];
		}

		if ( isset( $input['content_1'] ) ) {
			$sanitary_values['content_1'] = $input['content_1'];
		}

		if ( isset( $input['excerpt_2'] ) ) {
			$sanitary_values['excerpt_2'] = $input['excerpt_2'];
		}

		if ( isset( $input['author_3'] ) ) {
			$sanitary_values['author_3'] = $input['author_3'];
		}


		return $sanitary_values;
	}

	public function zearch_section_info() {
		
	}
	

	public function title_0_callback() {
		printf(
			'<input type="checkbox" name="test_option_name[title_0]" id="title_0" value="title_0" %s> <label for="title_0">Seachble</label>',
			( isset( $this->zearch_options['title_0'] ) && $this->zearch_options['title_0'] === 'title_0' ) ? 'checked' : ''
		);
	}

	public function content_1_callback() {
		printf(
			'<input type="checkbox" name="test_option_name[content_1]" id="content_1" value="content_1" %s> <label for="content_1">Seachble</label>',
			( isset( $this->zearch_options['content_1'] ) && $this->zearch_options['content_1'] === 'content_1' ) ? 'checked' : ''
		);
	}

	public function excerpt_2_callback() {
		printf(
			'<input type="checkbox" name="test_option_name[excerpt_2]" id="excerpt_2" value="excerpt_2" %s> <label for="excerpt_2">Seachble</label>',
			( isset( $this->zearch_options['excerpt_2'] ) && $this->zearch_options['excerpt_2'] === 'excerpt_2' ) ? 'checked' : ''
		);
	}

	public function author_3_callback() {
		printf(
			'<input type="checkbox" name="test_option_name[author_3]" id="author_3" value="author_3" %s> <label for="author_3">Seachble</label>',
			( isset( $this->zearch_options['author_3'] ) && $this->zearch_options['author_3'] === 'author_3' ) ? 'checked' : ''
		);
	}



}
if ( is_admin() )
	$zearch = new ZearchOptions();

//Api settings 
include('api-settings.php');